// Unregister any active Service Workers to prevent browser offline HTML caching
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(function(registrations) {
        for (let registration of registrations) {
            registration.unregister();
        }
    });
}

// Axios & Fetch default no-cache headers
if (window.axios) {
    window.axios.defaults.headers.common['Cache-Control'] = 'no-cache, no-store, must-revalidate';
    window.axios.defaults.headers.common['Pragma'] = 'no-cache';
    window.axios.defaults.headers.common['Expires'] = '0';
}

// Global cache-busting helper for forms and navigation
window.forceFreshReload = function() {
    window.location.href = window.location.pathname + '?v=' + Date.now();
};
