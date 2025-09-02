<?php

namespace App\Modules\Contratos\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContratoRequest extends FormRequest
{
    public function authorize()
    {
        return true; // TODO: Implementar autorización basada en roles
    }

    public function rules()
    {
        return [
            'IDEmpleado' => 'required|integer|exists:Empleados,IDEmpleado',
            'IDCategoria' => 'required|integer|exists:Categorias,IDCategoria',
            'IDCargo' => 'required|integer|exists:Cargos,IDCargo',
            'IDDepartamento' => 'required|integer|exists:Departamentos,IDDepartamento',
            'NumeroContrato' => 'nullable|string|max:50|unique:Contratos,NumeroContrato',
            'TipoContrato' => 'required|string|in:Indefinido,Plazo Fijo,Temporal,Consultoria,Practicante',
            'FechaContrato' => 'nullable|date|before_or_equal:today',
            'FechaInicio' => 'required|date',
            'FechaFin' => 'nullable|date|after:FechaInicio',
            'HaberBasico' => 'required|numeric|min:0|max:999999.99',
            'Estado' => 'nullable|boolean',
            'permitir_multiple' => 'nullable|boolean'
        ];
    }

    public function messages()
    {
        return [
            'IDEmpleado.required' => 'El empleado es obligatorio',
            'IDEmpleado.exists' => 'El empleado seleccionado no existe',
            'IDCategoria.required' => 'La categoría es obligatoria',
            'IDCategoria.exists' => 'La categoría seleccionada no existe',
            'IDCargo.required' => 'El cargo es obligatorio',
            'IDCargo.exists' => 'El cargo seleccionado no existe',
            'IDDepartamento.required' => 'El departamento es obligatorio',
            'IDDepartamento.exists' => 'El departamento seleccionado no existe',
            'NumeroContrato.unique' => 'Ya existe un contrato con este número',
            'NumeroContrato.max' => 'El número de contrato no puede exceder 50 caracteres',
            'TipoContrato.required' => 'El tipo de contrato es obligatorio',
            'TipoContrato.in' => 'El tipo de contrato debe ser: Indefinido, Plazo Fijo, Temporal, Consultoria o Practicante',
            'FechaContrato.date' => 'La fecha del contrato debe ser una fecha válida',
            'FechaContrato.before_or_equal' => 'La fecha del contrato no puede ser futura',
            'FechaInicio.required' => 'La fecha de inicio es obligatoria',
            'FechaInicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'FechaFin.date' => 'La fecha de fin debe ser una fecha válida',
            'FechaFin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'HaberBasico.required' => 'El haber básico es obligatorio',
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

    protected function prepareForValidation()
    {
        // Generar número de contrato automáticamente si no se proporciona
        if (empty($this->NumeroContrato)) {
            $this->merge([
                'NumeroContrato' => null // Se generará en el servicio
            ]);
        }

        // Establecer fecha de contrato por defecto
        if (empty($this->FechaContrato)) {
            $this->merge([
                'FechaContrato' => now()->toDateString()
            ]);
        }

        // Estado por defecto
        if (!isset($this->Estado)) {
            $this->merge([
                'Estado' => true
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validación adicional: Si es tipo "Plazo Fijo" debe tener fecha fin
            if ($this->TipoContrato === 'Plazo Fijo' && empty($this->FechaFin)) {
                $validator->errors()->add('FechaFin', 'Los contratos de plazo fijo deben tener fecha de fin');
            }

            // Validación adicional: Si es tipo "Indefinido" no debe tener fecha fin
            if ($this->TipoContrato === 'Indefinido' && !empty($this->FechaFin)) {
                $validator->errors()->add('FechaFin', 'Los contratos indefinidos no deben tener fecha de fin');
            }

            // Validación de fechas lógicas
            if ($this->FechaContrato && $this->FechaInicio) {
                if ($this->FechaContrato > $this->FechaInicio) {
                    $validator->errors()->add('FechaContrato', 'La fecha del contrato no puede ser posterior a la fecha de inicio');
                }
            }
        });
    }
}