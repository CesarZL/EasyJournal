<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Template;
use App\Models\Coauthor;
use Illuminate\Support\Facades\File;
use ZipArchive;




class ArticleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    // Función para mostrar el dashboard
    public function index()
    {
        //retorna la vista del dashboard con los articulos del usuario logueado
        $articles = Article::where('user_id', auth()->user()->id)->get();

        //retorna la vista del dashboard con los articulos del usuario logueado
        return view('dashboard', compact('articles'));
    }

    // Función para crear un nuevo artículo desde el dashboard
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

        // Redirige al dashboard después de crear un nuevo artículo
        return redirect()->route('dashboard');
    }

    // Función para mostrar la vista de edición de un artículo
    public function edit(Article $article)
    {
        // Pasa el contenido del campo 'content' a la vista
        $content = $article->content;

        //pasa los datos de las templates a la vista, esto para ver en el select las plantillas que tiene el usuario
        $templates = Template::where('user_id', auth()->user()->id)->get();
        
        // Retorna la vista 'edit-article' con los datos del artículo 
        return view('edit-article', compact('article', 'content', 'templates'));
        
    }
    
    // Función para actualizar un artículo y renderizar el pdf con el contenido actualizado
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

        if($request->template == null){

            $parsed_content = json_decode($article->content, true);
                
                // darle formato de \section al header, \subsection al header de otro nivel y \subsubsection al header de otro nivel y cada parrafo se le da formato será el contenido de su respectivo header
                $my_tex_content = '';
                foreach ($parsed_content['blocks'] as $block) {
                    if ($block['type'] == 'header') {
                        if ($block['data']['level'] == 1) {
                            $my_tex_content .= "\\section{" . $block['data']['text'] . "}\n";
                        } elseif ($block['data']['level'] == 2) {
                            $my_tex_content .= "\\subsection{" . $block['data']['text'] . "}\n";
                        } elseif ($block['data']['level'] == 3) {
                            $my_tex_content .= "\\subsubsection{" . $block['data']['text'] . "}\n";
                        }
                    } elseif ($block['type'] == 'paragraph') {
                        $my_tex_content .= $block['data']['text'] . "\n";
                    }
                }



                // \documentclass{article}
                // \usepackage[margin=2cm]{geometry}
                // \usepackage{orcidlink}
                // \usepackage{authblk}
                // \usepackage[utf8]{inputenc}
                // \usepackage{longtable}
                // \usepackage{graphicx}
                // \usepackage{subfig}

                // \date{}                     
                // \setcounter{Maxaffil}{0}
                // \renewcommand\Affilfont{\itshape\small}
                // \providecommand{\keywords}[1]
                // {
                //   \small  
                //   \textbf{\textit{Keywords---}} #1
                // } 

                // \title{Articulo de Prueba}
                // \author[1,*]{Marco Aurelio Nuno-Maganda \orcidlink{0000-1111-1111-2222}}
                // \author[2]{John Doe \orcidlink{0000-3333-4444-5555}}
                // \author[3]{Jane Smith \orcidlink{0000-6666-7777-8888}}
                // \affil[1]{Universidad Politécnica de victoria}
                // \affil[2]{University of XYZ}
                // \affil[3]{ABC Institute}

                // \begin{document}

                // \maketitle

                // \begin{abstract}
                // Este es un dummy de un abstract 
                // \end{abstract}

                // \keywords{palabra1, palabra2 , palabra3, palabra4}

                // \section{Seccion 1}
                // texto de sección 1
                // \section{Seccion2}
                // texto de seccion 2
                // \subsection{Subseccion1\_de\_seccion\_2}
                // texto de subseccion 1 de seccion 2,     
                // \subsection{ Subseccion2\_de\_seccion\_2}
                // texto de Subseccion2\_de\_seccion\_2
                // \subsubsection{Subsubseccion1\_deseccion2}
                // texto de la subsubseccion
                // \section{Seccion3}
                // texto de seccion 3

                // \end{document}

           

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
                $texContent .= "\\author[1,*]{" . auth()->user()->name . " " .  auth()->user()->lastname . " " . auth()->user()->surname . " \\orcidlink{" . auth()->user()->orcid . "}}\n";

                // si hay coautores agregarlos
                if ($article->coauthors->count() > 0) {
                    $i = 2;
                    foreach ($article->coauthors as $coauthor) {
                        $texContent .= "\\author[" . $i . "]{" . $coauthor->name . " " . $coauthor->surname . " " . $coauthor->last_name . " \\orcidlink{" . $coauthor->orcid . "}}\n";
                        $i++;
                    }
                }

                // agregar la afiliacion del autor principal


                $texContent .= "\\affil[1]{" . auth()->user()->affiliation . "}\n";

                // si hay coautores agregar sus afiliaciones
                if ($article->coauthors->count() > 0) {
                    $i = 2;
                    foreach ($article->coauthors as $coauthor) {
                        $texContent .= "\\affil[" . $i . "]{" . $coauthor->affiliation . "}\n";
                        $i++;
                    }
                }
                $texContent .= "\\begin{document}\n";
                $texContent .= "\\maketitle\n";
                $texContent .= "\\begin{abstract}\n";
                $texContent .= $article->abstract . "\n";
                $texContent .= "\\end{abstract}\n";
                $texContent .= "\\keywords{" . $article->keywords . "}\n";
                $texContent .= $my_tex_content;
                $texContent .= "\\end{document}";

                // save the tex content to a file
                // $texFile = public_path('templates_public/' . $article->id . '/article_test.tex');
                // file_put_contents($texFile, $texContent);

                // crea la carpeta templates_public si no existe
                if (!File::exists(public_path('templates_public/' . $article->id))) {
                    File::makeDirectory(public_path('templates_public/' . $article->id), 0777, true);
                }else{
                    // borrar todo dentro de la carpeta
                    File::cleanDirectory(public_path('templates_public/' . $article->id));
                }

                // save the tex content to a file
                $texFile = public_path('templates_public/' . $article->id . '/article_test.tex');
                file_put_contents($texFile, $texContent);
                

                // ejecutar el comando pdflatex para compilar el archivo .tex
                $process = new Process(['C:\Users\cesar\AppData\Local\Programs\MiKTeX\miktex\bin\x64\pdflatex.exe', "-output-directory=templates_public/{$article->id}", $texFile]);
                $process->run();

                // verificar si hubo un error al compilar el archivo .tex
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }

                // obtener la url del pdf generado
                $pdfUrl = asset("templates_public/{$article->id}/article_test.pdf");
                
                //mandar el url del pdf generado a la ruta de la vista
                return redirect()->route('articles.edit', $article->id)->with('pdf_url', $pdfUrl);




        }
        else{

            // busca la plantilla seleccionada 
            $template = Template::find($request->template);
            $template_path = $template->file;

            // Verifica si la carpeta ya existe
            if (!File::exists(public_path('templates_public/' . $article->id))) {
                File::makeDirectory(public_path('templates_public/' . $article->id), 0777, true);
            } else {

                // borrar todo dentro de la carpeta
                File::cleanDirectory(public_path('templates_public/' . $article->id));

                // extraer el contenido de la plantilla seleccionada
                $zip = new ZipArchive;
                if ($zip->open(storage_path('app/' . $template_path)) === TRUE) {
                    $zip->extractTo(public_path('templates_public/' . $article->id));
                    $zip->close();
                } else {
                    dd('No se pudo abrir el archivo ZIP');
                }

                // seleccionar el archivo con extension .tex más pesado

                $files = glob(public_path('templates_public/' . $article->id . '/*.tex'));
                $largestFile = '';
                $largestSize = 0;

                foreach ($files as $file) {
                    $size = filesize($file);
                    if ($size > $largestSize) {
                        $largestSize = $size;
                        $largestFile = $file;
                    }
                }

                // leer el contenido del archivo .tex
                $tex_content = file_get_contents($largestFile);

                // dd($tex_content);

                // $process = new Process(['C:\Users\cesar\AppData\Local\Programs\MiKTeX\miktex\bin\x64\pdflatex.exe', "-output-directory=templates/{$article->id}", $largestFile]);

                // $process->run();




                // parsear el contenido de mi articulo dando gerarquia a los titulos y subtitulos y contenido 
                // {"time":1713520460255,"blocks":[{"id":"V-Zt13mzz0","type":"header","data":{"text":"Primera seccion","level":1}},{"id":"zewtcGBMfZ","type":"paragraph","data":{"text":"contenido de la primera seccion este texto dummy"}},{"id":"nng-f_dR2U","type":"header","data":{"text":"Esta es una subseccion de la primera seccion","level":2}},{"id":"7JmugxmE03","type":"paragraph","data":{"text":"Este es texto de la primera subseccion de la seccion 1"}},{"id":"FsQXYWqVDH","type":"header","data":{"text":"Y esta es otra seccionç","level":1}},{"id":"_5642d_gy0","type":"header","data":{"text":"Esta es una subseccion inmediatamente de la segunda seccion","level":1}},{"id":"xpnQpYX1Kv","type":"header","data":{"text":"Y esta es una sub sub seccion de la segunda seccion","level":3}},{"id":"glBEnHHNcj","type":"paragraph","data":{"text":"Y este es el texto de la sub sub seccion y tambien es un dummy"}}],"version":"2.29.0"}

                $parsed_content = json_decode($article->content, true);
                
                // darle formato de \section al header, \subsection al header de otro nivel y \subsubsection al header de otro nivel y cada parrafo se le da formato será el contenido de su respectivo header
                $my_tex_content = '';
                foreach ($parsed_content['blocks'] as $block) {
                    if ($block['type'] == 'header') {
                        if ($block['data']['level'] == 1) {
                            $my_tex_content .= "\\section{" . $block['data']['text'] . "}\n";
                        } elseif ($block['data']['level'] == 2) {
                            $my_tex_content .= "\\subsection{" . $block['data']['text'] . "}\n";
                        } elseif ($block['data']['level'] == 3) {
                            $my_tex_content .= "\\subsubsection{" . $block['data']['text'] . "}\n";
                        }
                    } elseif ($block['type'] == 'paragraph') {
                        $my_tex_content .= $block['data']['text'] . "\n";
                    }
                }

                dd($my_tex_content);

                
                
                
            
            }
        }
        // regresa la vista de edicion del articulo
        return redirect()->route('articles.edit', $article->id);
    }

    // Función para mostrar la vista de edición de detalles de un artículo
    public function edit_details($id)
    {
        // buscar el articulo por id
        $article = Article::find($id);

        // encontrar todos los coautores que agregó el usuario
        $coauthors = Coauthor::where('created_by', auth()->user()->id)->get();

        return view('edit-details', ['article' => $article], ['coauthors' => $coauthors]);
    }


    //Aqui debe de ir la funcion para actualizar los detalles del articulo y añadir o quitar coautores de un articulo en especifico si se desea
    // Función para actualizar los detalles del artículo y añadir/quitar coautores
    public function updateDetails(Request $request, Article $article)
    {
        // Validar los datos del formulario
        $request->validate([
            'title' => 'required',
            'coauthors' => 'array', // Asegura que coauthors sea un arreglo
        ]);

        // Actualiza el título del artículo
        $article->title = $request->title;
        $article->save();

        // Actualiza los coautores del artículo
        $article->coauthors()->sync($request->coauthors);


        // Redirige de vuelta a la página de detalles del artículo
        return redirect()->route('articles.edit', $article->id);
    }




    
    // Función para eliminar un artículo de la base de datos
    public function destroy($id)
    {
        // Buscar el artículo por su ID
        $article = Article::find($id);
        // Eliminar el artículo de la base de datos
        $article->delete();

        return redirect()->route('dashboard');
    }
}
