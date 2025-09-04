<!-- Logo -->
<div class="flex h-16 shrink-0 items-center">
    <img class="h-8 w-auto" src="https://via.placeholder.com/150x40/1d4ed8/FFFFFF?text=YPFB" alt="YPFB">
    <div class="ml-3">
        <div class="text-lg font-bold text-gray-900">YPFB</div>
        <div class="text-xs text-gray-500">Sistema RRHH</div>
    </div>
</div>

<!-- Navegación -->
<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Dashboard
                    </a>
                </li>

                <!-- Empleados -->
                <li>
                    <a href="{{ route('empleados.index') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('empleados.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('empleados.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Empleados
                        @if(isset($empleadosCount) && $empleadosCount > 0)
                            <span class="ml-auto text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                {{ number_format($empleadosCount) }}
                            </span>
                        @endif
                    </a>
                </li>

                <!-- Contratos -->
                <li x-data="{ open: {{ request()->routeIs('contratos.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('contratos.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('contratos.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Contratos
                        <svg class="ml-auto h-5 w-5 transform transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition class="mt-1 px-2">
                        <li>
                            <a href="{{ route('contratos.index') }}" 
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('contratos.index') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                                Todos los contratos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contratos.vigentes') }}" 
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('contratos.vigentes') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                                Vigentes
                                @if(isset($contratosVigentesCount) && $contratosVigentesCount > 0)
                                    <span class="ml-auto text-xs bg-green-100 text-green-800 px-1.5 py-0.5 rounded-full">
                                        {{ $contratosVigentesCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contratos.alertas') }}" 
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('contratos.alertas') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                                Alertas
                                @if(isset($contratosAlertasCount) && $contratosAlertasCount > 0)
                                    <span class="ml-auto text-xs bg-red-100 text-red-800 px-1.5 py-0.5 rounded-full">
                                        {{ $contratosAlertasCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Planillas -->
                <li x-data="{ open: {{ request()->routeIs('planillas.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('planillas.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('planillas.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Planillas
                        <svg class="ml-auto h-5 w-5 transform transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition class="mt-1 px-2">
                        <li>
                            <a href="{{ route('planillas.index') }}" 
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('planillas.index') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                                Todas las planillas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('planillas.generar') }}" 
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('planillas.generar') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                                Generar planilla
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('planillas.reportes') }}" 
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('planillas.reportes') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                                Reportes
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </li>

        <!-- Sección de Reportes -->
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400">REPORTES Y ESTADÍSTICAS</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <li>
                    <a href="{{ route('reportes.dashboard') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('reportes.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('reportes.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        Dashboard Reportes
                    </a>
                </li>
            </ul>
        </li>

        <!-- Sección de Configuración -->
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400">CONFIGURACIÓN</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <li>
                    <a href="{{ route('configuracion.index') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('configuracion.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('configuracion.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Configuración
                    </a>
                </li>
            </ul>
        </li>

        <!-- Usuario actual (información) -->
        <li class="mt-auto">
            <div class="rounded-lg bg-gray-50 p-3">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-white">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-3 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ auth()->user()->name ?? 'Usuario' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ ucfirst(auth()->user()->role ?? 'user') }}
                        </p>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    <div class="flex justify-between">
                        <span>Empleados:</span>
                        <span class="font-medium">{{ number_format($empleadosCount ?? 308) }}</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span>Planillas:</span>
                        <span class="font-medium">{{ number_format($planillasCount ?? 8288) }}</span>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</nav>