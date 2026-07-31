<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return response()->json(Review::with('product')->orderBy('created_at', 'desc')->get());
    }

    public function destroy(int $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Commentaire / Avis supprimé avec succès']);
    }
}
