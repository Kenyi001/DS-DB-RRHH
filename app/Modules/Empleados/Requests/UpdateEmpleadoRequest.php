<?php

namespace App\Modules\Empleados\Requests;

use App\Modules\Empleados\Models\Empleado;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Implementar authorization policies
    }

    public function rules(): array
    {
        $empleadoId = $this->route('empleado') ?? $this->route('id');
        
        return Empleado::rules($empleadoId);
    }

    public function messages(): array
    {
        return Empleado::messages();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validación personalizada de CI boliviano
            if ($this->filled('ci') && !$this->validarCiBoliviano($this->ci)) {
                $validator->errors()->add('ci', 'El formato del CI no es válido.');
            }

            // Validación de edad mínima
            if ($this->filled('fecha_nacimiento')) {
                $edad = now()->diffInYears($this->fecha_nacimiento);
                if ($edad < 18) {
                    $validator->errors()->add('fecha_nacimiento', 'El empleado debe ser mayor de 18 años.');
                }
                if ($edad > 80) {
                    $validator->errors()->add('fecha_nacimiento', 'La edad máxima permitida es 80 años.');
                }
            }

            // Validación de fecha de ingreso
            if ($this->filled('fecha_ingreso')) {
                $fechaIngreso = \Carbon\Carbon::parse($this->fecha_ingreso);
                if ($fechaIngreso->isFuture()) {
                    $validator->errors()->add('fecha_ingreso', 'La fecha de ingreso no puede ser futura.');
                }
            }

            // Validar que celular o teléfono esté presente (solo si se envían)
            if ($this->has('telefono') || $this->has('celular')) {
                if (empty($this->telefono) && empty($this->celular)) {
                    $validator->errors()->add('telefono', 'Debe proporcionar al menos un teléfono o celular.');
                    $validator->errors()->add('celular', 'Debe proporcionar al menos un teléfono o celular.');
                }
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    private function validarCiBoliviano(string $ci): bool
    {
        if (strlen($ci) < 6 || strlen($ci) > 10) {
            return false;
        }

        if (!preg_match('/^\d{6,9}[0-9A-Z]?$/', $ci)) {
            return false;
        }

        return true;
    }

    public function prepareForValidation()
    {
        // Limpiar y formatear datos antes de la validación
        $data = [];
        
        if ($this->filled('ci')) {
            $data['ci'] = strtoupper(trim($this->ci));
        }
        
        if ($this->filled('nombres')) {
            $data['nombres'] = ucwords(strtolower(trim($this->nombres)));
        }
        
        if ($this->filled('apellido_paterno')) {
            $data['apellido_paterno'] = ucwords(strtolower(trim($this->apellido_paterno)));
        }
        
        if ($this->filled('apellido_materno')) {
            $data['apellido_materno'] = ucwords(strtolower(trim($this->apellido_materno)));
        }
        
        if ($this->filled('email')) {
            $data['email'] = strtolower(trim($this->email));
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }
}