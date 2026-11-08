<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\AiRecommendationService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /** Filter and paginate books, then supply fresh category models to the filter. */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $booksQuery = Book::with('category');

        if (! empty($filters['q'])) {
            $term = $filters['q'];

            $booksQuery->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('author', 'like', "%{$term}%");
            });
        }

        if (! empty($filters['category'])) {
            $booksQuery->where('category_id', $filters['category']);
        }

        $books = $booksQuery->latest()
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();

        return view('catalog', compact('books', 'categories'));
    }

    /** Load the selected book's category before showing its details. */
    public function show(Book $book)
    {
        $book->load('category');

        return view('book', compact('book'));
    }

    /** Validate a student's interest and return a catalog recommendation. */
    public function recommend(Request $request, AiRecommendationService $ai)
    {
        $data = $request->validate([
            'interest' => 'required|string|max:200',
        ]);

        return back()->with(
            'recommendation',
            $ai->suggest($data['interest'])
        );
    }
}
