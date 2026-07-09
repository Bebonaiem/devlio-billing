<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $articles = Article::query()
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('order_column')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category' => 'nullable|string|max:255',
            'order_column' => 'nullable|integer|min:0',
            'enabled' => 'boolean',
        ]);

        Article::create([
            ...$data,
            'order_column' => $data['order_column'] ?? 0,
            'enabled' => $request->boolean('enabled', true),
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article created successfully.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category' => 'nullable|string|max:255',
            'order_column' => 'nullable|integer|min:0',
            'enabled' => 'boolean',
        ]);

        $article->update([
            ...$data,
            'order_column' => $data['order_column'] ?? 0,
            'enabled' => $request->boolean('enabled', false),
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article deleted successfully.');
    }

    public function toggle(Article $article)
    {
        $article->update(['enabled' => !$article->enabled]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article status updated successfully.');
    }
}
