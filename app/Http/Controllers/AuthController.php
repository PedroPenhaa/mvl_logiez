<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // Validando o input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Tentativa de login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended('dashboard');
        }

        // Retornar com mensagem de erro personalizada
        return back()
            ->withInput(['email' => $request->email])
            ->with('login_error', 'Email ou senha incorretos.');
    }
    
    public function showRegisterForm()
    {
        return view('register');
    }
    
    public function register(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        // Criação do usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        // Criação do perfil do usuário
        UserProfile::create(['user_id' => $user->id]);
        
        // Remover o login automático e redirecionar para a página de login
        return redirect()->route('login')->with('success', 'Conta criada com sucesso! Por favor, faça login.');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
