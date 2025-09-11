<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class SocialAuthController extends Controller
{
    /**
     * Redireciona o usuário para a página de autenticação do provedor.
     *
     * @param string $provider Nome do provedor (google, apple)
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($provider)
    {
        if ($provider === 'google') {
            $clientId = config('services.google.client_id');
            $redirectUri = config('services.google.redirect');
            
            // Se a URL for relativa, construir a URL completa
            if (strpos($redirectUri, 'http') !== 0) {
                $redirectUri = url($redirectUri);
            }
            
            // Log para debug (remover em produção)
            \Log::info('Google OAuth Redirect URI: ' . $redirectUri);
            
            $params = [
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'openid profile email',
                'access_type' => 'offline',
                'prompt' => 'consent',
            ];
            
            $url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
            
            return redirect($url);
        } elseif ($provider === 'apple') {
            // Para o Apple Sign In, precisaríamos implementar um fluxo similar
            // mas com peculiaridades da Apple
            return redirect()->route('login')->with('error', 'Apple Sign In ainda não implementado');
        }
        
        return redirect()->route('login')->with('error', 'Provedor não suportado');
    }

    /**
     * Obtém as informações do usuário do provedor após a autenticação.
     *
     * @param string $provider Nome do provedor (google, apple)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback($provider)
    {
        try {
            if ($provider === 'google') {
                $code = request()->query('code');
                
                if (!$code) {
                    return redirect()->route('login')
                        ->with('error', 'Nenhum código de autorização recebido');
                }
                
                $clientId = config('services.google.client_id');
                $clientSecret = config('services.google.client_secret');
                $redirectUri = config('services.google.redirect');
                
                // Se a URL for relativa, construir a URL completa
                if (strpos($redirectUri, 'http') !== 0) {
                    $redirectUri = url($redirectUri);
                }
                
                // Troca o código de autorização por um token de acesso
                $response = Http::post('https://oauth2.googleapis.com/token', [
                    'code' => $code,
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUri,
                    'grant_type' => 'authorization_code',
                ]);
                
                if ($response->failed()) {
                    return redirect()->route('login')
                        ->with('error', 'Falha ao obter token de acesso: ' . $response->body());
                }
                
                $tokenData = $response->json();
                $accessToken = $tokenData['access_token'];
                
                // Obtém informações do usuário com o token
                // Adicionando parâmetro para obter mais campos, incluindo o telefone
                $userResponse = Http::withToken($accessToken)
                    ->get('https://www.googleapis.com/oauth2/v3/userinfo', [
                        'personFields' => 'names,emailAddresses,phoneNumbers,photos'
                    ]);
                
                // Também vamos tentar obter mais informações usando a People API
                $peopleResponse = Http::withToken($accessToken)
                    ->get('https://people.googleapis.com/v1/people/me', [
                        'personFields' => 'names,emailAddresses,phoneNumbers,photos'
                    ]);
                
                if ($userResponse->failed()) {
                    return redirect()->route('login')
                        ->with('error', 'Falha ao obter informações do usuário: ' . $userResponse->body());
                }
                
                $googleUser = $userResponse->json();
                
                // Tentamos obter o telefone da resposta da People API
                $phoneNumber = null;
                if (isset($peopleResponse) && $peopleResponse->successful()) {
                    $peopleData = $peopleResponse->json();
                    if (isset($peopleData['phoneNumbers'][0]['value'])) {
                        $phoneNumber = $peopleData['phoneNumbers'][0]['value'];
                    }
                }
                
                // Formata os dados do usuário
                $userData = [
                    'id' => $googleUser['sub'] ?? null,
                    'name' => $googleUser['name'] ?? null,
                    'email' => $googleUser['email'] ?? null,
                    'provider' => $provider,
                    'avatar' => $googleUser['picture'] ?? null,
                    'phone' => $phoneNumber,
                    'token' => $accessToken,
                    'refreshToken' => $tokenData['refresh_token'] ?? null,
                    'expiresIn' => $tokenData['expires_in'] ?? null,
                    'raw_response' => [
                        'user_info' => $googleUser,
                        'people_api' => $peopleResponse->successful() ? $peopleResponse->json() : null
                    ]
                ];
                
                // Verificar se o usuário já existe no banco
                $user = User::where('email', $googleUser['email'])->first();
                
                if ($user) {
                    // Usuário já existe - apenas fazer login
                    Auth::login($user, true); // true = remember me
                    
                    // Log para debug
                    \Log::info('Google OAuth - Usuário existente logado: ' . $user->email . ' - ID: ' . $user->id);
                    \Log::info('Google OAuth - Auth check após login: ' . (Auth::check() ? 'true' : 'false'));
                    
                    // Forçar regeneração da sessão
                    session()->regenerate();
                    
                    // Verificar se o login foi bem-sucedido antes de redirecionar
                    if (Auth::check()) {
                        \Log::info('Google OAuth - Redirecionando para dashboard com sucesso');
                        
                        // Forçar commit da sessão
                        session()->save();
                        
                        // Redirecionar diretamente para a URL do dashboard
                        return redirect('https://app.logiez.com.br/dashboard')
                            ->with('success', 'Login realizado com sucesso! Bem-vindo de volta, ' . $user->name . '!');
                    } else {
                        \Log::error('Google OAuth - Falha na autenticação após login');
                        return redirect()->route('login')->with('error', 'Erro na autenticação. Tente novamente.');
                    }
                } else {
                    // Usuário não existe - criar novo usuário
                    try {
                        $user = User::create([
                            'name' => $googleUser['name'],
                            'email' => $googleUser['email'],
                            'provider_id' => $googleUser['sub'],
                            'provider' => 'google',
                            'password' => bcrypt('google_oauth_' . $googleUser['sub']), // Senha temporária
                            'email_verified_at' => now(), // Marcar email como verificado
                        ]);
                        
                        \Log::info('Google OAuth - Novo usuário criado: ' . $user->email . ' - ID: ' . $user->id);
                        
                        // Login automático do novo usuário
                        Auth::login($user, true); // true = remember me
                        
                        // Log para debug
                        \Log::info('Google OAuth - Novo usuário logado: ' . $user->email . ' - ID: ' . $user->id);
                        \Log::info('Google OAuth - Auth check após login: ' . (Auth::check() ? 'true' : 'false'));
                        
                        // Forçar regeneração da sessão
                        session()->regenerate();
                        
                        // Verificar se o login foi bem-sucedido antes de redirecionar
                        if (Auth::check()) {
                            \Log::info('Google OAuth - Redirecionando novo usuário para dashboard com sucesso');
                            
                            // Forçar commit da sessão
                            session()->save();
                            
                            // Redirecionar diretamente para a URL do dashboard
                            return redirect('https://app.logiez.com.br/dashboard')
                                ->with('success', 'Conta criada e login realizado com sucesso! Bem-vindo, ' . $user->name . '!');
                        } else {
                            \Log::error('Google OAuth - Falha na autenticação após criação de usuário');
                            return redirect()->route('login')->with('error', 'Erro na autenticação. Tente novamente.');
                        }
                    } catch (\Exception $e) {
                        \Log::error('Google OAuth - Erro ao criar usuário: ' . $e->getMessage());
                        return redirect()->route('login')->with('error', 'Erro ao criar conta. Tente novamente.');
                    }
                }
            }
            
            return redirect()->route('login')->with('error', 'Provedor não suportado');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Erro na autenticação: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibe as informações do usuário autenticado por provedor social.
     *
     * @return \Illuminate\View\View
     */
    public function showUserData()
    {
        $userData = session('social_user_data');
        
        if (!$userData) {
            return redirect()->route('login')->with('error', 'Nenhum dado de usuário encontrado.');
        }
        
        // Garantir que está disponível na sessão para uso posterior
        session(['social_user_visible' => true]);
        
        // Exibir a view com os dados
        return view('social-user-data', ['userData' => $userData]);
    }
    
    /**
     * Processa as informações adicionais do usuário.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completeProfile(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string|size:14', // Com a máscara 000.000.000-00
            'phone' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'google_id' => 'required|string',
            'google_email' => 'required|email',
            'google_name' => 'required|string',
        ]);
        
        // Criar ou encontrar o usuário no banco de dados
        $user = User::firstOrCreate(
            ['email' => $request->google_email],
            [
                'name' => $request->google_name,
                'provider_id' => $request->google_id,
                'provider' => 'google',
                'password' => bcrypt('google_oauth_' . $request->google_id), // Senha temporária
            ]
        );
        
        // Atualizar dados do perfil se necessário
        if ($user->wasRecentlyCreated || !$user->provider_id) {
            $user->update([
                'provider_id' => $request->google_id,
                'provider' => 'google',
            ]);
        }
        
        // Login do usuário
        Auth::login($user);
        
        // Por enquanto, apenas exibimos os dados completos
        $userData = [
            'id' => $request->google_id,
            'name' => $request->google_name,
            'email' => $request->google_email,
            'cpf' => $request->cpf,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'address' => $request->address,
            'provider' => 'google'
        ];
        
        // Mostra mensagem de sucesso e armazena os dados completos na sessão
        session(['complete_user_data' => $userData]);
        session(['social_auth_complete' => true]);
        
        return redirect()->route('dashboard')
            ->with('success', 'Perfil completado com sucesso! Seus dados do Google foram importados.')
            ->with('user_data', $userData);
    }
} 