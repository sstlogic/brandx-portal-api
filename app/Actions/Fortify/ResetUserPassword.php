<?php

namespace App\Actions\Fortify;

use App\Booked\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function reset($user, array $input)
    {
        Validator::make($input, [
            'password' => ['required', 'string', 'min:8'],
        ])->validate();

        $repository = app(UserRepository::class);

        $bookedUser = $repository->findByEmail($input['email']);

        $repository->updatePassword($bookedUser->id, '', $input['password']);
    }
}
