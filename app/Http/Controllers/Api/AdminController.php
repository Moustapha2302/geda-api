<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * KPI globaux
     */
    public function stats()
    {
        try {
            // Nombre total de documents
            $totalDocuments = Document::count();

            // Documents créés ce mois-ci
            $documentsThisMonth = Document::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            // Nombre total d'utilisateurs
            $totalUsers = User::count();

            // Nombre de services
            $totalServices = Service::count();

            // Taille totale des fichiers (en Mo)
            // Vérifie si la colonne 'size' existe, sinon utilise une valeur par défaut
            $totalSizeMo = 0;
            try {
                $totalSize = Document::sum('size');
                $totalSizeMo = round($totalSize / 1024 / 1024, 2);
            } catch (\Exception $e) {
                // Si la colonne 'size' n'existe pas, on ignore cette métrique
                \Log::warning('Column size does not exist in documents table');
            }

            return response()->json([
                'total_documents'        => $totalDocuments,
                'documents_this_month'   => $documentsThisMonth,
                'total_users'            => $totalUsers,
                'total_services'         => $totalServices,
                'total_size_mo'          => $totalSizeMo,
                'generated_at'           => now()->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin stats error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to generate stats',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logs filtrables avec pagination
     */
    public function logs(Request $request)
    {
        try {
            $logFile = storage_path('logs/audit.log');

            if (!file_exists($logFile)) {
                return response()->json([
                    'error' => 'Log file not found',
                ], 404);
            }

            // Lire les dernières lignes du fichier
            $lines = $request->query('lines', 100); // Par défaut 100 lignes
            $content = shell_exec("tail -n {$lines} " . escapeshellarg($logFile));

            // Si shell_exec n'est pas disponible, utiliser file()
            if ($content === null) {
                $allLines = file($logFile);
                $content = implode('', array_slice($allLines, -$lines));
            }

            return response()->json([
                'logs' => $content,
                'lines_returned' => $lines,
                'log_file' => 'audit.log',
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin logs error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to retrieve logs',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lance une sauvegarde de la base de données
     */
    public function backup()
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupPath = storage_path("backups/backup_{$timestamp}.sql");

            // Créer le dossier backups s'il n'existe pas
            if (!file_exists(storage_path('backups'))) {
                mkdir(storage_path('backups'), 0755, true);
            }

            // Utiliser la méthode Laravel pour exporter
            $dbName = env('DB_DATABASE');

            // Liste de toutes les tables
            $tables = DB::select('SHOW TABLES');
            $tableKey = "Tables_in_{$dbName}";

            $sql = "-- Database Backup\n";
            $sql .= "-- Generated: " . now()->toDateTimeString() . "\n";
            $sql .= "-- Database: {$dbName}\n\n";

            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                try {
                    // Structure de la table
                    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    $sql .= "\n-- Table: {$tableName}\n";
                    $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                    $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

                    // Données de la table
                    $rows = DB::table($tableName)->get();

                    if ($rows->isNotEmpty()) {
                        $sql .= "-- Data for table {$tableName}\n";

                        foreach ($rows as $row) {
                            $columns = array_keys((array)$row);
                            $values = array_map(function($val) {
                                if (is_null($val)) {
                                    return 'NULL';
                                }
                                return "'" . str_replace("'", "''", $val) . "'";
                            }, array_values((array)$row));

                            $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                        }

                        $sql .= "\n";
                    }
                } catch (\Exception $e) {
                    \Log::warning("Could not backup table {$tableName}", [
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

            file_put_contents($backupPath, $sql);

            $fileSize = filesize($backupPath);

            \Log::info('Database backup created', [
                'file' => $backupPath,
                'size' => $fileSize,
                'tables_count' => count($tables),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully',
                'method' => 'Laravel DB export (Windows compatible)',
                'file' => basename($backupPath),
                'path' => 'storage/backups/' . basename($backupPath),
                'size' => round($fileSize / 1024 / 1024, 2) . ' MB',
                'tables_count' => count($tables),
                'created_at' => now()->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Backup error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Backup failed',
                'message' => $e->getMessage(),
                'suggestion' => 'Check storage/logs/laravel.log for more details',
            ], 500);
        }
    }

    /**
     * Exports CSV/PDF/XML des documents
     */
    public function exports(Request $request)
    {
        try {
            $format = $request->query('format', 'csv'); // csv, json, xml
            $type = $request->query('type', 'documents'); // documents, users, services

            switch ($type) {
                case 'documents':
                    $data = Document::with('service')->get();
                    break;
                case 'users':
                    $data = User::with('service')->get();
                    break;
                case 'services':
                    $data = Service::all();
                    break;
                default:
                    return response()->json(['error' => 'Invalid type'], 400);
            }

            switch ($format) {
                case 'csv':
                    return $this->exportToCsv($data, $type);
                case 'json':
                    return response()->json($data);
                case 'xml':
                    return $this->exportToXml($data, $type);
                default:
                    return response()->json(['error' => 'Invalid format'], 400);
            }

        } catch (\Exception $e) {
            \Log::error('Export error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Export failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export en CSV
     */
    private function exportToCsv($data, $type)
    {
        $filename = "{$type}_" . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data, $type) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($data->isNotEmpty()) {
                $firstItem = $data->first();

                // Headers personnalisés selon le type
                if ($type === 'documents') {
                    fputcsv($file, ['ID', 'Service', 'Titre', 'Chemin', 'Statut', 'Créé le', 'Mis à jour le']);

                    foreach ($data as $doc) {
                        fputcsv($file, [
                            $doc->id,
                            $doc->service->name ?? 'N/A',
                            $doc->title,
                            $doc->file_path,
                            $doc->status,
                            $doc->created_at,
                            $doc->updated_at,
                        ]);
                    }
                } elseif ($type === 'users') {
                    fputcsv($file, ['ID', 'Nom', 'Email', 'Service', 'Créé le']);

                    foreach ($data as $user) {
                        fputcsv($file, [
                            $user->id,
                            $user->name,
                            $user->email,
                            $user->service->name ?? 'N/A',
                            $user->created_at,
                        ]);
                    }
                } else {
                    // Par défaut, toutes les colonnes
                    fputcsv($file, array_keys($firstItem->toArray()));

                    foreach ($data as $row) {
                        fputcsv($file, $row->toArray());
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export en XML
     */
    private function exportToXml($data, $type)
    {
        $xml = new \SimpleXMLElement('<root/>');

        foreach ($data as $item) {
            $child = $xml->addChild('item');
            foreach ($item->toArray() as $key => $value) {
                $child->addChild($key, htmlspecialchars($value));
            }
        }

        return response($xml->asXML(), 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', "attachment; filename=\"{$type}_" . now()->format('Y-m-d_H-i-s') . ".xml\"");
    }
}
