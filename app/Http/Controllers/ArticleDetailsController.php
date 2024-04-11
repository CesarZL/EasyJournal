<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleDetailsController extends Controller
{
    public function edit($id)
    {
        // buscar el articulo por id
        $article = Article::find($id);

        return view('edit-details', ['article' => $article]);
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
