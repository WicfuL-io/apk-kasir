<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_approved' => false
        ]);

        // Kirim email ke admin
        try {
            \Mail::to(env('ADMIN_EMAIL'))
                ->send(new \App\Mail\AdminApprovalMail($user));
        } catch (\Exception $e) {
            \Log::error('Email gagal: ' . $e->getMessage());
        }
        // \Mail::to('admin@gmail.com')->send(new \App\Mail\AdminApprovalMail($user));
        // \Mail::to(env('ADMIN_EMAIL'))->send(new \App\Mail\AdminApprovalMail($user));

        return redirect()->route('login')->with('status', 'Menunggu persetujuan admin');

    }
}
