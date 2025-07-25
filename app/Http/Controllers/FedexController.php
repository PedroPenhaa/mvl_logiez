<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FedexController extends Controller
{
    private $fedexApiUrl = 'https://apis.fedex.com';

    public function auth(Request $request)
    {
        try {
            // 1. Autenticação na FedEx
            $auth = Http::asForm()->post(config('services.fedex.api_url') . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.fedex.client_id'),
                'client_secret' => config('services.fedex.client_secret'),
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'access_token' => $response->json()['access_token']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao autenticar com a FedEx',
                'error' => $response->json()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao autenticar com a FedEx',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getEtiqueta(Request $request)
    {
        try {
            $response = Http::withToken($request->access_token)
                ->post($this->fedexApiUrl . '/ship/v1/shipments', [
                    'accountNumber' => [
                        'value' => '207227690'
                    ],
                    'requestedShipment' => [
                        'shipDatestamp' => now()->format('Y-m-d'),
                        'serviceType' => 'INTERNATIONAL_PRIORITY',
                        'packagingType' => 'YOUR_PACKAGING',
                        'shipper' => [
                            // Adicione os dados do remetente aqui
                        ],
                        'recipients' => [
                            // Adicione os dados do destinatário aqui
                        ],
                        'labelSpecification' => [
                            'imageType' => 'PDF',
                            'labelStockType' => 'PAPER_85X11_TOP_HALF_LABEL'
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'trackingNumber' => $data['output']['transactionShipments'][0]['masterTrackingNumber'],
                    'labelUrl' => $data['output']['transactionShipments'][0]['pieceResponses'][0]['labelDocuments'][0]['url'],
                    // Adicione outros dados necessários aqui
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar etiqueta',
                'error' => $response->json()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar etiqueta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
