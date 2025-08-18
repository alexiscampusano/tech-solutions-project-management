import './bootstrap';
import './uf-component.js';

import { setupAuthEventListeners, initializeAuth, handleLogout, isAuthenticated, getUser, getToken, forceRefreshToken } from './auth.js';

import { setupModalEventListeners, showDeleteModal, hideDeleteModal, showResultModal } from './modals.js';

setupAuthEventListeners();
setupModalEventListeners();

window.initializeAuth = initializeAuth;
window.handleLogout = handleLogout;
window.isAuthenticated = isAuthenticated;
window.getUser = getUser;
window.getToken = getToken;
window.forceRefreshToken = forceRefreshToken;
window.showDeleteModal = showDeleteModal;
window.hideDeleteModal = hideDeleteModal;
window.showResultModal = showResultModal;
