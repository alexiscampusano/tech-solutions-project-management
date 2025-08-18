/**
 * UF Component - Displays the current UF value
 * Reusable component that consumes the UF API
 */
class UfComponent {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.options = {
            showDate: true,
            autoRefresh: true,
            refreshInterval: 300000,
            showConverter: false,
            apiBaseUrl: '/api/uf',
            ...options
        };
        
        this.data = null;
        this.refreshTimer = null;
        
        if (!this.container) {
            console.error(`UF Component: No se encontró el contenedor con ID: ${containerId}`);
            return;
        }
        
        this.init();
    }

    async init() {
        this.render();
        await this.loadUfValue();
        
        if (this.options.autoRefresh) {
            this.startAutoRefresh();
        }
    }

    /**
     * Load the current UF value from the API
     */
    async loadUfValue() {
        try {
            this.setLoading(true);
            
            const response = await fetch(this.options.apiBaseUrl);
            const result = await response.json();
            
            if (result.success && result.data) {
                this.data = result.data;
                this.render();
            } else {
                this.showError('No se pudo cargar el valor de la UF');
            }
        } catch (error) {
            console.error('Error cargando UF:', error);
            this.showError('Error de conexión al cargar UF');
        } finally {
            this.setLoading(false);
        }
    }

    /**
     * Convert pesos to UF
     */
    async convertirPesosAUf(monto) {
        try {
            const response = await fetch(`${this.options.apiBaseUrl}/convert`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ monto })
            });
            
            const result = await response.json();
            return result;
        } catch (error) {
            console.error('Error convirtiendo a UF:', error);
            return { success: false, error: 'Error de conexión' };
        }
    }

    /**
     * Render the component
     */
    render() {
        if (!this.container) return;

        const html = `
            <div x-data="{ open: false }" class="uf-widget">
                <!-- UF Value Display (Always Visible) -->
                <div @click="open = !open" class="flex items-center space-x-2 cursor-pointer bg-white border border-gray-200 rounded-lg p-2 shadow-sm hover:bg-gray-50 transition">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div id="uf-value" class="text-sm font-bold text-blue-600">
                            ${this.getValueDisplay()}
                        </div>
                    </div>
                    <div id="uf-loading-spinner" class="hidden">
                         <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Dropdown Panel (Converter and Details) -->
                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-72 origin-top-right bg-white border border-gray-200 rounded-md shadow-lg z-10"
                     style="display: none;">
                    
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-2">
                             <div>
                                <p class="text-sm font-medium text-gray-900">Valor de la UF</p>
                                ${this.options.showDate ? `
                                    <div id="uf-date" class="text-xs text-gray-500">
                                        ${this.getDateDisplay()}
                                    </div>
                                ` : ''}
                            </div>
                            <button id="uf-refresh" class="p-1 text-gray-400 hover:text-gray-600 transition-colors" title="Actualizar">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div id="uf-error" class="hidden my-2 text-xs text-red-600"></div>

                        ${this.options.showConverter ? this.renderConverter() : ''}
                    </div>
                </div>
            </div>
        `;

        this.container.innerHTML = html;
        this.attachEventListeners();
    }

    /**
     * Render the UF converter
     */
    renderConverter() {
        return `
            <div id="uf-converter-panel" class="mt-4 pt-4 border-t border-gray-200">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Convertir Pesos a UF</label>
                    <div class="flex space-x-2">
                        <input id="uf-converter-input" type="number" placeholder="Monto en pesos" 
                               class="flex-1 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <button id="uf-converter-btn" 
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Convertir
                        </button>
                    </div>
                    <div id="uf-converter-result" class="text-xs text-gray-600"></div>
                </div>
            </div>
        `;
    }

    /**
     * Get the value display
     */
    getValueDisplay() {
        if (!this.data) return '---';
        if (this.data.status === 'error') return 'N/A';
        return this.data.valor_formateado || '---';
    }

    /**
     * Get the date display
     */
    getDateDisplay() {
        if (!this.data || !this.data.fecha_formateada) return '---';
        return this.data.fecha_formateada;
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        const refreshBtn = document.getElementById('uf-refresh');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.loadUfValue());
        }

        const converterBtn = document.getElementById('uf-converter');
        if (converterBtn) {
            converterBtn.addEventListener('click', () => this.toggleConverter());
        }

        const convertBtn = document.getElementById('uf-converter-btn');
        if (convertBtn) {
            convertBtn.addEventListener('click', () => this.handleConversion());
        }

        const converterInput = document.getElementById('uf-converter-input');
        if (converterInput) {
            converterInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.handleConversion();
                }
            });
        }
    }


    /**
     * Handle conversion
     */
    async handleConversion() {
        const input = document.getElementById('uf-converter-input');
        const result = document.getElementById('uf-converter-result');
        
        if (!input || !result) return;

        const monto = parseFloat(input.value);
        if (isNaN(monto) || monto <= 0) {
            result.textContent = 'Ingresa un monto válido';
            result.className = 'text-xs text-red-600';
            return;
        }

        result.textContent = 'Convirtiendo...';
        result.className = 'text-xs text-gray-500';

        const conversion = await this.convertirPesosAUf(monto);
        
        if (conversion.success && conversion.data) {
            result.innerHTML = `
                <strong>${conversion.data.monto_pesos_formateado}</strong> = 
                <strong class="text-blue-600">${conversion.data.monto_uf_formateado}</strong>
            `;
            result.className = 'text-xs text-gray-700';
        } else {
            result.textContent = 'Error en la conversión';
            result.className = 'text-xs text-red-600';
        }
    }

    /**
     * Show loading state
     */
    setLoading(loading) {
        const loadingEl = document.getElementById('uf-loading-spinner');
        if (loadingEl) {
            loadingEl.classList.toggle('hidden', !loading);
        }
    }

    /**
     * Show error
     */
    showError(message) {
        const errorEl = document.getElementById('uf-error');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
            
            setTimeout(() => {
                errorEl.classList.add('hidden');
            }, 5000);
        }
    }

    /**
     * Start automatic refresh
     */
    startAutoRefresh() {
        this.refreshTimer = setInterval(() => {
            this.loadUfValue();
        }, this.options.refreshInterval);
    }

    /**
     * Stop automatic refresh
     */
    stopAutoRefresh() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
            this.refreshTimer = null;
        }
    }

    /**
     * Destroy the component
     */
    destroy() {
        this.stopAutoRefresh();
        if (this.container) {
            this.container.innerHTML = '';
        }
    }
}

window.UfComponent = UfComponent;

document.addEventListener('DOMContentLoaded', function() {
    const ufContainer = document.getElementById('uf-component');
    if (ufContainer) {
        setTimeout(() => {
            window.ufWidget = new UfComponent('uf-component', {
                showDate: true,
                autoRefresh: true,
                showConverter: true
            });
        }, 0);
    }
}); 