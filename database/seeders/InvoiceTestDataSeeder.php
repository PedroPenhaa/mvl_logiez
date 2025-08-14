<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shipment;
use App\Models\SenderAddress;
use App\Models\RecipientAddress;
use App\Models\ShipmentItem;
use App\Models\User;

class InvoiceTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar um usuário de teste se não existir
        $user = User::firstOrCreate(
            ['email' => 'teste@logiez.com'],
            [
                'name' => 'Usuário Teste',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Verificar se já existe um shipment com este tracking number
        $existingShipment = Shipment::where('tracking_number', '883501759146')->first();
        if ($existingShipment) {
            $this->command->info('Shipment já existe com tracking number: 883501759146');
            $this->command->info('Shipment ID: ' . $existingShipment->id);
            return;
        }

        // Criar um shipment de teste
        $shipment = Shipment::create([
            'user_id' => $user->id,
            'tracking_number' => '883501759146',
            'carrier' => 'FEDEX',
            'service_code' => 'FEDEX_INTERNATIONAL_PRIORITY',
            'service_name' => 'FedEx International Priority',
            'status' => 'active',
            'package_height' => 20.0,
            'package_width' => 15.0,
            'package_length' => 25.0,
            'package_weight' => 5.5,
            'total_price' => 150.00,
            'currency' => 'USD',
            'total_price_brl' => 750.00,
            'ship_date' => now(),
            'is_simulation' => false,
            'tipo_envio' => 'venda',
            'tipo_pessoa' => 'pf',
            'freight_usd' => 98.00,
            'volumes' => 2,
            'net_weight_lbs' => 12.13,
            'gross_weight_lbs' => 12.13,
            'container' => 0,
        ]);

        // Criar endereço do remetente
        SenderAddress::create([
            'shipment_id' => $shipment->id,
            'name' => 'LS COMÉRCIO ATACADISTA E VAREJISTA LTDA',
            'phone' => '+55(19) 98116-6445',
            'email' => 'envios@logiez.com.br',
            'address' => 'Rua 4, Pq Res. Dona Chiquinha',
            'address_complement' => 'Sala 101',
            'city' => 'Cosmópolis',
            'state' => 'SP',
            'postal_code' => '13150-000',
            'country' => 'BR',
            'is_residential' => false,
        ]);

        // Criar endereço do destinatário
        RecipientAddress::create([
            'shipment_id' => $shipment->id,
            'name' => 'Rui Vergani',
            'phone' => '+1-555-123-4567',
            'email' => 'rui.vergani@example.com',
            'address' => '123 Main Street',
            'address_complement' => 'Apt 4B',
            'city' => 'Celebration',
            'state' => 'FL',
            'postal_code' => '34747',
            'country' => 'US',
            'is_residential' => true,
        ]);

        // Criar itens do shipment
        ShipmentItem::create([
            'shipment_id' => $shipment->id,
            'description' => 'Produto Eletrônico',
            'weight' => 2.5,
            'quantity' => 2,
            'unit_price' => 50.00,
            'total_price' => 100.00,
            'currency' => 'USD',
            'country_of_origin' => 'BR',
            'harmonized_code' => '8517.12.00',
            'unit_price_usd' => 50.00,
            'total_price_usd' => 100.00,
            'unit_type' => 'PAR',
            'ncm' => '8517.12.00',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment->id,
            'description' => 'Acessórios',
            'weight' => 0.5,
            'quantity' => 1,
            'unit_price' => 25.00,
            'total_price' => 25.00,
            'currency' => 'USD',
            'country_of_origin' => 'BR',
            'harmonized_code' => '8529.90.00',
            'unit_price_usd' => 25.00,
            'total_price_usd' => 25.00,
            'unit_type' => 'PAR',
            'ncm' => '8529.90.00',
        ]);

        $this->command->info('Dados de teste para invoice criados com sucesso!');
        $this->command->info('Shipment ID: ' . $shipment->id);
        $this->command->info('Tracking Number: ' . $shipment->tracking_number);
    }
}
