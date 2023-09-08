<?php

namespace App\Http\Controllers;

use App\Booked\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserExistsController extends Controller
{
    public function __invoke(Request $request, UserRepository $repository)
    {
        $request->validate([
            'email' => ['string', 'nullable'],
        ]);

        if (! $email = $request->input('email')) {
            return $this->doesNotExist();
        }

        $user = $repository->findByEmail($email);

        return $user
            ? $this->exists($email)
            : $this->doesNotExist($email);
    }

    private function doesNotExist(?string $email = null)
    {
        return $this->response(false, $email);
    }

    private function exists(string $email)
    {
        return $this->response(true, $email);
    }

    private function response(bool $exists, ?string $email = null)
    {
        return new JsonResponse([
            'email' => $email,
            'exists' => $exists,
        ]);
    }
}
