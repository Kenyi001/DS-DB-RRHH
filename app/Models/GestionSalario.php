<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GestionSalario extends Model
{
    use HasFactory;

    protected $table = 'gestion_salarios';
    protected $primaryKey = 'IDGestion';
    
    public $timestamps = false;

    protected $fillable = [
        'IDContrato',
        'Mes',
        'Gestion',
        'HaberBasico',
        'TotalSubsidios',
        'TotalDescuentos',
        'LiquidoPagable',
        'FechaPago',
        'Estado',
        'UsuarioCreacion',
        'UsuarioModificacion'
    ];

    protected $casts = [
        'HaberBasico' => 'decimal:2',
        'TotalSubsidios' => 'decimal:2',
        'TotalDescuentos' => 'decimal:2',
        'LiquidoPagable' => 'decimal:2',
        'FechaPago' => 'date',
        'FechaCreacion' => 'datetime',
        'FechaModificacion' => 'datetime'
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'IDContrato', 'IDContrato');
    }

    public function scopePorPeriodo($query, int $mes, int $gestion)
    {
        return $query->where('Mes', $mes)->where('Gestion', $gestion);
    }

    public function scopePendientesPago($query)
    {
        return $query->where('Estado', 'Generado')->whereNull('FechaPago');
    }
}