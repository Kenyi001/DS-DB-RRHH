<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Empleados\Models\Empleado;

class Contrato extends Model
{
    protected $table = 'Contratos';
    protected $primaryKey = 'IDContrato';
    public $timestamps = false;

    protected $fillable = [
        'IDEmpleado',
        'IDCategoria', 
        'IDCargo',
        'IDDepartamento',
        'NumeroContrato',
        'TipoContrato',
        'FechaContrato',
        'FechaInicio',
        'FechaFin',
        'HaberBasico',
        'Estado'
    ];

    protected $casts = [
        'FechaContrato' => 'date',
        'FechaInicio' => 'date', 
        'FechaFin' => 'date',
        'HaberBasico' => 'decimal:2',
        'Estado' => 'boolean'
    ];

    // Relaciones
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'IDEmpleado', 'IDEmpleado');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'IDCategoria', 'IDCategoria');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'IDCargo', 'IDCargo');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'IDDepartamento', 'IDDepartamento');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('Estado', 1);
    }

    public function scopeVigentes($query)
    {
        return $query->where(function($q) {
            $q->whereNull('FechaFin')
              ->orWhere('FechaFin', '>=', now());
        });
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('TipoContrato', $tipo);
    }

    // Accessors
    public function getEsVigenteAttribute()
    {
        return is_null($this->FechaFin) || $this->FechaFin >= now();
    }

    public function getDiasVigenciaAttribute()
    {
        if (!$this->FechaFin) return null;
        return now()->diffInDays($this->FechaFin, false);
    }

    public function getEstadoTextoAttribute()
    {
        return $this->Estado ? 'Activo' : 'Inactivo';
    }

    public function getEmpleadoNombreCompletoAttribute()
    {
        return $this->empleado ? $this->empleado->nombre_completo : '';
    }
}