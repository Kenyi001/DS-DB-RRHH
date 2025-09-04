@extends('layouts.main')

@section('title', 'Detalle de Planilla')

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
                            <span class="ml-4 text-sm font-medium text-gray-700">Detalle</span>
                        </div>
                    </li>
                </ol>
            </nav>
            
            <div class="flex items-center">
                <div class="h-16 w-16 flex-shrink-0">
                    <div class="h-16 w-16 rounded-full bg-purple-100 flex items-center justify-center">
                        <span class="text-xl font-medium text-purple-600">
                            {{ substr($detalle->Nombres, 0, 1) }}{{ substr($detalle->ApellidoPaterno, 0, 1) }}
                        </span>
                    </div>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $detalle->Nombres }} {{ $detalle->ApellidoPaterno }} {{ $detalle->ApellidoMaterno }}
                    </h1>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="text-sm text-gray-500">
                            Planilla {{ str_pad($planilla->Mes, 2, '0', STR_PAD_LEFT) }}/{{ $planilla->Gestion }}
                        </span>
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
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5 2.75C5 1.784 5.784 1 6.75 1h6.5c.966 0 1.75.784 1.75 1.75V4h4.25c.966 0 1.75.784 1.75 1.75v6.5c0 .966-.784 1.75-1.75 1.75H16v4.25c0 .966-.784 1.75-1.75 1.75h-8.5c-.966 0-1.75-.784-1.75-1.75V14H1.75C.784 14 0 13.216 0 12.25v-6.5C0 4.784.784 4 1.75 4H6V2.75zM6.75 2.5a.25.25 0 00-.25.25V4h7V2.75a.25.25 0 00-.25-.25h-6.5zM1.75 5.5a.25.25 0 00-.25.25v6.5c0 .138.112.25.25.25H6v-1.75C6 9.784 6.784 9 7.75 9h4.5c.966 0 1.75.784 1.75 1.75V12.5h4.25a.25.25 0 00.25-.25v-6.5a.25.25 0 00-.25-.25H1.75zM7.75 10.5a.25.25 0 00-.25.25v6.5c0 .138.112.25.25.25h4.5a.25.25 0 00.25-.25v-6.5a.25.25 0 00-.25-.25h-4.5z" clip-rule="evenodd" />
                </svg>
                Imprimir
            </button>
            <a href="{{ route('planillas.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver
            </a>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información principal -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Información del empleado -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Información del Empleado</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Datos personales y laborales</p>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Nombre completo</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $detalle->Nombres }} {{ $detalle->ApellidoPaterno }} {{ $detalle->ApellidoMaterno }}
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Carnet de Identidad</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $detalle->CI }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Cargo</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $detalle->NombreCargo }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Departamento</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $detalle->NombreDepartamento }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $detalle->NombreCategoria }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Detalle de ingresos -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Ingresos</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Desglose detallado de ingresos</p>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Salario Base</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 sm:mt-0 sm:col-span-2">
                                Bs {{ number_format($planilla->SalarioBasico, 2) }}
                            </dd>
                        </div>
                        @if($planilla->BonoAntiguedad > 0)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Bono de Antigüedad</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Bs {{ number_format($planilla->BonoAntiguedad, 2) }}
                            </dd>
                        </div>
                        @endif
                        @if($planilla->HorasExtra > 0)
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Horas Extra</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Bs {{ number_format($planilla->HorasExtra, 2) }}
                            </dd>
                        </div>
                        @endif
                        @if($planilla->Bonos > 0)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Bonos Adicionales</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Bs {{ number_format($planilla->Bonos, 2) }}
                            </dd>
                        </div>
                        @endif
                        <div class="bg-green-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-t-2 border-green-200">
                            <dt class="text-sm font-semibold text-green-700">Total Ingresos</dt>
                            <dd class="mt-1 text-lg font-bold text-green-900 sm:mt-0 sm:col-span-2">
                                Bs {{ number_format($planilla->TotalIngresos, 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Detalle de descuentos -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Descuentos</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Desglose detallado de descuentos</p>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        @if($planilla->AFP > 0)
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">AFP</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Bs {{ number_format($planilla->AFP, 2) }}
                            </dd>
                        </div>
                        @endif
                        @if($planilla->CajaSalud > 0)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Caja de Salud</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Bs {{ number_format($planilla->CajaSalud, 2) }}
                            </dd>
                        </div>
                        @endif
                        @if($planilla->ImpuestoRC_IVA > 0)
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RC-IVA</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Bs {{ number_format($planilla->ImpuestoRC_IVA, 2) }}
                            </dd>
                        </div>
                        @endif
                        @if($planilla->OtrosDescuentos > 0)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Otros Descuentos</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Bs {{ number_format($planilla->OtrosDescuentos, 2) }}
                            </dd>
                        </div>
                        @endif
                        <div class="bg-red-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-t-2 border-red-200">
                            <dt class="text-sm font-semibold text-red-700">Total Descuentos</dt>
                            <dd class="mt-1 text-lg font-bold text-red-900 sm:mt-0 sm:col-span-2">
                                Bs {{ number_format($planilla->TotalDescuentos, 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Resumen de planilla -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Resumen de Planilla</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Período</dt>
                            <dd class="text-lg font-semibold text-gray-900">
                                {{ \Carbon\Carbon::createFromFormat('n', $planilla->Mes)->locale('es')->monthName }} {{ $planilla->Gestion }}
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Días Trabajados</dt>
                            <dd class="text-sm text-gray-900">
                                {{ $planilla->DiasTrabajados ?? 30 }} días
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Total Ingresos</dt>
                            <dd class="text-lg font-semibold text-green-600">
                                Bs {{ number_format($planilla->TotalIngresos, 2) }}
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Total Descuentos</dt>
                            <dd class="text-lg font-semibold text-red-600">
                                Bs {{ number_format($planilla->TotalDescuentos, 2) }}
                            </dd>
                        </div>
                        <div class="bg-blue-50 px-4 py-5 border-t-2 border-blue-200">
                            <dt class="text-sm font-semibold text-blue-700 mb-2">Líquido Pagable</dt>
                            <dd class="text-2xl font-bold text-blue-900">
                                Bs {{ number_format($planilla->LiquidoPagable, 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Estado de pago -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Estado de Pago</h3>
                </div>
                <div class="border-t border-gray-200 p-4">
                    <div class="text-center">
                        @if($planilla->EstadoPago === 'Pagado')
                            <div class="mb-4">
                                <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100">
                                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                    </svg>
                                </span>
                            </div>
                            <p class="text-lg font-semibold text-green-900">Planilla Pagada</p>
                            @if($planilla->FechaPago)
                                <p class="text-sm text-green-700 mt-1">
                                    Fecha: {{ \Carbon\Carbon::parse($planilla->FechaPago)->format('d/m/Y') }}
                                </p>
                            @endif
                        @else
                            <div class="mb-4">
                                <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100">
                                    <svg class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                            </div>
                            <p class="text-lg font-semibold text-amber-900">Pago Pendiente</p>
                            <p class="text-sm text-amber-700 mt-1">
                                Esperando procesamiento
                            </p>
                            <div class="mt-4">
                                <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <svg class="-ml-1 mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    Marcar como Pagado
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Información Adicional</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Fecha de generación</dt>
                            <dd class="text-sm text-gray-900">
                                {{ $planilla->FechaCreacion ? \Carbon\Carbon::parse($planilla->FechaCreacion)->format('d/m/Y H:i') : 'No registrada' }}
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Usuario de creación</dt>
                            <dd class="text-sm text-gray-900">
                                {{ $planilla->UsuarioCreacion ?? 'Sistema' }}
                            </dd>
                        </div>
                        @if($planilla->Observaciones)
                        <div class="bg-gray-50 px-4 py-5">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Observaciones</dt>
                            <dd class="text-sm text-gray-900">
                                {{ $planilla->Observaciones }}
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .print\\:hidden {
        display: none !important;
    }
    
    body * {
        visibility: hidden;
    }
    
    .print-area, .print-area * {
        visibility: visible;
    }
    
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endsection