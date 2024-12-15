<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;

class PasswordProtected
{
    public function handle($request, Closure $next)
    {
        $passwordEnteredAt = Session::get('password_entered_at');

        // Перевірка, чи є сесія з паролем і чи вона ще дійсна
        if (!$passwordEnteredAt || Carbon::parse($passwordEnteredAt)->addMinutes(30)->isPast()) {
            return redirect()->route('password.form')->with('error', 'Доступ вимагає повторного введення пароля.');
        }

        return $next($request);
    }
}
