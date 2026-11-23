# BookLeaf — Technical Approach & Trade-offs
**Author:** Raju Kumar Raja  
**Submission date:** 23 November 2026  
**Project:** Author Support & Communication Portal  
**Demo:** https://bookleaf.freedev.app/  
**Repository:** https://github.com/RajukrRaja/bookleaf-portal

## Approach and priorities
I built the ticket workflow first: distinct author/admin roles, book and royalty visibility, author ticket submission, a prioritized admin queue, editable AI response drafts, private internal notes, status changes, and assignment. The supplied JSON dataset is imported by a repeatable Laravel seeder (10 authors and 18 books). Ticket and book access is scoped to the signed-in user. The admin queue emphasizes higher priority and older tickets.

## AI integration
The server assembles a compact BookLeaf policy summary and the current ticket plus the last six messages, then requests a structured classification, priority score, and response draft. The prompt explicitly includes the approved royalty, ISBN, printing, distribution, and production policies and asks for actionable, empathetic writing without fabricated dates or completed actions. Categories/priorities are validated against allow-lists. Only an admin can edit and send the proposed response. When the AI provider fails or does not return the expected format, the app uses a deterministic fallback so support tickets still work; fallback is a resilience mechanism, not equivalent to model intelligence. A configurable API endpoint and server-only environment key avoid exposing credentials. To limit cost, full histories are not sent to the provider.

## Architecture and trade-offs
Laravel 12 handles auth, validation, data access, server rendering, REST endpoints, and AI orchestration. MySQL stores authors, books, tickets, messages, and notes. A 2.5-second polling interval provides near-real-time ticket updates without depending on a long-lived websocket service on the demo host. The UI uses responsive server-rendered Blade templates and lightweight JavaScript: **this differs from the brief's preference for a modern JavaScript frontend framework**, and is a conscious delivery trade-off. The external AI endpoint is configurable, but real model output depends on provider availability and correct environment configuration.

## Production evolution
I would separate the API from a React/Vue client; use queued AI and email jobs; add Redis caching, supervised queue workers, Laravel Reverb/WebSockets, automated feature tests and authorization regression tests, audit logs, file scanning and attachment download authorization, rate limits, monitoring, secure secrets management, and provider cost/latency dashboards. The public demo uses illustrative credentials and sample data only; those credentials must be rotated before use with real author records.

## Known limitations
End-to-end browser and provider-failure tests require a configured runtime environment. Attachment storage exists, but there is no public file-download interface or antivirus scanning. This prototype uses polling instead of WebSockets; model classification and response quality depend on the configured external AI endpoint. See README for setup, credentials, API details, and additional operational notes.
