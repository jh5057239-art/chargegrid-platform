<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View { return view('auth.login'); }
    public function showRegister(): View { return view('auth.register', ['invite' => request('invite', '')]); }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'phone' => ['required','string','max:25','unique:users,phone'],
            'email' => ['nullable','email','max:255','unique:users,email'],
            'password' => ['required','confirmed', Password::min(8)],
            'invite_code' => ['nullable','string','max:32'],
        ]);
        $referrer = null;
        if (!empty($data['invite_code'])) {
            $referrer = User::where('invite_code', $data['invite_code'])->first();
            if (!$referrer) return back()->withErrors(['invite_code' => 'El código de invitación no es válido.'])->withInput($request->except('password','password_confirmation'));
        }
        unset($data['invite_code']);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data + [
            'password' => Hash::make($request->password),
            'invite_code' => strtoupper(bin2hex(random_bytes(4))),
            'referred_by' => $referrer?->invite_code,
            'balance_mxn_cents' => 0,
            'is_admin' => false,
        ]);
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('dashboard')->with('status', 'Tu cuenta está creada. Esta plataforma sigue en fase de configuración.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['identifier' => ['required','string','max:255'], 'password' => ['required','string']]);
        $field = filter_var($credentials['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        if (Auth::attempt([$field => $credentials['identifier'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['identifier' => 'No pudimos validar esos datos. Revisa el teléfono o correo y la contraseña.'])->onlyInput('identifier');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
