
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
        

        

       </ul>
   </div>

    <div class="fixed bottom-0  left-0 justify-center p-4 space-x-4 w-64 flex bg-white dark:bg-gray-800 z-20 border-r border-gray-200 dark:border-gray-700">

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