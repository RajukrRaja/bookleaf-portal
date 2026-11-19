<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Sign in · BookLeaf</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body class="login-page">
        <div class="login-card">
            <a class="brand login-brand">
                <span class="brand-mark">B</span>
                <span>
                    bookleaf
                    <span class="brand-sub">PUBLISHING PORTAL</span>
                </span>
            </a>
            <div class="eyebrow">WELCOME BACK</div>
            <h1>
                Sign in to your
                <br>
                workspace.
            </h1>
            <p class="muted">Manage your books and get the support you need.</p>
            <form method="post" action="{{ route('login.post') }}" class="form-stack">
                @csrf
                <label>
                    Email address
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                </label>
                <label>
                    Password
                    <input type="password" name="password" placeholder="Enter your password" required>
                </label>
                <button class="btn primary full">
                    Sign in
                    <span>→</span>
                </button>
            </form>
            @if($errors->any())
                <div class="alert error">
                    {{ $errors->first() }}
                </div>
            @endif
            <p class="muted">New to BookLeaf? <a class="text-link" href="{{ route('register') }}">Create author account</a></p>
            <div class="demo-box">
                <b>Demo access</b>
                <span>Author: author@gmail.com</span>
                <span>Admin: admin@gmail.com</span>
                <span>Password: Raju.45@</span>
            </div>
            <div class="login-foot">
                ©
                {{ date('Y') }}
                BookLeaf Publishing
            </div>
        </div>
    </body>
</html>
