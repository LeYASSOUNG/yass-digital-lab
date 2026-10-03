<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu & Devis Proforma #DEV-{{ str_pad($quote->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1E293B;
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
        }
        .gold-bar {
            height: 6px;
            background-color: #D4AF37;
            width: 100%;
        }
        .header-bg {
            background-color: #0F172A;
            color: #FFFFFF;
            padding: 22px 38px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-img {
            height: 70px;
            width: auto;
            display: block;
        }
        .brand-sub {
            font-size: 10.5px;
            color: #E2E8F0;
            margin-top: 3px;
        }
        .brand-sub-sm {
            font-size: 9.5px;
            color: #94A3B8;
            margin-top: 2px;
        }
        .doc-badge {
            display: inline-block;
            background-color: #4F46E5;
            color: #FFFFFF;
            font-size: 9px;
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #D4AF37;
            margin-bottom: 6px;
        }
        .doc-num {
            font-size: 19px;
            font-weight: bold;
            color: #F5C027;
        }
        .doc-date {
            font-size: 10.5px;
            color: #CBD5E1;
            margin-top: 3px;
        }
        .container {
            padding: 30px 38px;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .info-card {
            width: 48%;
            vertical-align: top;
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 14px 16px;
        }
        .info-card-title {
            font-size: 10px;
            font-weight: bold;
            color: #4F46E5;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            border-bottom: 1.5px solid #D4AF37;
            padding-bottom: 5px;
        }
        .info-line {
            line-height: 1.65;
            color: #334155;
        }
        .info-line strong {
            color: #0F172A;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
            border-radius: 6px;
            overflow: hidden;
        }
        table.items-table th {
            background-color: #4F46E5;
            color: #FFFFFF;
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 11px 14px;
            text-align: left;
        }
        table.items-table td {
            padding: 14px;
            border-bottom: 1px solid #E2E8F0;
            background-color: #FFFFFF;
            vertical-align: top;
        }
        .total-wrapper {
            width: 100%;
            margin-bottom: 25px;
        }
        .total-table {
            width: 46%;
            margin-left: auto;
            border-collapse: collapse;
            background-color: #FFFBEB;
            border: 1.5px solid #FCD34D;
            border-radius: 6px;
        }
        .total-table td {
            padding: 8px 14px;
        }
        .total-highlight {
            background-color: #EEF2FF;
            font-weight: bold;
            font-size: 14px;
            color: #4F46E5;
            border-top: 2px solid #4F46E5;
        }
        .payment-box {
            background-color: #F8FAFC;
            border-left: 4px solid #D4AF37;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 25px;
        }
        .payment-title {
            font-weight: bold;
            color: #0F172A;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .payment-list {
            margin: 0;
            padding-left: 16px;
            color: #475569;
            line-height: 1.6;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        .signature-cell {
            width: 48%;
            vertical-align: top;
            border: 1px dashed #94A3B8;
            border-radius: 6px;
            padding: 14px;
            min-height: 85px;
            text-align: center;
        }
        .stamp-box {
            display: inline-block;
            border: 2px double #D4AF37;
            color: #4F46E5;
            font-size: 9px;
            font-weight: bold;
            padding: 6px 14px;
            text-transform: uppercase;
            margin-top: 8px;
            border-radius: 4px;
            background-color: #FFFBEB;
        }
        .footer-note {
            margin-top: 35px;
            text-align: center;
            font-size: 8.5px;
            color: #94A3B8;
            border-top: 1px solid #E2E8F0;
            padding-top: 12px;
        }
    </style>
</head>
<body>

    <!-- Ligne Supérieure Dorée -->
    <div class="gold-bar"></div>

    <!-- Header Banner Nuit & Or avec Logo Officiel Encadré et Très Lisible -->
    <div class="header-bg">
        <table class="header-table">
            <tr>
                <td style="vertical-align: middle; width: 200px;">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMjAgMTEwIj4KICA8IS0tIFBpeGVsIGRvdHMgdG9wLWxlZnQgLSBzb2xpZCBnb2xkLCBubyBncmFkaWVudCAtLT4KICA8cmVjdCB4PSI0IiAgeT0iNCIgIHdpZHRoPSIxMSIgaGVpZ2h0PSIxMSIgZmlsbD0iI0Y1QzAyNyIvPgogIDxyZWN0IHg9IjIwIiB5PSI0IiAgd2lkdGg9IjExIiBoZWlnaHQ9IjExIiBmaWxsPSIjRjVDMDI3Ii8+CiAgPHJlY3QgeD0iMjAiIHk9IjIwIiB3aWR0aD0iMTEiIGhlaWdodD0iMTEiIGZpbGw9IiNGNUMwMjciLz4KICA8cmVjdCB4PSI0IiAgeT0iMjAiIHdpZHRoPSIxMSIgaGVpZ2h0PSIxMSIgZmlsbD0iI0Y1QzAyNyIvPgoKICA8IS0tIFkgbGV0dGVyIC0gc29saWQgZ29sZCAtLT4KICA8cGF0aCBkPSJNMzYgMzYgTDU0IDY0IEw1NCA5MCBMNjggOTAgTDY4IDY0IEw4NiAzNiBMNzAgMzYgTDYxIDUyIEw1MiAzNiBaIiBmaWxsPSIjRjVDMDI3Ii8+CgogIDwhLS0gRCBsZXR0ZXIgLSBzb2xpZCB3aGl0ZSAtLT4KICA8cGF0aCBkPSJNOTAgMzYgSDEyMCBDMTQwIDM2IDE1MiA0OCAxNTIgNjMgQzE1MiA3OCAxNDAgOTAgMTIwIDkwIEg5MCBaIE0xMDQgNTAgVjc2IEgxMTggQzEzMCA3NiAxMzggNzAgMTM4IDYzIEMxMzggNTYgMTMwIDUwIDExOCA1MCBaIiBmaWxsPSIjRkZGRkZGIi8+CgogIDwhLS0gTCBsZXR0ZXIgLSBzb2xpZCBnb2xkIC0tPgogIDxwYXRoIGQ9Ik0xMDQgNjMgSDExOCBWOTAgSDE0NSBWMTAzIEgxMDQgWiIgZmlsbD0iI0Y1QzAyNyIvPgoKICA8IS0tIEJyYW5kIG5hbWUgdGV4dCAtIHNvbGlkIGNvbG9ycywgbm8gZ3JhZGllbnQgLS0+CiAgPHRleHQgeD0iMTYyIiB5PSI1MiIgZm9udC1mYW1pbHk9IkFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWYiIGZvbnQtd2VpZ2h0PSJib2xkIiBmb250LXNpemU9IjIyIiBmaWxsPSIjRjVDMDI3IiBsZXR0ZXItc3BhY2luZz0iMSI+WUFTUzwvdGV4dD4KICA8dGV4dCB4PSIxNjIiIHk9IjcyIiBmb250LWZhbWlseT0iQXJpYWwsIEhlbHZldGljYSwgc2Fucy1zZXJpZiIgZm9udC13ZWlnaHQ9ImJvbGQiIGZvbnQtc2l6ZT0iMTYiIGZpbGw9IiNGRkZGRkYiIGxldHRlci1zcGFjaW5nPSIwLjUiPkRJR0lUQUw8L3RleHQ+CiAgPHRleHQgeD0iMTYyIiB5PSI5MiIgZm9udC1mYW1pbHk9IkFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWYiIGZvbnQtd2VpZ2h0PSJib2xkIiBmb250LXNpemU9IjE2IiBmaWxsPSIjRjVDMDI3IiBsZXR0ZXItc3BhY2luZz0iMC41Ij5MQUI8L3RleHQ+Cjwvc3ZnPgo=" class="logo-img" alt="Logo Officiel Yass Digital Lab"/>
                    <div style="margin-top: 6px;">
                        <div class="brand-sub">Ingénierie Web, SaaS & IA Sur-Mesure</div>
                        <div class="brand-sub-sm">Abidjan, Côte d'Ivoire &bull; Paris, France</div>
                    </div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div class="doc-badge">REÇU & DEVIS PROFORMA</div>
                    <div class="doc-num">#DEV-{{ str_pad($quote->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="doc-date">Date d'émission : {{ date('d/m/Y') }}</div>
                    <div class="doc-date">Validité : 30 Jours</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        <!-- Grid Informative -->
        <table class="info-grid">
            <tr>
                <td class="info-card">
                    <div class="info-card-title">ÉMETTEUR & PRESTATAIRE</div>
                    <div class="info-line">
                        <strong>Yass Digital Lab CI</strong><br>
                        Services de Développement Web & IA<br>
                        <strong>Email :</strong> contact@yassdigitallab.com<br>
                        <strong>Tél / WhatsApp :</strong> +225 49 67 90 02<br>
                        <strong>Siège :</strong> Abidjan, Côte d'Ivoire
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td class="info-card">
                    <div class="info-card-title">CLIENT / REÇU PAR</div>
                    <div class="info-line">
                        <strong>Nom :</strong> {{ $quote->name }}<br>
                        <strong>Email :</strong> {{ $quote->email }}<br>
                        @if($quote->phone) <strong>Tél / WhatsApp :</strong> {{ $quote->phone }}<br> @endif
                        @if($quote->company) <strong>Entreprise :</strong> {{ $quote->company }}<br> @endif
                        <strong>Statut de la demande :</strong> <span style="text-transform: uppercase; color: #4F46E5; font-weight: bold;">{{ $quote->status ?: 'Confirmé' }}</span>
                    </div>
                </td>
            </tr>
        </table>

@php
    $raw = $quote->amount ?: ($quote->budget ?: '1500000');
    $digits = preg_replace('/[^\d]/', '', $raw);
    if ($digits !== '' && strlen($digits) > 0) {
        $formattedNum = number_format((float)$digits, 0, '.', ' ');
        $formattedNum = str_replace(["\xc2\xa0", "\xa0", "\u{00A0}"], ' ', $formattedNum);
        $displayAmount = $formattedNum . ' FCFA';
    } else {
        $displayAmount = str_replace(["\xc2\xa0", "\xa0"], ' ', $raw);
    }
@endphp

        <!-- Table des Détails -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Prestation & Service</th>
                    <th style="width: 40%;">Spécifications du Besoin</th>
                    <th style="width: 25%; text-align: right;">Montant Réel HT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong style="font-size: 12px; color: #0F172A;">{{ $quote->service_title }}</strong><br>
                        <span style="font-size: 10px; color: #64748B;">Développement sur-mesure, intégration & déploiement</span>
                        @if($quote->deadline)<br><span style="font-size: 10px; color: #4F46E5;"><strong>Délai souhaité :</strong> {{ $quote->deadline }}</span>@endif
                    </td>
                    <td style="line-height: 1.5; color: #334155;">
                        {{ $quote->details ?: 'Prestation technique et ingénierie logicielle conforme aux exigences.' }}
                    </td>
                    <td style="text-align: right; font-size: 13px; font-weight: bold; color: #0F172A;">
                        {{ $displayAmount }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Total Box Luxe avec Accent Doré -->
        <div class="total-wrapper">
            <table class="total-table">
                <tr>
                    <td style="color: #64748B;">Total Général HT :</td>
                    <td style="text-align: right; font-weight: bold;">{{ $displayAmount }}</td>
                </tr>
                <tr>
                    <td style="color: #64748B;">TVA (Exonéré / 0%) :</td>
                    <td style="text-align: right; color: #64748B;">0,00 FCFA</td>
                </tr>
                <tr class="total-highlight">
                    <td>NET À PAYER TTC :</td>
                    <td style="text-align: right; font-size: 14px;">{{ $displayAmount }}</td>
                </tr>
            </table>
        </div>

        <!-- Instructions de Règlement -->
        <div class="payment-box">
            <div class="payment-title">MODALITÉS DE RÈGLEMENT & OPTIONS DE PAIEMENT :</div>
            <ul class="payment-list">
                <li><strong>Mobile Money West Africa :</strong> Wave, Orange Money CI, MTN Mobile Money</li>
                <li><strong>Carte Bancaire Internationale :</strong> Traitement sécurisé via Stripe Checkout (Visa / Mastercard)</li>
                <li><strong>Virement Bancaire :</strong> Relevé d'Identité Bancaire fourni sur confirmation de commande</li>
            </ul>
        </div>

        <!-- Signatures & Cachet avec Bordure Dorée -->
        <table class="signatures-table">
            <tr>
                <td class="signature-cell">
                    <div style="font-weight: bold; color: #0F172A; margin-bottom: 6px;">Pour l'Émetteur</div>
                    <div style="color: #475569;">Yass Digital Lab</div>
                    <div class="stamp-box">RÉCEPTION & CERTIFICATION VALIDÉE</div>
                </td>
                <td style="width: 4%;"></td>
                <td class="signature-cell">
                    <div style="font-weight: bold; color: #0F172A; margin-bottom: 6px;">Pour le Client / Destinataire</div>
                    <div style="color: #64748B; font-style: italic; margin-top: 15px;">(Mention "Bon pour accord" + Date & Signature)</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer-note">
            Yass Digital Lab &mdash; SASU au capital de 1 000 000 FCFA &mdash; N&deg; RCCM Abidjan &mdash; Document édité le {{ date('d/m/Y H:i') }} &mdash; Document Proforma Officiel.
        </div>
    </div>

</body>
</html>
