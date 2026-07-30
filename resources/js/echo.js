import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const host = window.location.hostname;
const port = parseInt(import.meta.env.VITE_REVERB_PORT ?? '8080', 10);

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: host,
    wsPort: port,
    wssPort: port,
    forceTLS: false,
    enabledTransports: ['ws'],
});
