

<?php $__env->startSection('title', 'Configuración del Sistema'); ?>
<?php $__env->startSection('page-title', 'Configuración'); ?>
<?php $__env->startSection('page-description', 'Configuraciones generales del sistema RRHH'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <!-- Panel de configuraciones -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Configuraciones del Sistema</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                
                <!-- Configuración General -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="#" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Configuración General</p>
                                <p class="text-sm text-gray-500">Configuraciones básicas del sistema</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Gestión de Usuarios -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="<?php echo e(route('admin.usuarios')); ?>" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Gestión de Usuarios</p>
                                <p class="text-sm text-gray-500">Administrar usuarios del sistema</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Empresa -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="#" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Datos de la Empresa</p>
                                <p class="text-sm text-gray-500">Información de YPFB-Andina</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mi Perfil -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="<?php echo e(route('configuracion.perfil')); ?>" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Mi Perfil</p>
                                <p class="text-sm text-gray-500">Configurar mi perfil de usuario</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Base de Datos -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="<?php echo e(route('admin.sistema')); ?>" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Sistema y BD</p>
                                <p class="text-sm text-gray-500">Información del sistema y base de datos</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Respaldo y Restauración -->
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="#" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Respaldo y Restauración</p>
                                <p class="text-sm text-gray-500">Gestión de respaldos de datos</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Sistema -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Información del Sistema</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Versión del Sistema</dt>
                    <dd class="mt-1 text-sm text-gray-900">RRHH YPFB v1.0.0</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Framework</dt>
                    <dd class="mt-1 text-sm text-gray-900">Laravel 11.45.2</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Base de Datos</dt>
                    <dd class="mt-1 text-sm text-gray-900">SQL Server</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">PHP</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e(PHP_VERSION); ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Servidor Web</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e($_SERVER['SERVER_SOFTWARE'] ?? 'No detectado'); ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Sistema Operativo</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e(PHP_OS); ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Memoria PHP</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e(ini_get('memory_limit')); ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e(now()->format('d/m/Y H:i')); ?></dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Estado de los Módulos -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Estado de los Módulos</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                
                <!-- Módulo Empleados -->
                <div class="flex items-center justify-between py-3 px-4 bg-green-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Módulo de Empleados</p>
                            <p class="text-sm text-gray-500">Gestión completa de empleados</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </span>
                </div>

                <!-- Módulo Contratos -->
                <div class="flex items-center justify-between py-3 px-4 bg-green-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Módulo de Contratos</p>
                            <p class="text-sm text-gray-500">Gestión de contratos laborales</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </span>
                </div>

                <!-- Módulo Planillas -->
                <div class="flex items-center justify-between py-3 px-4 bg-green-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Módulo de Planillas</p>
                            <p class="text-sm text-gray-500">Gestión de nómina y salarios</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </span>
                </div>

                <!-- Módulo Reportes -->
                <div class="flex items-center justify-between py-3 px-4 bg-green-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Módulo de Reportes</p>
                            <p class="text-sm text-gray-500">Dashboard y reportes estadísticos</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </span>
                </div>

            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/configuracion/index.blade.php ENDPATH**/ ?>