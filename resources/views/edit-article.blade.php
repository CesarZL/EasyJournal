<x-edit-layout>
    @livewire('edit-menu', ['article' => $article])
    <div class="flex justify-between items-center py-3 mb-2">
        <h1 class="text-2xl font-bold text-primary-700">  {{ $article->title }} </h1>    
    
        <div class="flex items-center">

            {{-- Si existe el PDF, mostrar botón para descargarlo --}}
            @if (file_exists(public_path('templates_public/' . $article->id . '/' . $article->id . '.pdf')))
                <a href="{{route('articles.pdf', $article)}}" class="ml-2 flex-shrink-0 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-500 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600 cursor-pointer">
                    Descargar pdf
                </a>

                <a href="{{route('articles.zip', $article)}}" class="ml-2 flex-shrink-0 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-500 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600 cursor-pointer">
                    Descargar zip
                </a>
            @endif

            <a id="save-data" class="ml-2 flex-shrink-0 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-500 bg-gray-100 border border-gray-300 rounded-s-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600 cursor-pointer" onclick="event.preventDefault(); document.getElementById('form-update').submit()">
                <svg class="h-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                </svg>
                Guardar cambios 
            </a>
            <label for="template" class="sr-only">Selecciona una plantilla</label>
            <select id="template" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-e-lg border-s-gray-100 dark:border-s-gray-700 border-s-2 focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                <option selected>Plantilla básica</option>
                @foreach ($templates as $template)
                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                @endforeach
            </select>

        </div>
    </div>
    
    <div class="flex flex-col xl:flex-row ">
    
        <!-- Columna izquierda -->
        <div class="flex-1 xl:pe-3 py-3 xl:py-0">
            <div class="p-5 border-2 border-gray-300 border-dashed rounded-lg dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white overflow-y-auto h-screen">
                <form action="{{ route('articles.update', $article) }}" method="POST" enctype="multipart/form-data" id="form-update">
                    @csrf
                    @method('PUT')
                    
                    <div class="mt-4">
                        <x-label for="abstract" :value="__('Resumen')" />
                        <textarea rows="5" name="abstract" id="abstract" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">{{ old('abstract', $article->abstract) }}</textarea>
                        @error('abstract')
                            <p class="text-red-500 text-sm mt-3">{{ $message }}</p>
                        @enderror
                    </div>
        
                    <div class="mt-4">
                        <x-label for="keywords" :value="__('Palabras clave (separadas por comas)')" />
                        <x-input id="keywords" class="block mt-1 w-full" type="text" name="keywords" value="{{ old('keywords', $article->keywords) }}" required />
                        @error('keywords')
                            <p class="text-red-500 text-sm mt-3">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <textarea type="hidden" id="content" name="content" hidden>{{ old('content', $article->content ?? '') }}</textarea>
                        <div id="editorjs"></div>
            
                        @error('content')
                        <p class="text-red-500 text-sm mt-3">{{ $message }}</p>
                        @enderror
                    </div>

                    <input type="hidden" name="template" id="selected-template">

            
                </form>

            </div>
        </div>
        
        <!-- Columna derecha -->
        <div class="flex-1">
            <div class="rounded-lg bg-red-50 dark:bg-gray-700 h-screen">
                    @if (file_exists(public_path('templates_public/' . $article->id . '/' . $article->id . '.pdf')))
                    {{-- Si el PDF existe, mostrarlo --}}
                    <embed class="rounded-lg" id="pdf-embed" type="application/pdf" width="100%" height="100%" src="{{ asset('templates_public/' . $article->id . '/' . $article->id . '.pdf') }}" />
                    @else
                        {{-- Si el PDF no existe, mostrar mensaje --}}
                        <div class="flex items-center justify-center h-full">
                            <p class="text-gray-500 text-lg">No se ha generado un PDF para este artículo</p>
                        </div>
                    @endif
            </div>
        </div>
    </div>
    
   

</x-edit-layout>

@vite('resources/js/editor.js')