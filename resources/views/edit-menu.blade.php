
<aside id="default-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidenav">
   <div class="overflow-y-auto p-4 h-full bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700">
       
        <a class="flex items-center ps-2.5 mb-5 bg-primary-50 rounded-md p-2 " href="{{ route('dashboard') }}">
            <x-application-mark/>
        </a>
    
        <ul class="space-y-2">

        @php
            $isDashboardRoute = request()->routeIs('dashboard');
        @endphp

        @if ($isDashboardRoute)
            <li>
                <a data-modal-target="static-modal-new" data-modal-toggle="static-modal-new" type="button" class="mb-5 flex items-center p-2 text-base font-normal rounded-md dark:text-white hover:bg-primary-700 dark:bg-primary-700  dark:hover:bg-primary-500 group bg-primary-200 text-primary-800 hover:text-primary-100 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary-800 transition duration-75 dark:text-white group-hover:text-primary-100 dark:group-hover:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span class="ml-3">Crear artículo</span>
                </a>
            </li>

            <hr class="pt-3">
        @endif

        <li>
            <a href="{{route('dashboard')}}" class="flex items-center p-2 text-base font-normal text-gray-900 rounded-md dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group @if(request()->routeIs('dashboard')) bg-primary-200 text-primary-800 hover:text-primary-100 dark:bg-primary-700 dark:text-white dark:hover:bg-primary-500 @endif">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 w-5 h-5 text-gray-600 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                </svg>
               
                <span class="flex-1 ml-3 whitespace-nowrap">Mis artículos</span>
                {{-- <span class="inline-flex justify-center items-center w-6 h-6 text-xs font-semibold rounded-full text-primary-800 bg-primary-100 dark:bg-primary-200 dark:text-primary-800">
                    0   
                </span> --}}
            </a>
        </li>
           
        <li>
            <a href="{{route('templates')}}" class="flex items-center p-2 text-base font-normal text-gray-900 rounded-md dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group @if(request()->routeIs('templates')) bg-primary-200 text-primary-800 hover:text-primary-100 dark:bg-primary-700 dark:text-white dark:hover:bg-primary-500 @endif">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 w-5 h-5 text-gray-600 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                
                <span class="flex-1 ml-3 whitespace-nowrap">Mis plantillas</span>
                {{-- <span class="inline-flex justify-center items-center w-6 h-6 text-xs font-semibold rounded-full text-primary-800 bg-primary-100 dark:bg-primary-200 dark:text-primary-800">
                    0   
                </span> --}}
            </a>
        </li>

        <li>
            <a href="{{route('coauthors')}}" class="flex items-center p-2 text-base font-normal text-gray-900 rounded-md dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group @if(request()->routeIs('coauthors')) bg-primary-200 text-primary-800 hover:text-primary-100 dark:bg-primary-700 dark:text-white dark:hover:bg-primary-500 @endif">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 w-5 h-5 text-gray-600 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                  </svg>
                <span class="flex-1 ml-3 whitespace-nowrap">Coautores</span>

            </a>
        </li>

        {{-- <hr class="border-t border-gray-900 dark:border-white my-3"> --}}

        {{-- <li>
            <button type="button" class="flex items-center p-2 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700" aria-controls="subsections" data-collapse-toggle="subsections">
                <span class="flex-1 px-2 text-left whitespace-nowrap">Section 1</span>
                <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
            <ul id="subsections" class="hidden py-2 space-y-2">
                <li>
                    <a href="#" class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Subsection 1</a>
                    <ul class="pl-5">
                        <li>
                            <a href="#" class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Subsubsection 1.1</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Subsection 2</a>
                </li>
            </ul>
        </li>        

       </ul> --}}
   </div>

    <div class="fixed bottom-0  left-0 justify-center p-4 space-x-4 w-64 flex bg-white dark:bg-gray-800 z-20 border-r border-gray-200 dark:border-gray-700">

        {{-- /articles/{article}/edit/details', --}}



        <a href="{{ route('articles.edit-details', ['article' => $article->id]) }}" data-tooltip-target="tooltip-settings" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer dark:text-gray-400 dark:hover:text-white hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-600">            
           
            <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 w-7 h-7 text-gray-600 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
              
        </a>

        <div id="tooltip-settings" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-md shadow-sm opacity-0 transition-opacity duration-300 tooltip">
            Editar detalles
            <div class="tooltip-arrow" data-popper-arrow></div>
        </div>

        <button type="button" data-dropdown-toggle="language-dropdown" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer dark:hover:text-white dark:text-gray-400 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-600">
            <img class="h-7 w-7 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
        </button>

        <!-- Dropdown -->
        <div class="hidden z-50 my-4 text-base list-none rounded-xl bg-gray-100  divide-y divide-gray-100 shadow dark:bg-gray-700" id="language-dropdown">
            <ul class="p-1" role="none">
                <li class="text-center">
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <a href="{{ route('logout') }}" class="block py-2 px-4 text-sm text-gray-700 rounded-lg hover:bg-red-300 dark:hover:text-white dark:text-gray-300 dark:hover:bg-red-500" role="menuitem" @click.prevent="$root.submit();">
                            <div class="inline-flex items-center">             
                                Cerrar sesión
                            </div>
                        </a>
                    </form>
                </li>

                <li class="text-center">
                    <a href="{{ route('profile.show') }}" class="mt-1 block py-2 px-4 text-sm text-gray-700 rounded-lg hover:bg-red-300 dark:hover:text-white dark:text-gray-300 dark:hover:bg-green-500" role="menuitem">
                        <div class="inline-flex items-center">             
                            Configuración
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>

</aside>