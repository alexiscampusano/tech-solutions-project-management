@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('header', 'Iniciar Sesión')

@section('description', 'Accede a tu cuenta para gestionar proyectos')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-xl font-bold text-gray-900">
                Iniciar Sesión
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Regístrate aquí
                </a>
            </p>
        </div>

        <!-- Login Form -->
        <form class="mt-8 space-y-6" x-data="loginForm()" @submit.prevent="submitLogin">
            <div class="rounded-md shadow-sm space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Correo Electrónico
                    </label>
                    <input x-model="form.email" 
                           id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                           placeholder="tu@ejemplo.com">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Contraseña
                    </label>
                    <input x-model="form.password" 
                           id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="current-password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                           placeholder="Tu contraseña">
                </div>
            </div>

            <!-- Error messages -->
            <div x-show="error" x-text="error" class="text-red-600 text-sm mt-2"></div>

            <!-- Success messages -->
            <div x-show="success" x-text="success" class="text-green-600 text-sm mt-2"></div>

            <!-- Submit button -->
            <div>
                <button type="submit" 
                        :disabled="loading"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">Iniciar Sesión</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Iniciando sesión...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function loginForm() {
    return {
        form: {
            email: '',
            password: ''
        },
        loading: false,
        error: '',
        success: '',

        async submitLogin() {
            this.loading = true;
            this.error = '';
            this.success = '';

            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (data.success) {
                    localStorage.setItem('authToken', data.data.token);
                    localStorage.setItem('user', JSON.stringify(data.data.user));
                    
                    this.success = 'Inicio de sesión exitoso. Redirigiendo...';
                    
                    setTimeout(() => {
                        window.location.href = '/proyectos';
                    }, 500);
                } else {
                    this.error = data.message || 'Error al iniciar sesión';
                }
            } catch (error) {
                this.error = 'Error de conexión. Inténtalo de nuevo.';
                console.error('Login error:', error);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
