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

            // copiar la imagen a la carpeta del artículo
            File::copy(public_path("images/{$url}"), public_path('articles_public/' . $article->id . '/' . $url));

            // generar el contenido LaTeX de la imagen
            $tex_content = "\\begin{figure}[h]\n";
            $tex_content .= "\\centering\n";
            $tex_content .= "\\includegraphics[width=0.5\\textwidth]{" . $url . "}\n";
            $tex_content .= "\\caption{" . $caption . "}\n";
            $tex_content .= "\\label{fig:" . $url . "}\n";
            $tex_content .= "\\end{figure}\n";
            return $tex_content;
        }

        // Función para generar contenido LaTeX de tabla
        function generateTableTeX($code) {
            return $code; // Devolver el código sin modificaciones
        }

        // Parsear el contenido del artículo
        $parsed_content = json_decode($article->content, true);

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

                // crear el archivo bib con el contenido del campo bib 
                File::put(public_path('articles_public/' . $article->id . '/' . 'References.bib'), $article->bib);

                // borrar el archivo pdf si existe en la carpeta del artículo
                if (file_exists(public_path("articles_public/{$article->id}/{$article->id}.pdf"))) {
                    File::delete(public_path("articles_public/{$article->id}/{$article->id}.pdf"));
                }

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
                
                // verificar si se generó el archivo pdf
                if (file_exists(public_path("articles_public/{$article->id}/{$article->id}.pdf"))) {
                    // mandar el url del pdf generado a la ruta de la vista
                    $pdf_url = asset("articles_public/{$article->id}/{$article->id}.pdf");
                    return redirect()->route('articles.edit', $article->id)->with('pdf_url', $pdf_url);
                }else{
                    // borrar todo dentro de la carpeta y regresar a la vista con un mensaje de error
                    File::cleanDirectory(public_path('articles_public/' . $article->id));
                    return redirect()->route('articles.edit', $article->id)->with('error', 'No se pudo generar el PDF de este artículo.');
                }
                        
        }else{

            // busca la plantilla seleccionada
            $template = Template::find($request->template);
            $template_path = $template->file;

            // crea la carpeta articles_public si no existe
            if (!File::exists(public_path('articles_public/' . $article->id))) {
                File::makeDirectory(public_path('articles_public/' . $article->id), 0777, true);
            }else{
                // borrar todo el contenido de la carpeta articles_public/id/
                File::cleanDirectory(public_path('articles_public/' . $article->id));
            }
            
            // extraer el contenido de la plantilla seleccionada
            $zip = new ZipArchive;
            if ($zip->open(storage_path('app/' . $template_path)) === TRUE) {
                $zip->extractTo(public_path('articles_public/' . $article->id));
                $zip->close();
            } else {
                return redirect()->route('articles.edit', $article->id)->with('error', 'No se pudo extraer la plantilla seleccionada.');
            }

            // ya no es necesario buscar el archivo .tex con mayor tamaño, ya sabemos que trabajaremos con el que se llama main.tex
            $MainFile = public_path('articles_public/' . $article->id . '/main.tex');            

            // renombrar el archivo .tex con el id del artículo
            $new_file_name = public_path('articles_public/' . $article->id . '/' . $article->id . '.tex');
            rename($MainFile, $new_file_name);
            
            // función para quitar los comentarios del archivo .tex
            function remove_tex_comments($file)
            {
                $content = file_get_contents($file);
                $lines = explode("\n", $content);
                $fp = fopen($file, 'w');
            
                foreach ($lines as $line) {
                    $clean_line = preg_replace('/%.*$/', '', $line); // Eliminar comentarios
                    if (trim($clean_line) !== '') {
                        fwrite($fp, $clean_line . "\n");
                    }
                }
            
                fclose($fp);
            }            

            // remover el conteido dentro de \begin{abstract} y \end{abstract} o \abstract{ hasta el cierra llaves } y sustituir el contenido por la palabra AQUIVAELABSTRACT
            function remove_tex_abstract($file)
            {
                $content = file_get_contents($file);
                $abstract_start = strpos($content, '\begin{abstract}'); // Busca la primera ocurrencia de \begin{abstract}
                $abstract_end = strpos($content, '\end{abstract}'); // Busca la primera ocurrencia de \end{abstract}
                $abstract_command_start = strpos($content, '\abstract{'); // Busca la primera ocurrencia de \abstract{
                
                // Determina el tipo de comando de abstract
                if ($abstract_start !== false && $abstract_end !== false) {
                    // Si es \begin{abstract} y \end{abstract}
                    $abstract_content = substr($content, $abstract_start + strlen('\begin{abstract}'), $abstract_end - $abstract_start - strlen('\begin{abstract}'));
                    $new_content = substr($content, 0, $abstract_start + strlen('\begin{abstract}')) . 'AQUIVAELABSTRACT' . substr($content, $abstract_end);
                } elseif ($abstract_command_start !== false) {
                    // Si es \abstract{}
                    $abstract_end_brace = strpos($content, '}', $abstract_command_start); // Busca el cierre de llaves
                    if ($abstract_end_brace !== false) {
                        $abstract_content = substr($content, $abstract_command_start + strlen('\abstract{'), $abstract_end_brace - $abstract_command_start - strlen('\abstract{'));
                        $new_content = substr($content, 0, $abstract_command_start + strlen('\abstract{')) . 'AQUIVAELABSTRACT' . '}' . substr($content, $abstract_end_brace + 1);
                    }
                } else {
                    // No se encontró ningún comando de abstract, no se hace nada
                    $new_content = $content;
                    return;
                }
            
                // Reemplaza el contenido del abstract con 'AQUIVAELABSTRACT'
                $new_content = str_replace($abstract_content, 'AQUIVAELABSTRACT', $new_content);
                
                file_put_contents($file, $new_content);
            }

             // remover el conteido dentro de \begin{keywords} y \end{keywords} o \begin{keyword} y \end{keyword} o \begin{IEEEkeywords} y \end{IEEEkeywords} o \keywords{ hasta el cierra llaves } y sustituir el contenido por la palabra AQUIVANLASKEYWORDS
             function remove_tex_keywords($file){
                $content = file_get_contents($file);
                $keywords_start = strpos($content, '\begin{keywords}'); // Busca la primera ocurrencia de \begin{keywords}
                $keywords_end = strpos($content, '\end{keywords}'); // Busca la primera ocurrencia de \end{keywords}
                $keyword_start = strpos($content, '\begin{keyword}'); // Busca la primera ocurrencia de \begin{keyword}
                $keyword_end = strpos($content, '\end{keyword}'); // Busca la primera ocurrencia de \end{keyword}
                $IEEEkeywords_start = strpos($content, '\begin{IEEEkeywords}'); // Busca la primera ocurrencia de \begin{IEEEkeywords}
                $IEEEkeywords_end = strpos($content, '\end{IEEEkeywords}'); // Busca la primera ocurrencia de \end{IEEEkeywords}
                $keywords_command_start = strpos($content, '\keywords{'); // Busca la primera ocurrencia de \keywords{
                $keyword_command_start = strpos($content, '\keyword{'); // Busca la primera ocurrencia de \keyword{
            
                // Determina el tipo de comando de keywords
                if ($keywords_start !== false && $keywords_end !== false) {
                    // Si es \begin{keywords} y \end{keywords}
                    $keywords_content = substr($content, $keywords_start + strlen('\begin{keywords}'), $keywords_end - $keywords_start - strlen('\begin{keywords}'));
                    $new_content = substr($content, 0, $keywords_start + strlen('\begin{keywords}')) . 'AQUIVANLASKEYWORDS' . substr($content, $keywords_end);
                } elseif ($keyword_start !== false && $keyword_end !== false) {
                    // Si es \begin{keyword} y \end{keyword}
                    $keywords_content = substr($content, $keyword_start + strlen('\begin{keyword}'), $keyword_end - $keyword_start - strlen('\begin{keyword}'));
                    $new_content = substr($content, 0, $keyword_start + strlen('\begin{keyword}')) . 'AQUIVANLASKEYWORDS' . substr($content, $keyword_end);
                } elseif ($IEEEkeywords_start !== false && $IEEEkeywords_end !== false) {
                    // Si es \begin{IEEEkeywords} y \end{IEEEkeywords}
                    $keywords_content = substr($content, $IEEEkeywords_start + strlen('\begin{IEEEkeywords}'), $IEEEkeywords_end - $IEEEkeywords_start - strlen('\begin{IEEEkeywords}'));
                    $new_content = substr($content, 0, $IEEEkeywords_start + strlen('\begin{IEEEkeywords}')) . 'AQUIVANLASKEYWORDS' . substr($content, $IEEEkeywords_end);
                } elseif ($keywords_command_start !== false) {
                    // Si es \keywords{}
                    $keywords_end_brace = strpos($content, '}', $keywords_command_start); // Busca el cierre de llaves
                    if ($keywords_end_brace !== false) {
                        $keywords_content = substr($content, $keywords_command_start + strlen('\keywords{'), $keywords_end_brace - $keywords_command_start - strlen('\keywords{'));
                        $new_content = substr($content, 0, $keywords_command_start + strlen('\keywords{')) . 'AQUIVANLASKEYWORDS' . '}' . substr($content, $keywords_end_brace + 1);
                    }
                } elseif ($keyword_command_start !== false) {
                    // Si es \keyword{}
                    $keyword_end_brace = strpos($content, '}', $keyword_command_start); // Busca el cierre de llaves
                    if ($keyword_end_brace !== false) {
                        $keywords_content = substr($content, $keyword_command_start + strlen('\keyword{'), $keyword_end_brace - $keyword_command_start - strlen('\keyword{'));
                        $new_content = substr($content, 0, $keyword_command_start + strlen('\keyword{')) . 'AQUIVANLASKEYWORDS' . '}' . substr($content, $keyword_end_brace + 1);
                    }
                } else {
                    // No se encontró ningún comando de keywords, no se hace nada
                    $new_content = $content;
                    return;
                }
            
                // Reemplaza el contenido de keywords con 'AQUIVANLASKEYWORDS'
                $new_content = str_replace($keywords_content, 'AQUIVANLASKEYWORDS', $new_content);
            
                file_put_contents($file, $new_content);
            }
            
            function remove_tex_content($file)
            {
                $content = file_get_contents($file);
                $section_start = strpos($content, '\section'); // Busca la primera ocurrencia de \section
                $document_end = strrpos($content, '\end{document}'); // Busca la última ocurrencia de \end{document}
                $special_considerations = strpos($content, '\EOD'); // Busca la primera ocurrencia de \EOD
                
                if ($section_start !== false && $document_end !== false) {
                    if ($special_considerations !== false) {
                        $new_content = substr($content, 0, $section_start) . '\section{FUNDATION}' . "\n" . '\EOD' . "\n" . substr($content, $document_end);
                    } else {
                        $new_content = substr($content, 0, $section_start) . '\section{FUNDATION}' . "\n" . substr($content, $document_end);
                    }
                    file_put_contents($file, $new_content);
                }

            }

            function change_tex_title($file, $title)
            {
                // Obtener el contenido del archivo
                $content = File::get($file);
            
                // Buscar la posición del comando \title o \Title
                $title_commands = ['\title', '\Title'];
                $title_command_pos = false;
                foreach ($title_commands as $command) {
                    $title_command_pos = strpos($content, $command);
                    if ($title_command_pos !== false) {
                        break;
                    }
                }
            
                if ($title_command_pos !== false) {
                    // Buscar la posición de la llave de apertura '{'
                    $brace_open_pos = strpos($content, '{', $title_command_pos);
            
                    // Buscar la posición de la llave de cierre '}' correspondiente
                    $brace_close_pos = false;
                    $brace_count = 0;
                    for ($i = $brace_open_pos + 1; $i < strlen($content); $i++) {
                        if ($content[$i] == '{') {
                            $brace_count++;
                        } elseif ($content[$i] == '}') {
                            if ($brace_count == 0) {
                                $brace_close_pos = $i;
                                break;
                            } else {
                                $brace_count--;
                            }
                        }
                    }
            
                    // Si se encontró la llave de cierre, reemplazar el contenido del título
                    if ($brace_close_pos !== false) {
                        $content = substr_replace($content, $title, $brace_open_pos + 1, $brace_close_pos - $brace_open_pos - 1);
                    }
                }
            
                // Buscar la posición del comando \TitleCitation
                $title_citation_command = '\TitleCitation{';
                $title_citation_command_pos = strpos($content, $title_citation_command);
                if ($title_citation_command_pos !== false) {
                    // Encontrar la posición del cierre del corchete
                    $bracket_end_pos = strpos($content, '}', $title_citation_command_pos);
                    // Insertar el título dentro del comando \TitleCitation{}
                    $content = substr_replace($content, $title, $title_citation_command_pos + strlen($title_citation_command), 0);
                }
            
                // Guardar el contenido modificado en el archivo
                File::put($file, $content);
            }

            // Eliminar \tfootnote{Y todo lo que este dentro de las llaves} si es que existe
            function remove_tex_footnote($file)
            {
                $content = file_get_contents($file);
                $footnote_start = strpos($content, '\tfootnote{'); // Busca la primera ocurrencia de \tfootnote{
                $footnote_end = strpos($content, '}', $footnote_start); // Busca la primera ocurrencia de }
                
                if ($footnote_start !== false && $footnote_end !== false) {
                    $new_content = substr($content, 0, $footnote_start) . substr($content, $footnote_end + 1);
                    file_put_contents($file, $new_content);
                }
            }

            // funcion para mandar el contenido a gemini
            function send_to_gemini($file, $article){

                $content = file_get_contents($file);

                if (count($article->coauthors) > 1) {

                    $prompt = "Your purpose is to fill in sections of latex files with the information I will provide you, first, you are going to fill the author information, coauthors and affiliations and all those information that is needed, if you don't have the information, you must leave it blank, if you have extra data from the author or coauthors but isnt requiere in the template you must leave it blank, if the authors or coauthors share the same affiliation and institution then they can share the same number of affiliation and institution, if they don't share the same affiliation and institution then they need to have different numbers of affiliation and institution. You are going to fill that information with this data: \n\nThis is the principal author information: \n\nName: " . (auth()->user()->name ? auth()->user()->name . " " : "") . (auth()->user()->father_surname ? auth()->user()->father_surname . " " : "") . (auth()->user()->mother_surname ? auth()->user()->mother_surname : "") . "\nORCID: " . (auth()->user()->orcid ? auth()->user()->orcid : "") . "\nAffiliation: " . (auth()->user()->affiliation ? auth()->user()->affiliation : "") . "\nInstitution: " . (auth()->user()->institution ? auth()->user()->institution : "") . "\nInstitution address: " . (auth()->user()->institution_address ? auth()->user()->institution_address : "") . "\nEmail: " . (auth()->user()->email ? auth()->user()->email : "") . "\n\nAnd this is the coauthor's information: \n\n";
                    for ($i = 0; $i < count($article->coauthors); $i++) {
                        $prompt .= "Name: " . $article->coauthors[$i]->name . " " . $article->coauthors[$i]->father_surname . " " . $article->coauthors[$i]->mother_surname . "\nORCID: " . $article->coauthors[$i]->orcid . "\nAffiliation: " . $article->coauthors[$i]->affiliation . "\nInstitution: " . $article->coauthors[$i]->institution . "\nEmail: " . $article->coauthors[$i]->email . "\n\n";
                    }
                    $prompt .= "\n\nThe date of the article is: " . date('Y-m-d') . "\n\nAfter you fill the author information, you're going to give me the updated latex file ready to compile without any explanation, just the code. The latex without the author information is in the file " . $content . ".\n\n";
    
                }elseif(count($article->coauthors) == 1) {

                    $prompt = "Your purpose is to fill in sections of latex files with the information I will provide you, first, you are going to fill the author information, coauthor and affiliations and all those information that is needed, if you don't have the information, you must leave it blank, if you have extra data from the author or coauthor but isnt requiere in the template you must leave it blank, if the author or coauthor share the same affiliation and institution then they can share the same number of affiliation and institution, if they don't share the same affiliation and institution then they need to have different numbers of affiliation and institution. You are going to fill that information with this data: \n\nThis is the principal author information: \n\nName: " . (auth()->user()->name ? auth()->user()->name . " " : "") . (auth()->user()->father_surname ? auth()->user()->father_surname . " " : "") . (auth()->user()->mother_surname ? auth()->user()->mother_surname : "") . "\nORCID: " . (auth()->user()->orcid ? auth()->user()->orcid : "") . "\nAffiliation: " . (auth()->user()->affiliation ? auth()->user()->affiliation : "") . "\nInstitution: " . (auth()->user()->institution ? auth()->user()->institution : "") . "\nInstitution address: " . (auth()->user()->institution_address ? auth()->user()->institution_address : "") . "\nEmail: " . (auth()->user()->email ? auth()->user()->email : "") . "\n\n. Don't try to put a third author in the data there are just 2, author and coauthor. This is the one and only coauthor information: \n\n";
                    for ($i = 0; $i < count($article->coauthors); $i++) {
                        $prompt .= "Name: " . $article->coauthors[$i]->name . " " . $article->coauthors[$i]->father_surname . " " . $article->coauthors[$i]->mother_surname . "\nORCID: " . $article->coauthors[$i]->orcid . "\nAffiliation: " . $article->coauthors[$i]->affiliation . "\nInstitution: " . $article->coauthors[$i]->institution . "\nEmail: " . $article->coauthors[$i]->email . "\n\n And that's it, you must delete the other dummy author or coauthor in the latex file and put only the author and coauthor that i am given to you. \n\n";
                    }
                    $prompt .= "\n\nThe date of the article is: " . date('Y-m-d') . "\n\nAfter you fill the author information, you're going to give me the updated latex file ready to compile without any explanation, just the code. The latex without the author information is in the file " . $content . ".\n\n";

                } else {
                    $prompt = "Your purpose is to fill in sections of latex files with the information I will provide you, first, you are going to fill the author information and all those information that is needed, if you don't have the information, you must leave it blank, if you have extra data from the author but isnt requiere in the template you must leave it blank, if there is just one author you must have only one affiliation and institution. In this case there is just one author and you must delete other dummy author or coauthor in the latex file and put only the author that i am given to you. You are going to fill that information with this data: \n\nThis is the principal author information: \n\nName: " . (auth()->user()->name ? auth()->user()->name . " " : "") . (auth()->user()->father_surname ? auth()->user()->father_surname . " " : "") . (auth()->user()->mother_surname ? auth()->user()->mother_surname : "") . "\nORCID: " . (auth()->user()->orcid ? auth()->user()->orcid : "") . "\nAffiliation: " . (auth()->user()->affiliation ? auth()->user()->affiliation : "") . "\nInstitution: " . (auth()->user()->institution ? auth()->user()->institution : "") . "\nInstitution address: " . (auth()->user()->institution_address ? auth()->user()->institution_address : "") . "\nEmail: " . (auth()->user()->email ? auth()->user()->email : "");
                    $prompt .= "\n\nThe date of the article is: " . date('Y-m-d') .  "\n\nAfter you fill the author information, you're going to give me the updated latex file ready to compile without any explanation, just the code. The latex without the author information is in the file " . $content . ".\n\n";    
                }

                // dd($prompt);
    
                // mandar el contenido del archivo .tex modificado al modelo de lenguaje gemini pro
                $tex_content = Gemini::geminiPro()->generateContent($prompt);
    
                // convertir el contenido del archivo .tex modificado a texto plano
                $tex_content = $tex_content->text();

                // borrar cualquier ``` que se encuentre al inicio o al final de la respuesta
                $tex_content = preg_replace('/^```/', '', $tex_content);

                // guardar el contenido del archivo .tex modificado
                file_put_contents($file, $tex_content);

            }

            // Parsear el contenido del artículo
            $parsed_content = json_decode($article->content, true);

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

            // &nbsp
            $my_tex_content = str_replace("&nbsp;", " ", $my_tex_content);

            // borra el \section{FUNDATION} reemplaza directamente por my_tex_content
            function replace_tex_content($file, $my_tex_content)
            {
                $content = file_get_contents($file);
                $content = str_replace('\section{FUNDATION}', $my_tex_content, $content);
                file_put_contents($file, $content);
            }

            // sustituir el AQUIVAELABSTRACT por el contenido del abstract
            function replace_tex_abstract($file, $abstract)
            {
                $content = file_get_contents($file);
                $content = str_replace('AQUIVAELABSTRACT', $abstract, $content);
                file_put_contents($file, $content);
            }

            // sustituir el AQUIVANLASKEYWORDS por el contenido de las keywords
            function replace_tex_keywords($file, $keywords)
            {
                $content = file_get_contents($file);
                $content = str_replace('AQUIVANLASKEYWORDS', $keywords, $content);
                file_put_contents($file, $content);
            }

            // Buscar los paquetes caption y graphicx, si no existen, agregarlos antes de \begin{document}
            function add_packages($file)
            {
                // Leer el contenido del archivo
                $content = file_get_contents($file);

                // Separar el contenido en líneas
                $lines = explode("\n", $content);

                // Verificar si ya están presentes los paquetes caption y graphicx
                $caption_package_present = false;
                $graphicx_package_present = false;
                foreach ($lines as $line) {
                    if (strpos($line, '\usepackage{caption}') !== false) {
                        $caption_package_present = true;
                    }
                    if (strpos($line, '\usepackage{graphicx}') !== false) {
                        $graphicx_package_present = true;
                    }
                }

                // Si falta alguno de los paquetes, encontrar la posición de \begin{document} y agregar los paquetes faltantes justo antes
                if (!$caption_package_present || !$graphicx_package_present) {
                    $document_index = array_search('\begin{document}', $lines);
                    if ($document_index !== false) {
                        if (!$caption_package_present) {
                            array_splice($lines, $document_index, 0, '\usepackage{caption}');
                            $document_index++;
                        }
                        if (!$graphicx_package_present) {
                            array_splice($lines, $document_index, 0, '\usepackage{graphicx}');
                        }
                    }
                }

                // Reconstruir el contenido
                $new_content = implode("\n", $lines);

                // Sobrescribir el archivo con el nuevo contenido
                file_put_contents($file, $new_content);
            }

            // crear el archivo bib con el contenido del campo bib
            File::put(public_path('articles_public/' . $article->id . '/' . 'References.bib'), $article->bib);

            // quitar los comentarios del archivo .tex
            remove_tex_comments($new_file_name); 

            // quitar el contenido del abstract del archivo .tex
            remove_tex_abstract($new_file_name);

            // quitar el contenido de las keywords del archivo .tex
            remove_tex_keywords($new_file_name);
            
            // quitar las secciones del archivo .tex
            remove_tex_content($new_file_name);
            
            // Llamar a la función con el nombre del archivo y el título
            change_tex_title($new_file_name, $article->title);

            // quitar el contenido de las footnotes del archivo .tex
            remove_tex_footnote($new_file_name);

            // mandar el contenido del archivo .tex modificado al modelo de lenguaje gemini pro
            send_to_gemini($new_file_name, $article);

            add_packages($new_file_name);

            // sustituir el FUNDATION por el contenido de my_tex_content
            replace_tex_content($new_file_name, $my_tex_content);

            // sustituir el AQUIVAELABSTRACT por el contenido del abstract
            replace_tex_abstract($new_file_name, $article->abstract);

            // sustituir el AQUIVANLASKEYWORDS por el contenido de las keywords
            replace_tex_keywords($new_file_name, $article->keywords);

            // lineas para debuggear
            $tex_content = file_get_contents($new_file_name);

            // compilar el archivo tex
            $process = new Process(['/usr/bin/pdflatex', "--shell-escape -halt-on-error -interaction=nonstopmode", "-output-directory=articles_public/{$article->id}", public_path('articles_public/' . $article->id . '/' . $article->id . '.tex')]);
            $process->run();

            // compilar el archivo bib
            $process2 = new Process(['/usr/bin/biber', public_path('articles_public/' . $article->id . '/' . $article->id)]);
            $process2->run();

            // compilar el archivo tex
            $process3 = new Process(['/usr/bin/pdflatex', "--shell-escape -halt-on-error -interaction=nonstopmode", "-output-directory=articles_public/{$article->id}", public_path('articles_public/' . $article->id . '/' . $article->id . '.tex')]);
            $process3->run();

            // compilar el archivo tex
            $process4 = new Process(['/usr/bin/pdflatex', "--shell-escape -halt-on-error -interaction=nonstopmode", "-output-directory=articles_public/{$article->id}", public_path('articles_public/' . $article->id . '/' . $article->id . '.tex')]);
            $process4->run();

            // verificar si se generó el archivo pdf
            if (file_exists(public_path("articles_public/{$article->id}/{$article->id}.pdf"))) {
                // mandar el url del pdf generado a la ruta de la vista
                $pdf_url = asset("articles_public/{$article->id}/{$article->id}.pdf");

                // redirigir a la vista de edición del artículo con el pdf_url y el id de la plantilla seleccionada
                return redirect()->route('articles.edit', $article->id)->with('pdf_url', $pdf_url)->with('template_id', $template->id);
            }else{
                // borrar todo dentro de la carpeta y regresar a la vista con un mensaje de error
                // File::cleanDirectory(public_path('articles_public/' . $article->id));
                return redirect()->route('articles.edit', $article->id)->with('error', 'No se pudo generar el PDF de este artículo.')->with('template', $template->id);
            }
           
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
