<?php

namespace App\Modules\Empleados\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'Empleados';
    protected $primaryKey = 'IDEmpleado';

    public $timestamps = false; // La tabla SQL aprobada no tiene timestamps

    protected $fillable = [
        'Nombres',
        'ApellidoPaterno', 
        'ApellidoMaterno',
        'FechaNacimiento',
        'Telefono',
        'Email',
        'Direccion',
        'FechaIngreso',
        'Estado'
    ];

    protected $casts = [
        'FechaNacimiento' => 'date',
        'FechaIngreso' => 'date',
        'Estado' => 'boolean'
    ];

    protected $appends = [
        'nombre_completo',
        'edad',
        'antiguedad_anos'
    ];

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('Estado', 1);
    }

    public function scopePorNombre($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('Nombres', 'like', "%{$termino}%")
              ->orWhere('ApellidoPaterno', 'like', "%{$termino}%")
              ->orWhere('ApellidoMaterno', 'like', "%{$termino}%");
        });
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        $nombre = $this->Nombres . ' ' . $this->ApellidoPaterno;
        if ($this->ApellidoMaterno) {
            $nombre .= ' ' . $this->ApellidoMaterno;
        }
        return $nombre;
    }

    public function getEdadAttribute()
    {
        return $this->FechaNacimiento ? 
            $this->FechaNacimiento->diffInYears(Carbon::now()) : null;
    }

    public function getAntiguedadAnosAttribute()
    {
        return $this->FechaIngreso ? 
            $this->FechaIngreso->diffInYears(Carbon::now()) : 0;
    }

    // Mutators
    // Mutators removidos temporalmente - no hay campos CI, genero, etc en SQL aprobado

    public function setNombresAttribute($value)
    {
        $this->attributes['Nombres'] = ucwords(strtolower(trim($value)));
    }

    public function setApellidoPaternoAttribute($value)
    {
        $this->attributes['ApellidoPaterno'] = ucwords(strtolower(trim($value)));
    }

    public function setApellidoMaternoAttribute($value)
    {
        $this->attributes['ApellidoMaterno'] = $value ? ucwords(strtolower(trim($value))) : null;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['Email'] = strtolower(trim($value));
    }

    // Validaciones personalizadas
    public static function rules($id = null)
    {
        return [
            'ci' => 'required|string|max:20|unique:empleados,ci' . ($id ? ",$id" : ''),
            'nombres' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'required|date|before:today',
            'genero' => 'required|in:M,F',
            'estado_civil' => 'required|in:Soltero,Casado,Divorciado,Viudo',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'email' => 'required|email|max:150|unique:empleados,email' . ($id ? ",$id" : ''),
            'direccion' => 'required|string',
            'ciudad' => 'required|string|max:50',
            'codigo_empleado' => 'required|string|max:20|unique:empleados,codigo_empleado' . ($id ? ",$id" : ''),
            'fecha_ingreso' => 'required|date|before_or_equal:today',
            'estado' => 'required|in:Activo,Inactivo,Vacaciones,Licencia',
            'nacionalidad' => 'required|string|max:50'
        ];
    }

    public static function messages()
    {
        return [
            'ci.required' => 'El CI es obligatorio.',
            'ci.unique' => 'Ya existe un empleado con este CI.',
            'nombres.required' => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'genero.required' => 'El género es obligatorio.',
            'genero.in' => 'El género debe ser M o F.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'Ya existe un empleado con este email.',
            'codigo_empleado.required' => 'El código de empleado es obligatorio.',
            'codigo_empleado.unique' => 'Ya existe un empleado with este código.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'fecha_ingreso.before_or_equal' => 'La fecha de ingreso no puede ser futura.'
        ];
    }

    // Métodos de utilidad
    public function marcarComoBaja($motivo = null, $usuario = null)
    {
        $this->update([
            'estado' => 'Inactivo',
            'fecha_baja' => Carbon::now(),
            'motivo_baja' => $motivo,
            'usuario_baja' => $usuario
        ]);
    }

    public function reactivar()
    {
        $this->update([
            'estado' => 'Activo',
            'fecha_baja' => null,
            'motivo_baja' => null,
            'usuario_baja' => null
        ]);
    }
}