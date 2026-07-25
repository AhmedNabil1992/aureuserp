import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const host = window.location.hostname;
const port = parseInt(import.meta.env.VITE_REVERB_PORT ?? '8888', 10);
const scheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';

const isIpOrLocal = /^(?:\d{1,3}\.){3}\d{1,3}$/.test(host) || host === 'localhost' || host === '127.0.0.1';
const useTls = window.location.protocol === 'https:' && !isIpOrLocal && scheme === 'https';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: host,
    wsPort: port,
    wssPort: useTls ? (window.location.port || 443) : port,
    forceTLS: useTls,
    enabledTransports: ['ws', 'wss'],
});
