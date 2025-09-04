

<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard Principal'); ?>
<?php $__env->startSection('page-description', 'Resumen ejecutivo del sistema RRHH - YPFB'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="dashboardData()" x-init="loadData()" class="space-y-6">
    
    <!-- Debug temporal (remover en producción) -->
    <?php if(isset($stats['contratos']['debug']) && config('app.debug')): ?>
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-red-800">🔍 Debug Contratos (solo desarrollo)</h4>
        <div class="mt-2 text-sm text-red-700 grid grid-cols-2 gap-4">
            <div>
                <p><strong>Dashboard dice:</strong></p>
                <p>• Total vigentes: <?php echo e($stats['contratos']['vigentes']); ?></p>
                <p>• Por vencer: <?php echo e($stats['contratos']['porVencer']); ?></p>
            </div>
            <div>
                <p><strong>Análisis real:</strong></p>
                <p>• Indefinidos (sin fecha): <?php echo e($stats['contratos']['debug']['indefinidos'] ?? 0); ?></p>
                <p>• Con fecha de fin: <?php echo e($stats['contratos']['debug']['con_fecha_fin'] ?? 0); ?></p>
                <p>• Fechas en pasado: <?php echo e($stats['contratos']['debug']['fechas_pasado'] ?? 0); ?></p>
                <p>• Fechas futuras: <?php echo e($stats['contratos']['debug']['fechas_futuro'] ?? 0); ?></p>
            </div>
        </div>
        <?php if(($stats['contratos']['debug']['fechas_incorrectas'] ?? 0) > 0): ?>
        <div class="mt-2 p-2 bg-red-100 rounded text-xs">
            <strong>🚨 PROBLEMA CRÍTICO:</strong> 
            <?php echo e($stats['contratos']['debug']['fechas_incorrectas']); ?> contratos tienen fechas anteriores a 2020 (datos incorrectos)
            
            <?php if(isset($stats['contratos']['debug']['ejemplos_fechas_malas']) && $stats['contratos']['debug']['ejemplos_fechas_malas']->count() > 0): ?>
                <div class="mt-1">
                    <strong>Ejemplos:</strong>
                    <?php $__currentLoopData = $stats['contratos']['debug']['ejemplos_fechas_malas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ejemplo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($ejemplo->NumeroContrato); ?>: <?php echo e($ejemplo->FechaFin); ?>;
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
            
            <div class="mt-1">
                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">💡 Clic aquí para corregir automáticamente</a>
            </div>
        </div>
        <?php else: ?>
        <div class="mt-2 p-2 bg-green-100 rounded text-xs">
            <strong>✅ OK:</strong> No se detectaron fechas incorrectas en los contratos.
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- KPIs principales -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Empleados -->
        <div class="card hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total Empleados</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.empleados.total">...</p>
                    <p class="text-xs text-green-600">
                        <span x-text="stats.empleados.activos">...</span> activos
                    </p>
                </div>
            </div>
        </div>

        <!-- Contratos Vigentes -->
        <div class="card hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">Contratos Vigentes</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.contratos.vigentes">...</p>
                    <p class="text-xs text-red-600">
                        <span x-text="stats.contratos.porVencer">...</span> por vencer
                    </p>
                </div>
            </div>
        </div>

        <!-- Planillas del Mes -->
        <div class="card hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">Planillas Mes Actual</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.planillas.mesActual">...</p>
                    <p class="text-xs" :class="stats.planillas.pendientes > 0 ? 'text-red-600' : 'text-green-600'">
                        <span x-text="stats.planillas.pendientes">...</span> pendientes
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Nómina -->
        <div class="card hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">Nómina Mensual</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.nomina.mensual)">...</p>
                    <p class="text-xs text-gray-500">
                        Promedio: <span x-text="formatCurrency(stats.nomina.promedio)">...</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y análisis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Distribución por Departamento -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Empleados por Departamento</h3>
                <div class="flex space-x-2">
                    <button @click="chartType = 'pie'" 
                            :class="chartType === 'pie' ? 'btn-primary' : 'btn-secondary'"
                            class="px-2 py-1 text-xs rounded">Circular</button>
                    <button @click="chartType = 'bar'" 
                            :class="chartType === 'bar' ? 'btn-primary' : 'btn-secondary'"
                            class="px-2 py-1 text-xs rounded">Barras</button>
                </div>
            </div>
            <div class="h-64">
                <canvas id="departamentosChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Evolución de Planillas -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Evolución de Planillas</h3>
                <select x-model="periodoSeleccionado" @change="updatePlanillasChart()" 
                        class="text-sm border border-gray-300 rounded px-2 py-1">
                    <option value="6">Últimos 6 meses</option>
                    <option value="12">Último año</option>
                    <option value="24">Últimos 2 años</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="planillasChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Alertas y notificaciones -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Alertas de Contratos -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Alertas de Contratos</h3>
            <div class="space-y-3">
                <template x-for="alerta in alertas.contratos" :key="alerta.id">
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-red-900" x-text="alerta.empleado"></p>
                            <p class="text-xs text-red-600" x-text="alerta.mensaje"></p>
                        </div>
                        <div class="text-xs text-red-500" x-text="alerta.dias + ' días'"></div>
                    </div>
                </template>
                <template x-if="alertas.contratos.length === 0">
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500">✅ No hay alertas de contratos</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Actividad Reciente</h3>
            <div class="space-y-3">
                <template x-for="actividad in actividadReciente" :key="actividad.id">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900" x-text="actividad.descripcion"></p>
                            <p class="text-xs text-gray-500" x-text="actividad.fecha"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🚀 Accesos Rápidos</h3>
            <div class="space-y-2">
                <a href="<?php echo e(route('empleados.create')); ?>" 
                   class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Nuevo Empleado</span>
                </a>
                
                <a href="<?php echo e(route('planillas.generar')); ?>" 
                   class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Generar Planilla</span>
                </a>
                
                <a href="<?php echo e(route('reportes.dashboard')); ?>" 
                   class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Ver Reportes</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Loader -->
    <div x-show="loading" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Cargando datos...</span>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function dashboardData() {
    return {
        loading: false,
        chartType: 'pie',
        periodoSeleccionado: '6',
        stats: {
            empleados: { total: 0, activos: 0 },
            contratos: { vigentes: 0, porVencer: 0 },
            planillas: { mesActual: 0, pendientes: 0 },
            nomina: { mensual: 0, promedio: 0 }
        },
        alertas: {
            contratos: []
        },
        actividadReciente: [],
        departamentosChart: null,
        planillasChart: null,
        
        async loadData() {
            this.loading = true;
            try {
                // Simular datos mientras no tenemos APIs específicas
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                this.stats = {
                    empleados: { total: 308, activos: 300 },
                    contratos: { vigentes: 245, porVencer: 15 },
                    planillas: { mesActual: 259, pendientes: 12 },
                    nomina: { mensual: 2187824, promedio: 8446 }
                };
                
                this.alertas.contratos = [
                    { id: 1, empleado: 'Juan Pérez', mensaje: 'Contrato vence pronto', dias: 5 },
                    { id: 2, empleado: 'María García', mensaje: 'Contrato vencido', dias: -2 }
                ];
                
                this.actividadReciente = [
                    { id: 1, descripcion: 'Planilla agosto generada', fecha: 'Hace 2 horas' },
                    { id: 2, descripcion: 'Nuevo empleado registrado', fecha: 'Hace 4 horas' },
                    { id: 3, descripcion: 'Contrato renovado', fecha: 'Ayer' }
                ];
                
                this.$nextTick(() => {
                    this.initCharts();
                });
                
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            } finally {
                this.loading = false;
            }
        },
        
        initCharts() {
            this.initDepartamentosChart();
            this.initPlanillasChart();
        },
        
        initDepartamentosChart() {
            const ctx = document.getElementById('departamentosChart');
            if (!ctx) return;
            
            const data = {
                labels: ['RRHH', 'TI', 'Operaciones', 'Finanzas', 'Legal', 'Exploración'],
                datasets: [{
                    data: [45, 62, 78, 34, 28, 61],
                    backgroundColor: [
                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'
                    ]
                }]
            };
            
            this.departamentosChart = new Chart(ctx, {
                type: this.chartType,
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        },
        
        initPlanillasChart() {
            const ctx = document.getElementById('planillasChart');
            if (!ctx) return;
            
            this.planillasChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago'],
                    datasets: [{
                        label: 'Planillas Generadas',
                        data: [245, 258, 267, 255, 249, 259],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        },
        
        updatePlanillasChart() {
            if (!this.planillasChart) return;
            // Actualizar datos según período seleccionado
            this.planillasChart.update();
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('es-BO', {
                style: 'currency',
                currency: 'BOB',
                minimumFractionDigits: 0
            }).format(amount);
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/dashboard/index.blade.php ENDPATH**/ ?>