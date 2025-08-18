<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $table = "user_sessions";

    protected $primaryKey = "id";

    protected $fillable = [
        'id',
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
        'last_heartbeat_at'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
