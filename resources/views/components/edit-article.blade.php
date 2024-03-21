<div class="flex flex-col md:flex-row">

    <!-- Columna izquierda -->
    <div class="flex-1 md:pe-3 py-3 md:py-0">
        <div class="p-5 border-2 border-gray-300 border-dashed rounded-lg dark:border-gray-700 h-screen bg-gray-50 dark:bg-gray-800 dark:text-white" >
            
            <div class="p-5" id="editorjs">

            </div>
            <a id="save-data" href="{{ route('articles.update', $article->id) }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg mt-4">Guardar Cambios</a>

        </div>
    </div>
    
    <!-- Columna derecha -->
    <div class="flex-1">
        <div class="rounded-lg h-screen bg-red-50 dark:bg-gray-800">
            <embed class="rounded-md" id="pdf-embed" src="{{ asset('articles/' . $article->id . '.pdf') }}" type="application/pdf" width="100%" height="100%" />
        </div>
    </div>

    
</div>




