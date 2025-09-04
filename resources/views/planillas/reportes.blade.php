@extends('layouts.main')

@section('title', 'Reportes de Planillas')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('planillas.index') }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="flex-shrink-0 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 10v8a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H8a1 1 0 00-1 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-8a1 1 0 01.293-.707l7-7z" clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Planillas</span>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <a href="{{ route('planillas.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Planillas</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-700">Reportes</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Reportes de Planillas</h1>
            <p class="mt-1 text-sm text-gray-600">
                Análisis y estadísticas de planillas por período
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('planillas.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver a planillas
            </a>
        </div>
    </div>

    <!-- Debug temporal (remover en producción) -->
    @if(isset($reportes['debug']) && config('app.debug'))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-blue-800">Debug Info (solo desarrollo)</h4>
        <div class="mt-2 text-sm text-blue-700">
            <p><strong>Total planillas en sistema:</strong> {{ $reportes['debug']['total_planillas_sistema'] }}</p>
            <p><strong>Filtros aplicados:</strong> Gestión {{ $reportes['debug']['filtros_aplicados']['gestion'] }}, Mes {{ $reportes['debug']['filtros_aplicados']['mes'] }}</p>
            
            @if($reportes['debug']['periodos_disponibles']->count() > 0)
                <p><strong>Períodos con datos disponibles:</strong></p>
                <div class="mt-1 grid grid-cols-4 gap-2">
                    @foreach($reportes['debug']['periodos_disponibles'] as $periodo)
                        <div class="text-xs bg-blue-100 rounded px-2 py-1">
                            {{ str_pad($periodo->Mes, 2, '0', STR_PAD_LEFT) }}/{{ $periodo->Gestion }} 
                            ({{ $periodo->cantidad }} planillas)
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-red-600"><strong>⚠️ No hay planillas en el sistema</strong></p>
            @endif
        </div>
    </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <form method="GET" action="{{ route('planillas.reportes') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                
                <!-- Gestión -->
                <div>
                    <label for="gestion" class="block text-sm font-medium text-gray-700">Año</label>
                    <select name="gestion" id="gestion" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @foreach($gestiones as $g)
                            <option value="{{ $g }}" {{ ($filtros['gestion'] ?? '') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Mes -->
                <div>
                    <label for="mes" class="block text-sm font-medium text-gray-700">Mes</label>
                    <select name="mes" id="mes" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="1" {{ ($filtros['mes'] ?? '') == '1' ? 'selected' : '' }}>Enero</option>
                        <option value="2" {{ ($filtros['mes'] ?? '') == '2' ? 'selected' : '' }}>Febrero</option>
                        <option value="3" {{ ($filtros['mes'] ?? '') == '3' ? 'selected' : '' }}>Marzo</option>
                        <option value="4" {{ ($filtros['mes'] ?? '') == '4' ? 'selected' : '' }}>Abril</option>
                        <option value="5" {{ ($filtros['mes'] ?? '') == '5' ? 'selected' : '' }}>Mayo</option>
                        <option value="6" {{ ($filtros['mes'] ?? '') == '6' ? 'selected' : '' }}>Junio</option>
                        <option value="7" {{ ($filtros['mes'] ?? '') == '7' ? 'selected' : '' }}>Julio</option>
                        <option value="8" {{ ($filtros['mes'] ?? '') == '8' ? 'selected' : '' }}>Agosto</option>
                        <option value="9" {{ ($filtros['mes'] ?? '') == '9' ? 'selected' : '' }}>Septiembre</option>
                        <option value="10" {{ ($filtros['mes'] ?? '') == '10' ? 'selected' : '' }}>Octubre</option>
                        <option value="11" {{ ($filtros['mes'] ?? '') == '11' ? 'selected' : '' }}>Noviembre</option>
                        <option value="12" {{ ($filtros['mes'] ?? '') == '12' ? 'selected' : '' }}>Diciembre</option>
                    </select>
                </div>

                <!-- Departamento -->
                <div>
                    <label for="departamento_id" class="block text-sm font-medium text-gray-700">Departamento</label>
                    <select name="departamento_id" id="departamento_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Todos</option>
                        @foreach($departamentos as $dept)
                            <option value="{{ $dept->IDDepartamento }}" {{ ($filtros['departamento_id'] ?? '') == $dept->IDDepartamento ? 'selected' : '' }}>
                                {{ $dept->NombreDepartamento }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón -->
                <div class="flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-ypfb-blue hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        Generar Reporte
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Estadísticas Generales -->
    @if(isset($reportes['totales']))
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Resumen Período {{ str_pad($filtros['mes'], 2, '0', STR_PAD_LEFT) }}/{{ $filtros['gestion'] }}
            </h3>
            
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="overflow-hidden rounded-lg bg-blue-50 px-4 py-5">
                    <dt class="text-sm font-medium text-blue-600 truncate">Total Planillas</dt>
                    <dd class="mt-1 text-3xl font-semibold text-blue-900">{{ number_format($reportes['totales']->TotalPlanillas ?? 0) }}</dd>
                </div>
                <div class="overflow-hidden rounded-lg bg-green-50 px-4 py-5">
                    <dt class="text-sm font-medium text-green-600 truncate">Total Ingresos</dt>
                    <dd class="mt-1 text-3xl font-semibold text-green-900">Bs {{ number_format($reportes['totales']->TotalIngresos ?? 0, 2) }}</dd>
                </div>
                <div class="overflow-hidden rounded-lg bg-red-50 px-4 py-5">
                    <dt class="text-sm font-medium text-red-600 truncate">Total Descuentos</dt>
                    <dd class="mt-1 text-3xl font-semibold text-red-900">Bs {{ number_format($reportes['totales']->TotalDescuentos ?? 0, 2) }}</dd>
                </div>
                <div class="overflow-hidden rounded-lg bg-yellow-50 px-4 py-5">
                    <dt class="text-sm font-medium text-yellow-600 truncate">Líquido Pagable</dt>
                    <dd class="mt-1 text-3xl font-semibold text-yellow-900">Bs {{ number_format($reportes['totales']->TotalLiquido ?? 0, 2) }}</dd>
                </div>
            </dl>

            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5">
                    <dt class="text-sm font-medium text-gray-600 truncate">Planillas Pagadas</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($reportes['totales']->PlanillasPagadas ?? 0) }}</dd>
                </div>
                <div class="overflow-hidden rounded-lg bg-orange-50 px-4 py-5">
                    <dt class="text-sm font-medium text-orange-600 truncate">Planillas Pendientes</dt>
                    <dd class="mt-1 text-2xl font-semibold text-orange-900">{{ number_format($reportes['totales']->PlanillasPendientes ?? 0) }}</dd>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Reporte por Departamentos -->
    @if(isset($reportes['departamentos']) && $reportes['departamentos']->isNotEmpty())
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Reporte por Departamentos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleados</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Salarios</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ingresos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Descuentos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Líquido Pagable</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reportes['departamentos'] as $dept)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $dept->NombreDepartamento }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($dept->TotalEmpleados) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Bs {{ number_format($dept->TotalSalarios, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Bs {{ number_format($dept->TotalIngresos, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Bs {{ number_format($dept->TotalDescuentos, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Bs {{ number_format($dept->TotalLiquido, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Bs {{ number_format($dept->PromedioLiquido, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Top Empleados por Sueldo -->
    @if(isset($reportes['topSueldos']) && $reportes['topSueldos']->isNotEmpty())
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Top 10 Empleados - Mayor Sueldo</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Líquido Pagable</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reportes['topSueldos'] as $empleado)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $empleado->Nombres }} {{ $empleado->ApellidoPaterno }} {{ $empleado->ApellidoMaterno }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empleado->NombreCargo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empleado->NombreDepartamento }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">Bs {{ number_format($empleado->LiquidoPagable, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if(!isset($reportes['totales']) || ($reportes['totales']->TotalPlanillas ?? 0) == 0)
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v8a2 2 0 002 2h5.586l4.707-4.707A1 1 0 0118.414 12H21a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5H7a2 2 0 00-2 2v8a2 2 0 002 2h5.586l4.707-4.707A1 1 0 0118.414 12H21a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012 2v6.586l4.707 4.707A1 1 0 0116.414 18H19a2 2 0 002-2v-5a2 2 0 00-2-2H9z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay planillas para el período seleccionado</h3>
        <p class="mt-1 text-sm text-gray-500">
            No se encontraron planillas para 
            {{ str_pad($filtros['mes'], 2, '0', STR_PAD_LEFT) }}/{{ $filtros['gestion'] }}
        </p>
        @if(isset($reportes['debug']) && $reportes['debug']['periodos_disponibles']->count() > 0)
            <div class="mt-4">
                <p class="text-sm text-gray-600 mb-2">Períodos disponibles:</p>
                <div class="flex flex-wrap justify-center gap-2">
                    @foreach($reportes['debug']['periodos_disponibles']->take(6) as $periodo)
                        <a href="{{ route('planillas.reportes', ['gestion' => $periodo->Gestion, 'mes' => $periodo->Mes]) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-700 hover:bg-blue-200">
                            {{ str_pad($periodo->Mes, 2, '0', STR_PAD_LEFT) }}/{{ $periodo->Gestion }}
                            <span class="ml-1 text-xs">({{ $periodo->cantidad }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @endif

</div>
@endsection