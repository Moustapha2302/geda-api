<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TwoFAController extends Controller
{
    public function enable(Request $request)
    {
        // TODO : générer QR code TOTP
        return response()->json(['success' => true, 'message' => '2FA à implémenter']);
    }

    public function verify(Request $request)
    {
        // TODO : vérifier TOTP
        return response()->json(['success' => true, 'message' => 'Vérification 2FA à implémenter']);
    }
}
