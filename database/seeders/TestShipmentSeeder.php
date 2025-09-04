<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shipment;
use App\Models\User;
use App\Models\SenderAddress;
use App\Models\RecipientAddress;
use Carbon\Carbon;

class TestShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o primeiro usuário ou criar um de teste
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Usuário Teste',
                'email' => 'teste@logiez.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Criar alguns envios de teste
        $shipments = [
            [
                'user_id' => $user->id,
                'tracking_number' => 'TEST001',
                'carrier' => 'FEDEX',
                'status' => 'created',
                'package_weight' => 2.5,
                'package_height' => 30,
                'package_width' => 20,
                'package_length' => 40,
                'ship_date' => Carbon::now()->subDays(2),
                'estimated_delivery_date' => Carbon::now()->addDays(5),
                'total_price' => 45.50,
                'currency' => 'USD',
                'total_price_brl' => 250.00,
            ],
            [
                'user_id' => $user->id,
                'tracking_number' => 'TEST002',
                'carrier' => 'FEDEX',
                'status' => 'pending_payment',
                'package_weight' => 1.8,
                'package_height' => 25,
                'package_width' => 15,
                'package_length' => 35,
                'ship_date' => Carbon::now()->subDays(1),
                'estimated_delivery_date' => Carbon::now()->addDays(7),
                'total_price' => 38.75,
                'currency' => 'USD',
                'total_price_brl' => 210.00,
            ],
            [
                'user_id' => $user->id,
                'tracking_number' => 'TEST003',
                'carrier' => 'FEDEX',
                'status' => 'confirmed',
                'package_weight' => 3.2,
                'package_height' => 35,
                'package_width' => 25,
                'package_length' => 45,
                'ship_date' => Carbon::now(),
                'estimated_delivery_date' => Carbon::now()->addDays(6),
                'total_price' => 52.30,
                'currency' => 'USD',
                'total_price_brl' => 285.00,
            ]
        ];

        foreach ($shipments as $shipmentData) {
            $shipment = Shipment::create($shipmentData);

            // Criar endereço de origem
            SenderAddress::create([
                'shipment_id' => $shipment->id,
                'name' => 'João Silva',
                'company' => 'Empresa Teste',
                'address_line_1' => 'Rua das Flores, 123',
                'city' => 'São Paulo',
                'state' => 'SP',
                'postal_code' => '01234-567',
                'country' => 'BR',
                'phone' => '+55 11 99999-9999',
            ]);

            // Criar endereço de destino
            RecipientAddress::create([
                'shipment_id' => $shipment->id,
                'name' => 'Maria Santos',
                'company' => 'Destino Corp',
                'address_line_1' => 'Main Street, 456',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'US',
                'phone' => '+1 555-123-4567',
            ]);
        }

        $this->command->info('Envios de teste criados com sucesso!');
    }
}
