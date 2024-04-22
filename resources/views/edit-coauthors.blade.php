<x-app-layout>

    <div class="flex flex-col xl:flex-row ">

        <!-- Columna izquierda -->
        <div class="flex-1 xl:pe-3 py-3 xl:py-0">
            <div class="p-5 rounded-lg bg-gray-50 dark:bg-gray-800 overflow-y-auto h-screen"> 

                {{-- card header 1 --}}
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4 dark:text-white">Editar coautor</h2>
                </div>

                {{-- Horizontal line  --}}
                <div class="border-b border-gray-300 dark:border-gray-600 my-4"></div>
                
                <form action="{{ route('coauthors.update', $coauthor) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mt-4">
                            <x-label-required for="name" :value="__('Nombre')" />
                            <x-input id="name" name="name" type="text" value="{{ old('name', $coauthor->name) }}" />
                            @error('name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-label-required for="father_surname" :value="__('Apellido paterno')" />
                            <x-input id="father_surname" name="father_surname" type="text" value="{{ old('father_surname', $coauthor->father_surname) }}" />
                            @error('father_surname')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-label-required for="mother_surname" :value="__('Apellido materno')" />
                            <x-input id="mother_surname" name="mother_surname" type="text" value="{{ old('last_name', $coauthor->mother_surname) }}" />
                            @error('mother_surname')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-label-required for="email" :value="__('Correo electrónico')" />
                            <x-input id="email" name="email" type="email" value="{{ old('email', $coauthor->email) }}" />
                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-label-required for="institution" :value="__('Institución')" />
                            <x-input id="institution" name="institution" type="text" value="{{ old('institution', $coauthor->institution) }}" />
                            @error('institution')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror                        
                        </div>

                        <div class="mt-4">
                            <x-label for="affiliation_url" :value="__('URL de la institution')" />
                            <x-input id="affiliation_url" name="affiliation_url" type="text" value="{{ old('affiliation_url', $coauthor->affiliation_url) }}" />
                            @error('affiliation_url')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        
                        <div class="mt-4">
                            <x-label-required for="affiliation" :value="__('Afiliación')" />
                            <x-input id="affiliation" name="affiliation" type="text" value="{{ old('affiliation', $coauthor->affiliation) }}" />
                            @error('affiliation')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- select de paises, el value es el nombre del pais --}}
                        <div class="mt-4">
                            <x-label-required for="country" :value="__('País')" />
                            <select id="country" name="country" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="México" @if(old('country', $coauthor->country) == 'México') selected @endif>México</option>
                                <option value="Estados Unidos" @if(old('country', $coauthor->country) == 'Estados Unidos') selected @endif>Estados Unidos</option>
                                <option value="Canadá" @if(old('country', $coauthor->country) == 'Canadá') selected @endif>Canadá</option>
                                <option value="Argentina" @if(old('country', $coauthor->country) == 'Argentina') selected @endif>Argentina</option>
                                <option value="Brasil" @if(old('country', $coauthor->country) == 'Brasil') selected @endif>Brasil</option>
                                <option value="Colombia" @if(old('country', $coauthor->country) == 'Colombia') selected @endif>Colombia</option>
                                <option value="Chile" @if(old('country', $coauthor->country) == 'Chile') selected @endif>Chile</option>
                            </select>
                            @error('country')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="mt-4">
                            <x-label-required for="phone" :value="__('Teléfono')" />
                            <x-input id="phone" name="phone" type="text" value="{{ old('phone', $coauthor->phone) }}" />
                            @error('phone')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-label-required for="address" :value="__('Dirección')" />
                            <x-input id="address" name="address" type="text" value="{{ old('address', $coauthor->address) }}" />
                            @error('address')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-label-required for="orcid" :value="__('ORCID')" />
                            <x-input id="orcid" type="text" name="orcid" :value="old('orcid')" required autocomplete="orcid" oninput="formatOrcid(this)" maxlength="19" pattern="[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}" value="{{ old('orcid', $coauthor->orcid) }}" />
                            @error('orcid')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-label for="scopus_id" :value="__('Scopus ID')" />
                            <x-input id="scopus_id" name="scopus_id" type="text" value="{{ old('scopus_id', $coauthor->scopus_id) }}" />
                            @error('scopus_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-label for="researcher_id" :value="__('Researcher ID')" />
                            <x-input id="researcher_id" name="researcher_id" type="text" value="{{ old('researcher_id', $coauthor->researcher_id) }}" />
                            @error('researcher_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-label for="url" :value="__('URL')" />
                            <x-input id="url" name="url" type="text" value="{{ old('url', $coauthor->url) }}" />
                            @error('url')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2 mt-4"> <!-- Added col-span-2 class here -->
                            <x-label for="biography" :value="__('Biografía')" />
                            <textarea rows="5" name="biography" id="biography" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">{{old('biography', $coauthor->biography) }}</textarea>
                            @error('biography')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4 flex justify-between space-x-4">
                        <div class="w-1/2">
                            <x-secondary-button class="block w-full">
                                <a href="{{ route('coauthors') }}">{{ __('Cancelar') }}</a>
                            </x-secondary-button>
                        </div>
                        
                        <div class="w-1/2">
                            <x-button class="block w-full">
                                {{ __('Guardar') }}
                            </x-button>
                        </div>
                    </div>

                </form>



            </div>
        </div>
        
                <!-- Columna derecha -->
                <div class="flex-1">
                    <div class="p-5 rounded-lg bg-gray-50 dark:bg-gray-800 h-screen">
        
                        {{-- card header 2 --}}
                        <div class="text-center">
                            <h2 class="text-2xl font-bold mb-4 dark:text-white">Coautores</h2>
                        </div>
        
                        {{-- Horizontal line  --}}
                        <div class="border-b border-gray-300 dark:border-gray-600 my-4"></div>
        
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($coauthors as $coauthor)
                                
                           
                             <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg p-4 dark:text-white hover:cursor-pointer hover:bg-gray-600">
                                 <h3 class="text-lg font-bold mb-2 ">{{$coauthor->name}} {{$coauthor->surname}} {{$coauthor->last_name}}</h3>
                                 <div class="text-sm break-words">
                                    <p>{{$coauthor->institution}}, {{$coauthor->country}}</p>
                                    <p>{{$coauthor->email}}</p>
                                    <p>{{$coauthor->phone}}</p>
                                    <p>{{$coauthor->address}}</p>
                                    <p>{{$coauthor->ORCID}}</p>
                                </div>
        
        
                                 
                                 <div class="flex justify-end mt-4">
                                      <button class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600" onclick="window.location.href='{{ route('coauthors.edit', $coauthor->id) }}'">Editar</button>
                                 </div>
                             </div>
                             @endforeach
                         </div>
        
        
                    </div>
                </div>
      
    
    </div>

    <script>
        function formatOrcid(input) {
            // Elimina todos los guiones previamente ingresados
            var orcid = input.value.replace(/-/g, '');
        
            // Agrega guiones cada 4 dígitos
            var formattedOrcid = orcid.match(/.{1,4}/g).join('-');
        
            // Actualiza el valor del campo con el ORCID formateado
            input.value = formattedOrcid;
        }
        </script>

</x-app-layout>