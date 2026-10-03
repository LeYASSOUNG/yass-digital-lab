<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture #FA-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0F172A; margin: 0; padding: 20px; font-size: 13px; }
        .invoice-card { border: 2px solid #D4AF37; padding: 30px; border-radius: 12px; }
        .top-header { border-bottom: 2px solid #E2E8F0; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-title {
            font-size: 26px;
            font-weight: 800;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }
        .accent { color: #D4AF37; }
        .invoice-details { text-align: right; }
        .bill-to {
            background: #F8FAFC;
            padding: 16px;
            border-radius: 8px;
            border-left: 4px solid #D4AF37;
            margin-bottom: 30px;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th {
            background: #0F172A;
            color: #FFFFFF;
            text-align: left;
            padding: 10px 14px;
            font-size: 12px;
            text-transform: uppercase;
        }
        td { padding: 12px 14px; border-bottom: 1px solid #E2E8F0; }
        .summary-box {
            width: 40%;
            float: right;
            background: #F8FAFC;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
        }
        .summary-row { display: flex; justify-content: space-between; padding: 4px 0; }
        .total-row {
            font-weight: bold;
            font-size: 16px;
            color: #D4AF37;
            border-top: 2px solid #D4AF37;
            padding-top: 8px;
            margin-top: 8px;
        }
        .footer-note {
            clear: both;
            margin-top: 60px;
            text-align: center;
            font-size: 11px;
            color: #64748B;
            border-top: 1px solid #E2E8F0;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-card">
        <table style="border: none; margin-bottom: 20px;">
            <tr style="border: none;">
                <td style="border: none; padding: 0;">
                    <div class="logo-title">YASS<span class="accent">DIGITAL</span>LAB</div>
                    <div style="font-size: 11px; color: #64748B; margin-top: 4px;">Outils Numériques Intelligents</div>
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <h2 style="margin: 0; color: #D4AF37;">FACTURE</h2>
                    <div style="font-weight: bold; margin-top: 4px;">
                        N° FA-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                    </div>
                    <div style="color: #64748B; font-size: 11px; margin-top: 2px;">
                        Date : {{ $order->created_at ? $order->created_at->format('d/m/Y') : date('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="bill-to">
            <strong style="color: #0F172A; text-transform: uppercase; font-size: 11px;">
                Client / Facturé à :
            </strong><br>
            <span style="font-size: 14px; font-weight: bold;">{{ $order->email }}</span><br>
            <span style="color: #64748B; font-size: 11px;">
                Statut du règlement : <strong style="color: #10B981;">Payé en ligne (Stripe)</strong>
            </span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Désignation du Produit / Service</th>
                    <th style="text-align: center;">Qté</th>
                    <th style="text-align: right;">Prix Unitaire HT</th>
                    <th style="text-align: right;">Total TTC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td style="font-weight: bold;">{{ $item->product_title }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($item->price, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ number_format($item->price * $item->quantity, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="width: 100%; display: inline-block;">
            <div class="summary-box">
                <div class="summary-row">
                    <span>Sous-total HT :</span>
                    <span>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="summary-row">
                    <span>TVA (0% - Auto-liquidation) :</span>
                    <span>0 FCFA</span>
                </div>
                <div class="summary-row total-row">
                    <span>Total à Payer :</span>
                    <span>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <div class="footer-note">
            <p><strong>Yass Digital Lab</strong> — Outils Numériques Intelligents & Développement Sur-Mesure</p>
            <p>Merci pour votre confiance ! Pour toute question : contact@yassdigitallab.com</p>
        </div>
    </div>
</body>
</html>
