<x-create-modal/>

<div class="shadow rounded-lg dark:bg-gray-800 bg-white">
    
    <div class=" p-4 relative rounded-lg overflow-hidden">
        
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-5">
            <div class="w-full md:w-1/4">
                <form class="flex items-center">
                    <label for="simple-search" class="sr-only">Buscar</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="simple-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Buscar" required="">
                    </div>
                </form>
            </div>
        </div>

        <div class="relative overflow-x-auto rounded-lg border border-gray-200  dark:border-gray-700">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Artículo
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Autor
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Fecha de creación
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Fecha de actualización
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Acción
                        </th>
                    </tr>
                </thead>
                <tbody id="searchable-table-body">
                    @foreach ($articles as $article)
                                
                    {{-- <tr class="bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 {{ $i == $totalFilas - 1 ? '' : 'border-b' }}"> --}}
                    <tr class="bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 {{ $loop->last ? '' : 'border-b' }}">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{-- Ruta para editar el articulo --}}
                            {{-- Route::get('/articles/{article}/edit', [App\Http\Controllers\ArticleController::class, 'edit'])->name('articles.edit'); --}}

                            <a href="{{ route('articles.edit', $article) }}" class="capitalize">
                                {{ $article->title }}
                            </a>
                        </th>
                        <td class="px-6 py-4">
                            {{ $article->user->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $article->created_at }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $article->updated_at }}
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('articles.destroy', $article) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-red-600 dark:text-red-500">Borrar</button>
                            </form>

                            <a href="{{ route('articles.edit-details', $article) }}" class="ms-2 font-medium text-yellow-600 dark:text-yellow-400">Editar</a>
                            
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
    
</div>

<script>
    // Obtenemos el input de búsqueda
    const searchInput = document.getElementById('simple-search');

    // Obtenemos el cuerpo de la tabla donde se agregarán las filas filtradas
    const tableBody = document.getElementById('searchable-table-body');

    // Función para filtrar los resultados
    const filterResults = () => {
        // Obtenemos el valor del input de búsqueda
        const searchTerm = searchInput.value.toLowerCase();

        // Obtenemos todas las filas de la tabla
        const rows = tableBody.getElementsByTagName('tr');

        // Iteramos sobre cada fila y la mostramos o ocultamos dependiendo si coincide con el término de búsqueda
        for (let row of rows) {
            const rowData = row.textContent.toLowerCase();
            if (rowData.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    };

    // Escuchamos el evento 'input' en el input de búsqueda para llamar a la función de filtrado
    searchInput.addEventListener('input', filterResults);
</script>
