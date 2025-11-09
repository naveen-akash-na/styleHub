function getCSRFToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
}

function fetchWithCSRF(url, options = {}) {
    const token = getCSRFToken();
    
    options.headers = options.headers || {};
    if (token) {
        options.headers['X-CSRF-Token'] = token;
    }
    
    return fetch(url, options);
}

window.fetchWithCSRF = fetchWithCSRF;
