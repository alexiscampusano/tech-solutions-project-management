/**
 * Modal System
 * Handles confirmation modals for deletion and results
 */

let currentDeleteProjectId = null;
let currentDeleteCallback = null;

export function showDeleteModal(projectId, projectName, onConfirm) {
    currentDeleteProjectId = projectId;
    currentDeleteCallback = onConfirm;
    
    const modal = document.getElementById('delete-modal');
    const messageElement = document.getElementById('modal-message');
    
    if (projectName) {
        messageElement.textContent = `¿Estás seguro de que deseas eliminar el proyecto "${projectName}"? Esta acción no se puede deshacer.`;
    } else {
        messageElement.textContent = '¿Estás seguro de que deseas eliminar este proyecto? Esta acción no se puede deshacer.';
    }
    
    modal.classList.remove('hidden');
}

export function hideDeleteModal() {
    const modal = document.getElementById('delete-modal');
    modal.classList.add('hidden');
    currentDeleteProjectId = null;
    currentDeleteCallback = null;
}

export function showResultModal(type, title, message, onOk = null) {
    const modal = document.getElementById('result-modal');
    const icon = document.getElementById('result-icon');
    const titleEl = document.getElementById('result-modal-title');
    const messageEl = document.getElementById('result-modal-message');
    
    if (type === 'success') {
        icon.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10';
        icon.innerHTML = `
            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        `;
    } else {
        icon.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10';
        icon.innerHTML = `
            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        `;
    }
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    const okBtn = document.getElementById('result-ok-btn');
    okBtn.onclick = () => {
        modal.classList.add('hidden');
        if (onOk) onOk();
    };
    
    modal.classList.remove('hidden');
}

export function setupModalEventListeners() {
    document.addEventListener('DOMContentLoaded', function() {
        const confirmBtn = document.getElementById('confirm-delete-btn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                if (currentDeleteCallback) {
                    currentDeleteCallback(currentDeleteProjectId);
                }
                hideDeleteModal();
            });
        }
        
        const cancelBtn = document.getElementById('cancel-delete-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', hideDeleteModal);
        }
        
        const deleteModal = document.getElementById('delete-modal');
        if (deleteModal) {
            deleteModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideDeleteModal();
                }
            });
        }
        
        const resultModal = document.getElementById('result-modal');
        if (resultModal) {
            resultModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideDeleteModal();
                const resultModal = document.getElementById('result-modal');
                if (resultModal) {
                    resultModal.classList.add('hidden');
                }
            }
        });
    });
}
