<?php

namespace App\Modules\Planillas\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerarPlanillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gestion' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'mes' => 'required|integer|min:1|max:12',
            'empleados' => 'nullable|array',
            'empleados.*' => 'integer|exists:Empleados,IDEmpleado',
            'opciones' => 'nullable|array',
            'opciones.dias_trabajados' => 'nullable|integer|min:1|max:31',
            'opciones.sobrescribir' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'gestion.required' => 'La gestión es requerida',
            'gestion.integer' => 'La gestión debe ser un número entero',
            'gestion.min' => 'La gestión debe ser mayor o igual a 2020',
            'gestion.max' => 'La gestión no puede ser mayor al próximo año',
            'mes.required' => 'El mes es requerido',
            'mes.integer' => 'El mes debe ser un número entero',
            'mes.min' => 'El mes debe ser mayor o igual a 1',
            'mes.max' => 'El mes debe ser menor o igual a 12',
            'empleados.array' => 'Los empleados debe ser un arreglo',
            'empleados.*.integer' => 'Cada empleado debe ser un número entero',
            'empleados.*.exists' => 'El empleado seleccionado no existe',
            'opciones.array' => 'Las opciones debe ser un arreglo',
            'opciones.dias_trabajados.integer' => 'Los días trabajados deben ser un número entero',
            'opciones.dias_trabajados.min' => 'Los días trabajados debe ser mayor a 0',
            'opciones.dias_trabajados.max' => 'Los días trabajados no puede ser mayor a 31',
            'opciones.sobrescribir.boolean' => 'La opción sobrescribir debe ser verdadero o falso',
        ];
    }

    public function attributes(): array
    {
        return [
            'gestion' => 'gestión',
            'empleados' => 'empleados',
            'opciones.dias_trabajados' => 'días trabajados',
            'opciones.sobrescribir' => 'sobrescribir',
        ];
    }
}