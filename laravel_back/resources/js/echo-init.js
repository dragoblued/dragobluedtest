import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');


window.Echo = new Echo({
   broadcaster: 'pusher',
   key: process.env.MIX_PUSHER_APP_KEY,
   cluster: process.env.MIX_PUSHER_APP_CLUSTER,
   wsHost: window.location.hostname,
   wsPort: 6101,
   forceTLS: !!process.env.MIX_PUSHER_APP_ENCRYPTED,
   encrypted: !!process.env.MIX_PUSHER_APP_ENCRYPTED,
   enabledTransports: ['ws', 'wss'],
   disabledTransports: ['sockjs', 'xhr_polling', 'xhr_streaming'],
   disableStats: true,
});
