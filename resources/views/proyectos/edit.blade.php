@extends('layouts.app')

@section('title', 'Editar Proyecto')

@section('header', 'Editar Proyecto')

@section('description', 'Modifica la información del proyecto "{{ $proyecto->nombre }}"')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
            <h3 class="text-lg font-medium text-yellow-900">Editar Proyecto</h3>
            <p class="mt-1 text-sm text-yellow-700">
                Actualiza los campos que desees modificar.
            </p>
        </div>

        <form action="{{ route('proyectos.update', $proyecto) }}" method="POST" class="px-6 py-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Nombre del Proyecto -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Proyecto *
                </label>
                <input type="text" 
                       name="nombre" 
                       id="nombre" 
                       value="{{ old('nombre', $proyecto->nombre) }}"
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm
                              @error('nombre') border-red-300 @enderror"
                       placeholder="Ej: Sistema de Facturación Electrónica"
                       required>
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fecha de Inicio -->
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Inicio *
                </label>
                <input type="date" 
                       name="fecha_inicio" 
                       id="fecha_inicio" 
                       value="{{ old('fecha_inicio', $proyecto->fecha_inicio->format('Y-m-d')) }}"
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm
                              @error('fecha_inicio') border-red-300 @enderror"
                       required>
                @error('fecha_inicio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado -->
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                    Estado del Proyecto *
                </label>
                <select name="estado" 
                        id="estado" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm
                               @error('estado') border-red-300 @enderror"
                        required>
                    <option value="">Selecciona un estado</option>
                    @foreach($estados as $valor => $etiqueta)
                        <option value="{{ $valor }}" 
                                {{ old('estado', $proyecto->estado) === $valor ? 'selected' : '' }}>
                            {{ $etiqueta }}
                        </option>
                    @endforeach
                </select>
                @error('estado')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Información del Estado Actual -->
                <div class="mt-2 p-3 bg-gray-50 rounded-md">
                    <p class="text-xs text-gray-600">
                        <span class="font-medium">Estado actual:</span>
                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full ml-1
                            @switch($proyecto->estado)
                                @case('planificacion')
                                    bg-yellow-100 text-yellow-800
                                    @break
                                @case('en_progreso')
                                    bg-blue-100 text-blue-800
                                    @break
                                @case('completado')
                                    bg-green-100 text-green-800
                                    @break
                                @case('pausado')
                                    bg-gray-100 text-gray-800
                                    @break
                                @case('cancelado')
                                    bg-red-100 text-red-800
                                    @break
                            @endswitch">
                            {{ $proyecto->estado_formateado }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Responsable -->
            <div>
                <label for="responsable" class="block text-sm font-medium text-gray-700 mb-2">
                    Responsable del Proyecto *
                </label>
                <input type="text" 
                       name="responsable" 
                       id="responsable" 
                       value="{{ old('responsable', $proyecto->responsable) }}"
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm
                              @error('responsable') border-red-300 @enderror"
                       placeholder="Ej: Juan Pérez"
                       required>
                @error('responsable')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Monto -->
            <div>
                <label for="monto" class="block text-sm font-medium text-gray-700 mb-2">
                    Monto del Proyecto (CLP) *
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" 
                           name="monto" 
                           id="monto" 
                           value="{{ old('monto', $proyecto->monto) }}"
                           min="0"
                           max="999999999999.99"
                           step="0.01"
                           class="block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm
                                  @error('monto') border-red-300 @enderror"
                           placeholder="0"
                           required>
                </div>
                <div class="mt-2 p-3 bg-gray-50 rounded-md">
                    <p class="text-xs text-gray-600">
                        <span class="font-medium">Monto actual:</span> {{ $proyecto->monto_formateado }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Ingresa el nuevo monto sin puntos ni comas. Ejemplo: 1500000 para $1.500.000
                    </p>
                </div>
                @error('monto')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información del Proyecto -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <h4 class="text-sm font-medium text-blue-900 mb-2">Información del Proyecto</h4>
                <div class="text-sm text-blue-800">
                    <p><span class="font-medium">ID:</span> {{ $proyecto->id }}</p>
                    <p><span class="font-medium">Creado:</span> {{ $proyecto->created_at->format('d/m/Y H:i') }}</p>
                    <p><span class="font-medium">Última actualización:</span> {{ $proyecto->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-between pt-6 border-t border-gray-200">
                <div class="flex space-x-3">
                    <a href="{{ route('proyectos.show', $proyecto) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                        Ver Detalles
                    </a>
                    <a href="{{ route('proyectos.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Cancelar
                    </a>
                </div>
                
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Actualizar Proyecto
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Formatear el input de monto mientras se escribe
    document.getElementById('monto').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^\d.]/g, '');
        if (value.split('.').length > 2) {
            value = value.substring(0, value.lastIndexOf('.'));
        }
        e.target.value = value;
    });
</script>
@endpush
@endsection 