<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'father_surname' => ['required', 'string', 'max:255'],
            'mother_surname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'orcid' => ['nullable', 'string', 'unique:users', 'max:20', 'min:16', 'regex:/[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}/'],
            'institution' => ['required', 'string', 'max:255'],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'father_surname' => $input['father_surname'],
            'mother_surname' => $input['mother_surname'],
            'phone' => $input['phone'],
            'country' => $input['country'],
            'email' => $input['email'],
            'orcid' => $input['orcid'],
            'institution' => $input['institution'],
            'affiliation' => $input['affiliation'],
            'password' => Hash::make($input['password']),
        ]);
    }
}




// 'orcid',
// 'institution',
// 'affiliation',