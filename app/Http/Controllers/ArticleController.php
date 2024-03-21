<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
        // regresa la vista de edición del artículo con el id del artículo
        return view('edit-article', compact('article'));
    }
    
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'content' => 'required',
        ]);

        

        // Reemplazar &nbsp; con espacios
        $article->content = str_replace('&nbsp;', ' ', $article->content);

        // Parsear el contenido JSON
        $contentData = json_decode($article->content);

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
                                // Otras profundidades no soportadas, puede agregar lógica adicional según sea necesario
                                break;
                        }
                        break;
                    case 'paragraph':
                        $tex .= str_replace('_', '\_', $block->data->text) . PHP_EOL;
                        break;
                    default:
                        // No hacer nada o manejar otros tipos de bloques según sea necesario
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
        $texContent .= "\\title{" . $article->title . "}\n";
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


        // $texContent .= "\\begin{document}\n";
        // $texContent .= $content_to_tex($contentData->blocks);
        // $texContent .= "\\end{document}";

    // Guardar contenido como archivo .tex
    $texFilePath = public_path("articles/{$article->id}.tex");
    file_put_contents($texFilePath, $texContent);

    // Convertir .tex a .pdf
    $process = new Process(['C:\Users\cesar\AppData\Local\Programs\MiKTeX\miktex\bin\x64\pdflatex.exe', "-output-directory=articles", $texFilePath]);
    $process->run();

    // Obtener la URL del PDF generado
    $pdfUrl = asset("articles/{$article->id}.pdf");

    // Actualizar el contenido del artículo si es necesario
    $article->content = $request->content;
    $article->save();

    return response()->json(['message' => 'El artículo se ha actualizado correctamente', 'pdf_url' => $pdfUrl]);
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
