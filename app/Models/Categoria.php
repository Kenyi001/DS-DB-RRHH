<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'Categorias';
    protected $primaryKey = 'IDCategoria';
    public $timestamps = false;

    protected $fillable = [
        'NombreCategoria',
        'DescripcionCategoria',
        'AniosMinimos',
        'AniosMaximos',
        'Estado'
    ];

    protected $casts = [
        'AniosMinimos' => 'integer',
        'AniosMaximos' => 'integer',
        'Estado' => 'boolean'
    ];

    // Relaciones
    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'IDCategoria', 'IDCategoria');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('Estado', 1);
    }

    // Accessors
    public function getEstadoTextoAttribute()
    {
        return $this->Estado ? 'Activa' : 'Inactiva';
    }
}