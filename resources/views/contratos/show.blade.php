@extends('layouts.main')

@section('title', 'Detalle de Contrato')
@section('page-title', 'Información del Contrato')
@section('page-description', 'Detalles completos del contrato laboral')

@section('content')
<div class="space-y-6">
    
    <!-- Navegación -->
    <div class="flex items-center space-x-2 text-sm text-gray-500">
        <a href="{{ route('contratos.index') }}" class="hover:text-gray-700">Contratos</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
        <span>Contrato #{{ $contrato->NumeroContrato ?? 'Sin número' }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información Principal -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Información del Empleado -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Información del Empleado</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <div class="h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-lg font-medium text-blue-600">
                                {{ substr($contrato->empleado->Nombres, 0, 1) }}{{ substr($contrato->empleado->ApellidoPaterno, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">
                                {{ $contrato->empleado->Nombres }} {{ $contrato->empleado->ApellidoPaterno }} {{ $contrato->empleado->ApellidoMaterno }}
                            </h4>
                            <p class="text-sm text-gray-500">CI: {{ $contrato->empleado->CI }}</p>
                            @if($contrato->empleado->Email)
                                <p class="text-sm text-gray-500">{{ $contrato->empleado->Email }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Contrato -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Detalles del Contrato</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Número de Contrato</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $contrato->NumeroContrato ?? 'No asignado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipo de Contrato</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $contrato->TipoContrato }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha del Contrato</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $contrato->FechaContrato ? \Carbon\Carbon::parse($contrato->FechaContrato)->format('d/m/Y') : 'No especificada' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Inicio</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($contrato->FechaInicio)->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Fin</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($contrato->FechaFin)
                                    {{ \Carbon\Carbon::parse($contrato->FechaFin)->format('d/m/Y') }}
                                @else
                                    <span class="text-green-600 font-medium">Indefinido</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Haber Básico</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">Bs {{ number_format($contrato->HaberBasico, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Información de Posición -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Posición y Ubicación</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cargo</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $contrato->cargo->NombreCargo }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Departamento</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $contrato->departamento->NombreDepartamento }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $contrato->categoria->NombreCategoria }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>

        <!-- Panel lateral -->
        <div class="space-y-6">
            
            <!-- Estado y Acciones -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Estado y Acciones</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    
                    <!-- Estado -->
                    <div>
                        <label class="text-sm font-medium text-gray-500">Estado Actual</label>
                        <div class="mt-1">
                            @if($contrato->Estado)
                                @if(!$contrato->FechaFin || $contrato->FechaFin >= now())
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
                                        Vigente
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800">
                                        Vencido
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">
                                    Inactivo
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="space-y-2">
                        <a href="{{ route('contratos.edit', $contrato->IDContrato) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Editar Contrato
                        </a>
                        
                        @if($contrato->Estado)
                            <button type="button" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                Desactivar
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            @if($contrato->FechaFin && $contrato->Estado)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Información de Vencimiento</h3>
                    </div>
                    <div class="px-6 py-4">
                        @php
                            $diasRestantes = \Carbon\Carbon::parse($contrato->FechaFin)->diffInDays(now(), false);
                        @endphp
                        
                        @if($diasRestantes <= 0)
                            <div class="text-sm">
                                <p class="font-medium text-green-600">Días restantes</p>
                                <p class="text-2xl font-bold text-green-600">{{ abs($diasRestantes) }}</p>
                            </div>
                        @else
                            <div class="text-sm">
                                <p class="font-medium text-red-600">Días vencido</p>
                                <p class="text-2xl font-bold text-red-600">{{ $diasRestantes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Historial de Planillas -->
    @if($planillas && $planillas->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Historial de Planillas</h3>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Período
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Líquido Pagable
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha de Pago
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($planillas as $planilla)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $planilla->Mes }}/{{ $planilla->Gestion }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Bs {{ number_format($planilla->LiquidoPagable, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($planilla->EstadoPago == 'Pagado')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Pagado
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $planilla->FechaPago ? \Carbon\Carbon::parse($planilla->FechaPago)->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection