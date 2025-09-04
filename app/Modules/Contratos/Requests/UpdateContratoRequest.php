<?php

namespace App\Modules\Contratos\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContratoRequest extends FormRequest
{
    public function authorize()
    {
        return true; // TODO: Implementar autorización basada en roles
    }

    public function rules()
    {
        $contratoId = $this->route('id');

        return [
            'IDEmpleado' => 'sometimes|integer|exists:Empleados,IDEmpleado',
            'IDCategoria' => 'sometimes|integer|exists:Categorias,IDCategoria',
            'IDCargo' => 'sometimes|integer|exists:Cargos,IDCargo',
            'IDDepartamento' => 'sometimes|integer|exists:Departamentos,IDDepartamento',
            'NumeroContrato' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('Contratos', 'NumeroContrato')->ignore($contratoId, 'IDContrato')
            ],
            'TipoContrato' => 'sometimes|string|in:Indefinido,Plazo Fijo,Temporal,Consultoria,Practicante',
            'FechaContrato' => 'sometimes|date|before_or_equal:today',
            'FechaInicio' => 'sometimes|date',
            'FechaFin' => 'sometimes|nullable|date|after:FechaInicio',
            'HaberBasico' => 'sometimes|numeric|min:0|max:999999.99',
            'Estado' => 'sometimes|boolean'
        ];
    }

    public function messages()
    {
        return [
            'IDEmpleado.exists' => 'El empleado seleccionado no existe',
            'IDCategoria.exists' => 'La categoría seleccionada no existe',
            'IDCargo.exists' => 'El cargo seleccionado no existe',
            'IDDepartamento.exists' => 'El departamento seleccionado no existe',
            'NumeroContrato.unique' => 'Ya existe otro contrato con este número',
            'NumeroContrato.max' => 'El número de contrato no puede exceder 50 caracteres',
            'TipoContrato.in' => 'El tipo de contrato debe ser: Indefinido, Plazo Fijo, Temporal, Consultoria o Practicante',
            'FechaContrato.date' => 'La fecha del contrato debe ser una fecha válida',
            'FechaContrato.before_or_equal' => 'La fecha del contrato no puede ser futura',
            'FechaInicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'FechaFin.date' => 'La fecha de fin debe ser una fecha válida',
            'FechaFin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'HaberBasico.numeric' => 'El haber básico debe ser un número',
            'HaberBasico.min' => 'El haber básico debe ser mayor a 0',
            'HaberBasico.max' => 'El haber básico no puede exceder 999,999.99'
        ];
    }

    public function attributes()
    {
        return [
            'IDEmpleado' => 'empleado',
            'IDCategoria' => 'categoría',
            'IDCargo' => 'cargo',
            'IDDepartamento' => 'departamento',
            'NumeroContrato' => 'número de contrato',
            'TipoContrato' => 'tipo de contrato',
            'FechaContrato' => 'fecha del contrato',
            'FechaInicio' => 'fecha de inicio',
            'FechaFin' => 'fecha de fin',
            'HaberBasico' => 'haber básico'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Si se está actualizando el tipo de contrato, validar coherencia
            if ($this->has('TipoContrato')) {
                if ($this->TipoContrato === 'Plazo Fijo' && empty($this->FechaFin)) {
                    // Verificar si ya tiene fecha fin en la base de datos
                    $contrato = \App\Models\Contrato::find($this->route('id'));
                    if ($contrato && empty($contrato->FechaFin)) {
                        $validator->errors()->add('FechaFin', 'Los contratos de plazo fijo deben tener fecha de fin');
                    }
                }

                if ($this->TipoContrato === 'Indefinido' && $this->has('FechaFin') && !empty($this->FechaFin)) {
                    $validator->errors()->add('FechaFin', 'Los contratos indefinidos no deben tener fecha de fin');
                }
            }

            // Validar que no se pueda cambiar fecha de inicio si el contrato ya comenzó
            if ($this->has('FechaInicio')) {
                $contrato = \App\Models\Contrato::find($this->route('id'));
                if ($contrato && $contrato->FechaInicio <= now() && $this->FechaInicio != $contrato->FechaInicio->format('Y-m-d')) {
                    $validator->errors()->add('FechaInicio', 'No se puede cambiar la fecha de inicio de un contrato que ya comenzó');
                }
            }

            // Validar coherencia de fechas
            if ($this->has('FechaContrato') && $this->has('FechaInicio')) {
                if ($this->FechaContrato > $this->FechaInicio) {
                    $validator->errors()->add('FechaContrato', 'La fecha del contrato no puede ser posterior a la fecha de inicio');
                }
            }
        });
    }
}