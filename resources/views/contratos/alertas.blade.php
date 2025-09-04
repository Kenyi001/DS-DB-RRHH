@extends('layouts.main')

@section('title', 'Alertas de Contratos')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('contratos.index') }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="flex-shrink-0 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 10v8a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H8a1 1 0 00-1 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-8a1 1 0 01.293-.707l7-7z" clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Contratos</span>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <a href="{{ route('contratos.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Contratos</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-700">Alertas</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Alertas de Contratos</h1>
            <p class="mt-1 text-sm text-gray-600">
                Contratos que requieren atención: próximos a vencer e indefinidos
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('contratos.vigentes') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                Ver Vigentes
            </a>
            <a href="{{ route('contratos.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Todos los Contratos
            </a>
        </div>
    </div>

    <!-- Resumen de Alertas -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Contratos por Vencer -->
        <div class="overflow-hidden rounded-lg bg-red-50 px-4 py-5 shadow">
            <dt class="text-sm font-medium text-red-600 truncate">Contratos por Vencer (30 días)</dt>
            <dd class="mt-1 text-3xl font-semibold text-red-900">{{ $contratosPorVencer->count() }}</dd>
            <div class="mt-2 text-sm text-red-700">
                {{ $contratosPorVencer->count() ? 'Requieren renovación urgente' : 'No hay contratos próximos a vencer' }}
            </div>
        </div>

        <!-- Contratos Indefinidos -->
        <div class="overflow-hidden rounded-lg bg-yellow-50 px-4 py-5 shadow">
            <dt class="text-sm font-medium text-yellow-600 truncate">Contratos Indefinidos</dt>
            <dd class="mt-1 text-3xl font-semibold text-yellow-900">{{ $contratosIndefinidos->count() }}</dd>
            <div class="mt-2 text-sm text-yellow-700">
                Sin fecha de finalización definida
            </div>
        </div>
    </div>

    <!-- Contratos por Vencer -->
    @if($contratosPorVencer->isNotEmpty())
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Contratos Próximos a Vencer</h3>
                    <p class="text-sm text-gray-500">{{ $contratosPorVencer->count() }} contratos vencen en los próximos 30 días</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Fin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Días Restantes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($contratosPorVencer as $contrato)
                    @php
                        $diasRestantes = \Carbon\Carbon::parse($contrato->FechaFin)->diffInDays(now());
                        $urgencia = $diasRestantes <= 7 ? 'text-red-600' : ($diasRestantes <= 15 ? 'text-orange-600' : 'text-yellow-600');
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-red-600">
                                            {{ substr($contrato->Nombres, 0, 1) }}{{ substr($contrato->ApellidoPaterno, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $contrato->Nombres }} {{ $contrato->ApellidoPaterno }} {{ $contrato->ApellidoMaterno }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Contrato #{{ $contrato->NumeroContrato }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $contrato->NombreCargo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $contrato->NombreDepartamento }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($contrato->FechaFin)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium {{ $urgencia }}">
                                {{ $diasRestantes }} días
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Bs {{ number_format($contrato->HaberBasico, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex space-x-2">
                                <a href="{{ route('contratos.show', $contrato->IDContrato) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                <a href="{{ route('contratos.edit', $contrato->IDContrato) }}" class="text-indigo-600 hover:text-indigo-900">Renovar</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Contratos Indefinidos -->
    @if($contratosIndefinidos->isNotEmpty())
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Contratos Indefinidos</h3>
                    <p class="text-sm text-gray-500">{{ $contratosIndefinidos->count() }} contratos sin fecha de finalización definida</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Inicio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Antigüedad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($contratosIndefinidos as $contrato)
                    @php
                        $antiguedad = \Carbon\Carbon::parse($contrato->FechaInicio)->diffForHumans(now(), true);
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-yellow-600">
                                            {{ substr($contrato->Nombres, 0, 1) }}{{ substr($contrato->ApellidoPaterno, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $contrato->Nombres }} {{ $contrato->ApellidoPaterno }} {{ $contrato->ApellidoMaterno }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Contrato #{{ $contrato->NumeroContrato }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $contrato->NombreCargo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $contrato->NombreDepartamento }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($contrato->FechaInicio)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $antiguedad }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Bs {{ number_format($contrato->HaberBasico, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex space-x-2">
                                <a href="{{ route('contratos.show', $contrato->IDContrato) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                <a href="{{ route('contratos.edit', $contrato->IDContrato) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Estado vacío -->
    @if($contratosPorVencer->isEmpty() && $contratosIndefinidos->isEmpty())
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">¡Sin alertas!</h3>
        <p class="mt-1 text-sm text-gray-500">No hay contratos que requieran atención en este momento.</p>
        <div class="mt-6">
            <a href="{{ route('contratos.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-ypfb-blue hover:bg-blue-700">
                Ver Todos los Contratos
            </a>
        </div>
    </div>
    @endif

</div>
@endsection