<?php

/**
 * ============================================================
 * CourseController — Yass Digital Lab
 * ============================================================
 * Contrôleur e-learning de gestion des formations vidéo et du suivi
 * de progression client en temps réel.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\ChapterProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Liste toutes les formations vidéo publiées.
     */
    public function index()
    {
        $courses = Course::withCount('chapters')
            ->where('is_published', true)
            ->get();

        return response()->json($courses);
    }

    /**
     * Retourne le détail d'un cours avec ses leçons et la progression du client connecté.
     */
    public function show(Request $request, int $id)
    {
        $course = Course::with(['chapters' => function($q) {
            $q->orderBy('order_num', 'asc');
        }, 'reviews' => function($q) {
            $q->approved()->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        $user = $request->user();
        $completedChapterIds = [];
        $progressPercentage = 0;

        if ($user) {
            $chapterIds = $course->chapters->pluck('id')->toArray();
            $completedChapterIds = ChapterProgress::where('user_id', $user->id)
                ->whereIn('chapter_id', $chapterIds)
                ->where('is_completed', true)
                ->pluck('chapter_id')
                ->toArray();

            $totalChapters = count($chapterIds);
            if ($totalChapters > 0) {
                $progressPercentage = round((count($completedChapterIds) / $totalChapters) * 100);
            }
        }

        return response()->json([
            'course'                => $course,
            'completed_chapter_ids' => $completedChapterIds,
            'progress_percentage'   => $progressPercentage,
        ]);
    }

    /**
     * Valide ou annule l'achèvement d'un chapitre pour l'utilisateur connecté.
     */
    public function toggleProgress(Request $request, int $chapterId)
    {
        $user = $request->user();
        $chapter = Chapter::findOrFail($chapterId);

        $progress = ChapterProgress::where('user_id', $user->id)
            ->where('chapter_id', $chapter->id)
            ->first();

        if ($progress) {
            $progress->is_completed = !$progress->is_completed;
            $progress->save();
        } else {
            $progress = ChapterProgress::create([
                'user_id'      => $user->id,
                'chapter_id'   => $chapter->id,
                'is_completed' => true,
            ]);
        }

        return response()->json([
            'message'      => $progress->is_completed ? 'Leçon marquée comme terminée ! 🎓' : 'Statut de leçon réinitialisé.',
            'is_completed' => $progress->is_completed,
            'chapter_id'   => $chapter->id,
        ]);
    }

    /**
     * Ajoute un avis sur une formation.
     */
    public function storeReview(Request $request, int $id)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email',
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $course = Course::findOrFail($id);

        $isVerifiedBuyer = false;
        if (!empty($validated['email'])) {
            $isVerifiedBuyer = \App\Models\Order::where('email', $validated['email'])
                ->where('status', 'paid')
                ->whereHas('items', function ($sub) use ($course) {
                    $sub->where('product_title', 'like', "%{$course->title}%");
                })->exists();
        }

        $validated['course_id'] = $id;
        $validated['status'] = 'approved';
        $validated['is_published'] = true;
        $validated['verified_buyer'] = $isVerifiedBuyer;

        unset($validated['email']);

        $review = \App\Models\Review::create($validated);
        return response()->json(['message' => 'Votre avis a été publié avec succès !', 'review' => $review], 201);
    }
}
