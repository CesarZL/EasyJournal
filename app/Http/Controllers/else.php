else{

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
                dd('No se pudo abrir el archivo ZIP');
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
            function remove_tex_comments($input_file, $output_file, $sections)
            {
                $content = file($input_file, FILE_IGNORE_NEW_LINES);
                $fp = fopen($output_file, 'w');

                $remove = false;
                $sectionbase_written = false;
                $in_abstract = false;

                foreach ($content as $line) {
                    $line = explode("%", $line)[0]; // Eliminar comentarios
                    if (strpos($line, "\\begin{abstract}") !== false) {
                        $in_abstract = true;
                        $remove = true;
                        fwrite($fp, $line . "\n");
                        continue;
                    } elseif (strpos($line, "\\end{abstract}") !== false) {
                        $in_abstract = false;
                        $remove = false;
                        fwrite($fp, $line . "\n");
                        continue;
                    } elseif ($in_abstract) {
                        continue;
                    }
                    if (!$remove && preg_match('/' . implode('|', array_map('preg_quote', $sections)) . '/', $line)) {
                        if (!$sectionbase_written) {
                            fwrite($fp, "\n\\section{SECTIONBASE}\n\n\n");
                            $sectionbase_written = true;
                        }
                        $remove = true;
                    } elseif ($remove && trim($line) == "}") {
                        $remove = false;
                    } elseif (!$remove || preg_match('/' . implode('|', array_map('preg_quote', ["\\end{document}", "\\EOD"])) . '/', $line)) {
                        fwrite($fp, $line . "\n");
                    }
                }
                fclose($fp);
            }

            // variables para los archivos de entrada y salida
            $input_file = public_path('articles_public/' . $article->id . '/' . $article->id . '.tex');
            // $output_file = public_path('articles_public/' . $article->id . '/' . $article->id . '_modified.tex');
            $output_file = public_path('articles_public/' . $article->id . '/' . $article->id . '.tex');
            // secciones y comandos a remover del archivo .tex
            $sections_to_remove = ["\\section{", "\\section*{", "\\subsection{", "\\subsection*{", "\\subsubsection{", "\\subsubsection*{", "\\appendixtitles{", "\\appendixtitle{", "\\appendix{", "\\appendix*{", "\\setcounter{section", "\\noindent"];
            remove_tex_comments($input_file, $output_file, $sections_to_remove);

            // leer el contenido del archivo .tex modificado
            $tex_content = file_get_contents($output_file);

            // prompt para el modelo de lenguaje gemini pro si el articulo tiene coautores

            if (count($article->coauthors) > 0) {

                $prompt = "Your purpose is to fill in sections of latex files with the information I will provide you, first, you are going to fill the author information, coauthors and affiliations and all those information that is needed, if you don't have the information, you must leave it blank, if you have extra data from the author or coauthors but isnt requiere in the template you must leave it blank, if the authors or coauthors share the same affiliation and institution then they can share the same number of affiliation and institution, if they don't share the same affiliation and institution then they need to have different numbers of affiliation and institution. You are going to fill that information with this data: \n\nThis is the principal author information: \n\nName: " . (auth()->user()->name ? auth()->user()->name . " " : "") . (auth()->user()->father_surname ? auth()->user()->father_surname . " " : "") . (auth()->user()->mother_surname ? auth()->user()->mother_surname : "") . "\nORCID: " . (auth()->user()->orcid ? auth()->user()->orcid : "") . "\nAffiliation: " . (auth()->user()->affiliation ? auth()->user()->affiliation : "") . "\nInstitution: " . (auth()->user()->institution ? auth()->user()->institution : "") . "\nEmail: " . (auth()->user()->email ? auth()->user()->email : "") . "\n\nAnd this is the coauthors information: \n\n";
                for ($i = 0; $i < count($article->coauthors); $i++) {
                    $prompt .= "Name: " . $article->coauthors[$i]->name . " " . $article->coauthors[$i]->father_surname . " " . $article->coauthors[$i]->mother_surname . "\nORCID: " . $article->coauthors[$i]->orcid . "\nAffiliation: " . $article->coauthors[$i]->affiliation . "\nInstitution: " . $article->coauthors[$i]->institution . "\nEmail: " . $article->coauthors[$i]->email . "\n\n";
                }
                $prompt .= "\n\nAfter you fill the author information, you're going to give me the updated latex file ready to compile without any explanation, just the code. The latex without the author information is in the file " . $tex_content . ".\n\n";

            } else {
                $prompt = "Your purpose is to fill in sections of latex files with the information I will provide you, first, you are going to fill the author information and all those information that is needed, if you don't have the information, you must leave it blank, if you have extra data from the author but isnt requiere in the template you must leave it blank, if there is just one author you must have only one affiliation and institution. In this case there is just one author and you must delete other dummy author or coauthor in the latex file and put only the author that i am given to you. You are going to fill that information with this data: \n\nThis is the principal author information: \n\nName: " . (auth()->user()->name ? auth()->user()->name . " " : "") . (auth()->user()->father_surname ? auth()->user()->father_surname . " " : "") . (auth()->user()->mother_surname ? auth()->user()->mother_surname : "") . "\nORCID: " . (auth()->user()->orcid ? auth()->user()->orcid : "") . "\nAffiliation: " . (auth()->user()->affiliation ? auth()->user()->affiliation : "") . "\nInstitution: " . (auth()->user()->institution ? auth()->user()->institution : "") . "\nEmail: " . (auth()->user()->email ? auth()->user()->email : "");
                $prompt .= "\n\nAfter you fill the author information, you're going to give me the updated latex file ready to compile without any explanation, just the code. The latex without the author information is in the file " . $tex_content . ".\n\n";
            }

            // $prompt = "Your purpose is to fill in sections of latex files with the information I will provide you, first, you are going to fill the author information, coauthors and affiliations and all those information that is needed, if you don't have the information, you must leave it blank, if you have extra data from the author or coauthors but isnt requiere in the template you must leave it blank, if the authors or coauthors share the same affiliation and institution then they can share the same number of affiliation and institution, if they don't share the same affiliation and institution then they need to have different numbers of affiliation and institution. You are going to fill that information with this data: \n\nThis is the principal author information: \n\nName: " . (auth()->user()->name ? auth()->user()->name . " " : "") . (auth()->user()->father_surname ? auth()->user()->father_surname . " " : "") . (auth()->user()->mother_surname ? auth()->user()->mother_surname : "") . "\nORCID: " . (auth()->user()->orcid ? auth()->user()->orcid : "") . "\nAffiliation: " . (auth()->user()->affiliation ? auth()->user()->affiliation : "") . "\nInstitution: " . (auth()->user()->institution ? auth()->user()->institution : "") . "\nEmail: " . (auth()->user()->email ? auth()->user()->email : "") . "\n\nAnd this is the coauthors information: \n\n";
            // for ($i = 0; $i < count($article->coauthors); $i++) {
            //     $prompt .= "Name: " . $article->coauthors[$i]->name . " " . $article->coauthors[$i]->father_surname . " " . $article->coauthors[$i]->mother_surname . "\nORCID: " . $article->coauthors[$i]->orcid . "\nAffiliation: " . $article->coauthors[$i]->affiliation . "\nInstitution: " . $article->coauthors[$i]->institution . "\nEmail: " . $article->coauthors[$i]->email . "\n\n";
            // }
            // $prompt .= "\n\nAfter you fill the author information, you're going to give me the updated latex file ready to compile without any explanation, just the code. The latex without the author information is in the file " . $tex_content . ".\n\n";

            // mandar el contenido del archivo .tex modificado al modelo de lenguaje gemini pro
            $tex_content = Gemini::geminiPro()->generateContent($prompt);

            // convertir el contenido del archivo .tex modificado a texto plano
            $tex_content = $tex_content->text();

            // busca el \title{} que no esté comentado y lo reemplaza por el título del artículo
            $tex_content = preg_replace('/(\\\\title\{.*\})/', "\\title{" . $article->title . "}", $tex_content);

            // busca el \begin{abstract} y \end{abstract} que no esté comentado y lo reemplaza por el abstract del artículo
            $tex_content = preg_replace('/(\\\\begin\{abstract\}.*\\\\end\{abstract\})/s', "\\begin{abstract}\n" . $article->abstract . "\n\\end{abstract}", $tex_content);

            // busca el \keywords{} o \keywords[]{} o \keywords{}[] que no esté comentado y si no existe se busca el \begin{keywords} y \end{keywords} que no esté comentado y lo reemplaza por las keywords del artículo
            if (preg_match('/(\\\\keywords\{.*\})/', $tex_content)) {
                $tex_content = preg_replace('/(\\\\keywords\{.*\})/', "\\keywords{" . $article->keywords . "}", $tex_content);
            } elseif (preg_match('/(\\\\begin\{keywords\}.*\\\\end\{keywords\})/s', $tex_content)) {
                $tex_content = preg_replace('/(\\\\begin\{keywords\}.*\\\\end\{keywords\})/s', "\\begin{keywords}\n" . $article->keywords . "\n\\end{keywords}", $tex_content);
            }

            //buscar y reemplazar &nbsp; por espacio en blanco
            $my_tex_content = str_replace("&nbsp;", " ", $my_tex_content);
            //buscar y reemplazar &, _, %, $, #, {, }, ~, ^, \ por su respectiva secuencia de escape
            $my_tex_content = str_replace("&", "\\&", $my_tex_content);
            $my_tex_content = str_replace("_", "\\_", $my_tex_content);
            $my_tex_content = str_replace("%", "\\%", $my_tex_content);
            $my_tex_content = str_replace("$", "\\$", $my_tex_content);
            $my_tex_content = str_replace("#", "\\#", $my_tex_content);
            $my_tex_content = str_replace("~", "\\textasciitilde ", $my_tex_content);
            $my_tex_content = str_replace("^", "\\textasciicircum ", $my_tex_content);

            // busca la sección SECTIONBASE y la reemplaza por el contenido del artículo
            $tex_content = preg_replace('/(\\\\section\{SECTIONBASE\})/', $my_tex_content, $tex_content);

            // guardando el contenido del articulo en un archivo .tex
            file_put_contents($output_file, $tex_content);

            // compilar el archivo .tex con -interaction=nonstopmode para que no se detenga en caso de error
            // $process = new Process(['C:\Users\cesar\AppData\Local\Programs\MiKTeX\miktex\bin\x64\pdflatex.exe', "-output-directory=articles_public/{$article->id}", $output_file]);

            // compilar el archivo .tex con -interaction=nonstopmode en windows
            // $process = new Process(['C:\Users\cesar\AppData\Local\Programs\MiKTeX\miktex\bin\x64\pdflatex.exe', "-interaction=nonstopmode", "-output-directory=articles_public/{$article->id}", $output_file]);

            // ejecutar el comando pdflatex para compilar el archivo .tex en linux
            $process = new Process(['/usr/bin/pdflatex', "-output-directory=articles_public/{$article->id}", $output_file]);

            $process->run();

            // verificar si hubo un error al compilar el archivo .tex
            if (!$process->isSuccessful()) {
                // Esto se tiene que cambiar por un mensaje de error en la vista
                // throw new ProcessFailedException($process);

                // borrar todo dentro de la carpeta y regresar a la vista con un mensaje de error
                File::cleanDirectory(public_path('articles_public/' . $article->id));
                return redirect()->route('articles.edit', $article->id)->with('error', 'No se pudo generar el PDF de este artículo.');

            }

            // obtener la url del pdf generado
            $pdf_url = asset("articles_public/{$article->id}/{$article->id}.pdf");
            return redirect()->route('articles.edit', $article->id)->with('pdf_url', $pdf_url);
        }