<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;






class ArticleUploadController extends Controller
{
    public function index()
    {
        // leer todas las plantillas de la base de datos y mostrarlas en la vista upload-template
        $userId = auth()->user()->id;
        $templates = Template::where('user_id', $userId)->get();
        return view('upload-template', ['templates' => $templates]);
    }

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
    
        return redirect()->route('templates')->with('success', 'Archivo subido y descomprimido correctamente.');
    }

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

        // dd($heaviestFile);
        // dd(pathinfo($template->file, PATHINFO_FILENAME));


        $process = new Process(['C:\Users\cesar\AppData\Local\Programs\MiKTeX\miktex\bin\x64\pdflatex.exe', "-output-directory=storage/files/" . pathinfo($template->file, PATHINFO_FILENAME), $heaviestFile]);
        $process->run();

        if ($process->isSuccessful()) {
            $pdfPath = storage_path('app/public/files/' . pathinfo($template->file, PATHINFO_FILENAME) . '/' . pathinfo($heaviestFile, PATHINFO_FILENAME) . '.pdf');
            return response()->download($pdfPath)->deleteFileAfterSend();
        } else {
            // dd($process->getErrorOutput());
            return redirect()->route('templates')->with('error', 'No se pudo generar el PDF.');
        }

    
        // return view('preview-template', ['content' => $content]);
    }


    // public function destroy(Template $template)
    // {
    //     // Delete the zip file
    //     // Storage::delete($template->file);

    //     // Delete the template entry from the database
    //     // $template->delete();

    //     // Delete the extracted folder
        
        

    //     return redirect()->route('templates')->with('success', 'Archivo eliminado correctamente.');
    // }

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
