<?php

/**
 * ============================================================
 * AnalyticsController — Yass Digital Lab
 * ============================================================
 * Contrôleur d'analyse statistique et financière en temps réel.
 * Calcule le chiffre d'affaires, l'évolution mensuelle des ventes,
 * les métriques d'AOV, le classement des produits et les statistiques globales.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Retourne toutes les métriques analytics basées sur les données réelles de la base.
     */
    public function index(Request $request)
    {
        $period = $request->input('period', '6m'); // '30d', '6m', '1y'

        // 1. Métriques Globales KPIs
        $totalRevenue = (float) Order::where('status', 'paid')->sum('total_amount');
        $totalOrdersCount = Order::count();
        $paidOrdersCount = Order::where('status', 'paid')->count();
        $pendingOrdersCount = Order::where('status', 'pending')->count();

        $totalProducts = Product::count();
        $totalSubscribers = Subscriber::count();
        $totalUsers = User::count();

        // 2. Moyenne et Panier Moyen Client (AOV)
        $averageOrderValue = $paidOrdersCount > 0 ? ($totalRevenue / $paidOrdersCount) : 0.00;

        // 3. Agrégation Mensuelle des Ventes (Graphique SVG)
        $monthlySales = $this->calculateMonthlySalesData($period);

        // 4. Calcul du Meilleur Mois et de la Moyenne Mensuelle
        $bestMonthName = 'N/A';
        $bestMonthAmount = 0.00;
        $monthlyTotalSum = 0.00;

        foreach ($monthlySales as $m) {
            $monthlyTotalSum += $m['value'];
            if ($m['value'] > $bestMonthAmount) {
                $bestMonthAmount = $m['value'];
                $bestMonthName = $m['label'];
            }
        }
        $averageMonthlyRevenue = count($monthlySales) > 0 ? ($monthlyTotalSum / count($monthlySales)) : 0.00;

        // 5. Classement des Produits les Plus Vendus
        $topProducts = OrderItem::select('product_title', DB::raw('COUNT(*) as total_sales'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->groupBy('product_title')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // 6. Demandes de Devis par Statut
        $quoteStats = [
            'pending'   => QuoteRequest::where('status', 'pending')->count(),
            'contacted' => QuoteRequest::where('status', 'contacted')->count(),
            'completed' => QuoteRequest::where('status', 'completed')->count(),
            'total'     => QuoteRequest::count(),
        ];

        return response()->json([
            'kpis' => [
                'total_revenue'        => round($totalRevenue, 2),
                'total_orders'         => $totalOrdersCount,
                'paid_orders'          => $paidOrdersCount,
                'pending_orders'       => $pendingOrdersCount,
                'total_products'       => $totalProducts,
                'total_subscribers'    => $totalSubscribers,
                'total_users'          => $totalUsers,
                'average_order_value'  => round($averageOrderValue, 2),
                'average_monthly_rev'  => round($averageMonthlyRevenue, 2),
                'best_month_name'      => $bestMonthName,
                'best_month_amount'    => round($bestMonthAmount, 2),
            ],
            'chart' => [
                'period' => $period,
                'points' => $monthlySales,
            ],
            'top_products' => $topProducts,
            'quote_stats'  => $quoteStats,
        ]);
    }

    /**
     * Génère les points d'analyse mensuelle selon la période sélectionnée.
     */
    private function calculateMonthlySalesData(string $period): array
    {
        $monthsCount = match ($period) {
            '1m' => 1,
            '1y' => 12,
            default => 6,
        };

        $points = [];
        $driver = DB::getDriverName();

        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $query = Order::where('status', 'paid')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

            $sum = (float) $query->sum('total_amount');
            $ordersCount = $query->count();

            $label = $i === 0 
                ? ucfirst($date->locale('fr')->translatedFormat('F')) . ' (Actuel)'
                : ucfirst($date->locale('fr')->translatedFormat('F'));

            $points[] = [
                'label'  => $label,
                'value'  => round($sum, 2),
                'orders' => $ordersCount,
            ];
        }

        // Calcul des coordonnées SVG (viewBox 0 0 520 190)
        $count = count($points);
        $maxValue = max(array_merge([100], array_column($points, 'value')));

        foreach ($points as $index => &$pt) {
            $x = $count > 1 ? 15 + ($index * (490 / ($count - 1))) : 260;
            // Échelle Y invertie : cy = 160 pour 0€, cy = 25 pour maxValue
            $y = 160 - (($pt['value'] / $maxValue) * 135);
            
            $pt['cx'] = round($x, 1);
            $pt['cy'] = round($y, 1);
        }

        return $points;
    }
}
