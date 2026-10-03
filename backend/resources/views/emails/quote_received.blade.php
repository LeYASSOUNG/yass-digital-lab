<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Demande de devis reçue — Yass Digital Lab</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #F1F5F9; color: #1E293B; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }

        /* Header */
        .header { background-color: #0F172A; padding: 0; border-bottom: 4px solid #D4AF37; }
        .header-top { padding: 24px 40px 20px; display: flex; align-items: center; justify-content: space-between; }
        .brand { font-size: 20px; font-weight: 800; color: #FFFFFF; letter-spacing: 0.5px; }
        .brand .gold { color: #F5C027; }
        .badge-received { background: rgba(99,102,241,0.25); color: #A5B4FC; font-size: 11px; font-weight: 700; padding: 5px 14px; border-radius: 20px; border: 1px solid rgba(99,102,241,0.3); letter-spacing: 0.5px; }
        .header-ref { background: rgba(245,192,39,0.1); border-top: 1px solid rgba(245,192,39,0.15); padding: 12px 40px; display: flex; align-items: center; justify-content: space-between; }
        .ref-text { font-size: 22px; font-weight: 800; color: #F5C027; }
        .ref-date { font-size: 12px; color: #64748B; }

        /* Hero */
        .hero { background: linear-gradient(135deg, #EEF2FF 0%, #EFF6FF 100%); padding: 32px 40px 28px; text-align: center; border-bottom: 1px solid #E2E8F0; }
        .hero-icon { font-size: 48px; margin-bottom: 10px; }
        .hero-title { font-size: 21px; font-weight: 800; color: #0F172A; margin: 0 0 6px; }
        .hero-sub { font-size: 14px; color: #475569; margin: 0; line-height: 1.6; }

        /* Timeline / What happens next */
        .section { padding: 28px 40px; }
        .section-title { font-size: 13px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 18px; }
        .timeline { list-style: none; padding: 0; margin: 0; }
        .timeline li { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 18px; }
        .tl-dot { width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; }
        .tl-dot-1 { background: rgba(99,102,241,0.15); }
        .tl-dot-2 { background: rgba(245,192,39,0.15); }
        .tl-dot-3 { background: rgba(22,163,74,0.15); }
        .tl-dot-4 { background: rgba(16,185,129,0.12); }
        .tl-content {}
        .tl-title { font-size: 14px; font-weight: 700; color: #0F172A; margin: 0 0 2px; }
        .tl-desc  { font-size: 13px; color: #64748B; margin: 0; line-height: 1.5; }

        /* Summary card */
        .summary-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 18px 22px; margin: 0 40px 24px; }
        .summary-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid #F1F5F9; font-size: 13.5px; }
        .summary-row:last-child { border-bottom: none; }
        .sl { color: #64748B; }
        .sv { color: #0F172A; font-weight: 700; text-align: right; max-width: 60%; }

        /* CTA */
        .cta-section { padding: 4px 40px 28px; text-align: center; }
        .cta-title { font-size: 14px; color: #475569; margin: 0 0 16px; }
        .btn-track { display: inline-block; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #FFFFFF; font-size: 15px; font-weight: 700; padding: 14px 30px; border-radius: 8px; text-decoration: none; margin-bottom: 10px; }
        .btn-wa    { display: inline-block; background: linear-gradient(135deg, #16A34A, #15803D); color: #FFFFFF; font-size: 14px; font-weight: 600; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin-left: 8px; }

        /* Info banner */
        .info-banner { background: #FFFBEB; border-left: 4px solid #F5C027; padding: 14px 20px; margin: 0 40px 28px; border-radius: 4px; font-size: 13px; color: #78350F; }
        .info-banner strong { display: block; margin-bottom: 4px; color: #92400E; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Footer */
        .footer { background: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 20px 40px; text-align: center; }
        .footer-text { font-size: 11px; color: #94A3B8; line-height: 1.7; }
        .footer-text a { color: #4F46E5; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Header -->
    <div class="header">
        <div class="header-top">
            <div class="brand"><span class="gold">YASS</span> DIGITAL <span class="gold">LAB</span></div>
            <span class="badge-received">📋 DEMANDE REÇUE</span>
        </div>
        <div class="header-ref">
            <span class="ref-text">#{{ $refNum }}</span>
            <span class="ref-date">{{ \Carbon\Carbon::now()->locale('fr')->isoFormat('D MMMM YYYY [à] HH[h]mm') }}</span>
        </div>
    </div>

    <!-- Hero -->
    <div class="hero">
        <div class="hero-icon">✅</div>
        <h1 class="hero-title">Bonjour {{ $quote->name }}, votre demande est bien enregistrée !</h1>
        <p class="hero-sub">
            Nous avons bien reçu votre demande de devis. Notre équipe va l'étudier<br>
            et vous recontactera dans les <strong>24 à 48 heures ouvrées</strong>.
        </p>
    </div>

    <!-- Récapitulatif -->
    <div class="section">
        <p class="section-title">Récapitulatif de votre demande</p>
    </div>
    <div class="summary-card">
        <div class="summary-row">
            <span class="sl">Référence</span>
            <span class="sv" style="color: #4F46E5;">#{{ $refNum }}</span>
        </div>
        <div class="summary-row">
            <span class="sl">Service demandé</span>
            <span class="sv">{{ $quote->service_title }}</span>
        </div>
        @if($displayBudget !== 'À définir')
        <div class="summary-row">
            <span class="sl">Budget indiqué</span>
            <span class="sv" style="color: #16A34A;">{{ $displayBudget }}</span>
        </div>
        @endif
        @if($quote->deadline)
        <div class="summary-row">
            <span class="sl">Délai souhaité</span>
            <span class="sv">{{ $quote->deadline }}</span>
        </div>
        @endif
        @if($quote->company)
        <div class="summary-row">
            <span class="sl">Entreprise</span>
            <span class="sv">{{ $quote->company }}</span>
        </div>
        @endif
        <div class="summary-row">
            <span class="sl">Statut actuel</span>
            <span class="sv">⏳ En cours de traitement</span>
        </div>
    </div>

    <!-- Ce qui se passe maintenant -->
    <div class="section">
        <p class="section-title">Ce qui va se passer maintenant</p>
        <ul class="timeline">
            <li>
                <div class="tl-dot tl-dot-1">📋</div>
                <div class="tl-content">
                    <p class="tl-title">Analyse de votre demande <span style="font-size:11px;color:#94A3B8;font-weight:500;">(Maintenant)</span></p>
                    <p class="tl-desc">Votre demande #{{ $refNum }} a été enregistrée et transmise à notre équipe commerciale pour étude.</p>
                </div>
            </li>
            <li>
                <div class="tl-dot tl-dot-2">📞</div>
                <div class="tl-content">
                    <p class="tl-title">Prise de contact <span style="font-size:11px;color:#94A3B8;font-weight:500;">(Sous 24–48h)</span></p>
                    <p class="tl-desc">Un expert de notre équipe vous contactera par email ou WhatsApp pour clarifier les détails de votre projet.</p>
                </div>
            </li>
            <li>
                <div class="tl-dot tl-dot-3">✅</div>
                <div class="tl-content">
                    <p class="tl-title">Validation du devis <span style="font-size:11px;color:#94A3B8;font-weight:500;">(Sous 72h)</span></p>
                    <p class="tl-desc">Un devis proforma officiel vous sera soumis. Dès validation, vous recevrez un email de confirmation avec le montant final.</p>
                </div>
            </li>
            <li>
                <div class="tl-dot tl-dot-4">🚀</div>
                <div class="tl-content">
                    <p class="tl-title">Démarrage du projet <span style="font-size:11px;color:#94A3B8;font-weight:500;">(Après accord)</span></p>
                    <p class="tl-desc">Une fois le devis accepté, nous planifions ensemble le démarrage de votre projet selon le délai convenu.</p>
                </div>
            </li>
        </ul>
    </div>

    <!-- Info banner -->
    <div class="info-banner">
        <strong>💡 Suivez votre demande en temps réel</strong>
        Utilisez votre référence <strong>#{{ $refNum }}</strong> ou votre email sur notre page de suivi pour connaître l'avancement de votre demande à tout moment.
    </div>

    <!-- CTAs -->
    <div class="cta-section">
        <p class="cta-title">Suivez l'avancement de votre demande ou contactez-nous :</p>
        <a href="{{ $trackingUrl }}" class="btn-track">🔍 Suivre mon Devis en ligne</a>
        <a href="{{ $whatsappUrl }}" class="btn-wa">💬 WhatsApp</a>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p class="footer-text">
            <strong>Yass Digital Lab</strong> — Ingénierie Web, SaaS & Intelligence Artificielle<br>
            Abidjan, Côte d'Ivoire &bull; Paris, France<br>
            <a href="mailto:contact@yassdigitallab.com">contact@yassdigitallab.com</a> &bull;
            <a href="https://wa.me/22549679002">+225 49 67 90 02</a>
        </p>
        <p class="footer-text" style="margin-top: 8px; color: #CBD5E1; font-size: 10.5px;">
            Vous recevez cet email car vous avez soumis une demande de devis sur yassdigitallab.com.<br>
            Référence de suivi : <strong>{{ $refNum }}</strong>
        </p>
    </div>

</div>
</body>
</html>
