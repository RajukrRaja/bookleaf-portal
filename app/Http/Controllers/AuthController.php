<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => strtolower(trim($request->input('email')))]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'not_in:author@gmail.com,admin@gmail.com', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.not_in' => 'This email is reserved for a demo account. Please use your own email address.',
        ]);

        if (strlen($data['password']) > 72) {
            throw ValidationException::withMessages(['password' => 'Use a password of 72 bytes or fewer.']);
        }

        try {
            $author = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'author',
                'joined_date' => today(),
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['email' => 'This email is already registered.']);
        }

        Auth::login($author);
        $request->session()->regenerate();

        return redirect()->route('author.dashboard')->with('success', 'Your author account is ready.');
    }

    public function login(Request $r)
    {
        $credentials = $r->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (($credentials['email'] ?? '') === 'author@gmail.com') $credentials['email'] = 'priya.sharma@email.com';
        if (!Auth::attempt($credentials, $r->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.'
            ])->onlyInput('email');
        }
        $r->session()->regenerate();
        return redirect()->intended(Auth::user()->role === 'admin' ? route('admin.dashboard'): route('author.dashboard'));
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
}
