import './bootstrap';
import Alpine from 'alpinejs';

// Inicializar Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Utilities globales
window.formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB'
    }).format(amount);
};

window.formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO');
};

// API helpers
window.api = {
    get: async (url, options = {}) => {
        return fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || 'dummy'}`,
                ...options.headers
            },
            ...options
        });
    },
    
    post: async (url, data = {}, options = {}) => {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || 'dummy'}`,
                ...options.headers
            },
            body: JSON.stringify(data),
            ...options
        });
    }
};

console.log('🚀 Sistema RRHH YPFB-Andina inicializado');