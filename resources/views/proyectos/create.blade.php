@extends('layouts.app')

@section('title', 'Crear Proyecto')

@section('header', 'Crear Nuevo Proyecto')

@section('description', 'Completa la información del proyecto para agregarlo al sistema de gestión.')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-green-50 border-b border-green-200">
            <h3 class="text-sm font-medium text-green-900">Información del Proyecto</h3>
            <p class="mt-1 text-sm text-green-700">
                Completa todos los campos para crear un nuevo proyecto.
            </p>
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="hidden px-6 py-12 text-center">
            <div class="animate-pulse">
                <div class="mx-auto h-8 w-8 bg-green-300 rounded-full"></div>
                <div class="mt-4 h-4 bg-gray-300 rounded w-32 mx-auto"></div>
            </div>
            <p class="mt-2 text-sm text-gray-600">Cargando formulario...</p>
        </div>

        <!-- Form -->
        <form id="proyecto-form" class="px-6 py-6 space-y-6">
            <!-- Project name -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Proyecto *
                </label>
                <input type="text" 
                       name="nombre" 
                       id="nombre" 
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                       placeholder="Ej: Sistema de Facturación Electrónica"
                       required>
                <div id="nombre-error" class="hidden mt-1 text-sm text-red-600"></div>
            </div>

            <!-- Start date -->
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Inicio *
                </label>
                <input type="date" 
                       name="fecha_inicio" 
                       id="fecha_inicio" 
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                       required>
                <div id="fecha_inicio-error" class="hidden mt-1 text-sm text-red-600"></div>
            </div>

            <!-- State -->
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                    Estado del Proyecto *
                </label>
                <select name="estado" 
                        id="estado" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                        required>
                    <option value="">Selecciona un estado</option>
                    <!-- The options will be loaded dynamically -->
                </select>
                <div id="estado-error" class="hidden mt-1 text-sm text-red-600"></div>
            </div>

            <!-- Responsible -->
            <div>
                <label for="responsable" class="block text-sm font-medium text-gray-700 mb-2">
                    Responsable del Proyecto *
                </label>
                <input type="text" 
                       name="responsable" 
                       id="responsable" 
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                       placeholder="Ej: Juan Pérez, María González"
                       required>
                <div id="responsable-error" class="hidden mt-1 text-sm text-red-600"></div>
            </div>

            <!-- Amount -->
            <div>
                <label for="monto" class="block text-sm font-medium text-gray-700 mb-2">
                    Monto del Proyecto *
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" 
                           name="monto" 
                           id="monto" 
                           min="0" 
                           step="0.01"
                           class="block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                           placeholder="0.00"
                           required>
                </div>
                <div id="monto-error" class="hidden mt-1 text-sm text-red-600"></div>
                <p class="mt-1 text-xs text-gray-500">
                    Ingresa el monto en pesos chilenos. No incluyas puntos ni comas.
                </p>
            </div>

            <!-- Buttons -->
            <div class="pt-6 border-t border-gray-200">
                <div class="flex justify-between space-x-3">
                    <a href="{{ route('proyectos.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            id="submit-btn"
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="-ml-1 mr-2 h-5 w-5" id="submit-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span id="submit-text">Crear Proyecto</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>

let estadosDisponibles = {};

document.addEventListener('DOMContentLoaded', function() {
    loadEstados();
    setupForm();
});

async function loadEstados() {
    try {
        const response = await fetch('/api/proyectos/estados');
        const data = await response.json();
        
        if (data.success) {
            estadosDisponibles = data.data;
            populateEstadosSelect();
        } else {
            console.error('Error loading estados:', data.message);
        }
    } catch (error) {
        console.error('Error loading estados:', error);
    }
}

function populateEstadosSelect() {
    const select = document.getElementById('estado');
    
    while (select.children.length > 1) {
        select.removeChild(select.lastChild);
    }
    
    Object.entries(estadosDisponibles).forEach(([key, label]) => {
        const option = document.createElement('option');
        option.value = key;
        option.textContent = label;
        select.appendChild(option);
    });
}

function setupForm() {
    const form = document.getElementById('proyecto-form');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        await submitForm();
    });
}

async function submitForm() {
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitIcon = document.getElementById('submit-icon');
    
    submitBtn.disabled = true;
    submitText.textContent = 'Creando...';
    submitIcon.innerHTML = `
        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    `;
    
    clearErrors();
    
    try {
        const formData = new FormData(document.getElementById('proyecto-form'));
        const data = Object.fromEntries(formData.entries());
        
        const token = localStorage.getItem('authToken');
        if (!token) {
            showResultModal('error', 'Error de Autenticación', 'Debes iniciar sesión para crear un proyecto.');
            return;
        }
        
        // Send to the API
        const response = await fetch('/api/proyectos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showResultModal('success', 'Proyecto Creado', 'El proyecto ha sido creado exitosamente.', function() {
                window.location.href = '/proyectos';
            });
        } else {
            if (response.status === 422) {
                displayValidationErrors(result.errors || {});
            } else {
                showResultModal('error', 'Error al Crear', result.message || 'No se pudo crear el proyecto.');
            }
        }
        
    } catch (error) {
        console.error('Error:', error);
        showResultModal('error', 'Error de Conexión', 'No se pudo conectar con el servidor. Inténtalo de nuevo.');
    } finally {
        submitBtn.disabled = false;
        submitText.textContent = 'Crear Proyecto';
        submitIcon.innerHTML = `
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        `;
    }
}

function displayValidationErrors(errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const errorDiv = document.getElementById(`${field}-error`);
        const input = document.getElementById(field);
        
        if (errorDiv) {
            errorDiv.textContent = Array.isArray(messages) ? messages[0] : messages;
            errorDiv.classList.remove('hidden');
        }
        
        if (input) {
            input.classList.add('border-red-300');
        }
    });
}

function clearErrors() {
    const errorDivs = document.querySelectorAll('[id$="-error"]');
    errorDivs.forEach(div => {
        div.classList.add('hidden');
        div.textContent = '';
    });
    
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.classList.remove('border-red-300');
    });
}
</script>
@endsection