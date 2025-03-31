<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
            $clientId = env('GOOGLE_CLIENT_ID');
            $redirectUri = env('APP_URL') . env('GOOGLE_REDIRECT_URI');
            
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
                
                $clientId = env('GOOGLE_CLIENT_ID');
                $clientSecret = env('GOOGLE_CLIENT_SECRET');
                $redirectUri = env('APP_URL') . env('GOOGLE_REDIRECT_URI');
                
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
                
                // Armazenamos temporariamente na sessão
                session(['social_user_data' => $userData]);
                session(['authenticated' => true]);
                
                return redirect()->route('social.userData');
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
        
        // Aqui você pode salvar os dados no banco de dados
        // Exemplo de código para futuro uso:
        /*
        $user = User::firstOrCreate(
            ['email' => $request->google_email],
            [
                'name' => $request->google_name,
                'provider_id' => $request->google_id,
                'provider' => 'google',
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf), // Remove a máscara
                'phone' => $request->phone ? preg_replace('/[^0-9]/', '', $request->phone) : null,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
            ]
        );
        
        // Login do usuário
        Auth::login($user);
        */
        
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