<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // Credenciais fixas (substitua pelos valores que quiser)
        $validEmail = 'pedro@gmail.com';
        $validPassword = '123456';

        // Validando o input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);




        // Verificando se os dados estão corretos
        if ($request->email === $validEmail && $request->password === $validPassword) {


            
            return view('dashboard')->with('message', 'Deu certo!');
        }

        return back()->withErrors(['email' => 'Usuário ou senha inválidos.']);
    }
}
