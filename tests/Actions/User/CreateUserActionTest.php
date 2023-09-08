<?php

namespace Tests\Actions\User;

use App\Actions\User\CreateUserAction;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\FeatureTestCase;

class CreateUserActionTest extends FeatureTestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    /** @test */
    public function it_creates_a_new_user_in_booked()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->numerify("04########"),
            'password' => 'password',
        ];

        app(CreateUserAction::class)->execute($data);

        // $spy->shouldHaveReceived('post');
        // $this->assertInstanceOf(BookedUser::class, $bookedUser);
    }
}

