<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /** List categories with book totals for the librarian. */
    public function index()
    {
        $categories = Category::withCount('books')->orderBy('name')->paginate(15);

        return view('categories.index', compact('categories'));
    }

    /** Show the category creation form. */
    public function create()
    {
        return view('categories.form');
    }

    /** Validate and save a new category, then log the change. */
    public function store(Request $r, ActivityLogger $log)
    {
        $d = $r->validate(['name' => 'required|string|max:100|unique:categories,name', 'description' => 'nullable|string']);
        $c = Category::create($d);
        $log->write('CATEGORY_CREATED', $c->name);

        return redirect()->route('lib.categories.index')->with('success', 'Category created.');
    }

    /** Show the edit form for the category resolved by route model binding. */
    public function edit(Category $category)
    {
        return view('categories.form', compact('category'));
    }

    /** Validate and save category changes, then log the change. */
    public function update(Request $r, Category $category, ActivityLogger $log)
    {
        $d = $r->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string']);
        $category->update($d);
        $log->write('CATEGORY_UPDATED', $category->name);

        return redirect()->route('lib.categories.index')->with('success', 'Category updated.');
    }

    /** Prevent deleting categories that still contain books. */
    public function destroy(Category $category, ActivityLogger $log)
    {
        if ($category->books()->exists()) {
            return back()->withErrors(['category' => 'Move or delete books in this category first.']);
        }

        $name = $category->name;
        $category->delete();
        $log->write('CATEGORY_DELETED', $name);

        return back()->with('success', 'Category deleted.');
    }
}
