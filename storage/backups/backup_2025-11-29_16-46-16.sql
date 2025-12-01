-- Database Backup
-- Generated: 2025-11-29 16:46:16
-- Database: geda_api

SET FOREIGN_KEY_CHECKS=0;


-- Table: archives
DROP TABLE IF EXISTS `archives`;
CREATE TABLE `archives` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `status` enum('intermediate','final') NOT NULL DEFAULT 'intermediate',
  `arrived_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `moved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table archives
INSERT INTO `archives` (`id`, `title`, `status`, `arrived_at`, `moved_at`, `created_at`, `updated_at`, `deleted_at`) VALUES ('1', 'Test archive', 'intermediate', '2025-11-28 11:34:25', NULL, '2025-11-28 11:34:07', '2025-11-28 11:34:25', '2025-11-28 11:34:25');


-- Table: cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table cache
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES ('laravel-cache-spatie.permission.cache', 'a:3:{s:5:"alias";a:4:{s:1:"a";s:2:"id";s:1:"b";s:4:"name";s:1:"c";s:10:"guard_name";s:1:"r";s:5:"roles";}s:11:"permissions";a:2:{i:0;a:4:{s:1:"a";i:1;s:1:"b";s:16:"transfers.accept";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:1;a:4:{s:1:"a";i:2;s:1:"b";s:21:"metadata-types.manage";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:2;}}}s:5:"roles";a:2:{i:0;a:3:{s:1:"a";i:1;s:1:"b";s:8:"chef_urb";s:1:"c";s:3:"web";}i:1;a:3:{s:1:"a";i:2;s:1:"b";s:6:"chef_2";s:1:"c";s:3:"web";}}}', '1764519747');


-- Table: cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: documents
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `service_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `size` bigint(20) DEFAULT NULL COMMENT 'File size in bytes',
  `ocr_text` text DEFAULT NULL,
  `md5` varchar(32) NOT NULL,
  `status` enum('pending','ocr_done','in_review','validated','rejected','archived') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `ocr_status` varchar(50) NOT NULL DEFAULT 'pending',
  `ocr_processed_at` timestamp NULL DEFAULT NULL,
  `ocr_error` text DEFAULT NULL,
  `ocr_confidence` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documents_md5_unique` (`md5`),
  UNIQUE KEY `documents_uuid_unique` (`uuid`),
  KEY `documents_service_id_foreign` (`service_id`),
  KEY `documents_user_id_foreign` (`user_id`),
  CONSTRAINT `documents_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table documents
INSERT INTO `documents` (`id`, `uuid`, `service_id`, `user_id`, `title`, `file_path`, `size`, `ocr_text`, `md5`, `status`, `created_at`, `updated_at`, `deleted_at`, `ocr_status`, `ocr_processed_at`, `ocr_error`, `ocr_confidence`) VALUES ('1', 'f04a67a6-15e3-427e-9e6e-9374feea9f32', '2', '3', 'Capture d’écran (148).png', 'services/2/f04a67a6-15e3-427e-9e6e-9374feea9f32.png', NULL, 'SENELECOFFICIELLE V Suivi Fern) 120329 abonnés 3j-® Vous étes étudiant, développeur, start-up ou autres et passionné. par la recherche, le développement, innovation et lintelligence artificielle? Participez au Hackathon Edition 2025 organisé par Senelec. ‘Thame: Développer une application mobile et un dashboard IA pour I''écoute client et ''alignement stratégique. Envoyez votre candidature a hackathon@senelec.sn Date limite de dépét: le 09 Mai 2025 © HACKATHON SENELEC lopper une Application Mobile et Dashboard 1A pour Iécoute client et alignement stratégiai', '1fab313d22fa7dc3bb30a903c8fe68cc', 'validated', '2025-11-24 15:44:47', '2025-11-26 16:53:36', NULL, 'ocr_done', '2025-11-26 16:53:36', NULL, '95.00');
INSERT INTO `documents` (`id`, `uuid`, `service_id`, `user_id`, `title`, `file_path`, `size`, `ocr_text`, `md5`, `status`, `created_at`, `updated_at`, `deleted_at`, `ocr_status`, `ocr_processed_at`, `ocr_error`, `ocr_confidence`) VALUES ('5', '88fe4f66-3bdb-43f6-9743-e778892993ae', '2', '3', 'Capture d’écran (155).png', 'services/2/88fe4f66-3bdb-43f6-9743-e778892993ae.png', NULL, '9) Importer tes fa @ Nouveau chat Q Rechercher des chats ®) Bibliothéque © Sora 88 GPT @ canva ® Python Aujourd hui Résumé OTRS Implementation Ristactinn 1A Tavta at Cada Passer au plan supérieur 8s étendu aux meilleurs DOU! Instagram Bones (nttpat x sumé OTRS Implementat Mémoire pleine © ETUDE DE L''ENVIRONNEMENT TECHNIQUE OTRS Pour mettre en place le systéme, nous avons utilisé une machine virtuelle. Cela permet de simuler un vrai ordinateur a l''intérieur d''un autre. Nous avons d''abord installé le syst&me Debian, qui est une version de Linux. Ensuite, nous avons installé OTRS (Open Ticket Request System), le logiciel principal. Pour que le systéme fonctionne correctement, nous avons aussi installé : © _unserveur web (Apache) pour afficher les pages du systéme ; * une base de données MySQL pour stocker les tickets et les informations. Enfin, nous avons fait la configuration d‘OTRS : * création des réles (admin, agent, utilisateur), © définition des permissions pour chaque réle. Poser une question + F Outils (ChatGPT peut faire des erreurs. Envisagez de vérifier les informations importantes. 26°C A & @ @ G@) FRA ora 03/06/2025', 'f037c41fdce7a6381b47aa65dafbbefa', 'validated', '2025-11-24 20:19:30', '2025-11-26 13:49:44', NULL, 'ocr_done', '2025-11-26 13:49:44', NULL, '95.00');
INSERT INTO `documents` (`id`, `uuid`, `service_id`, `user_id`, `title`, `file_path`, `size`, `ocr_text`, `md5`, `status`, `created_at`, `updated_at`, `deleted_at`, `ocr_status`, `ocr_processed_at`, `ocr_error`, `ocr_confidence`) VALUES ('6', '2d651ce4-b0da-4ce7-8429-73c812ea69be', '2', '3', 'GEDAcccscvbc – Mairie de Ziguinchor.pdf', 'services/2/2d651ce4-b0da-4ce7-8429-73c812ea69be.pdf', NULL, NULL, 'bc031a9e2863d026ca6c6c79148772a0', 'pending', '2025-11-24 20:20:33', '2025-11-26 13:49:45', NULL, 'ocr_failed', '2025-11-26 13:49:45', 'Error! The command did not produce any output.

Generated command:
"C:\Program Files\Tesseract-OCR\tesseract.exe" "C:\Users\user\Desktop\Projet Mairie de zig\GEDA\geda-api\storage\app/private/services/2/2d651ce4-b0da-4ce7-8429-73c812ea69be.pdf" "C:\Users\user\AppData\Local\Temp\ocr819E.tmp" -l fra+eng --psm 3 --oem 3

Returned message:
Error opening data file C:\Program Files\Tesseract-OCR/tessdata/fra.traineddata
Please make sure the TESSDATA_PREFIX environment variable is set to your "tessdata" directory.
Failed loading language ''fra''
Error in pixReadStream: Pdf reading is not supported
Leptonica Error in pixRead: pix not read: C:\Users\user\Desktop\Projet Mairie de zig\GEDA\geda-api\storage\app/private/services/2/2d651ce4-b0da-4ce7-8429-73c812ea69be.pdf
Error during processing.', NULL);
INSERT INTO `documents` (`id`, `uuid`, `service_id`, `user_id`, `title`, `file_path`, `size`, `ocr_text`, `md5`, `status`, `created_at`, `updated_at`, `deleted_at`, `ocr_status`, `ocr_processed_at`, `ocr_error`, `ocr_confidence`) VALUES ('7', 'c179db67-8739-444c-9e91-8ad9e7113e90', '2', '3', 'Capture d’écran (157).png', 'services/2/c179db67-8739-444c-9e91-8ad9e7113e90.png', NULL, 'Centre Réseau et partage - ao x an | _ x & > Panneau de configuration > Réseau et Internet > Centre Réseau et partage vo Rechercher & a ; . , . doment Afficher les informations de base de votre réseau et configurer des connexions p Page d''accueil du panneau de configuration Alfcher vos réseaux actif E ager Moalifier les paramétres de la . -_ . carte Réseau non identifié Type d''accés: Pas d''accés réseau Modifier les paramatres de Réseau public ffl Best ec was partage avencés @ Proprietés de WiFi x ( Options de diffusion Modifier vos paramétres réseau deco rmultimédia en continu pa Gestion de Portage AGW Configurer une nouvelle connexion ou uf CConfigurez une connexion haut debit, d''¢ Proprités de: Protocole Internet version 4 (TCP/IPvA) x point daccés. Général Résoudre les problémes jagnostiquer et réparez les problémes Cq_Lesparamétres IP peuvent étre déterminés automatiquement si votre Diagnostiquer et réparez les problémes réseaule permet. Sion, vous devez demander les paramétres IP approprés 8 votre admiistrateur réseau. (Q Obtenir une adresse IP automatiquement © Uitiser adresse IP suivante : Adresse IP : 0. 22.62.21 Masque de sous-réseau : 255 . 255.255. 0 Passerelle par défaut : 10. 22. 62.254 Voir aussi Obtenir les adresses des serveurs DNS automatiquement Options Internet @ User adresse de serveur DNS suivante : Pare-feu Windows Defender Serveur ONS préféré 0.2.2.1 + a Serveur DNS auxiiire : 2.1.28 = | [alder les paramétres en cuitant Tew r) OK ‘aonuler 1) @Eror Q Find and replace 6 Console @ Start Proxy @ Cookies m om | P& Taper ici pour rechercher', 'b01c03c8ee7b921b628c417324b78d23', 'validated', '2025-11-24 20:22:11', '2025-11-26 13:49:50', NULL, 'ocr_done', '2025-11-26 13:49:50', NULL, '95.00');
INSERT INTO `documents` (`id`, `uuid`, `service_id`, `user_id`, `title`, `file_path`, `size`, `ocr_text`, `md5`, `status`, `created_at`, `updated_at`, `deleted_at`, `ocr_status`, `ocr_processed_at`, `ocr_error`, `ocr_confidence`) VALUES ('9', '178f56bb-8028-49b8-aae6-7e29905bbdc9', '2', '3', 'Capture d’écran (153).png', 'services/2/178f56bb-8028-49b8-aae6-7e29905bbdc9.png', NULL, 'ER Astah - [C:\Users\user\Desktop\Cours UT Génie logiciel\PFC\Use case. Ticket.asta] Edit Diagram Alignment View Tool Window Help New New By Template Open. Save Save AS... Close GCevools: Merge Project... Ipro]Reference Model Management [pro]Compare Project. Print Setup (Project) Print Setup (Diagram) [a Print Preview Preview Mutt Print Multi Print Bit 1. C\Users\user\Desktop\Cours UT Génie logiciel\PFC\Use case Ticketasta 2. CProgram Files (x86)\astah-UML\Welcome.asta Java ce Oct CtHeN, Ctrles ctHeQ ar ig} in +o F | _ ee — ee Q Eeonsuiter 1 Historique Eonsuiter leo statistiques Globalez —__ a données des sickate Le ‘Close om | P& Taper ici pour rechercher', 'a2aabb4c1823faa444b87e9fbc379d71', 'validated', '2025-11-25 12:12:00', '2025-11-26 13:49:53', NULL, 'ocr_done', '2025-11-26 13:49:53', NULL, '95.00');
INSERT INTO `documents` (`id`, `uuid`, `service_id`, `user_id`, `title`, `file_path`, `size`, `ocr_text`, `md5`, `status`, `created_at`, `updated_at`, `deleted_at`, `ocr_status`, `ocr_processed_at`, `ocr_error`, `ocr_confidence`) VALUES ('10', '99240d3d-45c6-4879-bb0f-6caee299a99f', '2', '3', 'Cahier des charges (1).pdf', 'services/2/99240d3d-45c6-4879-bb0f-6caee299a99f.pdf', NULL, NULL, '6203ac2039edbad809184d5ddef21e2b', 'pending', '2025-11-26 13:48:36', '2025-11-26 13:49:53', NULL, 'ocr_failed', '2025-11-26 13:49:53', 'Error! The command did not produce any output.

Generated command:
"C:\Program Files\Tesseract-OCR\tesseract.exe" "C:\Users\user\Desktop\Projet Mairie de zig\GEDA\geda-api\storage\app/private/services/2/99240d3d-45c6-4879-bb0f-6caee299a99f.pdf" "C:\Users\user\AppData\Local\Temp\ocrA19D.tmp" -l fra+eng --psm 3 --oem 3

Returned message:
Error opening data file C:\Program Files\Tesseract-OCR/tessdata/fra.traineddata
Please make sure the TESSDATA_PREFIX environment variable is set to your "tessdata" directory.
Failed loading language ''fra''
Error in pixReadStream: Pdf reading is not supported
Leptonica Error in pixRead: pix not read: C:\Users\user\Desktop\Projet Mairie de zig\GEDA\geda-api\storage\app/private/services/2/99240d3d-45c6-4879-bb0f-6caee299a99f.pdf
Error during processing.', NULL);
INSERT INTO `documents` (`id`, `uuid`, `service_id`, `user_id`, `title`, `file_path`, `size`, `ocr_text`, `md5`, `status`, `created_at`, `updated_at`, `deleted_at`, `ocr_status`, `ocr_processed_at`, `ocr_error`, `ocr_confidence`) VALUES ('11', 'd60bf382-e37f-4b9a-b154-5736e2a9fe60', '2', '3', 'Cahier des charges – Hackathon Musée des Civilisations Noires.docx VF.pdf', 'services/2/d60bf382-e37f-4b9a-b154-5736e2a9fe60.pdf', NULL, NULL, '2093e4e7aaea6b00b80a285ae39c4db2', 'pending', '2025-11-26 13:49:20', '2025-11-26 13:49:54', NULL, 'ocr_failed', '2025-11-26 13:49:54', 'Error! The command did not produce any output.

Generated command:
"C:\Program Files\Tesseract-OCR\tesseract.exe" "C:\Users\user\Desktop\Projet Mairie de zig\GEDA\geda-api\storage\app/private/services/2/d60bf382-e37f-4b9a-b154-5736e2a9fe60.pdf" "C:\Users\user\AppData\Local\Temp\ocrA4BA.tmp" -l fra+eng --psm 3 --oem 3

Returned message:
Error opening data file C:\Program Files\Tesseract-OCR/tessdata/fra.traineddata
Please make sure the TESSDATA_PREFIX environment variable is set to your "tessdata" directory.
Failed loading language ''fra''
Error in pixReadStream: Pdf reading is not supported
Leptonica Error in pixRead: pix not read: C:\Users\user\Desktop\Projet Mairie de zig\GEDA\geda-api\storage\app/private/services/2/d60bf382-e37f-4b9a-b154-5736e2a9fe60.pdf
Error during processing.', NULL);
INSERT INTO `documents` (`id`, `uuid`, `service_id`, `user_id`, `title`, `file_path`, `size`, `ocr_text`, `md5`, `status`, `created_at`, `updated_at`, `deleted_at`, `ocr_status`, `ocr_processed_at`, `ocr_error`, `ocr_confidence`) VALUES ('12', 'd9bfff38-66f5-4bdd-b63d-d4bf5af924dd', '2', '3', 'Cahier des Charges Consolidé – Projet JOKKALANTE.pdf', 'services/2/d9bfff38-66f5-4bdd-b63d-d4bf5af924dd.pdf', NULL, NULL, '2c3fee4eccda8cf9d36ae1614899df07', 'pending', '2025-11-26 13:49:20', '2025-11-26 13:56:21', NULL, 'ocr_failed', '2025-11-26 13:56:21', 'Error! The command did not produce any output.

Generated command:
"C:\Program Files\Tesseract-OCR\tesseract.exe" "C:\Users\user\Desktop\Projet Mairie de zig\GEDA\geda-api\storage\app/private/services/2/d9bfff38-66f5-4bdd-b63d-d4bf5af924dd.pdf" "C:\Users\user\AppData\Local\Temp\ocr8D20.tmp" -l fra+eng --psm 3 --oem 3

Returned message:
Error opening data file C:\Program Files\Tesseract-OCR/tessdata/fra.traineddata
Please make sure the TESSDATA_PREFIX environment variable is set to your "tessdata" directory.
Failed loading language ''fra''
Error in pixReadStream: Pdf reading is not supported
Leptonica Error in pixRead: pix not read: C:\Users\user\Desktop\Projet Mairie de zig\GEDA\geda-api\storage\app/private/services/2/d9bfff38-66f5-4bdd-b63d-d4bf5af924dd.pdf
Error during processing.', NULL);
INSERT INTO `documents` (`id`, `uuid`, `service_id`, `user_id`, `title`, `file_path`, `size`, `ocr_text`, `md5`, `status`, `created_at`, `updated_at`, `deleted_at`, `ocr_status`, `ocr_processed_at`, `ocr_error`, `ocr_confidence`) VALUES ('13', '55ae6e63-a825-413a-811b-889720198a5e', '2', '7', 'Facture test métadonnées', 'fake/path.pdf', NULL, NULL, '1cf04c8d27a287dd96375058f07b9936', 'pending', '2025-11-27 16:42:35', '2025-11-28 10:43:02', '2025-11-28 10:43:02', 'pending', NULL, NULL, NULL);


-- Table: failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table jobs
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES ('1', 'default', '{"uuid":"3d41ed5e-349a-4515-96ce-414d2f07c140","displayName":"App\\Jobs\\ProcessOcr","job":"Illuminate\\Queue\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":300,"retryUntil":null,"data":{"commandName":"App\\Jobs\\ProcessOcr","command":"O:19:\"App\\Jobs\\ProcessOcr\":1:{s:11:\"\u0000*\u0000document\";O:45:\"Illuminate\\Contracts\\Database\\ModelIdentifier\":5:{s:5:\"class\";s:19:\"App\\Models\\Document\";s:2:\"id\";i:10;s:9:\"relations\";a:0:{}s:10:\"connection\";s:5:\"mysql\";s:15:\"collectionClass\";N;}}"},"createdAt":1764164918,"delay":null}', '0', NULL, '1764164919', '1764164919');
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES ('2', 'default', '{"uuid":"a7ce93ff-dc17-419f-823d-526c4d6ae996","displayName":"App\\Jobs\\ProcessOcr","job":"Illuminate\\Queue\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":300,"retryUntil":null,"data":{"commandName":"App\\Jobs\\ProcessOcr","command":"O:19:\"App\\Jobs\\ProcessOcr\":1:{s:11:\"\u0000*\u0000document\";O:45:\"Illuminate\\Contracts\\Database\\ModelIdentifier\":5:{s:5:\"class\";s:19:\"App\\Models\\Document\";s:2:\"id\";i:11;s:9:\"relations\";a:0:{}s:10:\"connection\";s:5:\"mysql\";s:15:\"collectionClass\";N;}}"},"createdAt":1764164960,"delay":null}', '0', NULL, '1764164960', '1764164960');
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES ('3', 'default', '{"uuid":"618e6753-ec38-4c0c-a999-709657d58c2c","displayName":"App\\Jobs\\ProcessOcr","job":"Illuminate\\Queue\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":300,"retryUntil":null,"data":{"commandName":"App\\Jobs\\ProcessOcr","command":"O:19:\"App\\Jobs\\ProcessOcr\":1:{s:11:\"\u0000*\u0000document\";O:45:\"Illuminate\\Contracts\\Database\\ModelIdentifier\":5:{s:5:\"class\";s:19:\"App\\Models\\Document\";s:2:\"id\";i:12;s:9:\"relations\";a:0:{}s:10:\"connection\";s:5:\"mysql\";s:15:\"collectionClass\";N;}}"},"createdAt":1764164960,"delay":null}', '0', NULL, '1764164960', '1764164960');


-- Table: metadata_types
DROP TABLE IF EXISTS `metadata_types`;
CREATE TABLE `metadata_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`fields`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `metadata_types_service_id_index` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: metadata_values
DROP TABLE IF EXISTS `metadata_values`;
CREATE TABLE `metadata_values` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) unsigned NOT NULL,
  `metadata_type_id` bigint(20) unsigned NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`value`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `metadata_values_document_id_metadata_type_id_unique` (`document_id`,`metadata_type_id`),
  KEY `metadata_values_metadata_type_id_foreign` (`metadata_type_id`),
  CONSTRAINT `metadata_values_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `metadata_values_metadata_type_id_foreign` FOREIGN KEY (`metadata_type_id`) REFERENCES `metadata_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table migrations
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('1', '0001_01_01_000000_create_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('2', '0001_01_01_000001_create_cache_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('3', '0001_01_01_000002_create_jobs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('4', '2025_11_24_104318_create_personal_access_tokens_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('5', '2025_11_24_104943_create_services_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('6', '2025_11_24_104944_create_documents_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('7', '2025_11_24_104946_create_types_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('8', '2025_11_24_112311_add_service_and_role_to_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('9', '2025_11_24_120914_make_service_id_nullable_in_users', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('10', '2025_11_24_154033_add_uuid_to_documents_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('11', '2025_11_25_105722_update_documents_status_values', '3');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('12', '2025_11_25_141805_add_ocr_text_to_documents_table', '4');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('13', '2025_11_26_104544_add_soft_deletes_to_documents_table', '5');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('14', '2025_11_26_111345_add_ocr_columns_to_documents_final', '6');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('15', '2025_11_26_150440_update_status_enum_in_documents_table', '7');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('16', '2025_11_26_160023_create_transfers_table', '8');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('17', '2025_11_27_101705_add_soft_delete_to_transfers_table', '9');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('18', '2025_11_27_115954_create_permission_tables', '9');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('19', '2025_11_27_122313_create_metadata_types_table', '10');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('20', '2025_11_27_160412_create_metadata_values_table', '11');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('21', '2025_11_27_162156_modify_title_column_in_documents_table', '12');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('22', '2025_11_28_104949_create_archives_table', '13');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('23', '2025_11_28_112657_add_soft_delete_to_archives_table', '14');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('24', '2025_11_29_163101_add_size_to_documents_table', '15');


-- Table: model_has_permissions
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: model_has_roles
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table model_has_roles
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES ('1', 'App\Models\User', '7');
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES ('2', 'App\Models\User', '3');
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES ('2', 'App\Models\User', '7');
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES ('4', 'App\Models\User', '1');
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES ('4', 'App\Models\User', '8');
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES ('6', 'App\Models\User', '1');


-- Table: password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table permissions
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ('1', 'transfers.accept', 'web', '2025-11-27 12:01:24', '2025-11-27 12:01:24');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ('2', 'metadata-types.manage', 'web', '2025-11-27 12:51:21', '2025-11-27 12:51:21');


-- Table: personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table personal_access_tokens
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('1', 'App\Models\User', '3', 'geda-token', '6dc8cffd67b9c5c12a5e01adef6d58f00de1f051b67927b21f2f8afddb979f69', '["*"]', NULL, NULL, '2025-11-24 12:47:25', '2025-11-24 12:47:25');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('3', 'App\Models\User', '3', 'geda-token', 'e35162c680744926ed9619bf117fa4fcb11e32f58ea29587ff6c1e68c0ba7f77', '["*"]', '2025-11-24 14:04:56', NULL, '2025-11-24 13:53:39', '2025-11-24 14:04:56');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('5', 'App\Models\User', '3', 'geda-token', '049309f4bbbba23999f858d45ece7835c12a15fa23a7f687b5521b126abbe983', '["*"]', '2025-11-24 14:43:04', NULL, '2025-11-24 14:27:52', '2025-11-24 14:43:04');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('6', 'App\Models\User', '3', 'geda-token', '37d9a6eee323f30c28e3f8cc65eb4242ade97a9a7032240442704e7c9bec0eaa', '["*"]', '2025-11-25 08:47:52', NULL, '2025-11-24 14:44:10', '2025-11-25 08:47:52');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('8', 'App\Models\User', '3', 'geda-token', '5b919fb82a3fc1fdd221204241be25876a5a1a4f39b850053e9b9592962bafca', '["*"]', '2025-11-25 09:04:12', NULL, '2025-11-24 20:07:58', '2025-11-25 09:04:12');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('9', 'App\Models\User', '5', 'geda-token', 'da16cb0b764e532f32ff088bc86c4e1be48857e9883bc86de56b95da291ef49e', '["*"]', NULL, NULL, '2025-11-25 08:54:34', '2025-11-25 08:54:34');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('10', 'App\Models\User', '6', 'geda-token', 'be20754ece5db25d8f0014dc22813c1d7ce2c24ca2b383f2e7519499ae845ee4', '["*"]', NULL, NULL, '2025-11-25 08:54:52', '2025-11-25 08:54:52');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('11', 'App\Models\User', '3', 'geda-token', 'fecccb2eb9698abe9ba3dd1a6166f48696de609dbc196cba6e7d3106aac1bb09', '["*"]', '2025-11-25 16:01:57', NULL, '2025-11-25 09:07:38', '2025-11-25 16:01:57');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('13', 'App\Models\User', '3', 'geda-token', '13d0d72564d30a0d45f633339f34106cfb637ddd876789bc26df21b80a7f9ec6', '["*"]', '2025-11-26 16:54:20', NULL, '2025-11-26 10:09:18', '2025-11-26 16:54:20');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('14', 'App\Models\User', '3', 'geda-token', 'bb9888d73b4a597aa5d03d45e2c9385910b4e3e82645dc71677c746577546a7f', '["*"]', '2025-11-26 15:46:08', NULL, '2025-11-26 14:06:44', '2025-11-26 15:46:08');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('15', 'App\Models\User', '3', 'geda-token', '15470c896816f76186be66cd9764aa15c2ef5ac11cd9a13b3571103b8683f70d', '["*"]', '2025-11-27 08:17:04', NULL, '2025-11-26 16:05:51', '2025-11-27 08:17:04');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('16', 'App\Models\User', '3', 'geda-token', '735047a24a03a838c7def17e683193cefedf374e448f093b9bd36099b1d73052', '["*"]', NULL, NULL, '2025-11-27 08:45:17', '2025-11-27 08:45:17');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('17', 'App\Models\User', '3', 'geda-token', '643310f1a86632de71740e0f73e87d2dd7231e2165e2d1bc3679b3b126cbb26f', '["*"]', '2025-11-27 08:48:45', NULL, '2025-11-27 08:46:37', '2025-11-27 08:48:45');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('18', 'App\Models\User', '3', 'test', 'ab38014c2d4257f4b3147aac629995661fc610af6a4cf11fe312937a882487a8', '["*"]', '2025-11-27 11:28:42', NULL, '2025-11-27 08:49:45', '2025-11-27 11:28:42');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('19', 'App\Models\User', '7', 'postman', '3025bf9138d64b934d9c4193b4514101076ea9383f75e8a94453f352e9651ab9', '["*"]', '2025-11-28 08:24:37', NULL, '2025-11-27 11:42:47', '2025-11-28 08:24:37');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('20', 'App\Models\User', '3', 'geda-token', '0b4c9a70b0a683c9ac2afe7ad2f3c7540418ff4d4885352664cdf0f9c52c7e3f', '["*"]', NULL, NULL, '2025-11-27 12:38:46', '2025-11-27 12:38:46');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('21', 'App\Models\User', '3', 'geda-token', 'de6bba254d0404c3c617d47defd417676ecb28d1e886e03634ed1d183e7781f3', '["*"]', '2025-11-28 15:37:27', NULL, '2025-11-28 08:55:56', '2025-11-28 15:37:27');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES ('25', 'App\Models\User', '1', 'admin-test', '9e467e4b52e7451d40b1735ce190bf64e78ee6d85b45b764832dfa871abaf750', '["*"]', '2025-11-29 16:46:15', NULL, '2025-11-29 16:07:08', '2025-11-29 16:46:15');


-- Table: role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table role_has_permissions
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES ('1', '1');
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES ('2', '2');


-- Table: roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table roles
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ('1', 'chef_urb', 'web', '2025-11-27 12:01:05', '2025-11-27 12:01:05');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ('2', 'chef_2', 'web', '2025-11-27 12:47:58', '2025-11-27 12:47:58');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ('3', 'chef_2', 'api', '2025-11-27 13:53:01', '2025-11-27 13:53:01');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ('4', 'ar', 'web', '2025-11-28 10:57:39', '2025-11-28 10:57:39');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ('5', 'a', 'web', '2025-11-28 10:57:39', '2025-11-28 10:57:39');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ('6', 'admin', 'web', '2025-11-29 15:46:05', '2025-11-29 15:46:05');


-- Table: services
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `services_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table services
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('1', 's01', 'État-Civil', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('2', 's02', 'Finances', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('3', 's03', 'Urbanisme', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('4', 's04', 'Ressources Humaines', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('5', 's05', 'Communication', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('6', 's06', 'Services Techniques Communnaux', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('7', 's07', 'Direction Planification & CT', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('8', 's08', 'Cellule Partenariat & Coopération', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('9', 's09', 'Cellule Juridique & Contentieux', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('10', 's10', 'Cellule Informatique', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('11', 's11', 'Secrétariat Général', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('12', 's12', 'Cabinet du Maire', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');
INSERT INTO `services` (`id`, `code`, `name`, `logo`, `created_at`, `updated_at`) VALUES ('13', 's13', 'Archives Municipales', NULL, '2025-11-24 12:10:09', '2025-11-24 12:10:09');


-- Table: sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table sessions
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('edhL1sFZp1vJDdxN0xUk8LJw3XEivAGSqZsJzzhf', NULL, '127.0.0.1', 'PostmanRuntime/7.49.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUXNaMFlaanZiT0hPVFVyenRQZHZjdkdPTTl2UjdHdWxZMkRPVW5FcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', '1764243777');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('iRsibeZIVXnVLMJyT3aIJZLsLCIjRH0449S5DyWl', NULL, '127.0.0.1', 'PostmanRuntime/7.49.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU0U2RlZKTXNTWDNxTXhRdDczSzF0OXdYam02TEdiYWhJcGRIUGhheiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', '1764262452');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('NjUycr5BsmaUVG7rjgZgf9KPd2JAcK0ZGdacnY5h', NULL, '127.0.0.1', 'PostmanRuntime/7.49.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWTdiUXFKa0hJdGJrT0xqSllrS3JhSHR6ZGpMWGdwdDJwNDNYYkVEOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', '1764318282');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('pSyF3nUC4ukmfcxqzKnMuLPqG8E5p0I5oIdbAq4s', NULL, '127.0.0.1', 'PostmanRuntime/7.49.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNjRkMHhKdVZiNllnV1ZXNEZpSmNqMU14YW5jUVMyelJtTlRzSERjdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', '1764068628');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('uuMgTneCpyG5xLPfMuApPHuwTAaWD2ZlNmiiLClM', NULL, '127.0.0.1', 'PostmanRuntime/7.49.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSUl5MWhoYmdRQlJsMnpkdWQ0RjJhQ3ZmVkZ1bFI3dlNIcDFJcE1xSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', '1764170608');


-- Table: transfers
DROP TABLE IF EXISTS `transfers`;
CREATE TABLE `transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) unsigned NOT NULL,
  `from_service_id` bigint(20) unsigned NOT NULL,
  `to_service_id` bigint(20) unsigned NOT NULL,
  `type` enum('internal','external') NOT NULL DEFAULT 'internal',
  `signed_url` varchar(2048) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','accepted','rejected','expired') NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `initiated_by` bigint(20) unsigned NOT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfers_document_id_foreign` (`document_id`),
  KEY `transfers_from_service_id_foreign` (`from_service_id`),
  KEY `transfers_to_service_id_foreign` (`to_service_id`),
  KEY `transfers_initiated_by_foreign` (`initiated_by`),
  KEY `transfers_processed_by_foreign` (`processed_by`),
  CONSTRAINT `transfers_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfers_from_service_id_foreign` FOREIGN KEY (`from_service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfers_initiated_by_foreign` FOREIGN KEY (`initiated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfers_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transfers_to_service_id_foreign` FOREIGN KEY (`to_service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table transfers
INSERT INTO `transfers` (`id`, `document_id`, `from_service_id`, `to_service_id`, `type`, `signed_url`, `expires_at`, `status`, `reason`, `initiated_by`, `processed_by`, `created_at`, `updated_at`, `deleted_at`) VALUES ('1', '1', '2', '3', 'internal', NULL, '2025-12-31 23:59:59', 'pending', NULL, '3', NULL, '2025-11-27 09:11:41', '2025-11-27 09:11:41', NULL);
INSERT INTO `transfers` (`id`, `document_id`, `from_service_id`, `to_service_id`, `type`, `signed_url`, `expires_at`, `status`, `reason`, `initiated_by`, `processed_by`, `created_at`, `updated_at`, `deleted_at`) VALUES ('2', '1', '2', '3', 'internal', NULL, '2025-12-31 23:59:59', 'pending', NULL, '3', NULL, '2025-11-27 10:49:48', '2025-11-27 10:49:48', NULL);
INSERT INTO `transfers` (`id`, `document_id`, `from_service_id`, `to_service_id`, `type`, `signed_url`, `expires_at`, `status`, `reason`, `initiated_by`, `processed_by`, `created_at`, `updated_at`, `deleted_at`) VALUES ('3', '1', '2', '3', 'internal', NULL, '2025-12-31 23:59:59', 'pending', NULL, '3', NULL, '2025-11-27 11:07:32', '2025-11-27 11:07:32', NULL);
INSERT INTO `transfers` (`id`, `document_id`, `from_service_id`, `to_service_id`, `type`, `signed_url`, `expires_at`, `status`, `reason`, `initiated_by`, `processed_by`, `created_at`, `updated_at`, `deleted_at`) VALUES ('4', '1', '2', '3', 'internal', NULL, '2025-12-31 23:59:59', 'pending', NULL, '3', NULL, '2025-11-27 11:12:30', '2025-11-27 11:12:30', NULL);


-- Table: types
DROP TABLE IF EXISTS `types`;
CREATE TABLE `types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `types_service_id_foreign` (`service_id`),
  CONSTRAINT `types_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `service_id` bigint(20) unsigned DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'agent',
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `two_factor_recovery_codes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_service_id_foreign` (`service_id`),
  CONSTRAINT `users_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table users
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `service_id`, `role`, `two_factor_secret`, `two_factor_recovery_codes`) VALUES ('1', 'Chef État-Civil', 'chef.etatcivil@mairie.sn', NULL, '$2y$12$4v2tqVizHKLB57rjrqebce/NQjblU0rZ/p6W7QJmgp.O2g/0P5bpm', NULL, '2025-11-24 12:10:10', '2025-11-24 12:10:10', '2', 'chef', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `service_id`, `role`, `two_factor_secret`, `two_factor_recovery_codes`) VALUES ('2', 'Agent État-Civil', 'agent.etatcivil@mairie.sn', NULL, '$2y$12$SbTEjUo8O1G6ZEKr6lemWOFaLbS7Xx.jJpAVSaPgEeC9Q/lkiLuQq', NULL, '2025-11-24 12:10:11', '2025-11-24 12:10:11', '1', 'agent', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `service_id`, `role`, `two_factor_secret`, `two_factor_recovery_codes`) VALUES ('3', 'Chef Finances', 'chef.finances@mairie.sn', NULL, '$2y$12$HQLHN7uqNq7OJr51WpKk2OTB9JBlFj7LRbCAULkQVjgsq1yJWPaNi', NULL, '2025-11-24 12:10:11', '2025-11-24 12:10:11', '2', 'chef', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `service_id`, `role`, `two_factor_secret`, `two_factor_recovery_codes`) VALUES ('4', 'Agent Finances', 'agent.finances@mairie.sn', NULL, '$2y$12$MjlhNTrRW40wdh1MwUhw/eX9Vdyv8b2lJD3L0UXk3vq6r0cJG16Dy', NULL, '2025-11-24 12:10:12', '2025-11-24 12:10:12', '2', 'agent', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `service_id`, `role`, `two_factor_secret`, `two_factor_recovery_codes`) VALUES ('5', 'Secrétaire Général', 'sg@mairie.sn', NULL, '$2y$12$2ElXlC0kYXd450ErR1p0wOEwhXeuZq07p//.x2HTjNlecVyQ5x.BK', NULL, '2025-11-24 12:10:13', '2025-11-24 12:10:13', NULL, 'sg', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `service_id`, `role`, `two_factor_secret`, `two_factor_recovery_codes`) VALUES ('6', 'Maire', 'maire@mairie.sn', NULL, '$2y$12$JHq0OUXcixXCyoFb9StsLOR7dQuqGrlVSmQF.3tj2DNCkm35v8A0y', NULL, '2025-11-24 12:10:13', '2025-11-24 12:10:13', NULL, 'maire', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `service_id`, `role`, `two_factor_secret`, `two_factor_recovery_codes`) VALUES ('7', 'Chef service urbanisme', 'chef.urbanismes@mairie.sn', NULL, '$2y$12$mUYsaeGeJpei5HbbYi4n2O8qcUWMe8UTre4IqjQyt8p0u3VXwgJL.', NULL, '2025-11-27 11:42:08', '2025-11-27 13:13:40', '2', 'agent', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `service_id`, `role`, `two_factor_secret`, `two_factor_recovery_codes`) VALUES ('8', 'Emanuel Brekke', 'reese.abbott@example.org', '2025-11-28 11:02:08', '$2y$12$5JSCOK9VKRm48/1yE65fe.VEnC6Fy5khDyEB8uyTf0tdpexQGzbBO', 'wkyR18Awjq', '2025-11-28 11:02:08', '2025-11-28 11:02:08', NULL, 'agent', NULL, NULL);


SET FOREIGN_KEY_CHECKS=1;
