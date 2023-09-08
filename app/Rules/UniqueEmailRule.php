<?php

namespace App\Rules;

use App\Booked\Repositories\UserRepository;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UniqueEmailRule implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value): bool
    {
        if ($value === Auth::user()?->email) {
            return true;
        }

        $repository = app(UserRepository::class);

        $user = $repository->findByEmail($value);

        return is_null($user);
    }

    public function message(): string
    {
        return 'This email already exists.';
    }
}
