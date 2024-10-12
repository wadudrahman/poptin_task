<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\{Auth, DB, Hash, Log};
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\{RedirectResponse, Request};

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        // Validation Rules
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            // Credentials Check
            if(Auth::attempt($validatedData))
            {
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

    public function register(Request $request): RedirectResponse
    {
        // Validation Rules
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => ['required', Password::min(8)]
        ]);

        try {
            // DB Operation
            DB::transaction(function () use ($validatedData) {
                User::query()->create([
                    'name' => ucwords(trim($validatedData['name'])),
                    'email' => trim($validatedData['email']),
                    'password' => Hash::make(trim($validatedData['password']))
                ]);
            });

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
}
