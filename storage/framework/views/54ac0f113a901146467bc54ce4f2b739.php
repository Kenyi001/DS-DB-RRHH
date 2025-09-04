

<?php $__env->startSection('title', 'Nuevo Contrato'); ?>
<?php $__env->startSection('page-title', 'Crear Nuevo Contrato'); ?>
<?php $__env->startSection('page-description', 'Registrar un nuevo contrato laboral en el sistema'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto">
    <form method="POST" action="<?php echo e(route('contratos.store')); ?>">
        <?php echo csrf_field(); ?>
        
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
                        <?php $__currentLoopData = $empleados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empleado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($empleado->IDEmpleado); ?>" <?php echo e(old('IDEmpleado') == $empleado->IDEmpleado ? 'selected' : ''); ?>>
                                <?php echo e($empleado->Nombres); ?> <?php echo e($empleado->ApellidoPaterno); ?> <?php echo e($empleado->ApellidoMaterno); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['IDEmpleado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <!-- Cargo -->
                    <div>
                        <label for="IDCargo" class="block text-sm font-medium text-gray-700">Cargo</label>
                        <select name="IDCargo" id="IDCargo" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar cargo...</option>
                            <?php $__currentLoopData = $cargos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cargo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cargo->IDCargo); ?>" <?php echo e(old('IDCargo') == $cargo->IDCargo ? 'selected' : ''); ?>>
                                    <?php echo e($cargo->NombreCargo); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['IDCargo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Departamento -->
                    <div>
                        <label for="IDDepartamento" class="block text-sm font-medium text-gray-700">Departamento</label>
                        <select name="IDDepartamento" id="IDDepartamento" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar departamento...</option>
                            <?php $__currentLoopData = $departamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($departamento->IDDepartamento); ?>" <?php echo e(old('IDDepartamento') == $departamento->IDDepartamento ? 'selected' : ''); ?>>
                                    <?php echo e($departamento->NombreDepartamento); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['IDDepartamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label for="IDCategoria" class="block text-sm font-medium text-gray-700">Categoría</label>
                        <select name="IDCategoria" id="IDCategoria" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar categoría...</option>
                            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($categoria->IDCategoria); ?>" <?php echo e(old('IDCategoria') == $categoria->IDCategoria ? 'selected' : ''); ?>>
                                    <?php echo e($categoria->NombreCategoria); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['IDCategoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Número de Contrato -->
                    <div>
                        <label for="NumeroContrato" class="block text-sm font-medium text-gray-700">Número de Contrato</label>
                        <input type="text" name="NumeroContrato" id="NumeroContrato" value="<?php echo e(old('NumeroContrato')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Se generará automáticamente si se deja vacío">
                        <?php $__errorArgs = ['NumeroContrato'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Tipo de Contrato -->
                    <div>
                        <label for="TipoContrato" class="block text-sm font-medium text-gray-700">Tipo de Contrato</label>
                        <select name="TipoContrato" id="TipoContrato" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Indefinido" <?php echo e(old('TipoContrato') == 'Indefinido' ? 'selected' : ''); ?>>Indefinido</option>
                            <option value="Temporal" <?php echo e(old('TipoContrato') == 'Temporal' ? 'selected' : ''); ?>>Temporal</option>
                            <option value="Consultoria" <?php echo e(old('TipoContrato') == 'Consultoria' ? 'selected' : ''); ?>>Consultoría</option>
                        </select>
                        <?php $__errorArgs = ['TipoContrato'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <!-- Fecha de Contrato -->
                    <div>
                        <label for="FechaContrato" class="block text-sm font-medium text-gray-700">Fecha del Contrato</label>
                        <input type="date" name="FechaContrato" id="FechaContrato" value="<?php echo e(old('FechaContrato', date('Y-m-d'))); ?>" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <?php $__errorArgs = ['FechaContrato'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Fecha de Inicio -->
                    <div>
                        <label for="FechaInicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                        <input type="date" name="FechaInicio" id="FechaInicio" value="<?php echo e(old('FechaInicio')); ?>" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <?php $__errorArgs = ['FechaInicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Fecha de Fin -->
                    <div>
                        <label for="FechaFin" class="block text-sm font-medium text-gray-700">Fecha de Fin <span class="text-gray-500">(opcional)</span></label>
                        <input type="date" name="FechaFin" id="FechaFin" value="<?php echo e(old('FechaFin')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <?php $__errorArgs = ['FechaFin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Haber Básico -->
                <div>
                    <label for="HaberBasico" class="block text-sm font-medium text-gray-700">Haber Básico (Bs)</label>
                    <input type="number" name="HaberBasico" id="HaberBasico" value="<?php echo e(old('HaberBasico')); ?>" required
                           step="0.01" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="0.00">
                    <?php $__errorArgs = ['HaberBasico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="<?php echo e(route('contratos.index')); ?>" 
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/contratos/create.blade.php ENDPATH**/ ?>