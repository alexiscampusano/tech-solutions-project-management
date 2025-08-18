@extends('layouts.app')

@section('title', 'Editar Proyecto')

@section('header', 'Editar Proyecto')

@section('description', 'Modifica la información del proyecto según sea necesario.')

@push('head')
<meta name="proyecto-id" content="{{ $proyectoId ?? '' }}">
@endpush

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Loading State -->
    <div id="loading-state" class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
            <div class="animate-pulse">
                <div class="h-4 bg-blue-300 rounded w-32 mb-2"></div>
                <div class="h-3 bg-blue-200 rounded w-64"></div>
            </div>
        </div>
        <div class="px-6 py-6">
            <div class="animate-pulse space-y-6">
                <div class="h-16 bg-gray-300 rounded"></div>
                <div class="h-16 bg-gray-300 rounded"></div>
                <div class="h-16 bg-gray-300 rounded"></div>
                <div class="h-16 bg-gray-300 rounded"></div>
                <div class="h-16 bg-gray-300 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Error State -->
    <div id="error-state" class="hidden bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Error al cargar proyecto</h3>
            <p class="mt-1 text-sm text-gray-500" id="error-message">Ha ocurrido un error.</p>
            <div class="mt-6">
                <button onclick="loadProyecto()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Reintentar
                </button>
            </div>
        </div>
    </div>

    <!-- Form State -->
    <div id="form-state" class="hidden bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
            <h3 class="text-sm font-medium text-blue-900">Editar Información del Proyecto</h3>
            <p class="mt-1 text-sm text-blue-700">
                Modifica los campos que necesites actualizar.
            </p>
        </div>

        <form id="proyecto-form" class="px-6 py-6 space-y-6">
            <!-- Project name -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Proyecto *
                </label>
                <input type="text" 
                       name="nombre" 
                       id="nombre" 
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
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
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
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
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
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
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
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
                           class="block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="0.00"
                           required>
                </div>
                <div id="monto-error" class="hidden mt-1 text-sm text-red-600"></div>
                <p class="mt-1 text-xs text-gray-500">
                    Ingresa el monto en pesos chilenos. No incluyas puntos ni comas.
                </p>
            </div>

            <!-- Creation information (read-only) -->
            <div class="pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Información de Creación</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Creado por:</span>
                        <span id="created-by" class="ml-1 font-medium text-gray-900">-</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Fecha de creación:</span>
                        <span id="created-at" class="ml-1 font-medium text-gray-900">-</span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-6 border-t border-gray-200">
                <div class="flex justify-between space-x-3">
                    <a href="/proyectos/{{ $proyectoId ?? '' }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Cancelar
                    </a>
                
                <button type="submit" 
                            id="submit-btn"
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="-ml-1 mr-2 h-5 w-5" id="submit-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                        <span id="submit-text">Actualizar Proyecto</span>
                </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let proyecto = null;
let estadosDisponibles = {};
const proyectoId = document.querySelector('meta[name="proyecto-id"]')?.getAttribute('content') || null;

document.addEventListener('DOMContentLoaded', function() {
    if (proyectoId) {
        loadData();
    } else {
        showError('ID de proyecto no válido');
    }
});

async function loadData() {
    showLoadingState();
    
    try {
        const [proyectoResponse, estadosResponse] = await Promise.all([
            fetch(`/api/proyectos/${proyectoId}`),
            fetch('/api/proyectos/estados')
        ]);
        
        const proyectoData = await proyectoResponse.json();
        const estadosData = await estadosResponse.json();
        
        if (proyectoData.success && estadosData.success) {
            proyecto = proyectoData.data;
            estadosDisponibles = estadosData.data;
            
            populateForm();
            setupForm();
            showFormState();
        } else {
            throw new Error(proyectoData.message || estadosData.message || 'Error al cargar datos');
        }
        
    } catch (error) {
        console.error('Error loading data:', error);
        showError(error.message);
    }
}

async function loadProyecto() {
    await loadData();
}

function populateForm() {
    const estadoSelect = document.getElementById('estado');
    
    while (estadoSelect.children.length > 1) {
        estadoSelect.removeChild(estadoSelect.lastChild);
    }
    
    Object.entries(estadosDisponibles).forEach(([key, label]) => {
        const option = document.createElement('option');
        option.value = key;
        option.textContent = label;
        estadoSelect.appendChild(option);
    });
    
    document.getElementById('nombre').value = proyecto.nombre;
    document.getElementById('fecha_inicio').value = proyecto.fecha_inicio;
    document.getElementById('estado').value = proyecto.estado;
    document.getElementById('responsable').value = proyecto.responsable;
    document.getElementById('monto').value = proyecto.monto;
    
    // Creation information (read-only)
    document.getElementById('created-by').textContent = proyecto.creador ? proyecto.creador.name : 'N/A';
    document.getElementById('created-at').textContent = formatDateTime(proyecto.created_at);
    
    // Update cancel link
    const cancelLink = document.querySelector('a[href*="/proyectos/"]');
    if (cancelLink) {
        cancelLink.href = `/proyectos/${proyecto.id}`;
    }
}

// Setup form handling
function setupForm() {
    const form = document.getElementById('proyecto-form');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        await submitForm();
    });
}

// Send form via API
async function submitForm() {
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitIcon = document.getElementById('submit-icon');

    submitBtn.disabled = true;
    submitText.textContent = 'Actualizando...';
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
            showResultModal('error', 'Error de Autenticación', 'Debes iniciar sesión para editar el proyecto.');
            return;
        }
        
        const response = await fetch(`/api/proyectos/${proyectoId}`, {
            method: 'PUT',
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
            showResultModal('success', 'Proyecto Actualizado', 'El proyecto ha sido actualizado exitosamente.', function() {
                window.location.href = `/proyectos/${proyectoId}`;
            });
        } else {
            if (response.status === 422) {
                displayValidationErrors(result.errors || {});
            } else {
                showResultModal('error', 'Error al Actualizar', result.message || 'No se pudo actualizar el proyecto.');
            }
        }
        
    } catch (error) {
        console.error('Error:', error);
        showResultModal('error', 'Error de Conexión', 'No se pudo conectar con el servidor. Inténtalo de nuevo.');
    } finally {
        submitBtn.disabled = false;
        submitText.textContent = 'Actualizar Proyecto';
        submitIcon.innerHTML = `
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
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

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('es-CL');
}

function showLoadingState() {
    document.getElementById('loading-state').classList.remove('hidden');
    document.getElementById('error-state').classList.add('hidden');
    document.getElementById('form-state').classList.add('hidden');
}

function showFormState() {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');
    document.getElementById('form-state').classList.remove('hidden');
}

function showError(message) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('error-state').classList.remove('hidden');
    document.getElementById('form-state').classList.add('hidden');
    
    const errorEl = document.getElementById('error-message');
    if (errorEl) {
        errorEl.textContent = message;
    }
}
</script>
@endsection 