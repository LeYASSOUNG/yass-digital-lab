<?php

/**
 * ============================================================
 * AffiliateController — Yass Digital Lab
 * ============================================================
 * Contrôleur du programme de parrainage et d'affiliation.
 * Gère la génération de liens uniques, le suivi des commissions,
 * les demandes de retrait de gains et la modération administrateur.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Commission;
use App\Models\AffiliatePayout;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AffiliateController extends Controller
{
    /**
     * Espace d'affiliation du client connecté : code parrain, lien unique, solde et statistiques.
     */
    public function getUserAffiliate(Request $request)
    {
        $user = $request->user();

        // Auto-génération du code parrain si non existant
        if (!$user->affiliate_code) {
            $user->affiliate_code = 'YASS-REF-' . strtoupper(Str::random(6));
            $user->save();
        }

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $referralLink = $frontendUrl . '/?ref=' . $user->affiliate_code;

        $commissions = Commission::with('order')
            ->where('affiliate_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $payouts = AffiliatePayout::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalEarned = (float) $commissions->sum('commission_amount');
        $referralsCount = $commissions->count();

        return response()->json([
            'affiliate_code'  => $user->affiliate_code,
            'referral_link'   => $referralLink,
            'balance'         => (float) $user->affiliate_balance,
            'total_earned'    => round($totalEarned, 2),
            'referrals_count' => $referralsCount,
            'commissions'     => $commissions,
            'payouts'         => $payouts,
        ]);
    }

    /**
     * Soumet une demande de retrait de gains d'affiliation.
     */
    public function requestPayout(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'amount'          => 'required|numeric|min:10',
            'payment_method'  => 'required|string|in:wave,orange_money,mtn,paypal,bank',
            'payment_details' => 'required|string|max:255',
        ]);

        $requestedAmount = (float) $validated['amount'];

        if ((float) $user->affiliate_balance < $requestedAmount) {
            return response()->json([
                'message' => 'Solde insuffisant pour effectuer ce retrait (Minimum disponible : ' . number_format($user->affiliate_balance, 0, ',', ' ') . ' FCFA).'
            ], 400);
        }

        // Déduction du solde disponible
        $user->affiliate_balance -= $requestedAmount;
        $user->save();

        $payout = AffiliatePayout::create([
            'user_id'         => $user->id,
            'amount'          => $requestedAmount,
            'payment_method'  => $validated['payment_method'],
            'payment_details' => $validated['payment_details'],
            'status'          => 'pending',
        ]);

        return response()->json([
            'message' => 'Votre demande de retrait de ' . number_format($requestedAmount, 0, ',', ' ') . ' FCFA a été enregistrée avec succès !',
            'payout'  => $payout,
            'new_balance' => (float) $user->affiliate_balance,
        ], 201);
    }

    /**
     * Vue d'ensemble des affiliations et demandes de retraits (Admin uniquement).
     */
    public function getAdminAffiliates(Request $request)
    {
        $payouts = AffiliatePayout::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $commissions = Commission::with(['affiliate', 'order'])
            ->orderBy('created_at', 'desc')
            ->get();

        $topAffiliates = User::whereNotNull('affiliate_code')
            ->where('affiliate_balance', '>', 0)
            ->orWhereHas('commissions')
            ->withCount('commissions')
            ->orderBy('affiliate_balance', 'desc')
            ->limit(10)
            ->get();

        $totalCommissionsPaid = (float) Commission::sum('commission_amount');
        $pendingPayoutsSum = (float) AffiliatePayout::where('status', 'pending')->sum('amount');

        return response()->json([
            'payouts'                => $payouts,
            'commissions'            => $commissions,
            'top_affiliates'         => $topAffiliates,
            'total_commissions_paid' => round($totalCommissionsPaid, 2),
            'pending_payouts_sum'    => round($pendingPayoutsSum, 2),
        ]);
    }

    /**
     * Traite et met à jour le statut d'une demande de retrait (Admin uniquement).
     */
    public function updatePayoutStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,paid,rejected'
        ]);

        $payout = AffiliatePayout::with('user')->findOrFail($id);
        $oldStatus = $payout->status;
        $newStatus = $validated['status'];

        // Si le retrait est rejeté alors qu'il était en attente, récréditer le solde
        if ($newStatus === 'rejected' && $oldStatus === 'pending') {
            $payout->user->affiliate_balance += (float) $payout->amount;
            $payout->user->save();
        }

        $payout->status = $newStatus;
        $payout->save();

        return response()->json([
            'message' => 'Statut du retrait mis à jour avec succès.',
            'payout'  => $payout,
        ]);
    }
}
