<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';
    protected $primaryKey = 'IDEmpleado';
    
    public $timestamps = false;

    protected $fillable = [
        'CI',
        'Nombres',
        'ApellidoPaterno', 
        'ApellidoMaterno',
        'FechaNacimiento',
        'Email',
        'Telefono',
        'Estado',
        'UsuarioCreacion',
        'UsuarioModificacion'
    ];

    protected $casts = [
        'FechaNacimiento' => 'date',
        'Estado' => 'boolean',
        'FechaCreacion' => 'datetime',
        'FechaModificacion' => 'datetime',
        'FechaBaja' => 'datetime'
    ];

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'IDEmpleado', 'IDEmpleado');
    }

    public function contratosActivos(): HasMany
    {
        return $this->contratos()->where('Estado', 'Activo');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->Nombres . ' ' . $this->ApellidoPaterno . ' ' . $this->ApellidoMaterno);
    }

    public function scopeActivos($query)
    {
        return $query->where('Estado', true);
    }
}