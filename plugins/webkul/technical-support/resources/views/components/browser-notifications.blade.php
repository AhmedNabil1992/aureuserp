<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Request desktop notification permission on first user interaction
        if ('Notification' in window && Notification.permission === 'default') {
            const requestNotificationPermission = () => {
                Notification.requestPermission();
                document.removeEventListener('click', requestNotificationPermission);
            };
            document.addEventListener('click', requestNotificationPermission, { once: true });
        }

        const playChime = () => {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.3);
            } catch (e) {}
        };

        const showDesktopNotification = (title, body) => {
            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    const notif = new Notification(title || 'إشعار جديد', {
                        body: body || 'وصلك إشعار جديد في النظام',
                        icon: '/images/favicon.ico',
                        badge: '/images/favicon.ico',
                    });

                    notif.onclick = function () {
                        window.focus();
                        this.close();
                    };
                } catch (e) {}
            }
        };

        // Listen for Filament in-app notifications
        window.addEventListener('filament-notification-sent', (event) => {
            const detail = event.detail || {};
            playChime();
            if (document.hidden) {
                showDesktopNotification(detail.title || 'إشعار جديد', detail.body || '');
            }
        });

        // Listen for Echo notification broadcasts if user channel is active
        if (window.Echo) {
            window.Echo.connector?.pusher?.bind('filament-notification', (data) => {
                playChime();
                showDesktopNotification(data.title || 'إشعار جديد', data.body || '');
            });
        }
    });
</script>
