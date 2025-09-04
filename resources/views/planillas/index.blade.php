@extends('layouts.main')

@section('title', 'Planillas de Pago')

@section('content')
<div x-data="planillasData()" x-init="loadData()" class="space-y-6">
    
    <!-- Header con título y acciones -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Planillas de Pago</h1>
            <p class="mt-1 text-sm text-gray-600">
                Gestión de nómina y planillas salariales
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('planillas.reportes') }}" 
               class="inline-flex items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M15.621 4.379a3 3 0 00-4.242 0l-7 7a3 3 0 004.241 4.243h.001l.497-.5a.75.75 0 011.064 1.057l-.498.501-.002.002a4.5 4.5 0 01-6.364-6.364l7-7a4.5 4.5 0 016.368 6.36l-3.455 3.553A2.625 2.625 0 119.52 9.52l3.45-3.451a.75.75 0 111.061 1.06l-3.45 3.451a1.125 1.125 0 001.587 1.595l3.454-3.553a3 3 0 000-4.242z" clip-rule="evenodd" />
                </svg>
                Reportes
            </a>
            <a href="{{ route('planillas.generar') }}" 
               class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Generar Planilla
            </a>
        </div>
    </div>

    <!-- Estadísticas del período -->
    @if(isset($stats) && $stats['total_planillas'] > 0)
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Planillas</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_planillas']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.467-.22-2.121-.659-1.172-.879-1.172-2.303 0-3.182s3.07-.879 4.242 0L15 9m-3-7h.01" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Nómina</dt>
                            <dd class="text-lg font-medium text-gray-900">Bs {{ number_format($stats['total_nomina'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Promedio Salarial</dt>
                            <dd class="text-lg font-medium text-gray-900">Bs {{ number_format($stats['promedio_sueldo'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Estado de Pagos</dt>
                            <dd class="text-sm text-gray-900">
                                <span class="text-green-600">{{ $stats['pagadas'] }} pagadas</span> / 
                                <span class="text-amber-600">{{ $stats['pendientes'] }} pendientes</span>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtros y búsqueda -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <form method="GET" action="{{ route('planillas.index') }}" class="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-5 lg:gap-4">
                
                <!-- Búsqueda -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ $filtros['search'] ?? '' }}"
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                               placeholder="Empleado, CI...">
                    </div>
                </div>

                <!-- Gestión -->
                <div>
                    <label for="gestion" class="block text-sm font-medium text-gray-700">Año</label>
                    <select name="gestion" id="gestion" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        @foreach($gestiones as $gestion)
                            <option value="{{ $gestion }}" {{ ($filtros['gestion'] ?? '') == $gestion ? 'selected' : '' }}>
                                {{ $gestion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mes -->
                <div>
                    <label for="mes" class="block text-sm font-medium text-gray-700">Mes</label>
                    <select name="mes" id="mes" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">Todos</option>
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

                <!-- Estado de Pago -->
                <div>
                    <label for="estado_pago" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select name="estado_pago" id="estado_pago" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">Todos</option>
                        <option value="Pendiente" {{ ($filtros['estado_pago'] ?? '') == 'Pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="Pagado" {{ ($filtros['estado_pago'] ?? '') == 'Pagado' ? 'selected' : '' }}>Pagados</option>
                    </select>
                </div>

                <!-- Empleado -->
                <div>
                    <label for="empleado_id" class="block text-sm font-medium text-gray-700">Empleado</label>
                    <select name="empleado_id" id="empleado_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">Todos</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->IDEmpleado }}" {{ ($filtros['empleado_id'] ?? '') == $empleado->IDEmpleado ? 'selected' : '' }}>
                                {{ $empleado->Nombres }} {{ $empleado->ApellidoPaterno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botones -->
                <div class="lg:col-span-5 flex justify-between items-end space-x-3">
                    <div class="flex space-x-3">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                            Filtrar
                        </button>
                        
                        <a href="{{ route('planillas.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                            </svg>
                            Limpiar
                        </a>
                    </div>
                    
                    <div class="text-sm text-gray-500">
                        Total: {{ $planillas->total() }} planillas
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="show = false" type="button" class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="show = false" type="button" class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla de planillas -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if($planillas->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Empleado
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Período
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Salario Base
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Ingresos
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Descuentos
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Líquido Pagable
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($planillas as $planilla)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-purple-600">
                                                    {{ substr($planilla->contrato->empleado->Nombres, 0, 1) }}{{ substr($planilla->contrato->empleado->ApellidoPaterno, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $planilla->contrato->empleado->Nombres }} {{ $planilla->contrato->empleado->ApellidoPaterno }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $planilla->contrato->empleado->ApellidoMaterno }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ str_pad($planilla->Mes, 2, '0', STR_PAD_LEFT) }}/{{ $planilla->Gestion }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::createFromFormat('n', $planilla->Mes)->locale('es')->monthName }} {{ $planilla->Gestion }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Bs {{ number_format($planilla->SalarioBasico, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Bs {{ number_format($planilla->TotalIngresos, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                    Bs {{ number_format($planilla->TotalDescuentos, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        Bs {{ number_format($planilla->LiquidoPagable, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($planilla->EstadoPago === 'Pagado')
                                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                            <svg class="mr-1 h-1.5 w-1.5 fill-green-400">
                                                <circle cx="1" cy="1" r="1" />
                                            </svg>
                                            Pagado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                            <svg class="mr-1 h-1.5 w-1.5 fill-amber-400">
                                                <circle cx="1" cy="1" r="1" />
                                            </svg>
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('planillas.show', $planilla->IDGestionSalario) }}" 
                                           class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50"
                                           title="Ver detalle">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        
                                        @if($planilla->EstadoPago !== 'Pagado')
                                            <button @click="marcarComoPagado({{ $planilla->IDGestionSalario }})" 
                                                    class="text-green-600 hover:text-green-900 p-1 rounded-full hover:bg-green-50"
                                                    title="Marcar como pagado">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($planillas->previousPageUrl())
                        <a href="{{ $planillas->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Anterior
                        </a>
                    @endif
                    @if($planillas->nextPageUrl())
                        <a href="{{ $planillas->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Siguiente
                        </a>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando
                            <span class="font-medium">{{ $planillas->firstItem() }}</span>
                            a
                            <span class="font-medium">{{ $planillas->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $planillas->total() }}</span>
                            planillas
                        </p>
                    </div>
                    <div>
                        {{ $planillas->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- Estado vacío -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No hay planillas</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request()->hasAny(['search', 'gestion', 'mes', 'estado_pago', 'empleado_id']))
                        No se encontraron planillas con los filtros aplicados.
                    @else
                        Comienza generando las planillas del mes.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('planillas.generar') }}" 
                       class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Generar Planilla
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function planillasData() {
    return {        
        loadData() {
            // Aquí se pueden cargar datos adicionales si es necesario
        },
        
        marcarComoPagado(planillaId) {
            if (confirm('¿Estás seguro de marcar esta planilla como pagada?')) {
                // Aquí implementaríamos la llamada AJAX para marcar como pagado
                // Por ahora mostramos un mensaje
                alert('Funcionalidad pendiente de implementar');
            }
        }
    }
}
</script>
@endsection