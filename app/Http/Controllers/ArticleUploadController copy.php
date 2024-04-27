<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Exception\ProcessFailedException;

// Muy bien, ahora quiero que abras el tex más grande y cambies las referencias por las nuevas, Ej. en la lista de nombres originales está Definitions/chicago2.bst y en la nueva está chicago2_0dq.bst, en el tex vas a buscar Definitions/chicago2 y la vas a cambiar por la nueva chicago2_0dq, así por cada archivo, si no se encuentra coincidencia simplemente se salta, puede que no todos los archivos son utilizados y recuerda buscar sin la extencion

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
            'description' => 'required',
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
    
        // Crea un nuevo artículo y lo guarda en la base de datos
        $template = new Template;
        $template->name = $request->name;
        $template->description = $request->description;
        $template->file = $zipPath;
        $template->user_id = auth()->user()->id;
        $template->save();

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

        // si no hay archivos .tex en la carpeta extraída, redirigir con un mensaje de error
        if ($heaviestFile === '') {
            // Borrar el archivo zip, la carpeta y el registro de la base de datos
            Storage::delete($zipPath);
            Storage::deleteDirectory('public/files/' . pathinfo($zipPath, PATHINFO_FILENAME));
            $template->delete();

            return redirect()->route('templates')->with('error', 'No se encontraron archivos .tex en el archivo ZIP.');
        }

        // Obtener los subdirectorios y archivos de cada subdirectorio
        $subdirectories = array_filter($files, function ($file) use ($extractPath) {
            return is_dir($extractPath . '/' . $file) && $file !== '.' && $file !== '..';
        });


        // Obtener los archivos de cada subdirectorio
        $subdirectoryFiles = array_map(function ($subdirectory) use ($extractPath) {
            return array_diff(scandir($extractPath . '/' . $subdirectory), ['.', '..']);
        }, $subdirectories);

        // formatear en json para tener un treeview de los archivos de cada subdirectorio
        // $subdirectoryFilesJson = json_encode(array_combine($subdirectories, $subdirectoryFiles));
        // dd($subdirectoryFilesJson);

        // guardar en un array los archivos de cada subdirectorio en formato de subdirectorio_nombre/nombre_archivo.extensión
        $subdirectoryFilesArray = [];
        foreach ($subdirectories as $key => $subdirectory) {
            foreach ($subdirectoryFiles[$key] as $subdirectoryFile) {
                $subdirectoryFilesArray[] = $subdirectory . '/' . $subdirectoryFile;
            }
        }

        dd($subdirectoryFilesArray);


        // crear un archivo bash con el contenido anterior y ejecutarlo con el comando Process de Symfony
        $script = "#!/bin/bash\n\n";
        // $script .= "OUTDIR=" . $zipPath without extension .zip
        $script .= "OUTDIR=" . $extractPath . "\n";
        $script .= "FILENAME=" . pathinfo($heaviestFile, PATHINFO_FILENAME) . "\n";
        $script .= "OPTIONS=\"-interaction=nonstopmode\"\n\n";
        $script .= "mkdir -p \$OUTDIR\n\n";
        $script .= "/usr/bin/pdflatex \$OPTIONS --output-directory=\$OUTDIR \$FILENAME.tex\n";
        // $script .= "/usr/bin/biber --output-directory=\$OUTDIR \$FILENAME\n";
        // $script .= "/usr/bin/pdflatex \$OPTIONS --output-directory=\$OUTDIR \$FILENAME.tex\n";
        // $script .= "/usr/bin/pdflatex \$OPTIONS --output-directory=\$OUTDIR \$FILENAME.tex\n";

        // Guardar el script en un archivo .sh
        $scriptPath = storage_path('app/public/files/' . pathinfo($zipPath, PATHINFO_FILENAME) . '/compile.sh');
        file_put_contents($scriptPath, $script);

        // Ejecutar el script
        $process = new Process(['sh', $scriptPath]);
        $process->run();

        // Esta validación da falsos positivos, no es la mejor forma de hacerlo
        if (!$process->isSuccessful()) {
            // Esto se tiene que cambiar por un mensaje de error en la vista
            throw new ProcessFailedException($process);

            // Borrar el archivo zip, la carpeta y el registro de la base de datos
            Storage::delete($zipPath);
            Storage::deleteDirectory('public/files/' . pathinfo($zipPath, PATHINFO_FILENAME));
            $template->delete();
            
            return redirect()->route('templates')->with('error', 'No se pudo generar el PDF de esta plantilla.');
        }

        return redirect()->route('templates')->with('success', 'Plantilla subida correctamente.');
    }

    // Función para previsualizar una plantilla
    public function preview(Template $template)
    {
        // Ruta donde se extraen los archivos
        $extractPath = storage_path('app/public/files/' . pathinfo($template->file, PATHINFO_FILENAME));

        // compilar el archivo .tex más grande y mostrar el pdf generado
        $heaviestFile = '';
        $heaviestSize = 0;

        // Iterate through the extracted files
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

        // crear un archivo bash con el contenido anterior y ejecutarlo con el comando Process de Symfony
        $script = "#!/bin/bash\n\n";
        $script .= "OUTDIR=" . $extractPath . "\n";
        $script .= "FILENAME=" . pathinfo($heaviestFile, PATHINFO_FILENAME) . "\n";
        $script .= "OPTIONS=\"-interaction=nonstopmode\"\n\n";
        $script .= "mkdir -p \$OUTDIR\n\n";
        $script .= "/usr/bin/pdflatex \$OPTIONS --output-directory=\$OUTDIR \$FILENAME.tex\n";
        $script .= "/usr/bin/biber --output-directory=\$OUTDIR \$FILENAME\n";
        $script .= "/usr/bin/pdflatex \$OPTIONS --output-directory=\$OUTDIR \$FILENAME.tex\n";
        $script .= "/usr/bin/pdflatex \$OPTIONS --output-directory=\$OUTDIR \$FILENAME.tex\n";

        // Guardar el script en un archivo .sh
        $scriptPath = storage_path('app/public/files/' . pathinfo($template->file, PATHINFO_FILENAME) . '/compile.sh');
        file_put_contents($scriptPath, $script);

        // Ejecutar el script
        $process = new Process(['sh', $scriptPath]);
        $process->run();

        $pdfPath = storage_path('app/public/files/' . pathinfo($template->file, PATHINFO_FILENAME) . '/' . pathinfo($heaviestFile, PATHINFO_FILENAME) . '.pdf');

        // Obtener el nombre del archivo PDF generado
        $pdfFileName = pathinfo($pdfPath, PATHINFO_BASENAME);

        // Si la carpeta pdfs no existe, crearla
        if (!File::exists(public_path('pdfs'))) {
            File::makeDirectory(public_path('pdfs'));
        }

        // Mover el archivo PDF al directorio publico
        File::move($pdfPath, public_path('pdfs/' . $pdfFileName));

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
