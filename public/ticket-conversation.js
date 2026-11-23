// Pusher Channels private ticket events; no periodic polling.
(() => {
    const conversation = document.querySelector('[data-refresh-url]');
    if (!conversation) return;
    const statusBadge = document.querySelector('.backline .pill');
    const lastMessage = [...conversation.querySelectorAll('[data-message-id]')].at(-1);
    let latestId = Number(lastMessage?.dataset.messageId || 0);
    let refreshing = false;

    async function refreshConversation() {
        if (refreshing) return;
        refreshing = true;
        try {
            const url = new URL(conversation.dataset.refreshUrl, window.location.href);
            url.searchParams.set('after', String(latestId));
            const response = await fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' }, cache: 'no-store' });
            if (!response.ok) return;
            const ticket = await response.json();
            if (statusBadge && ticket.status) statusBadge.textContent = ticket.status;
            for (const message of ticket.messages || []) {
                if (Number(message.id) <= latestId) continue;
                const bubble = document.createElement('div');
                bubble.className = 'message-bubble ' + (Number(message.is_admin) ? 'admin-bubble' : 'author-bubble');
                bubble.dataset.messageId = message.id;
                const sender = document.createElement('b');
                sender.textContent = (message.user?.name || 'User') + ' ';
                const time = document.createElement('small');
                time.textContent = new Date(message.created_at).toLocaleString();
                sender.append(time);
                const body = document.createElement('p');
                body.textContent = message.message;
                bubble.append(sender, body);
                conversation.querySelector('[data-empty]')?.remove();
                conversation.append(bubble);
                latestId = Number(message.id);
            }
        } catch (error) {
            console.warn('BookLeaf refresh failed', error);
        } finally {
            refreshing = false;
        }
    }

    const key = conversation.dataset.pusherKey;
    const cluster = conversation.dataset.pusherCluster;
    const ticketId = conversation.dataset.ticketId;
    if (!key || !ticketId || !window.Pusher) {
        console.warn('BookLeaf real-time updates require Pusher app key and client SDK.');
        return;
    }
    const pusher = new window.Pusher(key, {
        cluster: cluster || 'ap2',
        forceTLS: true,
        channelAuthorization: {
            endpoint: '/broadcasting/auth',
            transport: 'ajax',
            headers: { 'X-CSRF-TOKEN': conversation.dataset.csrfToken || '' },
        },
    });
    const channel = pusher.subscribe('private-tickets.' + ticketId);
    channel.bind('ticket.updated', refreshConversation);
    pusher.connection.bind('connected', refreshConversation); // reconcile missed events on reconnect
    channel.bind('pusher:subscription_error', error => console.warn('BookLeaf Pusher authorization failed', error));
    window.addEventListener('beforeunload', () => pusher.disconnect(), { once: true });
})();
