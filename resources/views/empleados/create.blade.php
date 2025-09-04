@extends('layouts.main')

@section('title', 'Nuevo Empleado')

@section('content')
<div x-data="empleadoForm()" class="space-y-6">
    
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('empleados.index') }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="flex-shrink-0 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 10v8a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H8a1 1 0 00-1 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-8a1 1 0 01.293-.707l7-7z" clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Empleados</span>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <a href="{{ route('empleados.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Empleados</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-700">Nuevo</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Empleado</h1>
            <p class="mt-1 text-sm text-gray-600">
                Completa la información básica del empleado
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('empleados.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver a la lista
            </a>
        </div>
    </div>

    <!-- Alertas -->
    @if($errors->any())
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Errores en el formulario
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul role="list" class="list-disc space-y-1 pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Formulario -->
    <form method="POST" action="{{ route('empleados.store') }}" class="space-y-8">
        @csrf
        
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Información Personal</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Datos básicos de identificación del empleado
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        
                        <!-- Nombres -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="Nombres" class="block text-sm font-medium text-gray-700">
                                Nombres <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="Nombres" 
                                   id="Nombres" 
                                   value="{{ old('Nombres') }}"
                                   required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('Nombres') border-red-300 @enderror">
                            @error('Nombres')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Apellido Paterno -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="ApellidoPaterno" class="block text-sm font-medium text-gray-700">
                                Apellido Paterno <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="ApellidoPaterno" 
                                   id="ApellidoPaterno" 
                                   value="{{ old('ApellidoPaterno') }}"
                                   required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('ApellidoPaterno') border-red-300 @enderror">
                            @error('ApellidoPaterno')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Apellido Materno -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="ApellidoMaterno" class="block text-sm font-medium text-gray-700">
                                Apellido Materno
                            </label>
                            <input type="text" 
                                   name="ApellidoMaterno" 
                                   id="ApellidoMaterno" 
                                   value="{{ old('ApellidoMaterno') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('ApellidoMaterno') border-red-300 @enderror">
                            @error('ApellidoMaterno')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- CI -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="CI" class="block text-sm font-medium text-gray-700">
                                Carnet de Identidad <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="CI" 
                                   id="CI" 
                                   value="{{ old('CI') }}"
                                   required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('CI') border-red-300 @enderror"
                                   placeholder="12345678">
                            @error('CI')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="FechaNacimiento" class="block text-sm font-medium text-gray-700">
                                Fecha de Nacimiento <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="FechaNacimiento" 
                                   id="FechaNacimiento" 
                                   value="{{ old('FechaNacimiento') }}"
                                   required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('FechaNacimiento') border-red-300 @enderror">
                            @error('FechaNacimiento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Género -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="Genero" class="block text-sm font-medium text-gray-700">
                                Género <span class="text-red-500">*</span>
                            </label>
                            <select name="Genero" 
                                    id="Genero" 
                                    required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('Genero') border-red-300 @enderror">
                                <option value="">Seleccionar...</option>
                                <option value="M" {{ old('Genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('Genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                            @error('Genero')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Contacto -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Información de Contacto</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Datos de contacto y ubicación del empleado
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        
                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="Email" class="block text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <input type="email" 
                                   name="Email" 
                                   id="Email" 
                                   value="{{ old('Email') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('Email') border-red-300 @enderror"
                                   placeholder="ejemplo@ypfb.gov.bo">
                            @error('Email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Teléfono -->
                        <div class="col-span-6 sm:col-span-2">
                            <label for="Telefono" class="block text-sm font-medium text-gray-700">
                                Teléfono
                            </label>
                            <input type="text" 
                                   name="Telefono" 
                                   id="Telefono" 
                                   value="{{ old('Telefono') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('Telefono') border-red-300 @enderror"
                                   placeholder="70123456">
                            @error('Telefono')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dirección -->
                        <div class="col-span-6">
                            <label for="Direccion" class="block text-sm font-medium text-gray-700">
                                Dirección
                            </label>
                            <textarea name="Direccion" 
                                      id="Direccion" 
                                      rows="3"
                                      class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('Direccion') border-red-300 @enderror"
                                      placeholder="Dirección completa...">{{ old('Direccion') }}</textarea>
                            @error('Direccion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Laboral -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Información Laboral</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Datos relacionados con la posición del empleado
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        
                        <!-- Departamento -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="IDDepartamento" class="block text-sm font-medium text-gray-700">
                                Departamento <span class="text-red-500">*</span>
                            </label>
                            <select name="IDDepartamento" 
                                    id="IDDepartamento" 
                                    required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('IDDepartamento') border-red-300 @enderror">
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

                        <!-- Fecha de Ingreso -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="FechaIngreso" class="block text-sm font-medium text-gray-700">
                                Fecha de Ingreso <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="FechaIngreso" 
                                   id="FechaIngreso" 
                                   value="{{ old('FechaIngreso', date('Y-m-d')) }}"
                                   required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('FechaIngreso') border-red-300 @enderror">
                            @error('FechaIngreso')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="Estado" class="block text-sm font-medium text-gray-700">
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <select name="Estado" 
                                    id="Estado" 
                                    required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('Estado') border-red-300 @enderror">
                                <option value="1" {{ old('Estado', '1') == '1' ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('Estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('Estado')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('empleados.index') }}" 
               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancelar
            </a>
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                </svg>
                Guardar Empleado
            </button>
        </div>
    </form>
</div>

<script>
function empleadoForm() {
    return {
        // Funciones de validación y utilidades aquí si es necesario
    }
}
</script>
@endsection