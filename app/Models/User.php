<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'empleado_id',
        'role',
        'is_active',
        'avatar_path',
        'phone',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
        'preferences',
        'theme',
        'language',
        'timezone',
        'notifications_enabled',
        'two_factor_enabled',
        'password_changed_at',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'preferences' => 'json',
        'notifications_enabled' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'password_changed_at' => 'datetime',
    ];

    // Relación con empleado
    public function empleado()
    {
        return $this->belongsTo(\App\Modules\Empleados\Models\Empleado::class, 'empleado_id', 'IDEmpleado');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Métodos de utilidad
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function createApiToken($name = 'api-token')
    {
        return $this->createToken($name)->plainTextToken;
    }

    // Métodos adicionales
    public function getFullNameAttribute()
    {
        return $this->empleado ? $this->empleado->nombre_completo : $this->name;
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar_path ? asset('storage/' . $this->avatar_path) : asset('images/default-avatar.png');
    }

    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function incrementLoginAttempts()
    {
        $this->increment('login_attempts');
        
        // Bloquear después de 5 intentos fallidos por 30 minutos
        if ($this->login_attempts >= 5) {
            $this->update([
                'locked_until' => now()->addMinutes(30),
            ]);
        }
    }

    public function resetLoginAttempts()
    {
        $this->update([
            'login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    public function updateLastLogin($ipAddress = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ]);
    }

    public function hasPermission($permission)
    {
        $permissions = [
            'admin' => ['view', 'create', 'edit', 'delete', 'manage_users'],
            'manager' => ['view', 'create', 'edit'],
            'user' => ['view'],
        ];

        return in_array($permission, $permissions[$this->role] ?? []);
    }

    public function getPreference($key, $default = null)
    {
        return data_get($this->preferences, $key, $default);
    }

    public function setPreference($key, $value)
    {
        $preferences = $this->preferences ?? [];
        data_set($preferences, $key, $value);
        $this->update(['preferences' => $preferences]);
    }
}