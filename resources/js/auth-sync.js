    /**
     * Quick synchronous authentication verification
     * This script must be executed inline in the <head> to avoid flash
     */

(function() {
    const token = localStorage.getItem('authToken');
    const user = localStorage.getItem('user');
    
    if (token && user) {
        document.documentElement.style.setProperty('--auth-initial-state', 'user');
    } else {
        document.documentElement.style.setProperty('--auth-initial-state', 'guest');
    }
})();
