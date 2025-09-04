<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planilla extends Model
{
    protected $table = 'GestionSalarios';
    protected $primaryKey = 'IDGestionSalario';
    public $timestamps = false;

    protected $fillable = [
        'IDContrato',
        'Mes',
        'Gestion',
        'DiasTrabajos',
        'SalarioBasico',
        'TotalIngresos',
        'TotalDescuentos',
        'LiquidoPagable',
        'FechaPago',
        'EstadoPago'
    ];

    protected $casts = [
        'SalarioBasico' => 'decimal:2',
        'TotalIngresos' => 'decimal:2',
        'TotalDescuentos' => 'decimal:2',
        'LiquidoPagable' => 'decimal:2',
        'FechaPago' => 'date',
        'DiasTrabajos' => 'integer',
        'Mes' => 'integer',
        'Gestion' => 'integer'
    ];

    // Relaciones
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'IDContrato', 'IDContrato');
    }

    // Relación indirecta al empleado a través del contrato
    public function empleado()
    {
        return $this->hasOneThrough(
            \App\Modules\Empleados\Models\Empleado::class,
            Contrato::class,
            'IDContrato', // Foreign key en Contratos
            'IDEmpleado', // Foreign key en Empleados
            'IDContrato', // Local key en Planillas
            'IDEmpleado'  // Local key en Contratos
        );
    }

    // Scopes
    public function scopePorGestion($query, $gestion)
    {
        return $query->where('Gestion', $gestion);
    }

    public function scopePorMes($query, $mes)
    {
        return $query->where('Mes', $mes);
    }

    public function scopePorPeriodo($query, $gestion, $mes)
    {
        return $query->where('Gestion', $gestion)->where('Mes', $mes);
    }

    public function scopePagadas($query)
    {
        return $query->where('EstadoPago', 'Pagado');
    }

    public function scopePendientes($query)
    {
        return $query->where('EstadoPago', 'Pendiente');
    }

    public function scopeAnuladas($query)
    {
        return $query->where('EstadoPago', 'Anulado');
    }

    // Accessors y cálculos
    public function getPeriodoTextoAttribute()
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $meses[$this->Mes] . ' ' . $this->Gestion;
    }

    public function getEstadoTextoAttribute()
    {
        return match($this->EstadoPago) {
            'Pagado' => 'Pagado',
            'Pendiente' => 'Pendiente',
            'Anulado' => 'Anulado',
            default => 'Pendiente'
        };
    }

    public function getPorcentajeDescuentosAttribute()
    {
        if ($this->TotalIngresos == 0) return 0;
        return round(($this->TotalDescuentos / $this->TotalIngresos) * 100, 2);
    }

    public function getSalarioDiarioAttribute()
    {
        return round($this->SalarioBasico / 30, 2);
    }

    public function getSalarioPorDiasAttribute()
    {
        return round($this->SalarioDiario * $this->DiasTrabajos, 2);
    }

    // Métodos de utilidad
    public function calcularTotales()
    {
        // Aquí se implementarían los cálculos específicos de YPFB
        $salarioProporcional = $this->SalarioPorDias;
        
        // Ingresos adicionales (bonos, horas extra, etc.)
        $bonoAntiguedad = $this->calcularBonoAntiguedad();
        $horasExtra = $this->calcularHorasExtra();
        
        $totalIngresos = $salarioProporcional + $bonoAntiguedad + $horasExtra;
        
        // Descuentos obligatorios
        $descuentoAFP = $totalIngresos * 0.1271; // 12.71% AFP
        $descuentoSeguro = $totalIngresos * 0.03; // 3% Seguro
        $impuestoRCIVA = $this->calcularRCIVA($totalIngresos);
        
        $totalDescuentos = $descuentoAFP + $descuentoSeguro + $impuestoRCIVA;
        
        $liquidoPagable = $totalIngresos - $totalDescuentos;
        
        return [
            'TotalIngresos' => round($totalIngresos, 2),
            'TotalDescuentos' => round($totalDescuentos, 2),
            'LiquidoPagable' => round($liquidoPagable, 2),
            'desglose' => [
                'salario_proporcional' => round($salarioProporcional, 2),
                'bono_antiguedad' => round($bonoAntiguedad, 2),
                'horas_extra' => round($horasExtra, 2),
                'descuento_afp' => round($descuentoAFP, 2),
                'descuento_seguro' => round($descuentoSeguro, 2),
                'impuesto_rciva' => round($impuestoRCIVA, 2)
            ]
        ];
    }

    private function calcularBonoAntiguedad()
    {
        // Ejemplo: 5% del salario básico por cada año de antigüedad
        $empleado = $this->empleado;
        if (!$empleado) return 0;
        
        $anosAntiguedad = floor($empleado->antiguedad_anos ?? 0);
        return $this->SalarioBasico * 0.05 * $anosAntiguedad;
    }

    private function calcularHorasExtra()
    {
        // Por ahora retorna 0, se puede implementar según requerimientos
        return 0;
    }

    private function calcularRCIVA($totalIngresos)
    {
        // RC-IVA simplificado para Bolivia
        $minimoNoImponible = 24500; // Bs mensual
        
        if ($totalIngresos <= $minimoNoImponible) {
            return 0;
        }
        
        $baseImponible = $totalIngresos - $minimoNoImponible;
        return $baseImponible * 0.13; // 13% sobre excedente
    }

    // Métodos estáticos para generar planillas
    public static function generarPlanillaPorContrato($contratoId, $mes, $gestion, $diasTrabajados = 30)
    {
        $contrato = Contrato::with('empleado')->find($contratoId);
        
        if (!$contrato || !$contrato->Estado) {
            throw new \Exception("Contrato no válido o inactivo");
        }

        // Verificar si ya existe planilla para este período
        $existePlanilla = self::where('IDContrato', $contratoId)
                             ->where('Mes', $mes)
                             ->where('Gestion', $gestion)
                             ->exists();

        if ($existePlanilla) {
            throw new \Exception("Ya existe una planilla para este período");
        }

        // Crear nueva planilla
        $planilla = new self([
            'IDContrato' => $contratoId,
            'Mes' => $mes,
            'Gestion' => $gestion,
            'DiasTrabajos' => $diasTrabajados,
            'SalarioBasico' => $contrato->HaberBasico,
            'EstadoPago' => 'Pendiente'
        ]);

        // Calcular totales
        $calculos = $planilla->calcularTotales();
        $planilla->TotalIngresos = $calculos['TotalIngresos'];
        $planilla->TotalDescuentos = $calculos['TotalDescuentos'];
        $planilla->LiquidoPagable = $calculos['LiquidoPagable'];

        $planilla->save();

        return $planilla->load(['contrato.empleado']);
    }
}