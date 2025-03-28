<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register', [
            'pageTitle' => 'Register Account'
        ]);
    }

    /**
     * Handle user registration
     */
    // backend/app/Http/Controllers/AuthController.php
public function register(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:8',
    ]);

    \Log::debug('Attempting to create user', $validated);

    try {
        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'name' => 'User_'.Str::random(5),
        ]);

        \Log::info('User created successfully', ['user_id' => $user->id]);
        
        event(new Registered($user));
        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Registration successful!');

    } catch (\Exception $e) {
        \Log::error('Registration failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Registration failed. Please try again.');
    }
}

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login', [
            'pageTitle' => 'Login'
        ]);
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Login successful!');
        }

        return back()->withInput()
            ->withErrors([
                'email' => 'Invalid credentials',
            ]);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('status', 'You have been logged out.');
    }

    /**
     * Show password reset request form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password', [
            'pageTitle' => 'Reset Password'
        ]);
    }
}