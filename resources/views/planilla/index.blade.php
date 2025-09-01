@extends('layouts.app')

@section('title', 'Gestión de Planilla')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="planillaManager()">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900">Gestión de Planilla</h1>
        <p class="text-neutral-600 mt-1">Generar y administrar planillas mensuales</p>
    </div>

    <!-- Selección de Período -->
    <div class="bg-white rounded-lg shadow-sm border border-neutral-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-neutral-900 mb-4">Seleccionar Período</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="mes" class="block text-sm font-medium text-neutral-700 mb-1">Mes</label>
                <select 
                    id="mes"
                    x-model="periodo.mes"
                    @change="loadPreview()"
                    class="w-full rounded-lg border-neutral-300 focus:border-ypfb-blue focus:ring-ypfb-blue"
                >
                    <option value="">Seleccionar mes</option>
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </div>

            <div>
                <label for="gestion" class="block text-sm font-medium text-neutral-700 mb-1">Gestión</label>
                <select 
                    id="gestion"
                    x-model="periodo.gestion"
                    @change="loadPreview()"
                    class="w-full rounded-lg border-neutral-300 focus:border-ypfb-blue focus:ring-ypfb-blue"
                >
                    <option value="">Seleccionar año</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                </select>
            </div>

            <div class="flex items-end">
                <button 
                    @click="loadPreview()"
                    :disabled="!periodo.mes || !periodo.gestion"
                    class="btn btn-outline w-full"
                    :class="(!periodo.mes || !periodo.gestion) ? 'btn-disabled' : ''"
                >
                    Cargar Preview
                </button>
            </div>
        </div>
    </div>

    <!-- Preview de Planilla -->
    <div x-show="showPreview" class="bg-white rounded-lg shadow-sm border border-neutral-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-neutral-900">
                Preview Planilla <span x-text="getMesNombre(periodo.mes)"></span> <span x-text="periodo.gestion"></span>
            </h2>
            
            <button 
                @click="generarPlanilla()"
                :disabled="generando || !preview.total_empleados"
                class="btn btn-primary"
                :class="(generando || !preview.total_empleados) ? 'btn-disabled' : ''"
            >
                <span x-show="!generando">Generar Planilla</span>
                <span x-show="generando" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Generando...
                </span>
            </button>
        </div>

        <!-- Resumen del Preview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-neutral-50 rounded-lg p-4">
                <dt class="text-sm font-medium text-neutral-500">Total Empleados</dt>
                <dd class="mt-1 text-2xl font-semibold text-neutral-900" x-text="preview.total_empleados || 0"></dd>
            </div>
            <div class="bg-neutral-50 rounded-lg p-4">
                <dt class="text-sm font-medium text-neutral-500">Líquido Total</dt>
                <dd class="mt-1 text-2xl font-semibold text-neutral-900">
                    Bs. <span x-text="formatNumber(preview.total_liquido || 0)"></span>
                </dd>
            </div>
            <div class="bg-neutral-50 rounded-lg p-4">
                <dt class="text-sm font-medium text-neutral-500">Estado</dt>
                <dd class="mt-1 text-sm font-medium" 
                    :class="preview.estado === 'Completado' ? 'text-green-600' : 'text-yellow-600'"
                    x-text="preview.estado || 'Pendiente'"
                ></dd>
            </div>
            <div class="bg-neutral-50 rounded-lg p-4">
                <dt class="text-sm font-medium text-neutral-500">Fecha Generación</dt>
                <dd class="mt-1 text-sm text-neutral-600" x-text="preview.fecha_generacion || 'N/A'"></dd>
            </div>
        </div>
    </div>

    <!-- Estado de Generación -->
    <div x-show="planillaGenerada" class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">Planilla Generada Exitosamente</h3>
                <div class="mt-2 text-sm text-green-700">
                    <p>Planilla ID: <span x-text="resultado.planilla_id"></span></p>
                    <p x-text="resultado.message"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function planillaManager() {
    return {
        periodo: {
            mes: '',
            gestion: new Date().getFullYear()
        },
        preview: {},
        showPreview: false,
        generando: false,
        planillaGenerada: false,
        resultado: {},

        async loadPreview() {
            if (!this.periodo.mes || !this.periodo.gestion) return;

            try {
                const response = await fetch('/api/v1/planilla/preview', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.getAuthToken()}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.periodo)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.preview = data.data;
                    this.showPreview = true;
                }
            } catch (error) {
                console.error('Error cargando preview:', error);
            }
        },

        async generarPlanilla() {
            this.generando = true;
            this.planillaGenerada = false;

            try {
                const response = await fetch('/api/v1/planilla/generar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.getAuthToken()}`,
                        'Accept': 'application/json',
                        'Idempotency-Key': this.generateUUID()
                    },
                    body: JSON.stringify(this.periodo)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.resultado = data;
                    this.planillaGenerada = true;
                    
                    // Simular seguimiento de estado (en implementación real usar polling)
                    setTimeout(() => {
                        this.loadPreview(); // Recargar preview con datos actualizados
                    }, 2000);
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) {
                console.error('Error generando planilla:', error);
                alert('Error de conexión al generar planilla');
            } finally {
                this.generando = false;
            }
        },

        getMesNombre(mes) {
            const meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            return meses[parseInt(mes)] || '';
        },

        formatNumber(number) {
            return new Intl.NumberFormat('es-BO').format(number);
        },

        generateUUID() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        },

        getAuthToken() {
            // TODO: Implementar gestión de tokens Sanctum
            return localStorage.getItem('auth_token') || 'dummy_token';
        }
    }
}
</script>
@endsection