// public/js/app.js

const basePath = '/bookbox';

function getToken() {
    return localStorage.getItem('token');
}

function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = basePath + '/';
}

async function apiRequest(endpoint, options = {}) {
    const url = basePath + endpoint;
    const headers = {
        'Content-Type': 'application/json',
        ...options.headers
    };
    
    const token = getToken();
    if (token) {
        headers['Authorization'] = 'Bearer ' + token;
    }
    
    const response = await fetch(url, {
        ...options,
        headers
    });
    
    if (response.status === 401) {
        logout();
        return null;
    }
    
    return response;
}