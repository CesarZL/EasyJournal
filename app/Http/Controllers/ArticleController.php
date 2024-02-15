<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{

    //Autenticar
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $articles = Article::all();
        return view('home', compact('articles'));
    }

    public function store(Request $request)
    {



        $article = new Article;
        $article->name = $request->name;
        // $article->content = $request->content;
        $article->user_id = auth()->id();
        $article->save;
        return redirect()->route('articles.index');
    }

}
