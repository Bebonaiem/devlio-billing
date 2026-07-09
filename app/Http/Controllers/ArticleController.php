<?php
namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::where('enabled', true)
            ->orderBy('order_column')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        return view('articles.index', compact('articles'));
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)
            ->where('enabled', true)
            ->firstOrFail();

        $article->increment('views');

        return view('articles.show', compact('article'));
    }
}
