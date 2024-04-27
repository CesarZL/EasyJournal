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
use Gemini\Laravel\Facades\Gemini;

class ArticleController extends Controller
{

    // Función para proteger las rutas
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Función para mostrar el dashboard
    public function index()
    {
        //retorna la vista del dashboard con los articulos del usuario logueado
        $articles = Article::where('user_id', auth()->user()->id)->get();

        $title = 'Borrar artículo';
        $text = "¿Estás seguro de que quieres borrar este artículo?";
        confirmDelete($title, $text);

        //retorna la vista del dashboard con los articulos del usuario logueado
        return view('dashboard', compact('articles'));
    }

    // Función para crear un nuevo artículo desde el dashboard
    public function store(Request $request)
    {
        //validaciones de los campos del formulario
        $request->validate([
            'title' => 'required',
        ]);

        //crea un nuevo articulo y lo guarda en la base de datos
        $article = new Article;
        $article->title = $request->title;
        $article->user_id = auth()->user()->id;
        $article->save();

        // Redirige al dashboard después de crear un nuevo artículo
        return redirect()->route('dashboard')->with('success', 'Artículo creado correctamente.');
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
        // Validar los datos del formulario
        $request->validate([
            'abstract' => 'required',
            'keywords' => 'required',
            'content' => 'required',
        ]);

        // Actualiza los campos del artículo
        $article->abstract = $request->abstract;
        $article->keywords = $request->keywords;
        $article->content = $request->content;
        $article->save();

        // Parsear el contenido del artículo
        $parsed_content = json_decode($article->content, true);

        // Función para generar contenido LaTeX de lista
        function generateListTeX($items, $style) {
            $tex_content = "\\begin{itemize}\n";
            foreach ($items as $item) {
                $tex_content .= "\\item " . $item . "\n";
            }
            $tex_content .= "\\end{itemize}\n";
            return $tex_content;
        }

        // Función para generar contenido LaTeX de imagen
        function generateImageTeX($url, $caption, $article) {
            // obtener el nombre de la imagen de la url
            $url = explode("/", $url);
            $url = end($url);

            // copiar la imagen a la carpeta articles_public/id con el mismo nombre
            File::copy(public_path("images/{$url}"), public_path("articles_public/{$article->id}/{$url}"));

            // generar el contenido LaTeX de la imagen
            $tex_content = "\\begin{figure}[h]\n";
            $tex_content .= "\\centering\n";
            $tex_content .= "\\includegraphics[width=0.5\\textwidth]{" . $url . "}\n";
            $tex_content .= "\\caption{" . $caption . "}\n";
            $tex_content .= "\\end{figure}\n";
            return $tex_content;
        }

        // Función para generar contenido LaTeX de tabla
        function generateTableTeX($code) {
            return $code; // Devolver el código sin modificaciones
        }

        // Generar contenido LaTeX
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
                $my_tex_content .= $block['data']['text'] . "\\newline\n";
            } elseif ($block['type'] == 'list') {
                $my_tex_content .= generateListTeX($block['data']['items'], $block['data']['style']);
            } elseif ($block['type'] == 'image') {
                $my_tex_content .= generateImageTeX($block['data']['file']['url'], $block['data']['caption'], $article);
            }elseif ($block['type'] == 'code') {
                $my_tex_content .= generateTableTeX($block['data']['code']);
            }
        }

        //reemplazar cualquier  2&nbsp; por espacio en blanco
        $my_tex_content = str_replace("2&nbsp;", " ", $my_tex_content);

        // si no se selecciona una plantilla se crea una plantilla por defecto
        if($request->template == null){
                $tex_content = "\\documentclass{article}\n";
                $tex_content .= "\\usepackage[margin=2cm]{geometry}\n";
                $tex_content .= "\\usepackage{orcidlink}\n";
                $tex_content .= "\\usepackage{authblk}\n";
                $tex_content .= "\\usepackage[utf8]{inputenc}\n";
                $tex_content .= "\\usepackage{longtable}\n";
                $tex_content .= "\\usepackage{graphicx}\n";
                $tex_content .= "\\usepackage{subfig}\n";
                $tex_content .= "\\usepackage[backend=biber]{biblatex}\n";
                $tex_content .= "\\addbibresource{References.bib}\n";
                $tex_content .= "\\date{}\n";
                $tex_content .= "\\setcounter{Maxaffil}{0}\n";
                $tex_content .= "\\renewcommand\\Affilfont{\\itshape\\small}\n";
                $tex_content .= "\\providecommand{\\keywords}[1]\n";
                $tex_content .= "{\n";
                $tex_content .= "  \\small  \n";
                $tex_content .= "  \\textbf{\\textit{Keywords---}} #1\n";
                $tex_content .= "} \n";
                $tex_content .= "\\title{" . $article->title . "}\n";

                for ($i = 0; $i < count($article->coauthors) + 1; $i++) {
                    if ($i == 0) {
                        $tex_content .= "\\author[" . ($i + 1) . ",*]{" . auth()->user()->name . " " . auth()->user()->father_surname . " " . auth()->user()->mother_surname . " \\orcidlink{" . auth()->user()->orcid . "}} \n";
                    } else {
                        $tex_content .= "\\author[" . ($i + 1) . "]{" . $article->coauthors[$i - 1]->name . " " . $article->coauthors[$i - 1]->father_surname . " " . $article->coauthors[$i - 1]->mother_surname . " \\orcidlink{" . $article->coauthors[$i - 1]->orcid . "}}\n";
                    }
                }

                for ($i = 0; $i < count($article->coauthors) + 1; $i++) {
                    if ($i == 0) {
                        $tex_content .= "\\affil[" . ($i + 1) . "]{" . auth()->user()->affiliation . ", " . auth()->user()->institution . "}\n";
                    } else {
                        $tex_content .= "\\affil[" . ($i + 1) . "]{" . $article->coauthors[$i - 1]->affiliation . ", " . $article->coauthors[$i - 1]->institution . "}\n";
                    }
                }

                $tex_content .= "\\begin{document}\n";
                $tex_content .= "\\maketitle\n";
                $tex_content .= "\\begin{abstract}\n";
                $tex_content .= $article->abstract . "\n";
                $tex_content .= "\\end{abstract}\n";
                $tex_content .= "\\keywords{" . $article->keywords . "}\n";
                $tex_content .= $my_tex_content . "\n";
                $tex_content .= "\\printbibliography\n";
                $tex_content .= "\\end{document}\n";

                // crea la carpeta articles_public si no existe
                if (!File::exists(public_path('articles_public/' . $article->id))) {
                    File::makeDirectory(public_path('articles_public/' . $article->id), 0777, true);
                }

                // crea el archivo tex
                File::put(public_path('articles_public/' . $article->id . '/' . $article->id . '.tex'), $tex_content);

                //crear el archivo bib con el contenido del campo bib 
                File::put(public_path('articles_public/' . $article->id . '/' . 'References.bib'), $article->bib);

                // compilar el archivo tex 
                $process = new Process(['/usr/bin/pdflatex', "-output-directory=articles_public/{$article->id}", public_path('articles_public/' . $article->id . '/' . $article->id . '.tex')]);
                $process->run();

                // compilar el archivo bib
                $process2 = new Process(['/usr/bin/biber', public_path('articles_public/' . $article->id . '/' . $article->id)]);
                $process2->run();

                // compilar el archivo tex
                $process3 = new Process(['/usr/bin/pdflatex', "-output-directory=articles_public/{$article->id}", public_path('articles_public/' . $article->id . '/' . $article->id . '.tex')]);
                $process3->run();

                // compilar el archivo tex
                $process4 = new Process(['/usr/bin/pdflatex', "-output-directory=articles_public/{$article->id}", public_path('articles_public/' . $article->id . '/' . $article->id . '.tex')]);
                $process4->run();
                        
                // si alguno de los 4 procesos falla se regresa a la vista con un mensaje de error
                if (!$process->isSuccessful() || !$process2->isSuccessful() || !$process3->isSuccessful() || !$process4->isSuccessful()) {
                    // Esto se tiene que cambiar por un mensaje de error en la vista
                    // throw new ProcessFailedException($process);

                    // borrar todo dentro de la carpeta y regresar a la vista con un mensaje de error
                    File::cleanDirectory(public_path('articles_public/' . $article->id));
                    return redirect()->route('articles.edit', $article->id)->with('error', 'No se pudo generar el PDF de este artículo.');
                }

                // obtener la url del pdf generado
                $pdf_url = asset("articles_public/{$article->id}/{$article->id}.pdf");

                //mandar el url del pdf generado a la ruta de la vista
                return redirect()->route('articles.edit', $article->id)->with('pdf_url', $pdf_url);
        }else{

            // busca la plantilla seleccionada
            $template = Template::find($request->template);
            $template_path = $template->file;

            // crea la carpeta articles_public si no existe
            if (!File::exists(public_path('articles_public/' . $article->id))) {
                File::makeDirectory(public_path('articles_public/' . $article->id), 0777, true);
            }

            // extraer el contenido de la plantilla seleccionada
            $zip = new ZipArchive;
            if ($zip->open(storage_path('app/' . $template_path)) === TRUE) {
                $zip->extractTo(public_path('articles_public/' . $article->id));
                $zip->close();
            } else {
                return redirect()->route('articles.edit', $article->id)->with('error', 'No se pudo extraer la plantilla seleccionada.');
            }

            // buscar el archivo .tex con mayor tamaño
            $files = glob(public_path('articles_public/' . $article->id . '/*.tex'));
            $largestFile = '';
            $largestSize = 0;

            foreach ($files as $file) {
                $size = filesize($file);
                if ($size > $largestSize) {
                    $largestSize = $size;
                    $largestFile = $file;
                }
            }

            // renombrar el archivo .tex con el id del artículo
            $new_file_name = public_path('articles_public/' . $article->id . '/' . $article->id . '.tex');
            rename($largestFile, $new_file_name);

            // Crear el archivo bib con el contenido del campo bib
            File::put(public_path('articles_public/' . $article->id . '/' . 'References.bib'), $article->bib);

            // función para eliminar comentarios y secciones innecesarias del archivo .tex
            

            

           
        }
    }

    // Función para descargar el pdf de un artículo
    public function pdf(Article $article)
    {
        // descargar el pdf
        return response()->download(public_path("articles_public/{$article->id}/{$article->id}.pdf"));
    }

    // Función para descargar el zip de un artículo
    public function zip(Article $article)
    {
        //crea la carpeta templates_zip si no existe y si existe borra su contenido
        if (!File::exists(public_path('templates_zip'))) {
            File::makeDirectory(public_path('templates_zip'), 0777, true);
        } else {
            File::cleanDirectory(public_path('templates_zip'));
        }

        // crea el archivo zip
        $zip = new ZipArchive;
        $zip->open(public_path("templates_zip/{$article->id}.zip"), ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(public_path("articles_public/{$article->id}")), \RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $name => $file) {
            // saltar los directorios
            if (!$file->isDir()) {
                // obtener la ruta del archivo
                $filePath = $file->getRealPath();
                // obtener la ruta relativa del archivo
                $relativePath = substr($filePath, strlen(public_path("articles_public/{$article->id}")) + 1);
                // añadir el archivo al archivo zip
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        // descargar el archivo zip
        return response()->download(public_path("templates_zip/{$article->id}.zip"));
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
        $article->bib = $request->bib;
        $article->save();

        // Actualiza los coautores del artículo
        $article->coauthors()->sync($request->coauthors);

        // Redirige de vuelta a la página de detalles del artículo
        return redirect()->route('articles.edit', $article->id)->with('success', 'Detalles actualizados correctamente.');
    }

    // Función para eliminar un artículo de la base de datos
    public function destroy($id)
    {
        // Buscar el artículo por su ID
        $article = Article::find($id);
        // Eliminar el artículo de la base de datos
        $article->delete();

        // borrar la carpeta del artículo
        File::deleteDirectory(public_path('articles_public/' . $id));

        return redirect()->route('dashboard');
    }


    // Función para subir una imagen
    public function uploadImage(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Guardar la imagen en la carpeta public/images
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        // Redirigir con la ruta de la imagen
        return response()->json([
            'success' => 1,
            'file' => [
                'url' => asset('images/' . $imageName),
            ]
        ]);
    }

}
