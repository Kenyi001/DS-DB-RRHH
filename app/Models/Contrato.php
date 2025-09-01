<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    use HasFactory;

    protected $table = 'contratos';
    protected $primaryKey = 'IDContrato';
    
    public $timestamps = false;

    protected $fillable = [
        'IDEmpleado',
        'IDDepartamento',
        'IDCargo',
        'NumeroContrato',
        'FechaInicio',
        'FechaFin',
        'HaberBasico',
        'Estado',
        'Observaciones',
        'UsuarioCreacion',
        'UsuarioModificacion'
    ];

    protected $casts = [
        'FechaInicio' => 'date',
        'FechaFin' => 'date', 
        'HaberBasico' => 'decimal:2',
        'FechaCreacion' => 'datetime',
        'FechaModificacion' => 'datetime'
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'IDEmpleado', 'IDEmpleado');
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'IDDepartamento', 'IDDepartamento');
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'IDCargo', 'IDCargo');
    }

    public function gestionSalarios(): HasMany
    {
        return $this->hasMany(GestionSalario::class, 'IDContrato', 'IDContrato');
    }

    public function scopeActivos($query)
    {
        return $query->where('Estado', 'Activo');
    }

    public function scopeVigentes($query)
    {
        $hoy = now()->toDateString();
        return $query->where('FechaInicio', '<=', $hoy)
                    ->where(function($q) use ($hoy) {
                        $q->whereNull('FechaFin')
                          ->orWhere('FechaFin', '>=', $hoy);
                    });
    }
}