<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email'
        ], [
            'email.unique' => 'Cet email est déjà inscrit à notre newsletter.'
        ]);

        Subscriber::create($validated);

        return response()->json(['message' => 'Merci pour votre inscription à notre newsletter !']);
    }

    public function index()
    {
        return response()->json(Subscriber::all());
    }
}
