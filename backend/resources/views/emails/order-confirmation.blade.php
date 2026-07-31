<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirmation de Commande</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #F8FAFC; color: #0F172A; }
    .wrapper {
      max-width: 600px;
      margin: 32px auto;
      background: #FFFFFF;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    }
    .header { background: linear-gradient(135deg, #0F172A, #1E293B); padding: 40px 32px; text-align: center; }
    .header h1 { font-size: 26px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.5px; }
    .header span { color: #D4AF37; }
    .badge {
      display: inline-block;
      background: rgba(212,175,55,0.2);
      color: #D4AF37;
      border: 1px solid rgba(212,175,55,0.4);
      padding: 4px 14px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      margin-top: 10px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }
    .body { padding: 36px 32px; }
    .greeting { font-size: 18px; font-weight: 700; margin-bottom: 10px; color: #0F172A; }
    .intro { font-size: 15px; color: #475569; line-height: 1.6; margin-bottom: 28px; }
    .order-box {
      background: #F8FAFC;
      border-radius: 12px;
      border: 1px solid #E2E8F0;
      padding: 20px 24px;
      margin-bottom: 24px;
    }
    .order-box .order-id {
      font-size: 12px;
      color: #94A3B8;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 6px;
    }
    .order-box .order-num { font-size: 20px; font-weight: 800; color: #D4AF37; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table th {
      background: #F1F5F9;
      color: #64748B;
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
      color: #D4AF37;
      border-top: 2px solid #E2E8F0;
      border-bottom: none;
    }
    .cta { text-align: center; margin: 28px 0; }
    .cta a {
      display: inline-block;
      background: linear-gradient(135deg, #F0CC55, #D4AF37);
      color: #050811;
      font-weight: 800;
      font-size: 15px;
      padding: 14px 32px;
      border-radius: 12px;
      text-decoration: none;
    }
    .features { display: flex; gap: 16px; margin: 24px 0; flex-wrap: wrap; }
    .feature {
      flex: 1;
      min-width: 150px;
      background: #F8FAFC;
      border-radius: 10px;
      padding: 16px;
      border: 1px solid #E2E8F0;
      text-align: center;
    }
    .feature .icon { font-size: 24px; margin-bottom: 6px; }
    .feature .label { font-size: 12px; font-weight: 700; color: #475569; }
    .footer { background: #0F172A; padding: 24px 32px; text-align: center; }
    .footer p { color: rgba(255,255,255,0.6); font-size: 12px; line-height: 1.8; }
    .footer a { color: #D4AF37; text-decoration: none; font-weight: 700; }
  </style>
</head>
<body>
  <div class="wrapper">

    <!-- Header -->
    <div class="header">
      <h1><span>YASS</span> DIGITAL <span>LAB</span></h1>
      <div class="badge">✅ Commande Confirmée</div>
    </div>

    <!-- Body -->
    <div class="body">
      <p class="greeting">Merci pour votre achat ! 🎉</p>
      <p class="intro">
        Votre commande a été enregistrée et validée avec succès. Vous pouvez accéder immédiatement à vos fichiers
        et clés de licence depuis votre espace client.
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
            <td>{{ $item->product_title }}</td>
            <td style="text-align:center;">{{ $item->quantity }}</td>
            <td style="text-align:right;">{{ number_format($item->price, 2, ',', ' ') }} €</td>
          </tr>
          @endforeach
          <tr class="total-row">
            <td colspan="2">Total Payé</td>
            <td style="text-align:right;">{{ number_format($order->total_amount, 2, ',', ' ') }} €</td>
          </tr>
        </tbody>
      </table>

      <!-- Feature badges -->
      <div class="features">
        <div class="feature">
          <div class="icon">⚡</div>
          <div class="label">Livraison Instantanée</div>
        </div>
        <div class="feature">
          <div class="icon">🔑</div>
          <div class="label">Licence à Vie</div>
        </div>
        <div class="feature">
          <div class="icon">🎧</div>
          <div class="label">Support 24/7</div>
        </div>
      </div>

      <!-- CTA Button -->
      <div class="cta">
        <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}/dashboard">
          Accéder à mon Espace Client →
        </a>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>
        © 2026 <a href="{{ env('FRONTEND_URL') }}">Yass Digital Lab</a> — Outils Numériques Intelligents<br>
        Créateur : Diarrassouba Yassoungo Youssouf<br>
        <a href="{{ env('FRONTEND_URL') }}/contact">Nous Contacter</a>
      </p>
    </div>

  </div>
</body>
</html>
