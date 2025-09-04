<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        // Buscar usuario
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'No existe un usuario con este email.',
            ])->withInput($request->except('password'));
        }
        
        // Verificar si la cuenta está bloqueada
        if ($user->isLocked()) {
            return back()->withErrors([
                'email' => 'Cuenta temporalmente bloqueada. Intenta más tarde.',
            ])->withInput($request->except('password'));
        }
        
        // Verificar si el usuario está activo
        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'Tu cuenta está desactivada. Contacta al administrador.',
            ])->withInput($request->except('password'));
        }
        
        // Intentar autenticación
        if (!Hash::check($credentials['password'], $user->password)) {
            // Incrementar intentos fallidos
            $user->incrementLoginAttempts();
            
            return back()->withErrors([
                'password' => 'La contraseña es incorrecta.',
            ])->withInput($request->except('password'));
        }
        
        // Login exitoso
        Auth::login($user, $request->boolean('remember'));
        
        // Resetear intentos de login y actualizar información
        $user->update([
            'login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);
        
        $request->session()->regenerate();
        
        return redirect()->intended(route('dashboard'));
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Sesión cerrada exitosamente.');
    }
}