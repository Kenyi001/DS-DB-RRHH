@extends('layouts.app')

@section('title', 'Gestión de Empleados')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="empleadosTable()">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900">Gestión de Empleados</h1>
        <p class="text-neutral-600 mt-1">Administrar información de empleados de YPFB-Andina</p>
    </div>

    <!-- Filtros y Acciones -->
    <div class="bg-white rounded-lg shadow-sm border border-neutral-200 p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Búsqueda -->
            <div class="flex-1 max-w-md">
                <label for="search" class="sr-only">Buscar empleados</label>
                <input 
                    type="text" 
                    id="search"
                    x-model="filters.search"
                    @input.debounce.300ms="loadEmpleados()"
                    placeholder="Buscar por nombre, CI o email..."
                    class="w-full rounded-lg border-neutral-300 focus:border-ypfb-blue focus:ring-ypfb-blue"
                >
            </div>

            <!-- Filtro Departamento -->
            <div class="w-full lg:w-48">
                <select 
                    x-model="filters.departamento_id"
                    @change="loadEmpleados()"
                    class="w-full rounded-lg border-neutral-300 focus:border-ypfb-blue focus:ring-ypfb-blue"
                >
                    <option value="">Todos los departamentos</option>
                    <!-- TODO: Cargar dinámicamente desde API -->
                    <option value="1">Recursos Humanos</option>
                    <option value="2">Contabilidad</option>
                    <option value="3">Operaciones</option>
                </select>
            </div>

            <!-- Botón Nuevo -->
            <button 
                @click="showCreateModal = true"
                class="btn btn-primary"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Empleado
            </button>
        </div>
    </div>

    <!-- Tabla de Empleados -->
    <div class="bg-white rounded-lg shadow-sm border border-neutral-200 overflow-hidden">
        <!-- Loading State -->
        <div x-show="loading" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-ypfb-blue"></div>
            <p class="mt-2 text-neutral-600">Cargando empleados...</p>
        </div>

        <!-- Desktop Table -->
        <div x-show="!loading" class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            Empleado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            CI
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            Departamento
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                    <template x-for="empleado in empleados" :key="empleado.IDEmpleado">
                        <tr class="hover:bg-neutral-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-neutral-900" x-text="empleado.nombre_completo"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500" x-text="empleado.CI"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500" x-text="empleado.departamento || 'N/A'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500" x-text="empleado.Email"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="empleado.Estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    x-text="empleado.Estado ? 'Activo' : 'Inactivo'"
                                ></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="viewEmpleado(empleado.IDEmpleado)" class="text-ypfb-blue hover:text-ypfb-blue/80 mr-3">
                                    Ver
                                </button>
                                <button @click="editEmpleado(empleado.IDEmpleado)" class="text-neutral-600 hover:text-neutral-800">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div x-show="!loading" class="md:hidden">
            <template x-for="empleado in empleados" :key="empleado.IDEmpleado">
                <div class="border-b border-neutral-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-neutral-900" x-text="empleado.nombre_completo"></h3>
                            <p class="text-sm text-neutral-500" x-text="'CI: ' + empleado.CI"></p>
                            <p class="text-sm text-neutral-500" x-text="empleado.Email"></p>
                        </div>
                        <div class="flex flex-col items-end">
                            <span 
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mb-2"
                                :class="empleado.Estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                x-text="empleado.Estado ? 'Activo' : 'Inactivo'"
                            ></span>
                            <button @click="viewEmpleado(empleado.IDEmpleado)" class="text-ypfb-blue text-sm">
                                Ver Detalle
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Paginación -->
        <div x-show="!loading && pagination.total > pagination.per_page" class="px-6 py-4 border-t border-neutral-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-neutral-700">
                    Mostrando <span x-text="pagination.from"></span> a <span x-text="pagination.to"></span> 
                    de <span x-text="pagination.total"></span> empleados
                </div>
                <div class="flex space-x-2">
                    <button 
                        @click="previousPage()"
                        :disabled="pagination.current_page <= 1"
                        class="btn btn-secondary btn-sm"
                        :class="pagination.current_page <= 1 ? 'btn-disabled' : ''"
                    >
                        Anterior
                    </button>
                    <button 
                        @click="nextPage()"
                        :disabled="pagination.current_page >= pagination.last_page"
                        class="btn btn-secondary btn-sm"
                        :class="pagination.current_page >= pagination.last_page ? 'btn-disabled' : ''"
                    >
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function empleadosTable() {
    return {
        empleados: [],
        loading: true,
        filters: {
            search: '',
            departamento_id: ''
        },
        pagination: {
            current_page: 1,
            per_page: 15,
            total: 0,
            last_page: 1,
            from: 0,
            to: 0
        },
        showCreateModal: false,

        init() {
            this.loadEmpleados();
        },

        async loadEmpleados() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    ...this.filters
                });

                const response = await fetch(`/api/v1/empleados?${params}`, {
                    headers: {
                        'Authorization': `Bearer ${this.getAuthToken()}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.empleados = data.data.data;
                    this.pagination = {
                        current_page: data.data.current_page,
                        per_page: data.data.per_page,
                        total: data.data.total,
                        last_page: data.data.last_page,
                        from: data.data.from,
                        to: data.data.to
                    };
                }
            } catch (error) {
                console.error('Error cargando empleados:', error);
            } finally {
                this.loading = false;
            }
        },

        nextPage() {
            if (this.pagination.current_page < this.pagination.last_page) {
                this.pagination.current_page++;
                this.loadEmpleados();
            }
        },

        previousPage() {
            if (this.pagination.current_page > 1) {
                this.pagination.current_page--;
                this.loadEmpleados();
            }
        },

        viewEmpleado(id) {
            window.location.href = `/empleados/${id}`;
        },

        editEmpleado(id) {
            window.location.href = `/empleados/${id}/edit`;
        },

        getAuthToken() {
            // TODO: Implementar gestión de tokens Sanctum
            return localStorage.getItem('auth_token') || 'dummy_token';
        }
    }
}
</script>
@endsection