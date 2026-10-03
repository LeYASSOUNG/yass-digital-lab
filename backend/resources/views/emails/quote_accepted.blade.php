<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Votre Devis a été accepté — Yass Digital Lab</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #F1F5F9; color: #1E293B; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background-color: #0F172A; padding: 28px 40px; text-align: center; border-bottom: 4px solid #D4AF37; }
        .header-brand { color: #FFFFFF; font-size: 22px; font-weight: 800; letter-spacing: 0.5px; }
        .header-brand .gold { color: #F5C027; }
        .badge-accepted { display: inline-block; background-color: #16A34A; color: #FFFFFF; font-size: 12px; font-weight: 700; padding: 6px 18px; border-radius: 20px; margin-top: 14px; letter-spacing: 0.5px; }
        .hero { background: linear-gradient(135deg, #EEF2FF 0%, #F0FDF4 100%); padding: 36px 40px 28px; text-align: center; }
        .hero-icon { font-size: 52px; margin-bottom: 10px; }
        .hero-title { font-size: 22px; font-weight: 800; color: #0F172A; margin: 0 0 6px; }
        .hero-sub { font-size: 14px; color: #64748B; margin: 0; }
        .ref-badge { display: inline-block; background-color: #EEF2FF; color: #4F46E5; font-size: 20px; font-weight: 800; padding: 10px 24px; border-radius: 8px; border: 2px solid #C7D2FE; margin-top: 18px; }
        .body-section { padding: 32px 40px; }
        .info-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 18px 22px; margin-bottom: 22px; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #F1F5F9; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #64748B; font-weight: 500; }
        .info-value { color: #0F172A; font-weight: 700; }
        .amount-highlight { color: #16A34A; font-size: 18px; font-weight: 800; }
        .cta-block { text-align: center; margin: 28px 0; }
        .btn-primary { display: inline-block; background-color: #4F46E5; color: #FFFFFF; font-size: 15px; font-weight: 700; padding: 14px 32px; border-radius: 8px; text-decoration: none; margin: 0 6px 10px; }
        .btn-whatsapp { display: inline-block; background-color: #16A34A; color: #FFFFFF; font-size: 15px; font-weight: 700; padding: 14px 32px; border-radius: 8px; text-decoration: none; margin: 0 6px 10px; }
        .btn-outline { display: inline-block; background-color: #FFFFFF; color: #4F46E5; font-size: 14px; font-weight: 600; padding: 12px 28px; border-radius: 8px; text-decoration: none; border: 2px solid #4F46E5; margin: 0 6px 10px; }
        .next-steps { background: #FFFBEB; border-left: 4px solid #D4AF37; border-radius: 4px; padding: 16px 20px; margin-bottom: 22px; }
        .next-steps-title { font-weight: 700; color: #92400E; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .next-steps ul { margin: 0; padding-left: 18px; color: #78350F; font-size: 13px; line-height: 1.7; }
        .footer { background-color: #F8FAFC; padding: 20px 40px; text-align: center; border-top: 1px solid #E2E8F0; }
        .footer-text { font-size: 11px; color: #94A3B8; line-height: 1.6; }
        .footer-text a { color: #4F46E5; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Header -->
    <div class="header">
        <div class="header-brand">
            <span class="gold">YASS</span> DIGITAL <span class="gold">LAB</span>
        </div>
        <div class="badge-accepted">✅ DEVIS ACCEPTÉ</div>
    </div>

    <!-- Hero -->
    <div class="hero">
        <div class="hero-icon">🎉</div>
        <h1 class="hero-title">Bonne nouvelle, {{ $quote->name }} !</h1>
        <p class="hero-sub">Votre demande de devis a été étudiée et validée par notre équipe.</p>
        <div class="ref-badge">#{{ $refNum }}</div>
    </div>

    <!-- Body -->
    <div class="body-section">

        <!-- Info Card -->
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Service demandé</span>
                <span class="info-value">{{ $quote->service_title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Montant validé</span>
                <span class="info-value amount-highlight">{{ $displayAmount }}</span>
            </div>
            @if($quote->deadline)
            <div class="info-row">
                <span class="info-label">Délai estimé</span>
                <span class="info-value">{{ $quote->deadline }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Statut</span>
                <span class="info-value" style="color: #16A34A;">✅ Accepté</span>
            </div>
        </div>

        <!-- Prochaines étapes -->
        <div class="next-steps">
            <div class="next-steps-title">Prochaines étapes</div>
            <ul>
                <li>Notre équipe vous contactera sous <strong>24h</strong> pour valider les détails du projet</li>
                <li>Un bon de commande officiel vous sera transmis avant le démarrage</li>
                <li>Vous pouvez suivre l'avancement de votre devis en temps réel via la page de suivi</li>
                <li>Pour toute question urgente, contactez-nous directement sur WhatsApp</li>
            </ul>
        </div>

        <!-- CTAs -->
        <div class="cta-block">
            <a href="{{ $trackingUrl }}" class="btn-primary">Suivre mon Devis en ligne</a>
            <a href="{{ $whatsappUrl }}" class="btn-whatsapp">Démarrer sur WhatsApp</a>
        </div>

        <p style="font-size: 13px; color: #64748B; text-align: center; margin: 0;">
            Vous pouvez également télécharger votre devis proforma officiel depuis la page de suivi.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p class="footer-text">
            Yass Digital Lab — Ingénierie Web, SaaS & Intelligence Artificielle<br>
            Abidjan, Côte d'Ivoire &bull; Paris, France<br>
            <a href="mailto:contact@yassdigitallab.com">contact@yassdigitallab.com</a> &bull;
            <a href="https://wa.me/22549679002">+225 49 67 90 02</a>
        </p>
        <p class="footer-text" style="margin-top: 8px; color: #CBD5E1;">
            Vous recevez cet email car vous avez soumis une demande de devis sur notre plateforme.
        </p>
    </div>

</div>
</body>
</html>
