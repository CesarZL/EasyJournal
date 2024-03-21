<x-guest-layout>
    <div class="bg-gray-100 dark:bg-gray-900  h-screen">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                EasyJournal  
            </a>
            <div class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:p-8">
                <h1 class="mb-1 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    ¿Olvidaste tu contraseña?
                </h1>
                <p class="font-light text-gray-500 dark:text-gray-400">¡No te preocupes! Solo escribe tu correo y te enviaremos un código para restablecer tu contraseña</p>
                

                <form class="mt-4 space-y-4 lg:mt-5 md:space-y-5" method="POST" action="{{ route('password.email') }}">
                    @csrf
    
                    <div>
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    </div>

                    <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Reestablecer contraseña</button>
                
                    <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                        ¿Recordaste tu contraseña? <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Inicia sesión</a>
                    </p>

                </form>

                <x-validation-errors class="mt-4" />


            </div>
    
            @if (session('status'))
                <div class="p-4 mt-4 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400 shadow" role="alert">
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif
        </div>
      </div>
</x-guest-layout>
