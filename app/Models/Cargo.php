<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    use HasFactory;

    protected $table = 'Cargos';
    protected $primaryKey = 'IDCargo';
    
    public $timestamps = false;

    protected $fillable = [
        'NombreCargo',
        'Descripcion',
        'Estado'
    ];

    protected $casts = [
        'Estado' => 'boolean',
        'FechaCreacion' => 'datetime',
        'FechaModificacion' => 'datetime'
    ];

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'IDCargo', 'IDCargo');
    }

    public function scopeActivos($query)
    {
        return $query->where('Estado', true);
    }
}