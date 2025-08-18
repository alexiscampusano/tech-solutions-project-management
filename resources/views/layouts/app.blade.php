<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema de Gestión de Proyectos') }} - @yield('title', 'Tech Solutions')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional meta tags from views -->
    @stack('head')
    
    <!-- Quick synchronous authentication verification to avoid flash (inline for performance) -->
    <script>
        (function() {
            const token = localStorage.getItem('authToken');
            const user = localStorage.getItem('user');
            
            if (token && user) {
                document.documentElement.style.setProperty('--auth-initial-state', 'user');
            } else {
                document.documentElement.style.setProperty('--auth-initial-state', 'guest');
            }
        })();
    </script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation Header -->
        <nav class="bg-white shadow-lg border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-xl font-bold text-gray-900">
                                <a href="{{ route('proyectos.index') }}" class="hover:text-blue-600 transition-colors">
                                    🏢 Tech Solutions
                                </a>
                            </h1>
                        </div>
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                            <a href="{{ route('proyectos.index') }}" 
                               class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors
                                      {{ request()->routeIs('proyectos.index') ? 'bg-blue-50 text-blue-600' : '' }}">
                                📋 Proyectos
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:flex md:items-center md:ml-6 space-x-4">
                        <!-- Widget UF -->
                        <div class="relative">
                            <div id="uf-component"></div>
                        </div>
                        
                        <!-- Authentication links -->
                        <div id="auth-nav" class="flex items-center space-x-3 ml-4">
                            <!-- Initial loading state -->
                            <div id="auth-loading" class="flex items-center space-x-2">
                                <div class="animate-pulse flex items-center space-x-2">
                                    <div class="h-4 w-4 bg-gray-300 rounded-full"></div>
                                    <div class="h-4 w-16 bg-gray-300 rounded"></div>
                                </div>
                            </div>
                            
                            <!-- When not authenticated -->
                            <div id="guest-links" class="hidden items-center space-x-3">
                                <a href="{{ route('login') }}" 
                                   class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    🔐 Iniciar Sesión
                                </a>
                                <a href="{{ route('register') }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    📝 Registrarse
                                </a>
                            </div>
                            
                            <!-- When authenticated -->
                            <div id="user-menu" class="hidden items-center space-x-3">
                                <span id="user-welcome" class="text-sm text-gray-700">Hola, <span id="user-name"></span></span>
                                <button id="logout-btn"
                                        class="text-red-600 hover:text-red-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    🚪 Cerrar Sesión
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <div class="border-b border-gray-200 pb-5">
                        <h3 class="text-xl leading-6 font-bold text-gray-900">
                            @yield('header', 'Sistema de Gestión de Proyectos')
                        </h3>
                        @hasSection('description')
                            <p class="mt-2 max-w-4xl text-sm text-gray-500">
                                @yield('description')
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Main Content -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Delete confirmation modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            
            <!-- Centrar el modal verticalmente -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Confirmar Eliminación
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="modal-message">
                                    ¿Estás seguro de que deseas eliminar este proyecto? Esta acción no se puede deshacer.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirm-delete-btn" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Eliminar
                    </button>
                    <button type="button" id="cancel-delete-btn"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Result modal -->
    <div id="result-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="result-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div id="result-icon" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Dynamic icon -->
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="result-modal-title">
                                <!-- Dynamic title -->
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="result-modal-message">
                                    <!-- Dynamic message -->
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="result-ok-btn"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating alerts (Toast) -->
    <div x-data="{ show: @if(session('success') || session('error')) true @else false @endif }"
         x-init="setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed bottom-4 right-4 z-50 w-full max-w-sm">

        @if(session('success'))
            <div class="bg-white border border-green-200 rounded-lg shadow-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">¡Éxito!</p>
                        <p class="mt-1 text-sm text-gray-500">{{ session('success') }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" class="inline-flex text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-white border border-red-200 rounded-lg shadow-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">Error</p>
                        <p class="mt-1 text-sm text-gray-500">{{ session('error') }}</p>
                    </div>
                     <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" class="inline-flex text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Additional scripts -->
    @stack('scripts')
    

</body>
</html> 