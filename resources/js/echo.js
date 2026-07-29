import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const host = window.location.hostname;
const port = parseInt(import.meta.env.VITE_REVERB_PORT ?? '8888', 10);
const scheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: host,
    wsPort: port,
    wssPort: port,
    forceTLS: scheme === 'https',
    enabledTransports: ['ws', 'wss'],
});
