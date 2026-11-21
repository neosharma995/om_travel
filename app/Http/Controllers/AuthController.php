<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
        }

        $request->session()->regenerate();

        $user = $request->user();
        $intended = $request->session()->pull('url.intended');
        $intendedPath = is_string($intended) ? (parse_url($intended, PHP_URL_PATH) ?: '') : '';

        if ($user?->isAdmin()) {
            if ($intendedPath !== '' && str_starts_with($intendedPath, '/admin')) {
                return redirect()->to($intended);
            }

            return redirect()->route('admin.dashboard');
        }

        if ($intendedPath !== '' && ! str_starts_with($intendedPath, '/admin')) {
            return redirect()->to($intended);
        }

        return redirect()->route('customer.dashboard');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Welcome to OmTravel! Your account is ready.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'You have been signed out.');
    }
}
