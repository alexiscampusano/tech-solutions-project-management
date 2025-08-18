@extends('layouts.app')

@section('title', 'Detalles del Proyecto')

@section('header', 'Detalles del Proyecto')

@section('description', 'Información completa del proyecto seleccionado')

@push('head')
<meta name="proyecto-id" content="{{ $proyectoId ?? '' }}">
@endpush

@section('content')
<!-- Loading State -->
<div id="loading-state" class="bg-white shadow-lg rounded-lg overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-gray-400 to-gray-500">
        <div class="animate-pulse">
            <div class="h-6 bg-gray-300 rounded w-64 mb-2"></div>
            <div class="h-4 bg-gray-300 rounded w-32"></div>
        </div>
    </div>
    <div class="px-6 py-6">
        <div class="animate-pulse grid grid-cols-1 md:grid-cols-2 gap-6">
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

<!-- Content State -->
<div id="content-state" class="hidden bg-white shadow-lg rounded-lg overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
        <h2 id="proyecto-nombre" class="text-xl font-bold text-white"></h2>
        <div class="mt-2">
            <span id="proyecto-estado" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"></span>
        </div>
    </div>

    <div class="px-6 py-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500 uppercase tracking-wide">ID del Proyecto</dt>
                <dd id="proyecto-id" class="mt-1 text-sm font-medium text-gray-900"></dd>
            </div>
            
            <div>
                <dt class="text-sm font-medium text-gray-500 uppercase tracking-wide">Responsable</dt>
                <dd id="proyecto-responsable" class="mt-1 text-sm font-medium text-gray-900"></dd>
            </div>
            
            <div>
                <dt class="text-sm font-medium text-gray-500 uppercase tracking-wide">Fecha de Inicio</dt>
                <dd id="proyecto-fecha" class="mt-1 text-sm font-medium text-gray-900"></dd>
            </div>
            
            <div>
                <dt class="text-sm font-medium text-gray-500 uppercase tracking-wide">Monto</dt>
                <dd id="proyecto-monto" class="mt-1 text-sm font-medium text-gray-900"></dd>
            </div>
            
            <div>
                <dt class="text-sm font-medium text-gray-500 uppercase tracking-wide">Creado por</dt>
                <dd id="proyecto-creador" class="mt-1 text-sm font-medium text-gray-900"></dd>
            </div>
            
            <div>
                <dt class="text-sm font-medium text-gray-500 uppercase tracking-wide">Fecha de Creación</dt>
                <dd id="proyecto-created-at" class="mt-1 text-sm font-medium text-gray-900"></dd>
            </div>
        </dl>
    </div>

    <div class="bg-gray-50 px-6 py-4">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-2 sm:space-y-0">
            <a href="{{ route('proyectos.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver a la Lista
            </a>
            
            <div id="action-buttons" class="flex space-x-2">
              
            </div>
        </div>
    </div>
</div>

<script>

let proyecto = null;

const proyectoId = document.querySelector('meta[name="proyecto-id"]')?.getAttribute('content') || null;


document.addEventListener('DOMContentLoaded', function() {
    if (proyectoId) {
        loadProyecto();
    } else {
        showError('ID de proyecto no válido');
    }
});


async function loadProyecto() {
    showLoadingState();
    
    try {
        const response = await fetch(`/api/proyectos/${proyectoId}`);
        const data = await response.json();
        
        if (data.success) {
            proyecto = data.data;
            renderProyecto(proyecto);
            setupActionButtons(proyecto);
            showContentState();
        } else {
            throw new Error(data.message || 'Proyecto no encontrado');
        }
    } catch (error) {
        console.error('Error loading proyecto:', error);
        showError(error.message);
    }
}


function renderProyecto(proyecto) {

    document.getElementById('proyecto-nombre').textContent = proyecto.nombre;
    

    const estadoEl = document.getElementById('proyecto-estado');
    estadoEl.textContent = getEstadoLabel(proyecto.estado);
    estadoEl.className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${getEstadoClasses(proyecto.estado)}`;
    

    document.getElementById('proyecto-id').textContent = proyecto.id;
    document.getElementById('proyecto-responsable').textContent = proyecto.responsable;
    document.getElementById('proyecto-fecha').textContent = formatDate(proyecto.fecha_inicio);
    document.getElementById('proyecto-monto').textContent = `$${formatNumber(proyecto.monto)}`;
    document.getElementById('proyecto-creador').textContent = proyecto.creador ? proyecto.creador.name : 'N/A';
    document.getElementById('proyecto-created-at').textContent = formatDateTime(proyecto.created_at);
}


function setupActionButtons(proyecto) {
    const actionButtonsContainer = document.getElementById('action-buttons');
    const currentUser = localStorage.getItem('user');
    
    if (!currentUser) {

        actionButtonsContainer.innerHTML = '';
        return;
    }
    
    const userData = JSON.parse(currentUser);
    const userId = userData.id;
    
    if (proyecto.created_by === userId) {

        actionButtonsContainer.innerHTML = `
            <a href="/proyectos/${proyecto.id}/edit" 
               class="inline-flex items-center px-4 py-2 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Editar Proyecto
            </a>
            
            <button onclick="deleteProjectDetail(${proyecto.id})" 
                    class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Eliminar Proyecto
            </button>
        `;
    } else {

        actionButtonsContainer.innerHTML = `
            <div class="flex items-center text-sm text-gray-500">
                <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                Solo el creador puede modificar este proyecto
            </div>
        `;
    }
}

function deleteProjectDetail(proyectoId) {
    const projectName = proyecto ? proyecto.nombre : null;
    
    showDeleteModal(proyectoId, projectName, async function(id) {
        const token = localStorage.getItem('authToken');
        if (!token) {
            showResultModal('error', 'Error de Autenticación', 'No tienes permisos para eliminar este proyecto.');
            return;
        }
        
        try {
            const response = await fetch(`/api/proyectos/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showResultModal('success', 'Proyecto Eliminado', `El proyecto "${projectName}" ha sido eliminado exitosamente.`, function() {
                    window.location.href = '/proyectos';
                });
            } else {
                showResultModal('error', 'Error al Eliminar', data.message || 'No se pudo eliminar el proyecto.');
            }
        } catch (error) {
            console.error('Error:', error);
            showResultModal('error', 'Error de Conexión', 'No se pudo conectar con el servidor. Inténtalo de nuevo.');
        }
    });
}

function getEstadoClasses(estado) {
    const classes = {
        'planificacion': 'bg-yellow-100 text-yellow-800',
        'en_progreso': 'bg-blue-100 text-blue-800',
        'completado': 'bg-green-100 text-green-800',
        'pausado': 'bg-gray-100 text-gray-800',
        'cancelado': 'bg-red-100 text-red-800'
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
}

function getEstadoLabel(estado) {
    const labels = {
        'planificacion': 'Planificación',
        'en_progreso': 'En Progreso',
        'completado': 'Completado',
        'pausado': 'Pausado',
        'cancelado': 'Cancelado'
    };
    return labels[estado] || estado;
}

function formatNumber(number) {
    return new Intl.NumberFormat('es-CL').format(number);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-CL');
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('es-CL');
}

function showLoadingState() {
    document.getElementById('loading-state').classList.remove('hidden');
    document.getElementById('error-state').classList.add('hidden');
    document.getElementById('content-state').classList.add('hidden');
}

function showContentState() {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');
    document.getElementById('content-state').classList.remove('hidden');
}

function showError(message) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('error-state').classList.remove('hidden');
    document.getElementById('content-state').classList.add('hidden');
    
    const errorEl = document.getElementById('error-message');
    if (errorEl) {
        errorEl.textContent = message;
    }
}
</script>
@endsection