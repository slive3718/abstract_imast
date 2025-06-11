
// Session expiration time from server (in milliseconds)
let sessionExpireTime = 7200 * 1000; // 30 minutes

// Start countdown on page load
setTimeout(function () {
    location.reload(); // Reload page after session expiration
}, sessionExpireTime);
