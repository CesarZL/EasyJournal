<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Información del perfil') }}
    </x-slot>

    <x-slot name="description">
        {{-- {{ __('Update your account\'s profile information and email address.') }} --}}
        {{ __('Actualiza la información de perfil y la dirección de correo electrónico de tu cuenta.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Foto') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-2xl h-44 w-44 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-2xl w-44 h-44 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-4 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Seleccionar otra foto')}}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Quitar foto')}}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>

        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Nombre') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Father Surname -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="father_surname" value="{{ __('Apellido') }}" />
            <x-input id="father_surname" type="text" class="mt-1 block w-full" wire:model="state.father_surname" required autocomplete="father_surname" />
            <x-input-error for="father_surname" class="mt-2" />
        </div>


        <!-- Mother Surname -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="mother_surname" value="{{ __('Segundo apellido') }}" />
            <x-input id="mother_surname" type="text" class="mt-1 block w-full" wire:model="state.mother_surname" required autocomplete="mother_surname" />
            <x-input-error for="mother_surname" class="mt-2" />

        </div>

        <!-- Phone -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="phone" value="{{ __('Teléfono') }}" />
            <x-input id="phone" type="text" class="mt-1 block w-full" wire:model="state.phone" required autocomplete="phone" />
            <x-input-error for="phone" class="mt-2" />
        </div>

        <!-- Country -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="country" value="{{ __('País') }}" />
            <select id="country" name="country" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" wire:model="state.country">
                <option value="México">México</option>
                <option value="Estados Unidos">Estados Unidos</option>
                <option value="Canadá">Canadá</option>
                <option value="Argentina">Argentina</option>
                <option value="Brasil">Brasil</option>
                <option value="Colombia">Colombia</option>
                <option value="Chile">Chile</option>
            </select>
            <x-input-error for="country" class="mt-2" />
        </div>

        <!-- ORCID -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="orcid" value="{{ __('ORCID') }}" />
            <x-input id="orcid" type="text" class="mt-1 block w-full" wire:model="state.orcid" required autocomplete="orcid" oninput="formatOrcid(this)" maxlength="19" pattern="[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}" />
            <x-input-error for="orcid" class="mt-2" />
        </div>

        <!-- Institution -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="institution" value="{{ __('Institución') }}" />
            <x-input id="institution" type="text" class="mt-1 block w-full" wire:model="state.institution" required autocomplete="institution" />
            <x-input-error for="institution" class="mt-2" />
        </div>

        <!-- Affiliation -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="affiliation" value="{{ __('Afiliación') }}" />
            <x-input id="affiliation" type="text" class="mt-1 block w-full" wire:model="state.affiliation" required autocomplete="affiliation" />
            <x-input-error for="affiliation" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 dark:text-white">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{-- {{ __('Saved.') }} --}}
            {{ __('Guardado.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{-- {{ __('Save') }} --}}
            {{ __('Guardar') }}
        </x-button>
    </x-slot>
</x-form-section>
