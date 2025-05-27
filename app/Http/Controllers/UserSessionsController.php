<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use Illuminate\Http\Request;

class UserSessionsController extends Controller
{
    public function getSessiones(Request $request)
    {
        $userId = intval($request->Id);

        $query = UserSession::where('user_id', $userId)
            ->select(
                'ip_address as ip',
                'user_agent as dispositivo',
                'login_at as ingreso',
                'logout_at as salida'
            )
            // ->whereNotNull('logout_at')
            ->get();

        return response()->json($query);
    }
}
