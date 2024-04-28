<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Str;


use function Ramsey\Uuid\v1;

class ArticleUploadController extends Controller
{
    // Constructor para proteger las rutas
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // Función para mostrar todas las plantillas
    public function index()
    {
        // leer todas las plantillas de la base de datos y mostrarlas en la vista upload-template
        $userId = auth()->user()->id;
        $templates = Template::where('user_id', $userId)->get();

        $title = 'Borrar plantilla';
        $text = "¿Estás seguro de que quieres borrar esta plantilla?";
        confirmDelete($title, $text);

        return view('upload-template', ['templates' => $templates]);
    }

    // Función para subir una plantilla
    public function store(Request $request)
    {
        // Validaciones
        $request->validate([
            'name' => 'required',
            // 'description' => 'required',
            'file' => 'required|mimes:zip|max:2048',
        ]);
    
        // Guarda el archivo zip en la carpeta storage/app/public/files
        $zipPath = $request->file('file')->store('public/files');
    
        // Ruta donde se extraerán los archivos
        $extractPath = storage_path('app/public/files/' . pathinfo($zipPath, PATHINFO_FILENAME));
    
        // Crea una instancia de ZipArchive
        $zip = new ZipArchive;
    
        // Abre el archivo zip
        if ($zip->open(storage_path('app/' . $zipPath)) === TRUE) {
            // Extrae los archivos
            $zip->extractTo($extractPath);
    
            // Cierra el archivo zip
            $zip->close();
        } else {
            // Si no se puede abrir el archivo zip, redirige con un mensaje de error
            return redirect()->route('templates')->with('error', 'No se pudo abrir el archivo ZIP.');
        }

        // Verificar si hay archivos .tex en el archivo ZIP, si hay, guardar la plantilla en la base de datos
        $texFiles = glob($extractPath . '/*.tex');
        if (empty($texFiles)) {
            return redirect()->route('templates')->with('error', 'No se encontró ningún archivo .tex en el archivo ZIP.');
        }

        // Abrir el archivo tex más grande y mostrar el pdf generado
        $heaviestFile = '';
        $heaviestSize = 0;
        
        // Iterar a través de los archivos extraídos
        $files = scandir($extractPath);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'tex') {
                $filePath = $extractPath . '/' . $file;
                $fileSize = filesize($filePath);
                if ($fileSize > $heaviestSize) {
                    $heaviestFile = $filePath;
                    $heaviestSize = $fileSize;
                }
            }
        }

        // buscar si hay un pdf con el mismo nombre que el archivo tex más grande, si lo hay, eliminarlo
        $pdfFiles = glob($extractPath . '/*.pdf');
        foreach ($pdfFiles as $pdfFile) {
            if (pathinfo($pdfFile, PATHINFO_FILENAME) === pathinfo($heaviestFile, PATHINFO_FILENAME)) {
                unlink($pdfFile);
            }
        }

        // // cambiar el nombre al archivo tex más grande para que no tenga espacios por main.tex
        // $newHeaviestFile = $extractPath . '/main.tex';
        // rename($heaviestFile, $newHeaviestFile);
        // $heaviestFile = $newHeaviestFile;
        
        // Función recursiva para obtener todos los archivos dentro de un directorio y sus subdirectorios, devolviendo nombres relativos y moviendo archivos a la carpeta raíz con nombres modificados
        function getFilesRecursively($directory, $basePath = null, $templateRootPath = null, $excludeFile = null) {
            $originalNames = [];
            $renamedFiles = [];
        
            // Si no se proporciona una base path, usa el directorio dado como base
            if ($basePath === null) {
                $basePath = $directory;
            }
        
            // Si no se proporciona la ruta de la carpeta raíz del template, utiliza la carpeta raíz del directorio de extracción
            if ($templateRootPath === null) {
                $templateRootPath = $basePath;
            }
        
            // Escanea el directorio
            $items = scandir($directory);
            
            // Itera sobre los elementos
            foreach ($items as $item) {
                // Ignora los directorios especiales
                if ($item === '.' || $item === '..') {
                    continue;
                }
        
                // Construye la ruta completa del elemento
                $path = $directory . '/' . $item;
        
                // Si es un archivo y no es el archivo excluido, muévelo a la carpeta raíz con un nombre modificado
                if (is_file($path) && $path !== $excludeFile) {
                    // Genera un identificador único de 3 dígitos aleatorios
                    // $uniqueIdentifier = strtolower(Str::random(3));
        
                    // Obtiene la extensión del archivo
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
        
                    // Construye el nuevo nombre del archivo con el identificador único antes de la extensión
                    // $newName = strtolower(pathinfo($path, PATHINFO_FILENAME)) . '_' . $uniqueIdentifier . '.' . $extension;
                    $newName = strtolower(pathinfo($path, PATHINFO_FILENAME)) . '.' . $extension;
        
                    // Mueve el archivo a la carpeta raíz del template con el nuevo nombre
                    rename($path, $templateRootPath . '/' . $newName);
                                                
                    // Guarda el nombre original y el nuevo nombre con su ruta
                    $originalNames[] = substr($path, strlen($templateRootPath) + 1); 
                    
                    $renamedFiles[] = $newName;
                } 
                // Si es un directorio, llama recursivamente a esta función dentro de ese directorio y luego intenta eliminar el directorio si está vacío
                elseif (is_dir($path)) {
                    $subResults = getFilesRecursively($path, $basePath, $templateRootPath, $excludeFile);
                    $originalNames = array_merge($originalNames, $subResults['originalNames']);
                    $renamedFiles = array_merge($renamedFiles, $subResults['renamedFiles']);
                    // Intenta eliminar el directorio si está vacío
                    if (count(scandir($path)) === 2) { // Solo '.' y '..' en el directorio
                        rmdir($path);
                    }
                }
            }
        
            return [
                'originalNames' => $originalNames,
                'renamedFiles' => $renamedFiles
            ];
        }
        
        // Se llama a la función recursiva para obtener todos los archivos dentro del directorio de extracción y sus subdirectorios
        $results = getFilesRecursively($extractPath, null, null, $heaviestFile);
        
        // se quita la extensión de todos los nombres originales
        for ($i = 0; $i < count($results['originalNames']); $i++) {
            $originalName = $results['originalNames'][$i];
            $originalName = explode('.', $originalName)[0];
            $results['originalNames'][$i] = $originalName;
        }

        // se quita la extensión de todos los nombres renombrados
        for ($i = 0; $i < count($results['renamedFiles']); $i++) {
            $renamedFile = $results['renamedFiles'][$i];
            $renamedFile = explode('.', $renamedFile)[0];
            $results['renamedFiles'][$i] = $renamedFile;
        }

        // dd(originalNames, renamedFiles);
        // dd($results);

        // leer contenido del archivo tex más grande
        $content = file_get_contents($heaviestFile);
    
        // buscar y reemplazar cada referencia a los archivos extraídos con todo y path del directorio que los contiene por los nuevos nombres en el tex más grande
        for ($i = 0; $i < count($results['originalNames']); $i++) {
            $content = str_replace($results['originalNames'][$i], $results['renamedFiles'][$i], $content);
        }
        
        // guardar el contenido modificado en el archivo tex más grande
        file_put_contents($heaviestFile, $content);

        // Ahora hacer lo mismo para cada archivo cls 
        $clsFiles = glob($extractPath . '/*.cls');
        foreach ($clsFiles as $clsFile) {
            // leer contenido del archivo cls
            $content = file_get_contents($clsFile);

            // buscar sin importar mayúsculas y minúsculas
            for ($i = 0; $i < count($results['originalNames']); $i++) {
                // $content = preg_replace('/\b' . $results['originalNames'][$i] . '\b/i', $results['renamedFiles'][$i], $content); 
                $content = preg_replace('/\b' . preg_quote($results['originalNames'][$i], '/') . '\b/i', $results['renamedFiles'][$i], $content);  
                
                // buscar caracter unicode U+200b y borrarlo si existe
                $content = str_replace("\xE2\x80\x8B", "", $content);
            }

            // Si encuentras un \input{nombredearchivosinextension} le pones la extensión .tex al final quedando \input{nombredearchivosinextension.tex} solo si este no tiene extensión (o sea un punto)
            $content = preg_replace_callback('/\\\\input{([^}]+)}/i', function($matches) {
                $fileName = $matches[1];
                if (pathinfo($fileName, PATHINFO_EXTENSION) === '') {
                    return '\\input{' . $fileName . '.tex}';
                } else {
                    return $matches[0]; // No hacer nada si ya tiene extensión
                }
            }, $content);

            // guardar el contenido modificado en el archivo cls
            file_put_contents($clsFile, $content);
        }


        // Ahora hacer lo mismo para cada archivo bib
        $bibFiles = glob($extractPath . '/*.bib');
        foreach ($bibFiles as $bibFile) {
            // leer contenido del archivo bib
            $content = file_get_contents($bibFile);
        
            // buscar sin importar mayúsculas y minúsculas
            for ($i = 0; $i < count($results['originalNames']); $i++) {
                // $content = preg_replace('/\b' . $results['originalNames'][$i] . '\b/i', $results['renamedFiles'][$i], $content); 
                $content = preg_replace('/\b' . preg_quote($results['originalNames'][$i], '/') . '\b/i', $results['renamedFiles'][$i], $content); 
                // buscar caracter unicode U+200b y borarrlo si existe
                $content = str_replace("\xE2\x80\x8B", "", $content);
            }
            
            // guardar el contenido modificado en el archivo bib
            file_put_contents($bibFile, $content);
        }

        // Ahora hacer lo mismo para cada archivo bst
        $bstFiles = glob($extractPath . '/*.bst');
        foreach ($bstFiles as $bstFile) {
            // leer contenido del archivo bst
            $content = file_get_contents($bstFile);

            // buscar sin importar mayúsculas y minúsculas
            for ($i = 0; $i < count($results['originalNames']); $i++) {
                // $content = preg_replace('/\b' . $results['originalNames'][$i] . '\b/i', $results['renamedFiles'][$i], $content); 
                $content = preg_replace('/\b' . preg_quote($results['originalNames'][$i], '/') . '\b/i', $results['renamedFiles'][$i], $content); 
                // buscar caracter unicode U+200b y borarrlo si existe
                $content = str_replace("\xE2\x80\x8B", "", $content);
            }
            
            // guardar el contenido modificado en el archivo bst
            file_put_contents($bstFile, $content);
        }

        // Ahora hacer lo mismo para cada archivo sty
        $styFiles = glob($extractPath . '/*.sty');
        foreach ($styFiles as $styFile) {
            // leer contenido del archivo sty
            $content = file_get_contents($styFile);

            // buscar sin importar mayúsculas y minúsculas
            for ($i = 0; $i < count($results['originalNames']); $i++) {
                // $content = preg_replace('/\b' . $results['originalNames'][$i] . '\b/i', $results['renamedFiles'][$i], $content); 
                $content = preg_replace('/\b' . preg_quote($results['originalNames'][$i], '/') . '\b/i', $results['renamedFiles'][$i], $content); 
                // buscar caracter unicode U+200b y borarrlo si existe
                $content = str_replace("\xE2\x80\x8B", "", $content);
            }
            
            // guardar el contenido modificado en el archivo sty
            file_put_contents($styFile, $content);
        }

        // cambiar el nombde del tex mas grande a main.tex
        $newHeaviestFile = $extractPath . '/main.tex';
        rename($heaviestFile, $newHeaviestFile);
        $heaviestFile = $newHeaviestFile;

        // compilar el archivo .tex más grande y mostrar el pdf generado
        $script = "#!/bin/bash\n\n";
        $script .= "OUTDIR=" . $extractPath . "\n";
        $script .= "FILENAME=" . pathinfo($heaviestFile, PATHINFO_FILENAME) . "\n";
        $script .= "OPTIONS=\"--shell-escape -interaction=nonstopmode\"\n\n";
        $script .= "mkdir -p \$OUTDIR\n\n";
        $script .= "/usr/bin/pdflatex \$OPTIONS --output-directory=\$OUTDIR \$FILENAME.tex\n";

        // si existen eps en el directorio, convertirlos a pdf y guardarlos con el nombre-to-pdf. Ej. nombreoriginal.eps -> nombreoriginal-eps-converted-to.pdf
        $epsFiles = glob($extractPath . '/*.eps');
        foreach ($epsFiles as $epsFile) {
            $epsFileName = pathinfo($epsFile, PATHINFO_FILENAME);
            $epsToPdfFile = $extractPath . '/' . $epsFileName . '-eps-converted-to.pdf';
            $script .= "/usr/bin/epstopdf $epsFile --outfile=$epsToPdfFile\n";
        }

        // Guardar el script en un archivo .sh
        $scriptPath = storage_path('app/public/files/' . pathinfo($zipPath, PATHINFO_FILENAME) . '/compile.sh');
        file_put_contents($scriptPath, $script);

        // Ejecutar el script
        $process = new Process(['sh', $scriptPath]);
        $process->run();
        
        // Verificar si se generó el archivo PDF
        $pdfPath = $extractPath . '/' . pathinfo($heaviestFile, PATHINFO_FILENAME) . '.pdf';
        if (file_exists($pdfPath)) {
            // Crear y almacenar el objeto Template
            $template = new Template;
            $template->name = $request->name;
            $template->description = $request->description;
            $template->file = $zipPath;
            $template->user_id = auth()->user()->id;
            $template->save();

            return redirect()->route('templates')->with('success', 'Plantilla subida correctamente.');
        } else {
            // Si el archivo PDF no se generó, mostrar un mensaje de error y se borra el archivo ZIP y la carpeta extraída
            Storage::delete($zipPath);
            Storage::deleteDirectory('public/files/' . pathinfo($zipPath, PATHINFO_FILENAME));
            return redirect()->route('templates')->with('error', 'El archivo PDF no se generó correctamente.');
        }
    }

    // Función para previsualizar una plantilla
    public function preview(Template $template)
    {
        // Ruta donde se extraen los archivos
        $extractPath = storage_path('app/public/files/' . pathinfo($template->file, PATHINFO_FILENAME));

        // compilar el main.tex y mostrar el pdf generado
        $MainFile = $extractPath . '/main.tex';

        // crear un archivo bash con el contenido anterior y ejecutarlo con el comando Process de Symfony
        $script = "#!/bin/bash\n\n";
        $script .= "OUTDIR=" . $extractPath . "\n";
        $script .= "FILENAME=" . pathinfo($MainFile, PATHINFO_FILENAME) . "\n";
        $script .= "OPTIONS=\"--shell-escape -interaction=nonstopmode\"\n\n";
        $script .= "mkdir -p \$OUTDIR\n\n";
        $script .= "/usr/bin/pdflatex \$OPTIONS --output-directory=\$OUTDIR \$FILENAME.tex\n";
        // $script .= "/usr/bin/biber --output-directory=\$OUTDIR \$FILENAME\n";
        // $script .= "/usr/bin/pdflatex \$OPTIONS --output-directory=\$OUTDIR \$FILENAME.tex\n";
        // $script .= "/usr/bin/pdflatex \$OPTIONS --output-directory=\$OUTDIR \$FILENAME.tex\n";

        // Guardar el script en un archivo .sh
        $scriptPath = storage_path('app/public/files/' . pathinfo($template->file, PATHINFO_FILENAME) . '/compile.sh');
        file_put_contents($scriptPath, $script);

        // Ejecutar el script
        $process = new Process(['sh', $scriptPath]);
        $process->run();

        $pdfPath = storage_path('app/public/files/' . pathinfo($template->file, PATHINFO_FILENAME) . '/' . pathinfo($MainFile, PATHINFO_FILENAME) . '.pdf');

        // Obtener el nombre del archivo PDF generado
        $pdfFileName = pathinfo($pdfPath, PATHINFO_BASENAME);
        

        // Si la carpeta pdfs no existe, crearla
        if (!File::exists(public_path('pdfs'))) {
            File::makeDirectory(public_path('pdfs'));
        }else{
            // Si la carpeta pdfs existe, borrar todos los archivos que haya dentro
            $files = glob(public_path('pdfs/*')); //obtenemos todos los nombres de los ficheros
            foreach($files as $file){
                if(is_file($file))
                    unlink($file); //elimino el fichero
            }
        }

        // Mover el archivo PDF al directorio publico a la carpeta pdfs
        // File::move($pdfPath, public_path('pdfs/' . $pdfFileName));
        File::copy($pdfPath, public_path('pdfs/' . $pdfFileName));

        $pdfUrl = asset('pdfs/' . $pdfFileName);

        // devolver el pdf de publicPdfPath para mostrarlo en la vista
        return view('template-preview', ['pdfUrl' => $pdfUrl]);

    }


    // Función para borrar una plantilla
    public function destroy(Template $template)
    {
        // Delete the zip file
        Storage::delete($template->file);

        // Delete the template entry from the database
        $template->delete();

        // Delete the extracted folder
        Storage::deleteDirectory('public/files/' . pathinfo($template->file, PATHINFO_FILENAME));

        return redirect()->route('templates')->with('success', 'Archivo eliminado correctamente.');
    }

}
