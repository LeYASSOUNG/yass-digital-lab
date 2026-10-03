<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirmation de Commande — Yass Digital Lab</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Plus Jakarta Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #F8FAFC; color: #0F172A; }
    .wrapper {
      max-width: 600px;
      margin: 32px auto;
      background: #FFFFFF;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(15,23,42,0.08);
      border: 1px solid #E2E8F0;
    }
    .header { background: linear-gradient(135deg, #1E1B4B, #312E81); padding: 40px 32px; text-align: center; }
    .header h1 { font-size: 26px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.5px; }
    .header span { color: #818CF8; }
    .badge {
      display: inline-block;
      background: rgba(99,102,241,0.2);
      color: #818CF8;
      border: 1px solid rgba(99,102,241,0.4);
      padding: 5px 16px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      margin-top: 12px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }
    .body { padding: 36px 32px; }
    .greeting { font-size: 19px; font-weight: 800; margin-bottom: 10px; color: #0F172A; }
    .intro { font-size: 15px; color: #475569; line-height: 1.65; margin-bottom: 28px; }
    .order-box {
      background: #F8FAFC;
      border-radius: 12px;
      border: 1px solid #E2E8F0;
      padding: 20px 24px;
      margin-bottom: 24px;
    }
    .order-box .order-id {
      font-size: 12px;
      color: #64748B;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 6px;
    }
    .order-box .order-num { font-size: 22px; font-weight: 800; color: #6366F1; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table th {
      background: #F1F5F9;
      color: #475569;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 10px 14px;
      text-align: left;
      border-bottom: 1px solid #E2E8F0;
    }
    table td { padding: 12px 14px; font-size: 14px; color: #0F172A; border-bottom: 1px solid #F1F5F9; }
    .total-row td {
      font-weight: 800;
      font-size: 16px;
      color: #6366F1;
      border-top: 2px solid #E2E8F0;
      border-bottom: none;
    }
    .cta { text-align: center; margin: 30px 0; }
    .cta a {
      display: inline-block;
      background: linear-gradient(135deg, #6366F1, #4F46E5);
      color: #FFFFFF;
      font-weight: 800;
      font-size: 15px;
      padding: 14px 34px;
      border-radius: 12px;
      text-decoration: none;
      box-shadow: 0 8px 20px -4px rgba(99,102,241,0.4);
    }
    .footer { background: #0F172A; padding: 26px 32px; text-align: center; }
    .footer p { color: #94A3B8; font-size: 12px; line-height: 1.8; }
    .footer a { color: #818CF8; text-decoration: none; font-weight: 700; }
  </style>
</head>
<body>
  <div class="wrapper">

    <!-- Header -->
    <div class="header">
      <h1><span>YASS</span> DIGITAL <span>LAB</span></h1>
      <div class="badge">Commande Confirmée & Validée</div>
    </div>

    <!-- Body -->
    <div class="body">
      <p class="greeting">Merci pour votre achat !</p>
      <p class="intro">
        Votre commande a été enregistrée et validée avec succès. Vos produits numériques, fichiers et clés de licence sont immédiatement disponibles dans votre espace client.
      </p>

      <!-- Order ID Box -->
      <div class="order-box">
        <div class="order-id">Numéro de Commande</div>
        <div class="order-num">#{{ $orderNumber }}</div>
        <div style="font-size:13px; color:#64748B; margin-top:6px;">Client : {{ $order->email }}</div>
      </div>

      <!-- Items Table -->
      <table>
        <thead>
          <tr>
            <th>Produit</th>
            <th style="text-align:center;">Qté</th>
            <th style="text-align:right;">Prix</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $item)
          <tr>
            <td><strong>{{ $item->product_title }}</strong></td>
            <td style="text-align:center;">{{ $item->quantity }}</td>
            <td style="text-align:right;">{{ number_format($item->price, 0, ',', ' ') }} FCFA</td>
          </tr>
          @endforeach
          <tr class="total-row">
            <td colspan="2">Total Payé</td>
            <td style="text-align:right;">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
          </tr>
        </tbody>
      </table>

      <!-- CTA Button -->
      <div class="cta">
        <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}/dashboard">
          Accéder à mon Espace Client & Téléchargements →
        </a>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>
        © 2026 <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}">Yass Digital Lab</a> — Outils Numériques Intelligents<br>
        Créateur : Diarrassouba Yassoungo Youssouf<br>
        <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}/contact">Support & Assistance</a>
      </p>
    </div>

  </div>
</body>
</html>
