<x-app-layout>

    <div class="flex flex-col xl:flex-row ">

        <!-- Columna izquierda -->
        <div class="flex-1 xl:pe-3 py-3 xl:py-0">
            <div class="p-5 rounded-lg bg-gray-50 dark:bg-gray-800 overflow-y-auto h-screen"> 
                @foreach ($templates as $template)
                    
                    <div class="dark:bg-white bg-white p-4 rounded-lg dark:hover:bg-gray-100 hover:bg-gray-100 @if ($loop->first) mt-0 @else mt-4 @endif">
                        <h2 class="text-xl font-bold mb-2">{{ $template->name }}</h2>
                        <p class="text-gray-700">{{ $template->description }}</p>
                        <div class="flex justify-end mt-2">
                            
                            <form action="{{ route('templates.preview', $template->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="dark:bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md ml-2">Ver</button>
                            </form>

                            {{-- <form action="{{ route('templates.destroy', $template->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dark:bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md ml-2">Eliminar</button>
                            </form> --}}

                            {{-- <a href="{{ route('coauthors.destroy', $coauthor->id) }}" class="px-4 py-2 bg-red-500 text-white rounded-lg mr-2 hover:bg-red-600" data-confirm-delete="true">Eliminar</a> --}}

                            <a href="{{ route('templates.destroy', $template->id) }}" class="dark:bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md ml-2" data-confirm-delete="true">Eliminar</a>

                        </div>
                    </div>

                @endforeach
            </div>
        </div>
        
        <!-- Columna derecha -->
        <div class="flex-1">
            <div class="p-5 rounded-lg bg-gray-50 dark:bg-gray-800 h-screen">

                {{-- card header with subir plantilla --}}
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4 dark:text-white">Subir plantilla nueva</h2>
                </div>

                {{-- Horizontal line  --}}
                <div class="border-b border-gray-300 dark:border-gray-600 my-4"></div>

                {{-- Formulario para subir plantilla --}}

                <form action="{{ route('templates.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Nombre de la plantilla</label>
                        <input name="name" id="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" type="text"/>
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Descripción de la plantilla</label>
                        <textarea rows="4" name="description" id="description" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 @error('file') border-red-500 dark:border-red-500 @enderror">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                </svg>
                                <p id="file-name-placeholder" class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Da click aquí para subir tu archivo</span></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Solo se permiten archivos en formato ZIP (máximo 10MB)</p>
                                @error('file')
                                    <span class="mt-2 text-red-500 text-sm">{{ $message }}</span>    
                                @enderror
                            </div>
                            <input id="dropzone-file" type="file" accept=".zip" name="file" id="file" class="hidden"/>
                        </label>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button class="dark:bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md" type="submit">Subir plantilla</button>
                    </div>

                </form>



            </div>
        </div>
    
    </div>

    <script>
        document.getElementById('dropzone-file').addEventListener('change', function() {
            var file = this.files[0];
            var maxSize = 10 * 1024 * 1024; // 10MB
    
            if (file.size > maxSize) {
                alert('El tamaño del archivo excede el límite permitido de 10MB.');
                this.value = ''; // Limpiar el campo de archivo seleccionado
                document.getElementById('file-name-placeholder').innerHTML = '<span class="font-semibold text-red-500">El archivo excede el límite de tamaño</span>';
            } else {
                // Mostrar el nombre del archivo seleccionado
                document.getElementById('file-name-placeholder').innerHTML = '<span class="font-semibold text-green-500">' + file.name + '</span>';
            }
        });
    </script>

</x-app-layout>



