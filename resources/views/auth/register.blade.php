<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create author account · BookLeaf</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
    <main class="login-card">
        <a class="brand login-brand" href="{{ route('login') }}">
            <span class="brand-mark">B</span>
            <span>bookleaf<span class="brand-sub">PUBLISHING PORTAL</span></span>
        </a>
        <h1>Create author account</h1>
        <form method="post" action="{{ route('register.store') }}" class="form-stack">
            @csrf
            <label>
                Full name
                <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="255" required autofocus>
            </label>
            @error('name')
                <p class="alert error" role="alert">{{ $message }}</p>
            @enderror
            <label>
                Email address
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required>
            </label>
            @error('email')
                <p class="alert error" role="alert">{{ $message }}</p>
            @enderror
            <label>
                Password
                <input type="password" name="password" autocomplete="new-password" minlength="8" placeholder="At least 8 characters" required>
            </label>
            <label>
                Confirm password
                <input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required>
            </label>
            @error('password')
                <p class="alert error" role="alert">{{ $message }}</p>
            @enderror
            <button type="submit" class="btn primary full">Create author account</button>
        </form>
        <p class="muted">Already registered? <a class="text-link" href="{{ route('login') }}">Sign in</a></p>
    </main>
</body>
</html>
