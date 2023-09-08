<?php

namespace App\Booked\Client;

use App\Booked\Models\LoginResponse;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use function retry;

/**
 * @mixin \App\Booked\Client\BookedClient
 */
trait BookedAuthentication
{
    public function attemptLogin(): LoginResponse
    {
        $response = Http::acceptJson()->post($this->generateUri(self::$login), $this->config->getCredentials())->json();

        return LoginResponse::fromRequest($response);
    }

    public function logout(): bool
    {
        return $this->post(self::$login)->successful();
    }

    public function authenticate(): bool
    {
        $loginAttempt = $this->attemptLogin();

        if ($loginAttempt->isLoggedIn()) {
            $this->setCredentials($loginAttempt);

            return true;
        }

        throw new InvalidCredentialsException($this->config->getCredentials());
    }

    private function refreshAuth()
    {
        $this->resetCredentials();
        $this->getAuthHeaders();
    }

    private function resetCredentials()
    {
        Cache::forget("booked:session_token",);
        Cache::forget("booked:user_id",);
    }

    private function setCredentials(LoginResponse $loginResponse)
    {
        Cache::put("booked:session_token", $loginResponse->sessionToken, $loginResponse->timeUntilExpiry());
        Cache::put("booked:user_id", $loginResponse->userId, $loginResponse->timeUntilExpiry());
    }

    private function getAuthHeaders(): array
    {
        $sessionToken = $this->getSessionToken();
        $sessionUserId = $this->getSessionUserId();

        if (! $sessionToken || ! $sessionUserId) {
            retry(3, fn () => $this->authenticate(), 2000);
        }

        return [
            'X-Booked-SessionToken' => $this->getSessionToken(),
            'X-Booked-UserId' => $this->getSessionUserId(),
        ];
    }

    private function getSessionToken()
    {
        return Cache::get("booked:session_token");
    }

    private function getSessionUserId()
    {
        return Cache::get("booked:user_id");
    }
}
