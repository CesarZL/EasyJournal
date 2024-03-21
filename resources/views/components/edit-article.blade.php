<div class="flex justify-between items-center py-3 mb-2">
    <h1 class="text-2xl font-bold text-primary-700">  {{ $article->title }} </h1>
    
    <a id="save-data" href="{{ route('articles.update', $article->id) }}" class="px-4 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600">Guardar cambios</a>

</div>

<div class="flex flex-col xl:flex-row">

    <!-- Columna izquierda -->
    <div class="flex-1 xl:pe-3 py-3 xl:py-0">
        <div class="p-5 border-2 border-gray-300 border-dashed rounded-lg dark:border-gray-700 h-screen bg-gray-50 dark:bg-gray-800 dark:text-white" >
            
            <div class="p-5" id="editorjs">
                {{-- AQUI SE DEBE VOLVER A MOSTRAR EL FORMATO DE EDITORJS --}}
            </div>

        </div>
    </div>
    
    <!-- Columna derecha -->
    <div class="flex-1">
        <div class="rounded-lg h-screen bg-red-50 dark:bg-gray-800">
            @if(file_exists(public_path('articles_storage/' . $article->id . '.pdf')))
                <embed id="pdf-embed" src="{{ asset('articles_storage/' . $article->id . '.pdf') }}" type="application/pdf" width="100%" height="100%" />
            @endif
        </div>
    </div>


</div>

