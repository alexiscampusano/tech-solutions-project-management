@extends('layouts.app')

@section('title', 'Registrarse')

@section('header', 'Crear Cuenta')

@section('description', 'Únete para gestionar tus proyectos de manera eficiente')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-green-100">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-xl font-bold text-gray-900">
                Crear Cuenta
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Inicia sesión aquí
                </a>
            </p>
        </div>

        <!-- Registration Form -->
        <form class="mt-8 space-y-6" x-data="registerForm()" @submit.prevent="submitRegister">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nombre Completo
                    </label>
                    <input x-model="form.name" 
                           id="name" 
                           name="name" 
                           type="text" 
                           autocomplete="name" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                           placeholder="Ingresa tu nombre completo">
                </div>

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
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
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
                           autocomplete="new-password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                           placeholder="Mínimo 6 caracteres">
                </div>

                <!-- Confirmar Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirmar Contraseña
                    </label>
                    <input x-model="form.password_confirmation" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           autocomplete="new-password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                           placeholder="Repite tu contraseña">
                </div>
            </div>

            <!-- Real-time validation -->
            <div x-show="passwordMismatch" class="text-yellow-600 text-sm">
                Las contraseñas no coinciden
            </div>

            <!-- Error messages -->
            <div x-show="errors.length > 0" class="space-y-1">
                <template x-for="error in errors" :key="error">
                    <div x-text="error" class="text-red-600 text-sm"></div>
                </template>
            </div>

            <!-- Success messages -->
            <div x-show="success" x-text="success" class="text-green-600 text-sm"></div>

            <!-- Submit button -->
            <div>
                <button type="submit" 
                        :disabled="loading || passwordMismatch || !isFormValid"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">Crear Cuenta</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creando cuenta...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function registerForm() {
    return {
        form: {
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        },
        loading: false,
        errors: [],
        success: '',

        get passwordMismatch() {
            return this.form.password !== this.form.password_confirmation && 
                   this.form.password_confirmation.length > 0;
        },

        get isFormValid() {
            return this.form.name.length > 0 && 
                   this.form.email.length > 0 && 
                   this.form.password.length >= 6 &&
                   this.form.password === this.form.password_confirmation;
        },

        async submitRegister() {
            this.loading = true;
            this.errors = [];
            this.success = '';

            try {
                const response = await fetch('/api/auth/register', {
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
                    
                    this.success = 'Cuenta creada exitosamente. Redirigiendo...';
                    
                    setTimeout(() => {
                        window.location.href = '/proyectos';
                    }, 500);
                } else {
                    if (data.errors) {
                        this.errors = Object.values(data.errors).flat();
                    } else {
                        this.errors = [data.message || 'Error al crear la cuenta'];
                    }
                }
            } catch (error) {
                this.errors = ['Error de conexión. Inténtalo de nuevo.'];
                console.error('Register error:', error);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
