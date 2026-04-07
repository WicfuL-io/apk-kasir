<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;

class AdminController extends Controller
{
    public function approve($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_approved' => true
        ]);

        // kirim email ke user
        Mail::to($user->email)->send(new UserApprovedMail($user));

        return view('emails.approve-success', compact('user'));
    }

    public function reject($id)
    {
        $user = \App\Models\User::findOrFail($id);

        // optional: hapus user atau tandai ditolak
        $user->delete(); // atau pakai status reject

        // kirim email ke user
        Mail::to($user->email)->send(new UserRejectedMail($user));

        return view('emails.reject-success', compact('user'));
    }
}