<?php

namespace App\Rules;

use App\Booked\Client\BookedAuthClient;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class CurrentPasswordRule implements Rule
{
    private mixed $authClient;

    public function __construct(private User $user)
    {
        $this->authClient = app(BookedAuthClient::class);
    }

    public function passes($attribute, $value): bool
    {
        return $this->isPasswordCorrect($value);
    }

    public function message(): string
    {
        return 'Current password is incorrect.';
    }

    protected function isPasswordCorrect(string $password): bool
    {
        $bookedUser = $this->authClient->login(
            username: $this->user->email,
            password: $password
        );

        return ! is_null($bookedUser);
    }
}
