<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'father_surname' => ['required', 'string', 'max:255'],
            'mother_surname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric', 'digits:10'], // 10 digits
            'country' => ['required', 'string', 'max:255'],
            'orcid' => ['required', 'string', 'max:20'],
            'institution' => ['required', 'string', 'max:255'],
            'affiliation' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],

                'father_surname' => $input['father_surname'],
                'mother_surname' => $input['mother_surname'],
                'phone' => $input['phone'],
                'country' => $input['country'],
                'orcid' => $input['orcid'],
                'institution' => $input['institution'],
                'affiliation' => $input['affiliation'],


            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}


// <!-- Father Surname -->
//         <div class="col-span-6 sm:col-span-4">
//             <x-label for="father_surname" value="{{ __('Apellido') }}" />
//             <x-input id="father_surname" type="text" class="mt-1 block w-full" wire:model="state.father_surname" required autocomplete="father_surname" />
//             <x-input-error for="father_surname" class="mt-2" />
//         </div>


//         <!-- Mother Surname -->
//         <div class="col-span-6 sm:col-span-4">
//             <x-label for="mother_surname" value="{{ __('Segundo apellido') }}" />
//             <x-input id="mother_surname" type="text" class="mt-1 block w-full" wire:model="state.mother_surname" required autocomplete="mother_surname" />
//             <x-input-error for="mother_surname" class="mt-2" />

//         </div>

//         <!-- Phone -->
//         <div class="col-span-6 sm:col-span-4">
//             <x-label for="phone" value="{{ __('Teléfono') }}" />
//             <x-input id="phone" type="text" class="mt-1 block w-full" wire:model="state.phone" required autocomplete="phone" />
//             <x-input-error for="phone" class="mt-2" />
//         </div>

//         <!-- Country -->
//         <div class="col-span-6 sm:col-span-4">
//             <x-label for="country" value="{{ __('País') }}" />
//             <select id="country" name="country" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" wire:model="state.country">
//                 <option value="México">México</option>
//                 <option value="Estados Unidos">Estados Unidos</option>
//                 <option value="Canadá">Canadá</option>
//                 <option value="Argentina">Argentina</option>
//                 <option value="Brasil">Brasil</option>
//                 <option value="Colombia">Colombia</option>
//                 <option value="Chile">Chile</option>
//             </select>
//             <x-input-error for="country" class="mt-2" />
//         </div>

//         <!-- ORCID -->
//         <div class="col-span-6 sm:col-span-4">
//             <x-label for="orcid" value="{{ __('ORCID') }}" />
//             <x-input id="orcid" type="text" class="mt-1 block w-full" wire:model="state.orcid" required autocomplete="orcid" oninput="formatOrcid(this)" maxlength="19" pattern="[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}" />
//             <x-input-error for="orcid" class="mt-2" />
//         </div>

//         <!-- Institution -->
//         <div class="col-span-6 sm:col-span-4">
//             <x-label for="institution" value="{{ __('Institución') }}" />
//             <x-input id="institution" type="text" class="mt-1 block w-full" wire:model="state.institution" required autocomplete="institution" />
//             <x-input-error for="institution" class="mt-2" />
//         </div>

//         <!-- Affiliation -->
//         <div class="col-span-6 sm:col-span-4">
//             <x-label for="affiliation" value="{{ __('Afiliación') }}" />
//             <x-input id="affiliation" type="text" class="mt-1 block w-full" wire:model="state.affiliation" required autocomplete="affiliation" />
//             <x-input-error for="affiliation" class="mt-2" />
//         </div>