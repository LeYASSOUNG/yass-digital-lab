<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yass Digital Lab — API Service</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: #0F172A;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 24px;
        }
        .card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 20px;
            padding: 48px 36px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }
        h1 {
            font-size: 28px;
            font-weight: 800;
            color: #D4AF37;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        p {
            color: #94A3B8;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .badge {
            display: inline-block;
            background: rgba(16, 185, 129, 0.15);
            color: #10B981;
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 24px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #F0CC55, #D4AF37);
            color: #050811;
            font-weight: 700;
            font-size: 14px;
            padding: 12px 28px;
            border-radius: 12px;
            text-decoration: none;
            transition: transform 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">● Service API Opérationnel</div>
        <h1>YASS DIGITAL LAB</h1>
        <p>
            Serveur API Laravel 12 actif.<br>
            Plateforme e-commerce & SaaS d'outils numériques intelligents.
        </p>
        <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}" class="btn">
            Accéder à l'Application →
        </a>
    </div>
</body>
</html>
