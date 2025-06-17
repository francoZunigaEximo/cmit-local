<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use Illuminate\Http\Request;

class UserSessionsController extends Controller
{
    public function getSessiones(Request $request)
    {
        $query = UserSession::where('user_id', $request->Id)
            ->select(
                'ip_address as ip',
                'user_agent as dispositivo',
                'login_at as ingreso',
                'logout_at as salida'
            )
            ->whereNotNull('logout_at')
            ->orderBy('login_at', 'desc')
            ->get();

        return response()->json($query);
    }
}
