<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'secret_keyword'        => ['required', 'string'],
            'new_password'          => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Verify the secret keyword
        $validKeyword = config('app.secret_keyword') ?: env('SECRET_KEYWORD', '') ?: 'NodirbekRimzone1997';

        if (trim($request->input('secret_keyword')) !== trim($validKeyword)) {
            return back()->withErrors([
                'secret_keyword' => 'Maxfiy kalit noto\'g\'ri. Parol o\'zgartirilmadi.',
            ])->withInput();
        }

        // Use DB directly to bypass the 'hashed' cast (avoids double-hashing)
        \Illuminate\Support\Facades\DB::table('users')
            ->where('email', 'Rimzone.monipos@gmail.com')
            ->update(['password' => \Illuminate\Support\Facades\Hash::make($request->input('new_password'))]);

        return back()->with('success', 'Parol muvaffaqiyatli o\'zgartirildi! Endi yangi parol bilan kiring.');
    }
}
