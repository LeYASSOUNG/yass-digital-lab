<?php

/**
 * ============================================================
 * AiAssistantController — Yass Digital Lab
 * ============================================================
 * Contrôleur d'IA responsable de l'analyse sémantique des requêtes
 * utilisateur, de la recommandation de produits/services, et du
 * suivi direct des devis en temps réel.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    /**
     * Analyse le message de l'utilisateur et retourne une réponse IA personnalisée,
     * les produits/services recommandés, et d'éventuelles informations de devis.
     */
    public function recommend(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'email'   => 'nullable|email|max:255',
        ]);

        $rawMessage  = $validated['message'];
        $userMessage = mb_strtolower(trim($rawMessage));
        $userEmail   = $validated['email'] ?? null;

        $allProducts = Product::with('category')->get();
        $allServices = Service::all();

        $recommendedProducts = collect();
        $recommendedServices = collect();
        $quoteData           = null;
        $quickActions        = [];
        $replyText           = "";

        // ------------------------------------------------------------
        // 1. Détection de demande de suivi de devis (#DEV-XXXXXX ou ID)
        // ------------------------------------------------------------
        if (preg_match('/(?:dev[-_ ]?|#)?(\d{1,8})/i', $userMessage, $matches) && (Str::contains($userMessage, ['devis', 'suivi', 'dossier', 'statut', 'projet', '#']) || Str::startsWith($userMessage, 'dev-'))) {
            $quoteId = (int) $matches[1];
            $quote = QuoteRequest::find($quoteId);

            if ($quote) {
                $statusLabels = [
                    'pending'     => 'En attente d\'analyse ⏳',
                    'contacted'   => 'Contacté par l\'équipe 📞',
                    'accepted'    => 'Devis Validé & Approuvé ✅',
                    'in_progress' => 'Développement en cours 🚀',
                    'completed'   => 'Terminé & Livré 🎉',
                    'rejected'    => 'Non retenu ❌',
                ];

                $statusText = $statusLabels[$quote->status] ?? $quote->status;
                $refFormatted = 'DEV-' . str_pad($quote->id, 6, '0', STR_PAD_LEFT);

                $quoteData = [
                    'id'            => $quote->id,
                    'ref'           => $refFormatted,
                    'service_title' => $quote->service_title,
                    'status'        => $quote->status ?: 'pending',
                    'status_label'  => $statusText,
                    'amount'        => $quote->amount ?: $quote->budget,
                    'deadline'      => $quote->deadline,
                    'created_at'    => $quote->created_at->format('d/m/Y'),
                ];

                $replyText = "J'ai retrouvé votre dossier **#{$refFormatted}** !\n\n"
                    . "• **Prestation :** {$quote->service_title}\n"
                    . "• **Statut actuel :** {$statusText}\n"
                    . "• **Montant :** " . ($quote->amount ?: $quote->budget ?: 'Sur devis') . "\n"
                    . "• **Délai :** " . ($quote->deadline ?: '15 à 30 jours') . "\n\n"
                    . "Vous pouvez consulter la timeline complète et télécharger le PDF officiel sur la page de suivi.";

                $quickActions = [
                    ['label' => '🔍 Voir le suivi complet', 'url' => "/suivi-devis?ref={$quote->id}"],
                    ['label' => '💬 Contacter sur WhatsApp', 'url' => "https://wa.me/22549679002?text=" . urlencode("Bonjour Yass Digital Lab, je souhaite un suivi pour mon devis #{$refFormatted}.")],
                ];

                return response()->json([
                    'reply'                => $replyText,
                    'quote'                => $quoteData,
                    'recommended_products' => [],
                    'recommended_services' => [],
                    'quick_actions'        => $quickActions,
                ]);
            }
        }

        // ------------------------------------------------------------
        // 2. Recherche & Recommandation par Thématique
        // ------------------------------------------------------------
        if (Str::contains($userMessage, ['saas', 'template', 'starter', 'boilerplate', 'vue', 'laravel', 'auth', 'dashboard'])) {
            $recommendedProducts = $allProducts->filter(function($p) {
                return mb_stripos($p->title, 'template') !== false 
                    || mb_stripos($p->title, 'saas') !== false
                    || mb_stripos($p->type, 'pack') !== false;
            })->take(3);

            $replyText = "Voici nos **Templates SaaS & Web Apps** les plus performants. Construits avec **Vue 3, Tailwind CSS et Laravel 12**, ils intègrent l'authentification multi-rôles, le paiement Stripe/PayPal et une interface dark mode glassmorphism clé en main !";

            $quickActions = [
                ['label' => '🚀 Voir tous les Templates', 'url' => '/products'],
                ['label' => '🛠️ Demander un développement sur mesure', 'url' => '/services'],
            ];
        } 
        elseif (Str::contains($userMessage, ['prompt', 'gpt', 'chatgpt', 'claude', 'ia', 'intelligence artificielle', 'midjourney', 'copywriting', 'marketing'])) {
            $recommendedProducts = $allProducts->filter(function($p) {
                return mb_stripos($p->title, 'prompt') !== false 
                    || mb_stripos($p->type, 'prompts') !== false
                    || mb_stripos($p->description, 'chatgpt') !== false;
            })->take(3);

            $replyText = "Excellente initiative ! Nos **Packs de Prompts IA & Automatisation** (ChatGPT, Claude 3.7, Midjourney) sont spécialement conçus pour booster votre productivité, générer du contenu viral et automatiser votre marketing en quelques secondes.";

            $quickActions = [
                ['label' => '✨ Découvrir les Packs IA', 'url' => '/products'],
                ['label' => '🤖 Intégrer un Agent IA sur mesure', 'url' => '/services'],
            ];
        } 
        elseif (Str::contains($userMessage, ['script', 'python', 'automation', 'bot', 'scraping', 'automatiser', 'webhook', 'api'])) {
            $recommendedProducts = $allProducts->filter(function($p) {
                return mb_stripos($p->title, 'script') !== false 
                    || mb_stripos($p->type, 'script') !== false;
            })->take(3);

            $replyText = "Découvrez nos **Scripts d'Automatisation Python & Node.js**. Ils permettent d'automatiser l'extraction de données, l'envoi de notifications WhatsApp/Telegram et la synchronisation CRM sans effort.";

            $quickActions = [
                ['label' => '⚡ Voir les Scripts', 'url' => '/products'],
            ];
        }
        elseif (Str::contains($userMessage, ['service', 'prestation', 'sur mesure', 'devis', 'création site', 'créer application', 'agence', 'tarif'])) {
            $recommendedServices = $allServices->take(3);

            $replyText = "Chez **Yass Digital Lab**, nous concevons des plateformes SaaS, des sites vitrines & e-commerce ultra-rapides et des agents IA sur mesure. Nos devis sont analysés sous 24h ouvrées avec un cahier des charges transparent !";

            $quickActions = [
                ['label' => '📝 Remplir une demande de devis', 'url' => '/services'],
                ['label' => '📍 Suivre un devis existant', 'url' => '/suivi-devis'],
                ['label' => '💬 Échanger sur WhatsApp', 'url' => 'https://wa.me/22549679002'],
            ];
        }
        elseif (Str::contains($userMessage, ['téléchargement', 'telecharger', 'licence', 'achat', 'fichier', 'facture', 'accès'])) {
            $replyText = "Vos achats numériques (.zip), clés de licence VIP à vie et factures PDF sont immédiatement disponibles dans votre **Espace Client > Mes Achats** après paiement sécurisé.";

            $quickActions = [
                ['label' => '👤 Mon Espace Client', 'url' => '/dashboard'],
                ['label' => '🛒 Voir mon Panier', 'url' => '/checkout'],
            ];
        }
        elseif (Str::contains($userMessage, ['paiement', 'moyen de paiement', 'stripe', 'orange money', 'wave', 'mtn', 'moov', 'carte'])) {
            $replyText = "Nous acceptons les paiements sécurisés par **Carte Bancaire (Visa, Mastercard)** via Stripe, **PayPal**, ainsi que le **Mobile Money (Wave, Orange Money, MTN, Moov)** pour l'Afrique de l'Ouest.";

            $quickActions = [
                ['label' => '🛍️ Explorer le catalogue', 'url' => '/products'],
            ];
        }
        elseif (Str::contains($userMessage, ['qui es-tu', 'qui est', 'yass', 'fondateur', 'créateur', 'diarrassouba', 'contact'])) {
            $replyText = "**Yass Digital Lab** a été fondé par **Diarrassouba Yassoungo Youssouf**, ingénieur full-stack et architecte web spécialisé en Vue 3, Laravel et Intelligence Artificielle. Vous pouvez découvrir ses réalisations et contacter l'équipe directement !";

            $quickActions = [
                ['label' => '🌐 Portfolio du Fondateur', 'url' => 'https://portfolio-tau-inky-96i2vyeddb.vercel.app/'],
                ['label' => '✉️ Formulaire de Contact', 'url' => '/contact'],
            ];
        }
        else {
            // ========================================================
            // INTEGRATION DE LA VRAIE IA (OPENAI / GEMINI)
            // ========================================================
            $openAiKey = env('OPENAI_API_KEY');
            $geminiKey = env('GEMINI_API_KEY');

            if ($openAiKey || $geminiKey) {
                try {
                    $systemPrompt = "Tu es Yass AI, l'assistant virtuel intelligent de Yass Digital Lab (fondé par Diarrassouba Yassoungo Youssouf). Tu vends des templates web (SaaS, Vue 3, Laravel), des scripts d'automatisation, des formations et des services de développement sur mesure. Sois concis, professionnel et chaleureux.";
                    
                    if ($openAiKey) {
                        // Appel OpenAI
                        $response = Http::withToken($openAiKey)->timeout(10)->post('https://api.openai.com/v1/chat/completions', [
                            'model' => 'gpt-4o-mini',
                            'messages' => [
                                ['role' => 'system', 'content' => $systemPrompt],
                                ['role' => 'user', 'content' => $rawMessage],
                            ],
                            'max_tokens' => 200,
                        ]);
                        if ($response->successful()) {
                            $replyText = $response->json('choices.0.message.content');
                        }
                    } elseif ($geminiKey) {
                        // Appel Google Gemini
                        $response = Http::timeout(10)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiKey}", [
                            'contents' => [
                                ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\nMessage de l'utilisateur : " . $rawMessage]]]
                            ]
                        ]);
                        if ($response->successful()) {
                            $replyText = $response->json('candidates.0.content.parts.0.text');
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Erreur API IA : " . $e->getMessage());
                }
            }

            // Fallback si pas de clé API ou si l'API a échoué
            if (empty($replyText)) {
                // Sélection des produits vedettes
                $recommendedProducts = $allProducts->take(3);
                $replyText = "Bonjour ! Je suis l'Assistant IA de **Yass Digital Lab**. Je peux vous aider à choisir les meilleurs outils numériques, configurer vos templates SaaS ou suivre l'état de votre devis en direct. Que souhaitez-vous réaliser aujourd'hui ?";
            }

            $quickActions = [
                ['label' => '📦 Découvrir nos Outils SaaS', 'url' => '/products'],
                ['label' => '🚀 Demander un Devis', 'url' => '/services'],
                ['label' => '🔍 Suivre mon Devis', 'url' => '/suivi-devis'],
            ];
        }

        return response()->json([
            'reply'                => $replyText,
            'quote'                => $quoteData,
            'recommended_products' => $recommendedProducts->values()->map(function($p) {
                return [
                    'id'          => $p->id,
                    'title'       => $p->title,
                    'slug'        => $p->slug,
                    'price'       => (float) $p->price,
                    'image'       => $p->image,
                    'type'        => $p->type,
                    'description' => Str::limit($p->description, 85),
                ];
            }),
            'recommended_services' => $recommendedServices->values()->map(function($s) {
                return [
                    'id'             => $s->id,
                    'title'          => $s->title,
                    'slug'           => $s->slug,
                    'starting_price' => (float) $s->starting_price,
                    'description'    => Str::limit($s->description, 85),
                ];
            }),
            'quick_actions'        => $quickActions,
        ]);
    }

    /**
     * Admin : Génération automatique de description de produit via IA.
     */
    public function generateProductDescription(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'type'  => 'required|string'
        ]);

        $openAiKey = env('OPENAI_API_KEY');
        $geminiKey = env('GEMINI_API_KEY');
        $prompt = "Rédige une description marketing très accrocheuse (en HTML avec balises <ul>, <strong>) pour un produit numérique vendu sur Yass Digital Lab.\n\nTitre : {$request->title}\nType : {$request->type}\n\nConcentre-toi sur les bénéfices, le gain de temps et l'impact professionnel. Ne dépasse pas 150 mots.";

        if ($openAiKey) {
            $response = Http::withToken($openAiKey)->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
            ]);
            if ($response->successful()) {
                return response()->json(['description' => $response->json('choices.0.message.content')]);
            }
        } elseif ($geminiKey) {
            $response = Http::timeout(15)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);
            if ($response->successful()) {
                return response()->json(['description' => $response->json('candidates.0.content.parts.0.text')]);
            }
        }

        return response()->json(['description' => "<p><strong>{$request->title}</strong> est un outil professionnel indispensable conçu pour accélérer votre workflow.</p><ul><li>Design premium</li><li>Code optimisé</li><li>Support inclus</li></ul>"], 200);
    }
}
