<?php

namespace App\Http\Controllers;

use App\Booked\Repositories\UserRepository;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class UpdateUserPasswordController extends Controller
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function __invoke(UpdatePasswordRequest $request)
    {
        $user = Auth::user();
        $this->repository->updatePassword(
            $user->external_id,
            $request->input('current_password'),
            $request->input('password')
        );

        return new UserResource($user);
    }
}
