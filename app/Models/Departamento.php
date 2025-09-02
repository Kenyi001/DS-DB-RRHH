<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'Departamentos';
    protected $primaryKey = 'IDDepartamento';
    
    public $timestamps = false;

    protected $fillable = [
        'NombreDepartamento',
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
        return $this->hasMany(Contrato::class, 'IDDepartamento', 'IDDepartamento');
    }

    public function scopeActivos($query)
    {
        return $query->where('Estado', true);
    }
}