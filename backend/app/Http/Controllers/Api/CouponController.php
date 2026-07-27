<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json(['message' => 'Code promo invalide'], 404);
        }

        if ($coupon->expires_at && Carbon::parse($coupon->expires_at)->isPast()) {
            return response()->json(['message' => 'Ce code promo a expiré'], 400);
        }

        return response()->json([
            'discount_amount' => $coupon->discount_amount,
            'discount_percentage' => $coupon->discount_percentage,
            'message' => 'Code promo appliqué avec succès'
        ]);
    }

    public function index()
    {
        return response()->json(Coupon::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'discount_amount' => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric',
            'expires_at' => 'nullable|date'
        ]);

        return response()->json(Coupon::create($validated), 201);
    }
}
