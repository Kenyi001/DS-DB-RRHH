@extends('layouts.main')

@section('title', 'Nuevo Contrato')
@section('page-title', 'Crear Nuevo Contrato')
@section('page-description', 'Registrar un nuevo contrato laboral en el sistema')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('contratos.store') }}">
        @csrf
        
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Información del Contrato</h3>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                <!-- Empleado -->
                <div>
                    <label for="IDEmpleado" class="block text-sm font-medium text-gray-700">Empleado</label>
                    <select name="IDEmpleado" id="IDEmpleado" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar empleado...</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->IDEmpleado }}" {{ old('IDEmpleado') == $empleado->IDEmpleado ? 'selected' : '' }}>
                                {{ $empleado->Nombres }} {{ $empleado->ApellidoPaterno }} {{ $empleado->ApellidoMaterno }}
                            </option>
                        @endforeach
                    </select>
                    @error('IDEmpleado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <!-- Cargo -->
                    <div>
                        <label for="IDCargo" class="block text-sm font-medium text-gray-700">Cargo</label>
                        <select name="IDCargo" id="IDCargo" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar cargo...</option>
                            @foreach($cargos as $cargo)
                                <option value="{{ $cargo->IDCargo }}" {{ old('IDCargo') == $cargo->IDCargo ? 'selected' : '' }}>
                                    {{ $cargo->NombreCargo }}
                                </option>
                            @endforeach
                        </select>
                        @error('IDCargo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departamento -->
                    <div>
                        <label for="IDDepartamento" class="block text-sm font-medium text-gray-700">Departamento</label>
                        <select name="IDDepartamento" id="IDDepartamento" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar departamento...</option>
                            @foreach($departamentos as $departamento)
                                <option value="{{ $departamento->IDDepartamento }}" {{ old('IDDepartamento') == $departamento->IDDepartamento ? 'selected' : '' }}>
                                    {{ $departamento->NombreDepartamento }}
                                </option>
                            @endforeach
                        </select>
                        @error('IDDepartamento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label for="IDCategoria" class="block text-sm font-medium text-gray-700">Categoría</label>
                        <select name="IDCategoria" id="IDCategoria" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar categoría...</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->IDCategoria }}" {{ old('IDCategoria') == $categoria->IDCategoria ? 'selected' : '' }}>
                                    {{ $categoria->NombreCategoria }}
                                </option>
                            @endforeach
                        </select>
                        @error('IDCategoria')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Número de Contrato -->
                    <div>
                        <label for="NumeroContrato" class="block text-sm font-medium text-gray-700">Número de Contrato</label>
                        <input type="text" name="NumeroContrato" id="NumeroContrato" value="{{ old('NumeroContrato') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Se generará automáticamente si se deja vacío">
                        @error('NumeroContrato')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Contrato -->
                    <div>
                        <label for="TipoContrato" class="block text-sm font-medium text-gray-700">Tipo de Contrato</label>
                        <select name="TipoContrato" id="TipoContrato" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Indefinido" {{ old('TipoContrato') == 'Indefinido' ? 'selected' : '' }}>Indefinido</option>
                            <option value="Temporal" {{ old('TipoContrato') == 'Temporal' ? 'selected' : '' }}>Temporal</option>
                            <option value="Consultoria" {{ old('TipoContrato') == 'Consultoria' ? 'selected' : '' }}>Consultoría</option>
                        </select>
                        @error('TipoContrato')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <!-- Fecha de Contrato -->
                    <div>
                        <label for="FechaContrato" class="block text-sm font-medium text-gray-700">Fecha del Contrato</label>
                        <input type="date" name="FechaContrato" id="FechaContrato" value="{{ old('FechaContrato', date('Y-m-d')) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('FechaContrato')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Inicio -->
                    <div>
                        <label for="FechaInicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                        <input type="date" name="FechaInicio" id="FechaInicio" value="{{ old('FechaInicio') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('FechaInicio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Fin -->
                    <div>
                        <label for="FechaFin" class="block text-sm font-medium text-gray-700">Fecha de Fin <span class="text-gray-500">(opcional)</span></label>
                        <input type="date" name="FechaFin" id="FechaFin" value="{{ old('FechaFin') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('FechaFin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Haber Básico -->
                <div>
                    <label for="HaberBasico" class="block text-sm font-medium text-gray-700">Haber Básico (Bs)</label>
                    <input type="number" name="HaberBasico" id="HaberBasico" value="{{ old('HaberBasico') }}" required
                           step="0.01" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="0.00">
                    @error('HaberBasico')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('contratos.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Crear Contrato
                </button>
            </div>
        </div>
    </form>
</div>
@endsection