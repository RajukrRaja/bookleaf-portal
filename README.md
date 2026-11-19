# BookLeaf Author Support & Communication Portal

Laravel 12 + MySQL application for the BookLeaf four-day full-stack assignment. It includes author and admin workspaces, book/royalty views, ticket lifecycle, AI classification and response drafting, internal notes, assignment, filters and near-real-time ticket refresh.

## Stack
- Laravel 12 / PHP 8.2+
- MySQL 8 (InnoDB, utf8mb4)
- Blade responsive UI
- AI endpoint configured using `AI_CHAT_API_URL`; requests run server-side only
- 2.5-second polling on ticket detail pages for near-real-time updates

## Setup
1. Install PHP 8.2+, Composer, MySQL 8 and PHP extensions `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`, `tokenizer`, `xml`.
2. Create a MySQL database named `bookleaf` (or update `.env`).
3. Run `composer install`.
4. Copy `.env.example` to `.env`, configure DB credentials and AI settings.
5. Run `php artisan key:generate`.
6. Run `php artisan migrate --seed`.
7. Run `php artisan serve` and open `http://127.0.0.1:8000`.

## Demo credentials
- Admin: `admin@gmail.com` / `Raju.45@`
- Author: `author@gmail.com` / `Raju.45@`
- All 10 source authors are seeded with their source email and the demo password. `author@gmail.com` is a login alias for the first supplied author account (Priya Sharma), so the exact 10-author / 18-book dataset remains unchanged.

## AI integration
When a ticket is created, the backend sends its subject, description, selected book title and concise BookLeaf policy context to the configured AI endpoint. Expected JSON fields: `category`, `priority`, `priority_score`, `reply`. The parser also accepts OpenAI-style `choices[0].message.content` and a nested `data` object. Category and priority are allow-listed. The generated reply is a draft only; an admin must review/edit and explicitly send it. If the endpoint is unavailable, times out, rate-limits, or returns an unrecognized payload, a deterministic local fallback classifies common keywords and supplies a ticket-specific draft in the selected tone; ticket creation is not blocked.

`AI_CHAT_API_KEY` is optional and sent as a Bearer token only if configured. Never place secrets in frontend code or commit `.env`. The Knowledge Base is compact and sent per ticket rather than sending historical conversations or the entire database. For production, cache versioned policy context and log token usage if the provider exposes it.

Admins can regenerate a draft in Professional, Empathetic or Concise tone using the latest conversation. Approve & send publishes the edited text. Open ticket pages fetch new replies without interrupting typing. Existing tickets support regeneration without a migration.

## API
Session-authenticated API routes are defined in `routes/api.php`:
- `GET /api/tickets` — admins see all tickets; authors see only their own.
- `POST /api/tickets/{ticket}/reply` — add a reply after authorization.
- `GET /api/tickets/{ticket}/refresh` — ticket status and conversation refresh.

The browser-facing web workflow uses CSRF-protected routes in `routes/web.php`. The API uses Laravel's `auth:sanctum` guard; install Sanctum and publish its migration/config if enabling token-based API clients: `composer require laravel/sanctum && php artisan install:api && php artisan migrate`. For session-based web UI no API token is needed.

## Data
`database/data/bookleaf_sample_data.json` is the original supplied sample dataset. `DatabaseSeeder` imports all 10 authors and 18 books and creates the demo admin/author accounts. Each source author can sign in using their supplied email and password `Raju.45@`.

## Notes / limitations
- Attachment selection and server-side private storage are implemented; file download UI and antivirus scanning are not included.
- Ticket updates use 2.5-second polling. A production deployment can replace this with Laravel Reverb/Echo.
- Add Laravel Sanctum before exposing the API to external bearer-token clients.
- Configure HTTPS, mail delivery, rate limiting, backups, monitoring, queue workers and audit logging before production use.
- The Figma community file was not accessible to the build environment, so the interface uses a clean library-inspired editorial dashboard with BookLeaf green styling rather than claiming pixel-perfect reproduction.


## Email notifications

The portal sends email notifications to the ticket author and all users with the
`admin` role for these events:

- A new ticket is created.
- An author or admin sends a conversation reply (including the admin AI-draft send action).
- Ticket status, priority, or category changes (only when the value actually changes).
- A ticket is assigned to an admin.

Internal admin notes are intentionally private and never trigger email notifications.

### Gmail SMTP setup

Edit the mail settings in the existing server `.env`. Use a fresh Gmail App Password for the sending account and enter its 16 characters without spaces.

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=rajukumar.191813@gmail.com
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=rajukumar.191813@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
BOOKLEAF_LOCAL_TEST_EMAIL=rajukumar.191813@gmail.com
```

With APP_ENV=local, BOOKLEAF_LOCAL_TEST_EMAIL receives one email per ticket event. Clear this setting to test delivery to the actual ticket author and admins. Other environments always use the stored author and admin email addresses. The demo login alias is separate from the author's stored email address.

After setting the password, run:

```bash
php artisan config:clear
php artisan bookleaf:mail-test rajukumar.191813@gmail.com
```

The command reports whether the SMTP server accepted the message. Check the inbox and spam folder, then create or reply to a ticket to test the notification workflow. Web actions show a warning if sending fails. Details are recorded in storage/logs/laravel.log.
