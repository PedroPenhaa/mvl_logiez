<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GerarInvoiceTeste extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gerar:invoice-teste {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera um array de invoice comercial de teste a partir dos dados do usuário informado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = \App\Models\User::find($userId);
        if (!$user) {
            $this->error('Usuário não encontrado!');
            return 1;
        }

        // Buscar todos os envios com tracking_number
        $shipments = \App\Models\Shipment::where('user_id', $userId)
            ->whereNotNull('tracking_number')
            ->get();
        if ($shipments->isEmpty()) {
            $this->warn('Nenhum envio com tracking_number encontrado para este usuário.');
            return 0;
        }

        foreach ($shipments as $shipment) {
            // Buscar remetente e destinatário
            $sender = $shipment->senderAddress;
            $recipient = $shipment->recipientAddress;
            $items = $shipment->items;

            // Montar array de produtos
            $cartoons = [];
            $total_qty = 0;
            $total_amount = 0;
            foreach ($items as $item) {
                $cartoons[] = [
                    'goods' => $item->description ?? $item->name ?? 'Produto',
                    'ncm' => $item->ncm ?? '',
                    'qty_utd' => $item->quantity ?? 0,
                    'qty_unidade' => $item->unit_type ?? 'PAR',
                    'unit_price_usd' => $item->unit_price_usd ?? 0,
                    'amount_usd' => $item->total_price_usd ?? 0,
                ];
                $total_qty += $item->quantity ?? 0;
                $total_amount += $item->total_price_usd ?? 0;
            }

            // Array do invoice
            $invoice = [
                'invoice_number' => $shipment->id ? sprintf('#%05d', $shipment->id) : '#00000',
                'date' => $shipment->ship_date ? $shipment->ship_date->format('d/m/y') : now()->format('d/m/y'),
                'terms_of_payment' => 'INTERNACIONAL TRANSFER',
                'purchase_order' => $shipment->quote_id ?? '',
                'shipment' => 'FLIGHT',
                'marks' => 'N/A',
                'loading_airport' => 'VIRACOPOS (VCP)',
                'airport_of_discharge' => 'MIAMI AIRPORT (MIA)',
                'selling_conditions' => 'DAB',
                'pages' => 1,
                'cartoons' => $cartoons,
                'total_qty' => $total_qty,
                'total_amount' => $total_amount,
                'freight' => $shipment->freight_usd ?? 98,
                'volumes' => $shipment->volumes ?? 4,
                'net_weight' => $shipment->net_weight_lbs ?? 37.0392,
                'gross_weight' => $shipment->gross_weight_lbs ?? 35.19,
                'container' => $shipment->container ?? 0,
                'sender' => [
                    'name' => 'LS COMÉRCIO ATACADISTA E VAREJISTA LTDA',
                    'address' => 'Rua 4, Pq Res. Dona Chiquinha, Cosmópolis - SP - Brazil',
                    'contact' => '+55(19) 98116-6445 / envios@logiez.com.br',
                    'cnpj' => '48.103.206/0001-73',
                ],
                'recipient' => [
                    'name' => $recipient->name ?? 'Destinatário',
                    'address' => $recipient->address ?? '',
                    'city' => $recipient->city ?? '',
                    'state' => $recipient->state ?? '',
                    'country' => $recipient->country ?? '',
                ],
            ];
            $this->info('Invoice gerado para envio ID: ' . $shipment->id);
            $this->line(print_r($invoice, true));
        }
        return 0;
    }
}
