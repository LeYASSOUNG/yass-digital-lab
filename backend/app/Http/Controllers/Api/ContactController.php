<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $adminEmail = env('MAIL_FROM_ADDRESS', 'contact@yassdigitallab.com');

        try {
            Mail::raw("Nouveau message de contact de {$validated['name']} ({$validated['email']}):\n\nSujet: {$validated['subject']}\n\nMessage:\n{$validated['message']}", function($msg) use ($adminEmail, $validated) {
                $msg->to($adminEmail)
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject("[Yass Digital Lab Contact] {$validated['subject']}: {$validated['name']}");
            });

            return response()->json(['message' => 'Message envoyé avec succès.']);
        } catch (\Exception $e) {
            Log::error('Contact form email failed: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'envoi du message.'], 500);
        }
    }
}
