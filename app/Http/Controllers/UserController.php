<?php

namespace App\Http\Controllers;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Booked\Repositories\UserRepository;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\BookedUserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        //
    }

    public function store(CreateUserRequest $request, CreateUserAction $action, UserRepository $repository)
    {
        $user = $action->execute($request->validated());
        $bookedUser = $repository->find($user->id);
        $user->customAttributes = new BookedUserResource($bookedUser);
        return response($user, 200);
    }

    public function show(UserRepository $repository, User $user)
    {
        $bookedUser = $repository->find($user->external_id);
    }

    public function update(UpdateUserRequest $request, UpdateUserAction $action, User $user, UserRepository $repository)
    {
        // return response(json_encode($request->toArray()), 200);
        $bookedUser = $action->execute($user, $request->validated());
        $bookedUser_ = $repository->find($user->external_id);
        $bookedUser->customAttributes = new BookedUserResource($bookedUser_);

        return response($bookedUser, 200);
    }

    public function destroy(User $user)
    {
        //
    }

    public function asBookedUser(UserRepository $repository)
    {
        ray(Auth::user());
        $bookedUser = $repository->find(Auth::user()->external_id);

        return new BookedUserResource($bookedUser);
    }
}