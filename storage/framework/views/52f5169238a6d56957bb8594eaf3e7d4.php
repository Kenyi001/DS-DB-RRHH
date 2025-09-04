

<?php $__env->startSection('title', 'Reportes y Estadísticas'); ?>
<?php $__env->startSection('page-title', 'Dashboard de Reportes'); ?>
<?php $__env->startSection('page-description', 'Resumen ejecutivo y reportes estadísticos del sistema RRHH'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <!-- Tarjetas de estadísticas principales -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        
        <!-- Total Empleados -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Empleados</dt>
                            <dd class="text-3xl font-bold text-gray-900">308</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="<?php echo e(route('empleados.index')); ?>" class="font-medium text-blue-600 hover:text-blue-500">
                        Ver todos los empleados
                    </a>
                </div>
            </div>
        </div>

        <!-- Contratos Vigentes -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Contratos Vigentes</dt>
                            <dd class="text-3xl font-bold text-gray-900">245</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="<?php echo e(route('contratos.vigentes')); ?>" class="font-medium text-green-600 hover:text-green-500">
                        Ver contratos vigentes
                    </a>
                </div>
            </div>
        </div>

        <!-- Planillas Mes Actual -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Planillas <?php echo e(date('m/Y')); ?></dt>
                            <dd class="text-3xl font-bold text-gray-900">259</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="<?php echo e(route('planillas.index', ['gestion' => date('Y'), 'mes' => date('n')])); ?>" class="font-medium text-amber-600 hover:text-amber-500">
                        Ver planillas del mes
                    </a>
                </div>
            </div>
        </div>

        <!-- Nómina Total -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Nómina <?php echo e(date('m/Y')); ?></dt>
                            <dd class="text-2xl font-bold text-gray-900">Bs 2,187,824</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="<?php echo e(route('planillas.reportes')); ?>" class="font-medium text-purple-600 hover:text-purple-500">
                        Ver reportes de planilla
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas y Notificaciones -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Alertas y Notificaciones
            </h3>
            <div class="space-y-4">
                <!-- Contratos por vencer -->
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-2 w-2 bg-amber-400 rounded-full mt-2"></div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">
                            Contratos por vencer en 30 días
                        </p>
                        <p class="text-sm text-gray-500">
                            15 contratos requieren renovación próximamente
                        </p>
                        <div class="mt-2">
                            <a href="<?php echo e(route('contratos.alertas')); ?>" class="text-sm font-medium text-amber-600 hover:text-amber-500">
                                Ver detalles →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Planillas pendientes -->
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-2 w-2 bg-red-400 rounded-full mt-2"></div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">
                            Planillas pendientes de pago
                        </p>
                        <p class="text-sm text-gray-500">
                            12 planillas están pendientes de pago
                        </p>
                        <div class="mt-2">
                            <a href="<?php echo e(route('planillas.index', ['estado_pago' => 'Pendiente'])); ?>" class="text-sm font-medium text-red-600 hover:text-red-500">
                                Revisar planillas →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos a reportes -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
                Reportes Disponibles
            </h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                
                <!-- Reporte de Empleados -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="<?php echo e(route('empleados.index')); ?>" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Reporte de Empleados</p>
                                <p class="text-sm text-gray-500">Lista completa de empleados activos e inactivos</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Contratos -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="<?php echo e(route('contratos.index')); ?>" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Reporte de Contratos</p>
                                <p class="text-sm text-gray-500">Estado de contratos y vencimientos</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Planillas -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="<?php echo e(route('planillas.reportes')); ?>" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Reportes de Planillas</p>
                                <p class="text-sm text-gray-500">Análisis de nómina y estadísticas salariales</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Generar Planilla -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="<?php echo e(route('planillas.generar')); ?>" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Generar Planilla</p>
                                <p class="text-sm text-gray-500">Crear nueva planilla mensual</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contratos por Vencer -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="<?php echo e(route('contratos.alertas')); ?>" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Alertas de Contratos</p>
                                <p class="text-sm text-gray-500">Contratos próximos a vencer</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Exportar Datos -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="#" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Exportar Datos</p>
                                <p class="text-sm text-gray-500">Descargar reportes en Excel/PDF</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del sistema -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Información del Sistema
            </h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="text-center">
                    <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e(now()->format('d/m/Y H:i')); ?></dd>
                </div>
                <div class="text-center">
                    <dt class="text-sm font-medium text-gray-500">Versión del Sistema</dt>
                    <dd class="mt-1 text-sm text-gray-900">RRHH YPFB v1.0</dd>
                </div>
                <div class="text-center">
                    <dt class="text-sm font-medium text-gray-500">Base de Datos</dt>
                    <dd class="mt-1 text-sm text-gray-900">SQL Server (Activo)</dd>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/reportes/dashboard.blade.php ENDPATH**/ ?>