@extends('layouts.main')

@section('title', 'Generar Planillas')

@section('content')
<div x-data="generarPlanillasData()" x-init="init()" class="space-y-6">
    
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
                            <span class="ml-4 text-sm font-medium text-gray-700">Generar</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Generar Planillas</h1>
            <p class="mt-1 text-sm text-gray-600">
                Genera planillas de pago para empleados activos
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

    <!-- Alerta si ya existen planillas -->
    @if($planillasExistentes > 0)
        <div class="rounded-md bg-amber-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800">
                        Planillas existentes detectadas
                    </h3>
                    <div class="mt-2 text-sm text-amber-700">
                        <p>
                            Ya existen {{ $planillasExistentes }} planillas generadas para {{ str_pad($mesActual, 2, '0', STR_PAD_LEFT) }}/{{ $gestionActual }}. 
                            Solo se generarán planillas para empleados que aún no las tengan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

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

    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Formulario principal -->
    <form method="POST" action="{{ route('planillas.generar.post') }}" class="space-y-8">
        @csrf
        
        <!-- Configuración del período -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Período de Planilla</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Selecciona el mes y año para generar las planillas
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        
                        <!-- Mes -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="mes" class="block text-sm font-medium text-gray-700">
                                Mes <span class="text-red-500">*</span>
                            </label>
                            <select name="mes" 
                                    id="mes" 
                                    required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('mes') border-red-300 @enderror">
                                <option value="1" {{ old('mes', $mesActual) == '1' ? 'selected' : '' }}>Enero</option>
                                <option value="2" {{ old('mes', $mesActual) == '2' ? 'selected' : '' }}>Febrero</option>
                                <option value="3" {{ old('mes', $mesActual) == '3' ? 'selected' : '' }}>Marzo</option>
                                <option value="4" {{ old('mes', $mesActual) == '4' ? 'selected' : '' }}>Abril</option>
                                <option value="5" {{ old('mes', $mesActual) == '5' ? 'selected' : '' }}>Mayo</option>
                                <option value="6" {{ old('mes', $mesActual) == '6' ? 'selected' : '' }}>Junio</option>
                                <option value="7" {{ old('mes', $mesActual) == '7' ? 'selected' : '' }}>Julio</option>
                                <option value="8" {{ old('mes', $mesActual) == '8' ? 'selected' : '' }}>Agosto</option>
                                <option value="9" {{ old('mes', $mesActual) == '9' ? 'selected' : '' }}>Septiembre</option>
                                <option value="10" {{ old('mes', $mesActual) == '10' ? 'selected' : '' }}>Octubre</option>
                                <option value="11" {{ old('mes', $mesActual) == '11' ? 'selected' : '' }}>Noviembre</option>
                                <option value="12" {{ old('mes', $mesActual) == '12' ? 'selected' : '' }}>Diciembre</option>
                            </select>
                            @error('mes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gestión -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="gestion" class="block text-sm font-medium text-gray-700">
                                Año <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="gestion" 
                                   id="gestion" 
                                   value="{{ old('gestion', $gestionActual) }}"
                                   min="2020" 
                                   max="{{ now()->year + 1 }}"
                                   required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('gestion') border-red-300 @enderror">
                            @error('gestion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selección de empleados -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Empleados a Incluir</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Selecciona los empleados para generar sus planillas
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    
                    <!-- Controles de selección -->
                    <div class="mb-4 flex flex-wrap gap-2">
                        <button type="button" @click="selectAll()" class="px-3 py-1 text-sm text-blue-600 hover:text-blue-500 hover:bg-blue-50 rounded">
                            Seleccionar todos
                        </button>
                        <button type="button" @click="selectNone()" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-500 hover:bg-gray-50 rounded">
                            Deseleccionar todos
                        </button>
                        <button type="button" @click="filterByDepartment()" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-500 hover:bg-gray-50 rounded">
                            Filtrar por departamento
                        </button>
                    </div>

                    <!-- Búsqueda rápida -->
                    <div class="mb-4">
                        <input type="text" 
                               x-model="searchTerm"
                               @input="filterEmployees()"
                               @keyup="filterEmployees()"
                               placeholder="Buscar empleado por nombre o departamento..."
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <!-- Lista de empleados -->
                    <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-md">
                        <div class="divide-y divide-gray-200">
                            @foreach($empleadosActivos as $empleado)
                                <div class="p-3 hover:bg-gray-50 employee-item" 
                                     data-name="{{ strtolower($empleado->Nombres . ' ' . $empleado->ApellidoPaterno . ' ' . $empleado->ApellidoMaterno) }}"
                                     data-department="{{ strtolower($empleado->NombreDepartamento) }}">
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" 
                                               name="empleados[]" 
                                               value="{{ $empleado->IDEmpleado }}"
                                               @change="updateSelection()"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded employee-checkbox"
                                               {{ in_array($empleado->IDEmpleado, old('empleados', [])) ? 'checked' : '' }}>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-3">
                                                <div class="h-8 w-8 flex-shrink-0">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-blue-600">
                                                            {{ substr($empleado->Nombres, 0, 1) }}{{ substr($empleado->ApellidoPaterno, 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $empleado->Nombres }} {{ $empleado->ApellidoPaterno }} {{ $empleado->ApellidoMaterno }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $empleado->NombreCargo }} - {{ $empleado->NombreDepartamento }}
                                                    </div>
                                                </div>
                                                <div class="text-sm text-gray-900">
                                                    Bs {{ number_format($empleado->HaberBasico, 2) }}
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @error('empleados')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Resumen de selección -->
                    <div class="mt-4 p-3 bg-blue-50 rounded-md">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-blue-700">
                                <span x-text="selectedCount"></span> empleados seleccionados
                            </span>
                            <span class="text-sm text-blue-600">
                                Total estimado: Bs <span x-text="formatNumber(totalEstimated)"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('planillas.index') }}" 
               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" 
                    :disabled="selectedCount === 0"
                    :class="selectedCount === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Generar Planillas
            </button>
        </div>
    </form>
</div>

<script>
function generarPlanillasData() {
    return {
        searchTerm: '',
        selectedCount: 0,
        totalEstimated: 0,
        
        init() {
            // Inicializar
            this.updateSelection();
        },
        
        selectAll() {
            const items = document.querySelectorAll('.employee-item');
            const visibleCheckboxes = [];
            
            items.forEach(item => {
                if (item.style.display !== 'none') {
                    const checkbox = item.querySelector('.employee-checkbox');
                    if (checkbox && !checkbox.disabled) {
                        visibleCheckboxes.push(checkbox);
                    }
                }
            });
            
            visibleCheckboxes.forEach(cb => cb.checked = true);
            this.updateSelection();
        },
        
        selectNone() {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(cb => cb.checked = false);
            this.updateSelection();
        },
        
        filterByDepartment() {
            const dept = prompt('Ingresa el nombre del departamento:');
            if (dept) {
                this.searchTerm = dept;
                this.filterEmployees();
            }
        },
        
        filterEmployees() {
            const items = document.querySelectorAll('.employee-item');
            const term = this.searchTerm.toLowerCase().trim();
            
            items.forEach(item => {
                if (!term) {
                    item.style.display = '';
                    return;
                }
                
                const name = (item.dataset.name || '').toLowerCase();
                const department = (item.dataset.department || '').toLowerCase();
                
                if (name.includes(term) || department.includes(term)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        },
        
        updateSelection() {
            const checkboxes = document.querySelectorAll('.employee-checkbox:checked');
            this.selectedCount = checkboxes.length;
            
            let total = 0;
            checkboxes.forEach(cb => {
                // Buscar el salario en el item del empleado
                const employeeItem = cb.closest('.employee-item');
                if (employeeItem) {
                    // El salario está en el último div con texto que empieza con "Bs"
                    const salaryElements = employeeItem.querySelectorAll('div');
                    let salaryText = '';
                    
                    for (let div of salaryElements) {
                        const text = div.textContent.trim();
                        if (text.startsWith('Bs ')) {
                            salaryText = text;
                            break;
                        }
                    }
                    
                    if (salaryText) {
                        // Extraer el número del texto "Bs 1,234.56"
                        const salaryMatch = salaryText.match(/Bs\s+([\d,]+\.?\d*)/);
                        if (salaryMatch) {
                            const salary = parseFloat(salaryMatch[1].replace(/,/g, ''));
                            if (!isNaN(salary)) {
                                total += salary;
                            }
                        }
                    }
                }
            });
            
            this.totalEstimated = total;
        },
        
        formatNumber(num) {
            return new Intl.NumberFormat('es-BO', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            }).format(num || 0);
        }
    }
}

// Función auxiliar para debug
window.debugPlanillas = function() {
    const items = document.querySelectorAll('.employee-item');
    console.log('Employee items found:', items.length);
    
    items.forEach((item, index) => {
        const checkbox = item.querySelector('.employee-checkbox');
        const name = item.dataset.name;
        const salaryElements = item.querySelectorAll('div');
        let salaryText = '';
        
        for (let div of salaryElements) {
            const text = div.textContent.trim();
            if (text.startsWith('Bs ')) {
                salaryText = text;
                break;
            }
        }
        
        console.log(`Employee ${index}:`, {
            name,
            checked: checkbox?.checked,
            salary: salaryText
        });
    });
};
</script>
@endsection