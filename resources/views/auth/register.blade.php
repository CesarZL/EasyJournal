<x-guest-layout>


    <div class="bg-gray-100 dark:bg-gray-900 h-screen">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                EasyJournal
            </a>
            <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-xl xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Crea una cuenta
                    </h1>
                
                    <x-validation-errors class="mb-4" />
                
                    <form class="space-y-4 md:space-y-6 grid grid-cols-1 md:grid-cols-3 gap-x-4" method="POST" action="{{ route('register') }}">
                        @csrf
                
                        <div>
                            <x-label for="name" value="{{ __('Nombre') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        </div>
                
                        <div>
                            <x-label for="lastname" value="{{ __('Apellido') }}" />
                            <x-input id="lastname" class="block mt-1 w-full" type="text" name="lastname" :value="old('lastname')" required autocomplete="lastname" />
                        </div>
                
                        <div>
                            <x-label for="surname" value="{{ __('Segundo apellido') }}" />
                            <x-input id="surname" class="block mt-1 w-full" type="text" name="surname" :value="old('surname')" required autocomplete="surname" />
                        </div>
                
                        <div>
                            <x-label for="phone" value="{{ __('Teléfono') }}" />
                            <x-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required autocomplete="phone" />
                        </div>
                
                        <div>
                            <x-label for="country" value="{{ __('País') }}" />
                            <x-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country')" required autocomplete="country" />
                        </div>
                        
                        <div>
                            <x-label for="orcid" value="{{ __('ORCID') }}" />
                            <x-input id="orcid" class="block mt-1 w-full" type="text" name="orcid" :value="old('orcid')" required autocomplete="orcid" />
                        </div>

                        <div>
                            <x-label for="institution" value="{{ __('Institution') }}" />
                            <x-input id="institution" class="block mt-1 w-full" type="text" name="institution" :value="old('institution')" required autocomplete="institution" />
                        </div>

                        <div>
                            <x-label for="affiliation" value="{{ __('Affiliation') }}" />
                            <x-input id="affiliation" class="block mt-1 w-full" type="text" name="affiliation" :value="old('affiliation')" required autocomplete="affiliation" />
                        </div>
                
                        <div>
                            <x-label for="email" value="{{ __('Email') }}" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        </div>
                
                        <div class="md:col-span-3">
                            <x-label for="password" value="{{ __('Contraseña') }}" />
                            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                        </div>
                
                        <div class="md:col-span-3">
                            <x-label for="password_confirmation" value="{{ __('Confirmar contraseña') }}" />
                            <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                        </div>
                
                        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <div class="md:col-span-3">
                                <x-label for="terms">
                                    <div class="flex items-center">
                                        <x-checkbox name="terms" id="terms" required />
                                        <div class="ms-2">
                                            {!! __('Acepto los :terms_of_service y la :privacy_policy', [
                                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Términos de Servicio').'</a>',
                                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Política de Privacidad').'</a>',
                                            ]) !!}
                                        </div>
                                    </div>
                                </x-label>
                            </div>
                        @endif
                
                        <div class="md:col-span-3">
                            <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Crear cuenta</button>
                        </div>
                    </form>
                
                    <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                        ¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Inicia sesión</a>
                    </p>
                </div>
                
            </div>
        </div>
      </div>
      


</x-guest-layout>
