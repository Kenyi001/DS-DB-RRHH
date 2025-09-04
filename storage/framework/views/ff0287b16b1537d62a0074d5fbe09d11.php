<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Iniciar Sesión - Sistema RRHH YPFB</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        .login-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .form-input {
            @apply block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500;
        }
        
        .btn-primary {
            @apply w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500;
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="h-full login-bg">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        
        <!-- Logo y título -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md fade-in">
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <img class="w-10 h-10" src="https://via.placeholder.com/40x40/1d4ed8/FFFFFF?text=Y" alt="YPFB">
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold text-white">
                Sistema RRHH
            </h2>
            <p class="mt-2 text-center text-sm text-blue-100">
                Yacimientos Petrolíferos Fiscales Bolivianos
            </p>
        </div>

        <!-- Formulario de login -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md fade-in" style="animation-delay: 0.2s;">
            <div class="bg-white py-8 px-4 shadow-xl rounded-lg sm:px-10">
                
                <!-- Alertas -->
                <?php if(session('success')): ?>
                    <div class="mb-4 rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800"><?php echo e(session('success')); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="mb-4 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Error en el inicio de sesión
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc space-y-1 pl-5">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form method="POST" action="<?php echo e(route('login.post')); ?>" x-data="{ loading: false }" @submit="loading = true">
                    <?php echo csrf_field(); ?>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Correo electrónico
                        </label>
                        <div class="mt-1">
                            <input id="email" 
                                   name="email" 
                                   type="email" 
                                   autocomplete="email" 
                                   required 
                                   value="<?php echo e(old('email')); ?>"
                                   class="form-input"
                                   placeholder="tu.email@ypfb.gov.bo">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Contraseña
                        </label>
                        <div class="mt-1">
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   autocomplete="current-password" 
                                   required 
                                   class="form-input"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" 
                                   name="remember" 
                                   type="checkbox" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-900">
                                Recordarme
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" 
                                :disabled="loading"
                                class="btn-primary"
                                :class="loading ? 'opacity-50 cursor-not-allowed' : ''">
                            <span x-show="!loading">Iniciar sesión</span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Iniciando...
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Información de credenciales de prueba -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">🧪 Credenciales de prueba:</h4>
                    <div class="space-y-1 text-xs text-gray-600">
                        <div><strong>Admin:</strong> admin@ypfb.gov.bo / admin123</div>
                        <div><strong>Manager:</strong> manager@ypfb.gov.bo / manager123</div>
                        <div><strong>User:</strong> user@ypfb.gov.bo / user123</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center fade-in" style="animation-delay: 0.4s;">
            <p class="text-xs text-blue-100">
                © <?php echo e(date('Y')); ?> YPFB - Yacimientos Petrolíferos Fiscales Bolivianos
            </p>
            <p class="text-xs text-blue-200 mt-1">
                Sistema de Recursos Humanos v1.0
            </p>
        </div>
    </div>
</body>
</html><?php /**PATH /var/www/html/resources/views/auth/login.blade.php ENDPATH**/ ?>