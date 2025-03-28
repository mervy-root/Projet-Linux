<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Combined Web + API Authentication Routes
Route::middleware('guest')->group(function () {
    // WEB: Show forms (GET)
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');

    // API: Handle auth (POST)
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    
    // Add this for API clients to get CSRF cookie first
    Route::get('/sanctum/csrf-cookie', function () {
        return response()->json(['message' => 'CSRF cookie set']);
    });
});

// Authenticated routes (WEB + API)
Route::middleware(['auth:sanctum'])->group(function () {
    // WEB Dashboard
    Route::get('/dashboard', function () {
        return request()->expectsJson() 
            ? response()->json(['dashboard' => auth()->user()])
            : view('dashboard');
    })->name('dashboard');

    // Profile routes (WEB)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Unified logout (works for both web and API)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Tickets - Hybrid resource controller
    Route::resource('tickets', TicketController::class)->except(['index'])
        ->missing(function () {
            return request()->expectsJson()
                ? response()->json(['error' => 'Ticket not found'], 404)
                : abort(404);
        });

    // Custom ticket index
    Route::get('/tickets', [TicketController::class, 'index'])
        ->name('tickets.index')
        ->middleware('can:viewAny,App\Models\Ticket');
});

// Pure API routes (JSON only)
Route::prefix('api')->middleware('auth:sanctum')->group(function () {
    Route::get('/tickets', [TicketController::class, 'apiIndex'])->name('api.tickets.index');
});

// Database test route
Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        return response()->json([
            'status' => 'success',
            'db_connection' => 'OK',
            'tables' => DB::select('SHOW TABLES')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});