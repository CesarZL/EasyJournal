<x-app-layout>
    <div class="flex flex-col xl:flex-row ">

        <!-- Columna izquierda -->
        <div class="flex-1 xl:pe-3 py-3 xl:py-0">
            <div class="p-5 rounded-lg bg-gray-50 dark:bg-gray-800 overflow-y-auto h-screen"> 
    
                {{-- card header with subir plantilla --}}
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4 dark:text-white">Detalles del articulo</h2>
                </div>
    
                {{-- Horizontal line  --}}
                <div class="border-b border-gray-300 dark:border-gray-600 my-4"></div>
    
               {{-- form para cambiar el titulo, descripcion, autor, fecha de publicacion, orcid, doi, keywords, abstract, y contenido del articulo --}}
                <form action="{{ route('articles.update-details', $article->id)}}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-label for="title" :value="__('Titulo')" />
                        <x-input id="title" class="block mt-1 w-full" type="text" name="title" value="{{ $article->title }}" required autofocus />
                    </div>
                    
                    <div class="mb-4">
    
                    <x-label for="author" :value="__('Coautores')" />
                        @if ($coauthors->isEmpty())
                            <span class="block mt-3 text-sm font-medium text-red-900 dark:text-red-400">No hay coautores registrados</span>
                        @endif

                        @foreach ($coauthors as $coauthor)
                            <div class="flex items-center mt-1">
                                <input type="checkbox" name="coauthors[]" value="{{ $coauthor->id }}" class="form-checkbox w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800 text-primary-600"
                                {{ $article->coauthors->contains($coauthor->id) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-white">{{ $coauthor->name }} {{ $coauthor->surname }} {{ $coauthor->last_name }} - {{ $coauthor->email }}</span>
                            </div>
                        @endforeach
                        <span class="block mt-3 text-sm font-medium text-gray-900 dark:text-white"> ¿No encuentras al coautor que buscas? <a href="{{route('coauthors')}}" class="text-green-500 hover:text-green-400">Agrega un coautor nuevo</a></span>
                    </div>

                    <div class="mb-4">
                        <x-label for="bib" :value="__('Bibliografía (En formato bib)')" />
                        <textarea id="bib" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" name="bib" rows="25">{{ old('bib', $article->bib) }}</textarea>
                    </div>
    
                    <x-button class="block w-full" type="submit">
                        {{ __('Guardar') }}
                    </x-button>
    
    
                </form>
            </div>
        </div>
        
        <!-- Columna derecha -->
        <div class="flex-1">
            
        </div>
    
    </div>
</x-app-layout>
