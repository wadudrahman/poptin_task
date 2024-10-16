<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use App\Http\Requests\{LoginCredentialRequest, UserRegistrationRequest};
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\{Auth, Hash, Log};
use Ramsey\Uuid\Uuid;
use Illuminate\Http\{RedirectResponse, Request};

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', ['logout']),
        ];
    }

    public function showLogin(): View
    {
        return view('login');
    }

    public function login(LoginCredentialRequest $request): RedirectResponse
    {
        // Validation Rules
        $validatedData = $request->validated();

        try {
            // Credentials Check
            if (Auth::attempt($validatedData)) {
                $request->session()->regenerate();
                return redirect()->route('dashboard');
            }
        } catch (\Exception $exception) {
            // Log Error
            Log::error('Error while login account: ' . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        }

        return redirect()->route('showLogin')->with('failure', 'Provided credentials do not match.');
    }

    public function showRegister(): View
    {
        return view('register');
    }

    public function register(UserRegistrationRequest $request): RedirectResponse
    {
        // Validation Rules
        $validatedData = $request->validated();

        try {
            // DB Operation
            User::query()->create([
                'uuid' => Uuid::uuid4(),
                'name' => ucwords(trim($validatedData['name'])),
                'email' => trim($validatedData['email']),
                'password' => Hash::make(trim($validatedData['password']))
            ]);

            return redirect()->route('showLogin')->with('success', 'Account created successfully.');
        } catch (\Exception $exception) {
            // Log Error
            Log::error('Error while creating account: ' . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        }

        return redirect()->route('showRegister')->with('failure', 'Account creation failed.');
    }

    public function logout(Request $request): RedirectResponse
    {
        // Logout
        Auth::logout();

        // Invalid And Regenerate Session Token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('showLogin')->with('success', 'You have been logged out successfully.');
    }
}
