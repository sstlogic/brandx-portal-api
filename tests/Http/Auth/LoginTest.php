<?php

namespace Tests\Http\Auth;

use App\Models\User;
use Tests\Feature\FeatureTestCase;

class LoginTest extends FeatureTestCase
{
    /** @test */
    public function it_can_login_with_user_that_does_not_exist()
    {
        $response = $this->postJson('login', [
            'email' => 'api@lioneagle.solutions',
            'password' => 'password',
        ]);

        $response->assertSuccessful();
        $this->assertAuthenticated();
        $this->assertAuthenticatedAs(User::where('email', 'api@lioneagle.solutions')->first());
    }

    /** @test */
    public function it_can_login_with_user_that_does_exist()
    {
        User::factory()->create(['email' => 'api@lioneagle.solutions']);
        
        $response = $this->postJson('login', [
            'email' => 'api@lioneagle.solutions',
            'password' => 'password',
        ]);

        $response->assertSuccessful();
        $this->assertAuthenticated();
        $this->assertAuthenticatedAs(User::where('email', 'api@lioneagle.solutions')->first());
    }
}
