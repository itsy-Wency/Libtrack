<?php
namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\Http;

class AiRecommendationService
{
    /** Use a demo response without a key or ask the configured API for a suggestion. */
    public function suggest(string $interest): string
    {
        $key = config('services.openai.key');
        if (! $key) {
            return "Demo suggestion: search the catalog for books related to {$interest}. Add AI_API_KEY to enable a real AI response.";
        }

        $titles = Book::where('available_copies', '>', 0)->limit(25)->pluck('title')->implode(', ');
        $data   = Http::withToken($key)->timeout(20)->post(config('services.openai.base_url') . '/chat/completions', [
            'model'    => config('services.openai.model'),
            'messages' => [[
                'role'    => 'system',
                'content' => 'Recommend only titles from this library catalog.'],
                ['role' => 'user', 'content' => "Interest: {$interest}. Catalog: {$titles}"]],
        ])->throw()->json();

        return data_get($data, 'choices.0.message.content', 'No recommendation returned.');
    }
}
