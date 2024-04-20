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
            <form action="{{ route('articles.update', $article->id)}}" method="POST">
                @csrf
                <div class="mb-4">
                    <x-label for="title" :value="__('Titulo *')" /> 
                    <x-input id="title" class="block mt-1 w-full" type="text" name="title" value="{{ old('title', $article->title) }}" required autofocus />
                </div>
                <div class="mb-4">

                    <x-label for="author" :value="__('Coautores')" />

                        
                        @if ($coauthors->isEmpty())
                            <div class="mt-1 w-full text-sm font-medium text-gray-900 bg-white rounded-lg dark:bg-gray-700 dark:text-white">
                                <div class="block w-full px-4 py-2 cursor-pointer rounded-lg hover:bg-gray-100 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-700 focus:text-primary-700 dark:hover:bg-gray-600 dark:hover:text-white dark:focus:ring-gray-500 dark:focus:text-white">
                                    <div class="flex items-center justify-between">
                                        <span>No hay coautores registrados</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            @foreach ($coauthors as $coauthor)
                                <div class="mt-1 w-full text-sm font-medium text-gray-900 bg-white rounded-lg dark:bg-gray-700 dark:text-white">
                                    <div class="block w-full px-4 py-2 cursor-pointer rounded-lg hover:bg-gray-100 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-700 focus:text-primary-700 dark:hover:bg-gray-600 dark:hover:text-white dark:focus:ring-gray-500 dark:focus:text-white">
                                        <div class="flex items-center justify-between">
                                            <span>{{ $coauthor->name }} {{ $coauthor->last_name }} {{ $coauthor->surname }}</span>

                                            <button class="px-4 py-2 bg-green-500 text-white rounded-lg ml-auto hover:bg-green-600">
                                                Agregar
                                            </button> 

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                    <span class="block mt-3 text-sm font-medium text-gray-900 dark:text-white"> ¿No encuentras al coautor que buscas? <a href="{{route('coauthors')}}" class="text-green-500 hover:text-green-400">Agrega un coautor nuevo</a></span>
                </div>

                <x-button class="block w-full">
                    {{ __('Guardar') }}
                </x-button>


            </form>
        </div>
    </div>
    
    <!-- Columna derecha -->
    <div class="flex-1">
        <div class="p-5 rounded-lg bg-gray-50 dark:bg-gray-800 h-screen">

            {{-- card header with subir plantilla --}}
            <div class="text-center">
                <h2 class="text-2xl font-bold mb-4 dark:text-white">Coautores</h2>
            </div>

            {{-- Horizontal line  --}}
            <div class="border-b border-gray-300 dark:border-gray-600 my-4"></div>

            <div id="contenedor-coautores" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
               
                {{-- <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg p-4 dark:text-white hover:cursor-pointer hover:bg-gray-600">
                    <h3 class="text-lg font-bold mb-2 ">{{$coauthor->name}} {{$coauthor->surname}} {{$coauthor->last_name}}</h3>
                    <div class="text-sm break-words">
                       <p>{{$coauthor->institution}}, {{$coauthor->country}}</p>
                       <p>{{$coauthor->email}}</p>
                       <p>{{$coauthor->phone}}</p>
                       <p>{{$coauthor->address}}</p>
                       <p>{{$coauthor->ORCID}}</p>
                   </div>


                    
                    <div class="flex justify-end mt-4">
                        <button class="px-4 py-2 bg-red-500 text-white rounded-lg mr-2 hover:bg-red-600">Eliminar</button>
                        <button class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600" onclick="window.location.href='{{ route('coauthors.edit', $coauthor->id) }}'">Editar</button>
                    </div>
                </div> --}}
                   
            </div>


        </div>
    </div>

</div>

