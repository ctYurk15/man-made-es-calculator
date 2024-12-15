<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PasswordController extends Controller
{
    public function showForm()
    {
        return view('password-form');
    }

    public function validatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        // Перевірка пароля (можна змінити на динамічну перевірку)
        $correctPassword = 'my-secret-password'; // Замініть на реальний пароль або використовуйте базу даних

        if ($request->password === $correctPassword) {
            Session::put('password_entered_at', now());

            return redirect()->route('admin.page')->with('success', 'Пароль успішно підтверджено!');
        }

        return redirect()->back()->withErrors(['password' => 'Невірний пароль.']);
    }
}
