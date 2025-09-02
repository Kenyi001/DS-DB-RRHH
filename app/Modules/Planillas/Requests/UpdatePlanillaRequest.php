<?php

namespace App\Modules\Planillas\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dias_trabajos' => 'nullable|integer|min:1|max:31',
            'salario_basico' => 'nullable|numeric|min:0',
            'total_ingresos' => 'nullable|numeric|min:0',
            'total_descuentos' => 'nullable|numeric|min:0',
            'liquido_pagable' => 'nullable|numeric|min:0',
            'estado_pago' => 'nullable|in:Pendiente,Pagado,Anulado',
        ];
    }

    public function messages(): array
    {
        return [
            'dias_trabajos.integer' => 'Los días de trabajo deben ser un número entero',
            'dias_trabajos.min' => 'Los días de trabajo debe ser mayor a 0',
            'dias_trabajos.max' => 'Los días de trabajo no puede ser mayor a 31',
            'salario_basico.numeric' => 'El salario básico debe ser un número',
            'salario_basico.min' => 'El salario básico debe ser mayor o igual a 0',
            'total_ingresos.numeric' => 'El total de ingresos debe ser un número',
            'total_ingresos.min' => 'El total de ingresos debe ser mayor o igual a 0',
            'total_descuentos.numeric' => 'El total de descuentos debe ser un número',
            'total_descuentos.min' => 'El total de descuentos debe ser mayor o igual a 0',
            'liquido_pagable.numeric' => 'El líquido pagable debe ser un número',
            'liquido_pagable.min' => 'El líquido pagable debe ser mayor o igual a 0',
            'estado_pago.in' => 'El estado de pago debe ser: Pendiente, Pagado o Anulado',
        ];
    }

    public function attributes(): array
    {
        return [
            'dias_trabajos' => 'días de trabajo',
            'salario_basico' => 'salario básico',
            'total_ingresos' => 'total de ingresos',
            'total_descuentos' => 'total de descuentos',
            'liquido_pagable' => 'líquido pagable',
            'estado_pago' => 'estado de pago',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Mapear campos del frontend a los nombres de la base de datos
        $this->merge([
            'DiasTrabajos' => $this->input('dias_trabajos'),
            'SalarioBasico' => $this->input('salario_basico'),
            'TotalIngresos' => $this->input('total_ingresos'),
            'TotalDescuentos' => $this->input('total_descuentos'),
            'LiquidoPagable' => $this->input('liquido_pagable'),
            'EstadoPago' => $this->input('estado_pago'),
        ]);
    }
}