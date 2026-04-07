<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        if ($user->hasRole(['admin', 'teacher'])) {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/student/dashboard');
    }
}
