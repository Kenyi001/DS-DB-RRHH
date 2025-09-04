<div x-data="{ open: false }" class="relative">
    <!-- Botón del menú de usuario -->
    <button @click="open = !open" type="button" 
            class="relative flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 lg:p-2 lg:hover:bg-gray-50" 
            id="user-menu-button" aria-expanded="false" aria-haspopup="true">
        <span class="sr-only">Abrir menú de usuario</span>
        <div class="h-8 w-8 bg-blue-600 rounded-full flex items-center justify-center lg:h-6 lg:w-6">
            <span class="text-sm font-medium text-white lg:text-xs">
                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
            </span>
        </div>
        <span class="hidden ml-3 text-sm font-medium text-gray-700 lg:block">
            <span class="sr-only">Abrir menú de usuario, </span>
            {{ auth()->user()->name ?? 'Usuario' }}
        </span>
        <svg class="hidden ml-2 h-5 w-5 text-gray-400 lg:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Menú desplegable -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.outside="open = false"
         class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
         role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
        
        <!-- Información del usuario -->
        <div class="px-4 py-3 border-b border-gray-100">
            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'Usuario' }}</p>
            <p class="text-sm text-gray-500">{{ auth()->user()->email ?? 'usuario@ypfb.gov.bo' }}</p>
            <p class="text-xs text-gray-400 mt-1">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ ucfirst(auth()->user()->role ?? 'user') }}
                </span>
            </p>
        </div>

        <!-- Opciones del menú -->
        <a href="{{ route('perfil.index') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
           role="menuitem" tabindex="-1">
            <div class="flex items-center">
                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Mi perfil
            </div>
        </a>

        <a href="{{ route('configuracion.perfil') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
           role="menuitem" tabindex="-1">
            <div class="flex items-center">
                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Configuración
            </div>
        </a>

        <!-- Solo para administradores -->
        @if(auth()->user() && auth()->user()->role === 'admin')
            <hr class="my-1">
            <a href="{{ route('admin.usuarios') }}" 
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
               role="menuitem" tabindex="-1">
                <div class="flex items-center">
                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Gestionar usuarios
                </div>
            </a>

            <a href="{{ route('admin.sistema') }}" 
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
               role="menuitem" tabindex="-1">
                <div class="flex items-center">
                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75a4.5 4.5 0 01-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 11-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 016.336-4.486L21.75 6.75z" />
                    </svg>
                    Administración
                </div>
            </a>
        @endif

        <hr class="my-1">

        <!-- Cerrar sesión -->
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" 
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                    role="menuitem" tabindex="-1">
                <div class="flex items-center">
                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Cerrar sesión
                </div>
            </button>
        </form>
    </div>
</div>