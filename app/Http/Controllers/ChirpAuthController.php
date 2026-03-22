<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChirpAuthController extends Controller
{
    // Note: instead of having 3 methods here,
    // 3 separate invokable controllers would be a better approach.
    // This is an example, so we'll keep it simple.

    /**
     * Login
     */
    public function login(Request $request)
    {
        // Validate the input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log in
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session for security
            $request->session()->regenerate();

            // Redirect to the intended page, or default homepage
            return redirect()
                ->intended(route('chirps.index'))
                ->with('success', 'Welcome back!');
        }

        // If login fails, redirect back with an error
        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('chirps.index'))
            ->with('success', 'You have been logged out.');
    }

    /**
     * Register
     */
    public function register(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Log them in
        Auth::login($user);

        // Redirect to the Chirps feed page
        return redirect(route('chirps.index'))
            ->with('success', 'Welcome to Chirper!');
    }
}
