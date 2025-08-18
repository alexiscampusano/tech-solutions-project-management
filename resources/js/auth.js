/**
 * JWT Authentication System
 * Handles login, logout, token verification and UI states
 */

let refreshInterval = null;

export async function initializeAuth() {
    const token = localStorage.getItem('authToken');
    const user = localStorage.getItem('user');
    
    if (token) {
        if (user) {
            try {
                const userData = JSON.parse(user);
                const userName = document.getElementById('user-name');
                if (userName && userData.name) {
                    userName.textContent = userData.name;
                }
            } catch (e) {
                console.error('Error parsing user data:', e);
            }
        }
        
        const isValid = await verifyTokenWithServer(token);
        
        if (isValid) {
            setupAutoRefresh();
        } else {
            clearAuthData();
            showGuestLinks();
        }
    } else {
        showGuestLinks();
    }
}

async function verifyTokenWithServer(token) {
    try {
        const response = await fetch('/api/auth/me', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                localStorage.setItem('user', JSON.stringify(data.data.user));
                showUserMenu(data.data.user);
                return true;
            }
        }
        return false;
    } catch (error) {
        console.error('Error verifying token:', error);
        return false;
    }
}

function setupAutoRefresh() {
    refreshInterval = setInterval(async () => {
        await refreshTokenIfNeeded();
    }, 50 * 60 * 1000);
}

async function refreshTokenIfNeeded() {
    const token = localStorage.getItem('authToken');
    
    if (!token) return;

    try {
        const response = await fetch('/api/auth/refresh', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                localStorage.setItem('authToken', data.data.token);
                console.log('Token refreshed successfully');
            }
        } else {
            console.warn('Token refresh failed, logging out');
            await handleLogout();
        }
    } catch (error) {
        console.error('Error refreshing token:', error);
        await handleLogout();
    }
}

function clearAuthData() {
    localStorage.removeItem('authToken');
    localStorage.removeItem('user');
    
    document.documentElement.style.removeProperty('--auth-initial-state');
    
    if (refreshInterval) {
        clearInterval(refreshInterval);
        refreshInterval = null;
    }
}

function showUserMenu(user) {
    const authLoading = document.getElementById('auth-loading');
    const guestLinks = document.getElementById('guest-links');
    const userMenu = document.getElementById('user-menu');
    const userName = document.getElementById('user-name');
    
    document.documentElement.style.setProperty('--auth-initial-state', 'user');
    
    if (authLoading) authLoading.classList.add('hidden');
    
    if (guestLinks) guestLinks.classList.add('hidden');
    
    if (userMenu) {
        userMenu.classList.remove('hidden');
        userMenu.classList.add('flex');
    }
    if (userName && user.name) {
        userName.textContent = user.name;
    }
}

function showGuestLinks() {
    const authLoading = document.getElementById('auth-loading');
    const guestLinks = document.getElementById('guest-links');
    const userMenu = document.getElementById('user-menu');
    
    document.documentElement.style.setProperty('--auth-initial-state', 'guest');
    
    if (authLoading) authLoading.classList.add('hidden');
    
    if (userMenu) {
        userMenu.classList.add('hidden');
        userMenu.classList.remove('flex');
    }
    
    if (guestLinks) {
        guestLinks.classList.remove('hidden');
        guestLinks.classList.add('flex');
    }
}

export async function handleLogout() {
    const token = localStorage.getItem('authToken');
    
    if (token) {
        try {
            await fetch('/api/auth/logout', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        } catch (error) {
            console.error('Logout error:', error);
        }
    }
    
    clearAuthData();
    
    showGuestLinks();
    
    if (window.location.pathname === '/proyectos' || window.location.pathname.startsWith('/proyectos/')) {
        window.location.href = '/login';
    }
}

export async function isAuthenticated() {
    const token = localStorage.getItem('authToken');
    if (!token) return false;
    
    return await verifyTokenWithServer(token);
}

export async function getUser() {
    const token = localStorage.getItem('authToken');
    if (!token) return null;
    
    try {
        const response = await fetch('/api/auth/me', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                return data.data.user;
            }
        }
        return null;
    } catch (error) {
        console.error('Error getting user:', error);
        return null;
    }
}

export function getToken() {
    return localStorage.getItem('authToken');
}

export async function forceRefreshToken() {
    return await refreshTokenIfNeeded();
}

export function setupAuthEventListeners() {
    document.addEventListener('DOMContentLoaded', async function() {
        await initializeAuth();

        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async function() {
                await handleLogout();
            });
        }
    });
}
