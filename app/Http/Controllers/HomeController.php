<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->id();
        $articles = Article::where('user_id', $user)->get();
        return view('home', compact('articles'));
    }

    public function store()
    {
        $article = new Article;
        $article->name = request('name');
        $article->content = request('content');
        $article->user_id = auth()->id();
        $article->save();
        return redirect()->route('home');
    }

    public function destroy($id)
    {
        // destroy article by id
        $article = Article::find($id);
        $article->delete();
        return redirect()->route('home');

    }
}
