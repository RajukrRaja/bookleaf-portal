const conversation = document.querySelector('[data-refresh-url]');
const statusBadge = document.querySelector('.backline .pill');
let latestId = Number(conversation.lastElementChild?.dataset.messageId || 0);

async function updateConversation() {
    try {
        const url = new URL(conversation.dataset.refreshUrl, window.location.href);
        url.searchParams.set('after', latestId);
        const response = await fetch(url, {
            headers: { Accept: 'application/json' },
            cache: 'no-store',
        });
        if (!response.ok) return;
        const ticket = await response.json();
        statusBadge.textContent = ticket.status;

        for (const message of ticket.messages) {
            if (Number(message.id) <= latestId) continue;
            const bubble = document.createElement('div');
            bubble.className = 'message-bubble ' + (Number(message.is_admin) ? 'admin-bubble' : 'author-bubble');
            bubble.dataset.messageId = message.id;

            const sender = document.createElement('b');
            sender.textContent = message.user.name + ' ';
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
    } catch {
    } finally {
        window.setTimeout(updateConversation, 2500);
    }
}

updateConversation();
