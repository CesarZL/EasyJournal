<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Coauthor;

class ArticleDetailsController extends Controller
{
    public function edit($id)
    {
        // buscar el articulo por id
        $article = Article::find($id);

        // encontrar todos los coautores que agregó el usuario
        $coauthors = Coauthor::where('created_by', auth()->user()->id)->get();

        return view('edit-details', ['article' => $article], ['coauthors' => $coauthors]);
    }

    public function update(Request $request, $article)
    {
        $article->update([
            'title' => $request->title,
            'content' => $request->content
        ]);

        return redirect()->route('dashboard');
    }
}
