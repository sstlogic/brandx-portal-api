<?php

namespace App\Providers;

use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Booked\Client\BookedAuthClient;
use App\Booked\Models\BookedUser;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    protected BookedAuthClient $authClient;

    public function register()
    {
        // $this->app->instance(
        //     LoginResponse::class,
        //     new class implements LoginResponse
        //     {
        //         public function toResponse($request): UserResource
        //         {
        //             return new UserResource($request->user());
        //         }
        //     }
        // );
        $this->app->instance(
            LoginResponse::class,
            new class implements LoginResponse
            {
                public function toResponse($request)
                {
                    $user = $request->user();
                    $token = $user->createToken('MyToken')->plainTextToken;
                    $userResource =  new UserResource($user);

                    $userResource = $userResource->toArray($request);

                    // return new UserResource($user);
                    return response()->json(
                        ["data" => [
                            'token' => $token,
                            'uuid' => $userResource['uuid'],
                            'firstName' => $userResource["firstName"],
                            'lastName' => $userResource['lastName'],
                            'email' => $userResource['email'],
                            'phone' => $userResource['phone'],
                            'member' => $userResource['member'],
                            'memberSince' => $userResource['memberSince'],
                            'memberExpiry' => $userResource['memberExpiry'],
                            'memberRenewal' => $userResource['memberRenewal'],
                            'autoRenew' => !$userResource['autoRenew'],
                            'last4' => $userResource['last4'],
                            'existingMember' => $userResource['existingMember'],
                            'organisation' => $userResource['organisation'],
                            'customAttributes' => $userResource['customAttributes'],
                        ]],
                    );
                }
            }
        );


        $this->app->singleton(
            FailedPasswordResetLinkRequestResponse::class,
            fn () => new class implements FailedPasswordResetLinkRequestResponse
            {
                public function toResponse($request)
                {
                    return response('', 200);
                }
            }
        );
    }

    public function boot(BookedAuthClient $authClient)
    {
        $this->authClient = $authClient;

        Fortify::authenticateUsing(fn (Request $request) => $this->authenticate($request));

        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');

            return Limit::perMinute(5)->by($email . $request->ip());
        });

        ResetPassword::createUrlUsing(function (User $user, string $token) {
            $base = config('brandx.frontend_url') . '/reset-password';

            return $base . "?email=" . $user->email . "&token=" . $token;
        });
    }

    protected function authenticate(Request $request): ?User
    {
        $bookedUser = $this->authClient->login(
            username: $request->input('email'),
            password: $request->input('password')
        );

        if ($bookedUser) {
            $user = $this->findOrCreateUser($bookedUser);

            $this->updateUser($bookedUser, $user);
            $token = $user->createToken($user->id)->plainTextToken;
            $user['token'] = $token;
            return $user;
        }

        return null;
    }

    protected function findOrCreateUser(BookedUser $bookedUser): User
    {
        $user = User::where('email', $bookedUser->emailAddress)->first();

        if (!$user) {
            $user = $this->createUser($bookedUser);
        }



        return $user;
    }

    protected function updateUser(BookedUser $bookedUser, User $user)
    {
        return $user->update([
            'first_name' => $bookedUser->firstName,
            'last_name' => $bookedUser->lastName,
            'phone' => $bookedUser->phoneNumber,
            'existing_membership_expiry' => $bookedUser->existingMemberExpiry(),
            'organisation' => (bool) $bookedUser->organization,
            'member_since' => $bookedUser->getAttribute('member_date'),
        ]);
    }

    protected function createUser(BookedUser $bookedUser): User
    {
        return User::create([
            'email' => $bookedUser->emailAddress,
            'first_name' => $bookedUser->firstName,
            'last_name' => $bookedUser->lastName,
            'external_id' => $bookedUser->id,
            'phone' => $bookedUser->phoneNumber,
            'existing_membership_expiry' => $bookedUser->existingMemberExpiry(),
            'organisation' => (bool) $bookedUser->organization,
            'member_since' => Carbon::parse($bookedUser->getAttribute('member_date')),
        ]);
    }
}