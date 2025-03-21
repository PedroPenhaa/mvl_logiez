<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Temporariamente permitindo acesso sem verificar autenticação
        return $next($request);
        
        /*
        // Versão original que verifica autenticação
        if (Auth::check()) {
            return $next($request);
        }
        
        return redirect()->route('login');
        */
    }
}
