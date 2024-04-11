<div class="flex flex-col xl:flex-row ">

    <!-- Columna izquierda -->
    <div class="flex-1 xl:pe-3 py-3 xl:py-0">
        <div class="p-5 rounded-lg bg-gray-50 dark:bg-gray-800 overflow-y-auto h-screen"> 
           {{-- form para cambiar el titulo, descripcion, autor, fecha de publicacion, orcid, doi, keywords, abstract, y contenido del articulo --}}
            <form action="{{ route('articles.update', $article->id)}}" method="POST">
                @csrf

                <div class="mb-4">
                    <x-label for="title" :value="__('Titulo')" />
                    <x-input id="title" class="block mt-1 w-full" type="text" name="title" value="{{ old('title', $article->title) }}" required autofocus />
                </div>

                <div class="mb-4">
                    <x-label for="description" :value="__('Descripcion')" />
                    <x-input id="description" class="block mt-1 w-full" type="text" name="description" value="{{ old('description', $article->description) }}" required />
                </div>

                <div class="mb-4">
                    <x-label for="principal_author" :value="__('Autor Principal')" />
                    <x-input id="principal_author" class="block mt-1 w-full" type="text" name="principal_author" value="{{ old('principal_author', $article->principal_author) }}" required />
                </div>

                





            </form>

        </div>
    </div>
    
    <!-- Columna derecha -->
    <div class="flex-1">
        <div class="p-5 rounded-lg bg-gray-50 dark:bg-gray-800 h-screen">

            {{-- card header with subir plantilla --}}
            <div class="text-center">
                <h2 class="text-2xl font-bold mb-4 dark:text-white">{{ $article->title}}</h2>
            </div>

            {{-- Horizontal line  --}}
            <div class="border-b border-gray-300 dark:border-gray-600 my-4"></div>

            {{-- Formulario para subir plantilla --}}

            


        </div>
    </div>

</div>