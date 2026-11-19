<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>@yield('title','BookLeaf Portal')</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body>
        <div class="shell">
            <aside class="sidebar">
                <a class="brand" href="{{ auth()->user()->role==='admin'?route('admin.dashboard'):route('author.dashboard') }}">
                    <span class="brand-mark">B</span>
                    <span>
                        bookleaf
                        <span class="brand-sub">PUBLISHING PORTAL</span>
                    </span>
                </a>
                <div class="nav-label">WORKSPACE</div>
                @if(auth()->user()->role==='admin')
                    <a class="nav-item {{ request()->is('admin')?'active':'' }}" href="{{ route('admin.dashboard') }}">
                        ▦
                        <span>Ticket queue</span>
                    </a>
                @else
                    <a class="nav-item {{ request()->is('author')?'active':'' }}" href="{{ route('author.dashboard') }}">
                        ▦
                        <span>Overview</span>
                    </a>
                    <a class="nav-item {{ request()->is('author/books')?'active':'' }}" href="{{ route('author.books') }}">
                        ▤
                        <span>My books</span>
                    </a>
                    <a class="nav-item {{ request()->is('author/tickets*')?'active':'' }}" href="{{ route('author.tickets') }}">
                        ◷
                        <span>My tickets</span>
                    </a>
                    <a class="nav-item" href="{{ route('author.tickets.create') }}">
                        ＋
                        <span>Raise a ticket</span>
                    </a>
                @endif
                <div class="sidebar-bottom">
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                    </div>
                    <div class="profile">
                        <b>{{ auth()->user()->name }}</b>
                        <small>
                            {{ ucfirst(auth()->user()->role) }}
                            account
                        </small>
                    </div>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout" title="Sign out">↗</button>
                    </form>
                </div>
            </aside>
            <main class="main">
                <header class="topbar">
                    <div>
                        <span class="crumb">BOOKLEAF /</span>
                        @yield('section','Overview')
                    </div>
                    <div class="top-actions">
                        <span class="online-dot">
                        </span>
                        <span>Support workspace</span>
                    </div>
                </header>
                <section class="content">
                    @if(session('success'))
                        <div class="alert success">{{ session('success') }}</div>
                    @endif
                    @if(session('mail_warning'))
                        <div class="alert error" role="alert">{{ session('mail_warning') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert error">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    @yield('content')
                </section>
            </main>
        </div>
        <script>
            document.querySelectorAll('[data-confirm]').forEach(element => {
                element.addEventListener('click', event => {
                    if (!confirm(element.dataset.confirm)) event.preventDefault();
                });
            });
        </script>
        @stack('scripts')
    </body>
</html>
