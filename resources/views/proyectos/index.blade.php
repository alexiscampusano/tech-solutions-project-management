@extends('layouts.app')

@section('title', 'Lista de Proyectos')

@section('header', 'Gestión de Proyectos')

@section('description', 'Administra todos los proyectos de Tech Solutions desde esta interfaz centralizada.')

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Lista de Proyectos</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500" id="proyectos-count">
                Cargando proyectos...
            </p>
        </div>
        <div>
            <a href="{{ route('proyectos.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nuevo Proyecto
            </a>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="text-center py-12">
        <div class="animate-pulse">
            <div class="mx-auto h-12 w-12 bg-gray-300 rounded-full"></div>
            <div class="mt-4 h-4 bg-gray-300 rounded w-32 mx-auto"></div>
            <div class="mt-2 h-3 bg-gray-300 rounded w-48 mx-auto"></div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay proyectos</h3>
        <p class="mt-1 text-sm text-gray-500">Comienza creando tu primer proyecto.</p>
        <div class="mt-6">
            <a href="{{ route('proyectos.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Crear Primer Proyecto
            </a>
        </div>
    </div>

    <!-- Content Container -->
    <div id="content-container" class="hidden">
        <!-- Mobile/tablet view: Cards -->
        <div id="mobile-view" class="grid gap-4 lg:hidden"></div>

        <!-- Desktop view: Compact table -->
        <div id="desktop-view" class="hidden lg:block overflow-hidden shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado & Monto</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable & Fecha</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado por</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody id="desktop-table-body" class="bg-white divide-y divide-gray-200">
            
                </tbody>
            </table>
        </div>
    </div>

    <!-- Error State -->
    <div id="error-state" class="hidden text-center py-12">
        <svg class="mx-auto h-12 w-12 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Error al cargar proyectos</h3>
        <p class="mt-1 text-sm text-gray-500" id="error-message">Ha ocurrido un error.</p>
        <div class="mt-6">
            <button onclick="loadProyectos()" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Reintentar
            </button>
        </div>
    </div>
</div>

<script>

let proyectos = [];

document.addEventListener('DOMContentLoaded', function() {
    loadProyectos();
});

async function loadProyectos() {
    showLoadingState();
    
    try {
        const response = await fetch('/api/proyectos');
        const data = await response.json();
        
        if (data.success) {
            proyectos = data.data;
            updateProyectosCount(proyectos.length);
            
            if (proyectos.length === 0) {
                showEmptyState();
            } else {
                renderProyectos(proyectos);
                showContentState();
                setupUserPermissions();
            }
        } else {
            throw new Error(data.message || 'Error al cargar proyectos');
        }
    } catch (error) {
        console.error('Error loading proyectos:', error);
        showErrorState(error.message);
    }
}

function updateProyectosCount(count) {
    const countEl = document.getElementById('proyectos-count');
    if (countEl) {
        countEl.textContent = `Total: ${count} proyecto${count !== 1 ? 's' : ''}`;
    }
}

function renderProyectos(proyectos) {
    renderMobileView(proyectos);
    renderDesktopView(proyectos);
}

function renderMobileView(proyectos) {
    const container = document.getElementById('mobile-view');
    container.innerHTML = '';
    
    proyectos.forEach(proyecto => {
        const card = createMobileCard(proyecto);
        container.appendChild(card);
    });
}

function renderDesktopView(proyectos) {
    const tbody = document.getElementById('desktop-table-body');
    tbody.innerHTML = '';
    
    proyectos.forEach(proyecto => {
        const row = createDesktopRow(proyecto);
        tbody.appendChild(row);
    });
}

function createMobileCard(proyecto) {
    const card = document.createElement('div');
    card.className = 'bg-white rounded-lg shadow-sm border border-gray-200 p-4';
    
    card.innerHTML = `
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-3 flex-1">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-medium text-blue-600">
                        ${proyecto.nombre.substring(0, 2).toUpperCase()}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-medium text-gray-900 truncate">${proyecto.nombre}</h3>
                    <p class="text-xs text-gray-500">ID: ${proyecto.id}</p>
                </div>
            </div>
            <div class="flex space-x-1 ml-2">
                <a href="/proyectos/${proyecto.id}" class="text-blue-600 hover:text-blue-900 transition-colors p-1" title="Ver detalles">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                </a>
                <div class="user-actions flex space-x-1" data-proyecto-id="${proyecto.id}" data-created-by="${proyecto.created_by || 'null'}">
                    <span class="text-gray-400 p-1" title="Cargando opciones...">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-500">Estado:</span>
                <span class="ml-1 px-2 py-1 text-xs rounded-full ${getEstadoClasses(proyecto.estado)}">
                    ${getEstadoLabel(proyecto.estado)}
                </span>
            </div>
            <div>
                <span class="text-gray-500">Monto:</span>
                <span class="ml-1 font-medium text-gray-900">$${formatNumber(proyecto.monto)}</span>
            </div>
            <div>
                <span class="text-gray-500">Responsable:</span>
                <span class="ml-1 text-gray-900">${proyecto.responsable}</span>
            </div>
            <div>
                <span class="text-gray-500">Fecha:</span>
                <span class="ml-1 text-gray-900">${formatDate(proyecto.fecha_inicio)}</span>
            </div>
            <div class="col-span-2">
                <span class="text-gray-500">Creado por:</span>
                <span class="ml-1 text-gray-900">${proyecto.creador ? proyecto.creador.name : 'N/A'}</span>
            </div>
        </div>
    `;
    
    return card;
}

function createDesktopRow(proyecto) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50 transition-colors';
    
    row.innerHTML = `
        <td class="px-3 py-2 text-sm">
            <div class="flex items-center space-x-2">
                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-medium text-blue-600">
                        ${proyecto.nombre.substring(0, 2).toUpperCase()}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900 truncate">${proyecto.nombre}</p>
                    <p class="text-xs text-gray-500">ID: ${proyecto.id}</p>
                </div>
            </div>
        </td>
        <td class="px-3 py-2 text-sm">
            <div class="space-y-1">
                <span class="inline-block px-2 py-1 text-xs rounded-full ${getEstadoClasses(proyecto.estado)}">
                    ${getEstadoLabel(proyecto.estado)}
                </span>
                <p class="font-medium text-gray-900">$${formatNumber(proyecto.monto)}</p>
            </div>
        </td>
        <td class="px-3 py-2 text-sm">
            <div class="space-y-1">
                <p class="text-gray-900">${proyecto.responsable}</p>
                <p class="text-gray-500">${formatDate(proyecto.fecha_inicio)}</p>
            </div>
        </td>
        <td class="px-3 py-2 text-sm text-gray-900">
            ${proyecto.creador ? proyecto.creador.name : 'N/A'}
        </td>
        <td class="px-3 py-2 text-sm">
            <div class="flex items-center space-x-2">
                <a href="/proyectos/${proyecto.id}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Ver detalles">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                </a>
                <div class="user-actions flex space-x-1" data-proyecto-id="${proyecto.id}" data-created-by="${proyecto.created_by || 'null'}">
                    <span class="text-gray-400" title="Cargando opciones...">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </div>
            </div>
        </td>
    `;
    
    return row;
}

function setupUserPermissions() {
    const currentUser = localStorage.getItem('user');
    
    document.querySelectorAll('.user-actions').forEach(actionsContainer => {
        const proyectoId = actionsContainer.dataset.proyectoId;
        const createdBy = actionsContainer.dataset.createdBy;
        
        actionsContainer.innerHTML = '';
        
        if (!currentUser) {
            actionsContainer.innerHTML = `
                <span class="text-gray-400" title="Inicia sesión para gestionar proyectos">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            `;
            return;
        }
        
        const userData = JSON.parse(currentUser);
        const userId = userData.id;
        
        if (createdBy === String(userId)) {
            actionsContainer.innerHTML = `
                <a href="/proyectos/${proyectoId}/edit" class="text-green-600 hover:text-green-900 transition-colors" title="Editar">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                </a>
                <button onclick="deleteProject(${proyectoId})" class="text-red-600 hover:text-red-900 transition-colors" title="Eliminar">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;
        } else {
            actionsContainer.innerHTML = `
                <span class="text-gray-400" title="No tienes permisos para modificar este proyecto">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            `;
        }
    });
}

async function deleteProject(proyectoId) {
    const proyecto = proyectos.find(p => p.id == proyectoId);
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
                showResultModal('success', 'Proyecto Eliminado', 'El proyecto ha sido eliminado exitosamente.', function() {
                    loadProyectos();
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

function showLoadingState() {
    document.getElementById('loading-state').classList.remove('hidden');
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('content-container').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');
}

function showEmptyState() {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('empty-state').classList.remove('hidden');
    document.getElementById('content-container').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');
}

function showContentState() {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('content-container').classList.remove('hidden');
    document.getElementById('error-state').classList.add('hidden');
}

function showErrorState(message) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('content-container').classList.add('hidden');
    document.getElementById('error-state').classList.remove('hidden');
    
    const errorEl = document.getElementById('error-message');
    if (errorEl) {
        errorEl.textContent = message;
    }
}
</script>
@endsection