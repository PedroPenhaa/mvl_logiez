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
                <span class="header-title">{{ $invoice['recipient']['name'] }}</span><br>
                <span class="header-sub">
                    Address: {{ $invoice['recipient']['address'] }}<br>
                    <b>Contact: {{ $invoice['recipient']['contact'] }}</b>
                </span>
            </td>
            <td colspan="2" class="commercial-invoice" style="border-left: none; border-bottom: none;">COMMERCIAL<br>INVOICE</td>
        </tr>
        <tr>
            <td><b>INVOICE#</b><br>{{ $invoice['invoice_number'] }}</td>
            <td><b>Customer<br>(Customer)</b></td>
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
            <td><b>Date<br>(Date)</b><br>{{ $invoice['date'] }}</td>
            <td><b>Purchase Order<br>(Your Order)</b><br>{{ $invoice['purchase_order'] }}</td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td><b>Terms of Payment:<br>(Payment Conditions)</b><br>{{ $invoice['terms_of_payment'] }}</td>
            <td colspan="2"></td>
            <td><b>Shipment<br>(Embarkation)</b><br>{{ $invoice['shipment'] }}</td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td><b>Loading Airport<br>(Embarkation Airport)</b><br>{{ $invoice['loading_airport'] }}</td>
            <td><b>RE:</b></td>
            <td colspan="2"></td>
            <td><b>Airport of Discharge<br>(Destination Airport)</b><br>{{ $invoice['airport_of_discharge'] }}</td>
            <td><b>Notify:</b><br>THE SAME</td>
            <td><b>Pages<br>(Pages)</b><br>{{ $invoice['pages'] }}</td>
        </tr>
        <tr>
            <th>Cartoons<br>(Boxes)</th>
            <th>Goods (Goods)</th>
            <th>NCM</th>
            <th>Qty. (Qty.)</th>
            <th>Qty. (Unit)</th>
            <th>Unit Price US$<br>(Unit Price)</th>
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
            <td colspan="2"><b>Net Weight(Net):</b> {{ $invoice['net_weight'] }} LBS</td>
            <td colspan="2"><b>Gross Weight (Gross):</b> {{ $invoice['gross_weight'] }} LBS</td>
            <td class="total-usd" rowspan="2" style="vertical-align: middle;">TOTAL IN USD<br>{{ number_format($invoice['total_amount'] + $invoice['freight'], 2, ',', '.') }}</td>
        </tr>
    </table>
    
    <!-- Signature Section -->
    <div style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 70%; vertical-align: top; padding-right: 20px;">
                    <div style="border-bottom: 1px solid #000; height: 40px; margin-bottom: 5px;"></div>
                    <div style="text-align: center; font-size: 10px; font-weight: bold;">SIGNATURE</div>
                </td>
                <td style="width: 30%; vertical-align: top;">
                    <div style="border-bottom: 1px solid #000; height: 40px; margin-bottom: 5px;"></div>
                    <div style="text-align: center; font-size: 10px; font-weight: bold;">DATE</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
