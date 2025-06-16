<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; margin: 0; padding: 0; }
        .logo-logiez { width: 120px; margin: 10px auto 0 auto; display: block; }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        .invoice-table th, .invoice-table td {
            border: 1px dashed #222;
            padding: 4px 6px;
            font-size: 10px;
            vertical-align: top;
        }
        .invoice-table th {
            font-weight: bold;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .b { font-weight: bold; }
        .header-title { font-size: 15px; font-weight: bold; }
        .header-sub { font-size: 11px; }
        .commercial-invoice { font-size: 15px; font-weight: bold; text-align: right; }
        .no-border { border: none !important; }
        .small { font-size: 9px; }
        .nowrap { white-space: nowrap; }
        .total-usd { font-size: 15px; font-weight: bold; text-align: right; }
        .bg-gray { background: #f8f8f8; }
    </style>
</head>
<body>
    <img src="{{ public_path('img/logo_logiez.png') }}" alt="Logiez" class="logo-logiez">
    <table class="invoice-table" style="margin-bottom:0;">
        <tr>
            <td colspan="6" class="text-center" style="border-bottom: none;">
                <span class="header-title">LS COMÉRCIO ATACADISTA E VAREJISTA LTDA</span><br>
                <span class="header-sub">
                    Endereço: Rua 4, Pq Res. Dona Chiquinha, Cosmópolis - SP - Brazil<br>
                    <b>Contato: +55(19) 98116-6445 / envios@logiez.com.br</b><br>
                    CNPJ: 48.103.206/0001-73
                </span>
            </td>
            <td colspan="2" class="commercial-invoice" style="border-left: none; border-bottom: none;">COMMERCIAL<br>INVOICE</td>
        </tr>
        <tr>
            <td><b>INVOICE#</b><br>{{ $invoice['invoice_number'] }}</td>
            <td><b>Costumer<br>(Cliente)</b></td>
            <td colspan="4" class="text-center align-middle">
                <b>
                {{ $invoice['recipient']['name'] }}<br>
                {{ $invoice['recipient']['address'] }}<br>
                {{ $invoice['recipient']['city'] }} {{ $invoice['recipient']['state'] }} {{ $invoice['recipient']['country'] }}
                </b>
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td><b>Date<br>(Fecha)</b><br>{{ $invoice['date'] }}</td>
            <td><b>Purchase Order<br>(Su pedido)</b><br>{{ $invoice['purchase_order'] }}</td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td><b>Terms of Payment:<br>(Condiciones pago)</b><br>{{ $invoice['terms_of_payment'] }}</td>
            <td colspan="2"></td>
            <td><b>Shipment<br>(Embarque)</b><br>{{ $invoice['shipment'] }}</td>
            <td><b>Marks (Marcas):</b><br>{{ $invoice['marks'] }}</td>
            <td><b>HAVAIANAS</b></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td><b>Loading Airport<br>(Aeropuerto Embarque)</b><br>{{ $invoice['loading_airport'] }}</td>
            <td><b>RE:</b></td>
            <td colspan="2"></td>
            <td><b>Airport of Discharge<br>(Aeropuerto Destino)</b><br>{{ $invoice['airport_of_discharge'] }}</td>
            <td><b>Selling Conditions</b><br>{{ $invoice['selling_conditions'] }}</td>
            <td><b>Notify:</b><br>THE SAME</td>
            <td><b>Pages<br>(Hojas)</b><br>{{ $invoice['pages'] }}</td>
        </tr>
        <tr>
            <th>Cartoons<br>(Boxes)</th>
            <th>Goods (Mercadoria)</th>
            <th>NCM</th>
            <th>Qty. (Utd.)</th>
            <th>Qty. (Unidade)</th>
            <th>Unit Price US$<br>(Preço Unitário)</th>
            <th>Amount US$<br>(Total US$)</th>
            <th></th>
        </tr>
        @foreach($invoice['cartoons'] as $item)
        <tr>
            <td>Cardboard</td>
            <td>{{ $item['goods'] }}</td>
            <td>{{ $item['ncm'] }}</td>
            <td>{{ $item['qty_utd'] }}</td>
            <td>{{ $item['qty_unidade'] }}</td>
            <td>U${{ number_format($item['unit_price_usd'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['amount_usd'], 2, ',', '.') }}</td>
            <td></td>
        </tr>
        @endforeach
        <tr>
            <td colspan="3">Total</td>
            <td>{{ $invoice['total_qty'] }}</td>
            <td></td>
            <td></td>
            <td>{{ number_format($invoice['total_amount'], 2, ',', '.') }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="6">Freight</td>
            <td>{{ number_format($invoice['freight'], 2, ',', '.') }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2"><b>Volumes:</b> {{ $invoice['volumes'] }}</td>
            <td colspan="2"><b>Net Weight(Neto):</b> {{ $invoice['net_weight'] }} LBS</td>
            <td colspan="2"><b>Container:</b> {{ $invoice['container'] }}</td>
            <td><b>Gross Weight (Bruto):</b> {{ $invoice['gross_weight'] }} LBS</td>
            <td class="total-usd" rowspan="2" style="vertical-align: middle;">TOTAL DAS USD<br>{{ number_format($invoice['total_amount'] + $invoice['freight'], 2, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
