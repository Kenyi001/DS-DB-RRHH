

<?php $__env->startSection('title', 'Empleado - ' . $empleado->Nombres . ' ' . $empleado->ApellidoPaterno); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <a href="<?php echo e(route('empleados.index')); ?>" class="text-gray-500 hover:text-gray-700">
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
                            <a href="<?php echo e(route('empleados.index')); ?>" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Empleados</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-700"><?php echo e($empleado->Nombres); ?> <?php echo e($empleado->ApellidoPaterno); ?></span>
                        </div>
                    </li>
                </ol>
            </nav>
            
            <div class="flex items-center">
                <div class="h-16 w-16 flex-shrink-0">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-xl font-medium text-blue-600">
                            <?php echo e(substr($empleado->Nombres, 0, 1)); ?><?php echo e(substr($empleado->ApellidoPaterno, 0, 1)); ?>

                        </span>
                    </div>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <?php echo e($empleado->Nombres); ?> <?php echo e($empleado->ApellidoPaterno); ?> <?php echo e($empleado->ApellidoMaterno); ?>

                    </h1>
                    <div class="flex items-center space-x-2 mt-1">
                        <?php if($empleado->Estado): ?>
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                Activo
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                Inactivo
                            </span>
                        <?php endif; ?>
                        <span class="text-sm text-gray-500">ID: <?php echo e($empleado->IDEmpleado); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?php echo e(route('empleados.edit', $empleado->IDEmpleado)); ?>" 
               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                    <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                </svg>
                Editar
            </a>
            <a href="<?php echo e(route('empleados.index')); ?>" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Alertas -->
    <?php if(session('success')): ?>
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
                    <p class="text-sm font-medium text-green-800"><?php echo e(session('success')); ?></p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="show = false" type="button" class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información Personal -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Datos Básicos -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Información Personal</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Datos básicos del empleado</p>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Nombres completos</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?php echo e($empleado->Nombres); ?> <?php echo e($empleado->ApellidoPaterno); ?> <?php echo e($empleado->ApellidoMaterno); ?>

                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Carnet de Identidad</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?php echo e($empleado->CI); ?></dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Fecha de nacimiento</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?php echo e($empleado->FechaNacimiento ? \Carbon\Carbon::parse($empleado->FechaNacimiento)->format('d/m/Y') : '-'); ?>

                                <?php if($empleado->FechaNacimiento): ?>
                                    <span class="text-gray-500">
                                        (<?php echo e(\Carbon\Carbon::parse($empleado->FechaNacimiento)->age); ?> años)
                                    </span>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Género</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?php echo e($empleado->Genero == 'M' ? 'Masculino' : 'Femenino'); ?>

                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Información de Contacto</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Datos de contacto y ubicación</p>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Correo electrónico</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?php if($empleado->Email): ?>
                                    <a href="mailto:<?php echo e($empleado->Email); ?>" class="text-blue-600 hover:text-blue-500">
                                        <?php echo e($empleado->Email); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-500">No registrado</span>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?php if($empleado->Telefono): ?>
                                    <a href="tel:<?php echo e($empleado->Telefono); ?>" class="text-blue-600 hover:text-blue-500">
                                        <?php echo e($empleado->Telefono); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-500">No registrado</span>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?php echo e($empleado->Direccion ?? 'No registrada'); ?>

                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Contratos -->
            <?php if($contratos->count() > 0): ?>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Historial de Contratos</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Contratos y posiciones del empleado</p>
                    </div>
                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                        <?php echo e($contratos->count()); ?> contratos
                    </span>
                </div>
                <div class="border-t border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $contratos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrato): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($contrato->NombreCargo); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($contrato->NombreCategoria); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($contrato->NombreDepartamento); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e(\Carbon\Carbon::parse($contrato->FechaInicio)->format('d/m/Y')); ?>

                                        -
                                        <?php echo e($contrato->FechaFin ? \Carbon\Carbon::parse($contrato->FechaFin)->format('d/m/Y') : 'Presente'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($contrato->Estado): ?>
                                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                                Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                                Finalizado
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Panel lateral -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Información Laboral -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Información Laboral</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Departamento</dt>
                            <dd class="text-sm text-gray-900">
                                <?php echo e($empleado->departamento->NombreDepartamento ?? 'Sin asignar'); ?>

                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Fecha de ingreso</dt>
                            <dd class="text-sm text-gray-900">
                                <?php echo e($empleado->FechaIngreso ? \Carbon\Carbon::parse($empleado->FechaIngreso)->format('d/m/Y') : '-'); ?>

                                <?php if($empleado->FechaIngreso): ?>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?php echo e(\Carbon\Carbon::parse($empleado->FechaIngreso)->diffForHumans()); ?>

                                    </div>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Estado actual</dt>
                            <dd class="text-sm">
                                <?php if($empleado->Estado): ?>
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                        <svg class="mr-1 h-1.5 w-1.5 fill-green-400">
                                            <circle cx="1" cy="1" r="1" />
                                        </svg>
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                        <svg class="mr-1 h-1.5 w-1.5 fill-red-400">
                                            <circle cx="1" cy="1" r="1" />
                                        </svg>
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Resumen de Planillas -->
            <?php if($planillas->count() > 0): ?>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Planillas Recientes</h3>
                    <p class="mt-1 text-sm text-gray-500">Últimas <?php echo e($planillas->count()); ?> planillas</p>
                </div>
                <div class="border-t border-gray-200">
                    <div class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $planillas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planilla): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="px-4 py-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        <?php echo e($planilla->Mes); ?>/<?php echo e($planilla->Gestion); ?>

                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Bs <?php echo e(number_format($planilla->LiquidoPagable, 2)); ?>

                                    </p>
                                </div>
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                    <?php echo e($planilla->EstadoPago); ?>

                                </span>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Acciones rápidas -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Acciones Rápidas</h3>
                </div>
                <div class="border-t border-gray-200">
                    <div class="divide-y divide-gray-200">
                        <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                            <svg class="mr-3 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V7.621a1.5 1.5 0 00-.44-1.06L14.439 4.44A1.5 1.5 0 0013.378 4H4.5z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M12 8a1 1 0 011 1v2.5a1 1 0 11-2 0V10H8v1.5a1 1 0 11-2 0V9a1 1 0 011-1h4z" clip-rule="evenodd" />
                            </svg>
                            Ver todas las planillas
                        </a>
                        <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                            <svg class="mr-3 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                            Ver contratos
                        </a>
                        <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                            <svg class="mr-3 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M15.621 4.379a3 3 0 00-4.242 0l-7 7a3 3 0 004.241 4.243h.001l.497-.5a.75.75 0 011.064 1.057l-.498.501-.002.002a4.5 4.5 0 01-6.364-6.364l7-7a4.5 4.5 0 016.368 6.36l-3.455 3.553A2.625 2.625 0 119.52 9.52l3.45-3.451a.75.75 0 111.061 1.06l-3.45 3.451a1.125 1.125 0 001.587 1.595l3.454-3.553a3 3 0 000-4.242z" clip-rule="evenodd" />
                            </svg>
                            Generar reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/empleados/show.blade.php ENDPATH**/ ?>