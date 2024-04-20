<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Template;

class ArticleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //retorna la vista del dashboard con los articulos del usuario logueado
        $articles = Article::where('user_id', auth()->user()->id)->get();
        return view('dashboard', compact('articles'));
    }

    public function store(Request $request)
    {
        //validaciones
        $request->validate([
            'title' => 'required',
        ]);

        //crea un nuevo articulo y lo guarda en la base de datos
        $article = new Article;
        $article->title = $request->title;
        $article->user_id = auth()->user()->id;
        $article->save();

        return redirect()->route('dashboard');
    }

    public function edit(Article $article)
    {
        // Pasa el contenido del campo 'content' a la vista
        $content = $article->content;

        //pasa los datos de las templates a la vista
        $templates = Template::where('user_id', auth()->user()->id)->get();
        
        return view('edit-article', compact('article', 'content', 'templates'));
        
    }

    public function editDetails(Article $article)
    {
        // Pasa el contenido del campo 'content' a la vista
        $content = $article->content;

        dd($article);
        
        // return view('edit-article-details', compact('article', 'content'));
    }
    
    

    
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'abstract' => 'required',
            'keywords' => 'required',
            'content' => 'required',
        ]);

        $article->abstract = $request->abstract;
        $article->keywords = $request->keywords;
        $article->content = $request->content;
        
        $article->save();

        $articleId = $article->id;

        // Leer el artículo más reciente por ID
        $latestArticle = Article::find($articleId);

        // Reemplazar &nbsp; con espacios
        $latestArticle->content = str_replace('&nbsp;', ' ', $latestArticle->content);

        // Parsear el contenido JSON
        $contentData = json_decode($latestArticle->content);

        // Función para convertir el contenido a formato tex
        $content_to_tex = function ($blocks, $depth = 1) use (&$content_to_tex) {
            $tex = '';
            foreach ($blocks as $block) {
                switch ($block->type) {
                    case 'header':
                        $headerText = str_replace('_', '\_', $block->data->text); // Escapar guiones bajos
                        switch ($block->data->level) {
                            case 1:
                                $tex .= '\section{' . $headerText . '}' . PHP_EOL;
                                break;
                            case 2:
                                $tex .= '\subsection{' . $headerText . '}' . PHP_EOL;
                                break;
                            case 3:
                                $tex .= '\subsubsection{' . $headerText . '}' . PHP_EOL;
                                break;
                            default:
                                break;
                        }
                        break;
                    case 'paragraph':
                        $tex .= str_replace('_', '\_', $block->data->text) . PHP_EOL;
                        break;
                    default:
                        break;
                }
                if (isset($block->data->children)) {
                    $tex .= $content_to_tex($block->data->children, $depth + 1);
                }
            }
            return $tex;
        };

        // Convertir contenido a formato tex
        $texContent = "\\documentclass{article}\n";
        $texContent .= "\\usepackage[margin=2cm]{geometry}\n";
        $texContent .= "\\usepackage{orcidlink}\n";
        $texContent .= "\\usepackage{authblk}\n";
        $texContent .= "\\usepackage[utf8]{inputenc}\n";
        $texContent .= "\\usepackage{longtable}\n";
        $texContent .= "\\usepackage{graphicx}\n";
        $texContent .= "\\usepackage{subfig}\n";
        $texContent .= "\\date{}\n";
        $texContent .= "\\setcounter{Maxaffil}{0}\n";
        $texContent .= "\\renewcommand\\Affilfont{\\itshape\\small}\n";
        $texContent .= "\\providecommand{\\keywords}[1]\n";
        $texContent .= "{\n";
        $texContent .= "  \\small  \n";
        $texContent .= "  \\textbf{\\textit{Keywords---}} #1\n";
        $texContent .= "} \n";
        $texContent .= "\\title{" . $latestArticle->title . "}\n";
        $texContent .= "\\author[1,*]{" . auth()->user()->name . " \\orcidlink{0000-1111-1111-2222}}\n";
        $texContent .= "\\affil[1]{Universidad Politécnica de victoria}\n";
        $texContent .= "\\begin{document}\n";
        $texContent .= "\\maketitle\n";
        $texContent .= "\\begin{abstract}\n";
        $texContent .= "This is the abstract of the article.\n";
        $texContent .= "\\end{abstract}\n";
        $texContent .= "\\keywords{keyword1, keyword2, keyword3}\n";
        $texContent .= $content_to_tex($contentData->blocks);
        $texContent .= "\\end{document}";

        // Crear la carpeta articles_storage si no existe
        $articlesStoragePath = public_path('articles_storage');
        if (!file_exists($articlesStoragePath)) {
            mkdir($articlesStoragePath, 0777, true);
        }

        // Guardar contenido como archivo .tex
        $texFilePath = public_path("articles_storage/{$latestArticle->id}.tex");
        file_put_contents($texFilePath, $texContent);

        $process = new Process(['C:\Users\cesar\AppData\Local\Programs\MiKTeX\miktex\bin\x64\pdflatex.exe', "-output-directory=articles_storage", $texFilePath]);
        // $process = new Process(['/usr/bin/pdflatex', '-output-directory=articles_storage', $texFilePath]);

        $process->run();

        if ($process->isSuccessful()) {
            // Obtener la URL del PDF generado
            $pdfUrl = asset("articles_storage/{$article->id}.pdf");
            return response()->json(['message' => 'El artículo se ha actualizado correctamente', 'pdf_url' => $pdfUrl]);
        } else {
            // Eliminar el archivo .tex si la compilación falla
            unlink($texFilePath);
            return response()->json(['message' => 'Error al compilar el archivo .tex a PDF']);
        }
    }

    

    public function destroy($id)
    {
        // Buscar el artículo por su ID
        $article = Article::find($id);
        // Eliminar el artículo de la base de datos
        $article->delete();

        return redirect()->route('dashboard');
    }
}
