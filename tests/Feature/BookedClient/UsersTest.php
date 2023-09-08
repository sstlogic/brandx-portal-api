<?php

namespace Tests\Feature\BookedClient;

use App\Booked\Models\BookedUser;
use App\Booked\Repositories\UserRepository;
use App\Exceptions\BookedModelNotFoundException;
use Faker\Generator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Tests\Feature\FeatureTestCase;

class UsersTest extends FeatureTestCase
{
    protected UserRepository $repository;

    protected Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = app(Generator::class);
        $this->repository = app(UserRepository::class);
    }

    /** @test */
    public function it_can_get_all_users()
    {
        $userCollection = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $userCollection);

        $userCollection->each(fn (BookedUser $user) => $this->assertInstanceOf(BookedUser::class, $user));
    }

    /** @test */
    public function it_can_get_users_by_email()
    {
        $user = $this->repository->findByEmail('api@lioneagle.solutions');

        $this->assertInstanceOf(BookedUser::class, $user);
    }

    /** @test */
    public function it_returns_null_if_user_not_found()
    {
        $user = $this->repository->findByEmail('foo-email@does-not-exist.com');
        
        $this->assertNull($user);
    }

    /** @test */
    public function it_can_get_a_single_user()
    {
        /** @var BookedUser $user */
        $user = $this->repository->all()->random();

        $user = $this->repository->find($user->id);

        $this->assertInstanceOf(BookedUser::class, $user);
    }

    /** @test */
    public function it_throws_exception_if_user_not_found()
    {
        $this->expectException(BookedModelNotFoundException::class);
        $this->repository->find(9999999);
    }

    /** @test */
    public function it_can_create_a_new_user()
    {
        $data = [
            "password" => "password",
            "language" => "en_us",
            "firstName" => "first",
            "lastName" => "last",
            "emailAddress" => $this->faker->safeEmail(),
            "userName" => $this->faker->safeEmail(),
            "timezone" => "America\/Chicago",
            "phone" => "123-456-7989",
            "organization" => "organization",
            "position" => "position",
        ];

        $user = $this->repository->create($data);

        $this->assertInstanceOf(BookedUser::class, $user);
    }

    /** @test */
    public function it_throws_validation_error_with_invalid_data_when_creating_a_user()
    {
        $data = [
            "password" => "password",
            "language" => "en_us",
            "firstName" => "first",
            "lastName" => "last",
            "emailAddress" => "invalid email",
            "userName" => "not a good username",
            "timezone" => "America\/Chicago",
            "phone" => "123-456-7989",
            "organization" => "organization",
            "position" => "position",
        ];

        $this->expectException(ValidationException::class);
        $this->repository->create($data);
    }

    /** @test */
    public function it_can_delete_a_user_when_given_a_user()
    {
        $data = [
            "password" => "password",
            "language" => "en_us",
            "firstName" => "first",
            "lastName" => "last",
            "emailAddress" => $this->faker->safeEmail(),
            "userName" => $this->faker->safeEmail(),
            "timezone" => "America\/Chicago",
            "phone" => "123-456-7989",
            "organization" => "organization",
            "position" => "position",
        ];

        $user = $this->repository->create($data);

        $result = $this->repository->delete($user);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_delete_a_user_when_given_a_user_id()
    {
        $data = [
            "password" => "password",
            "language" => "en_us",
            "firstName" => "first",
            "lastName" => "last",
            "emailAddress" => $this->faker->safeEmail(),
            "userName" => $this->faker->safeEmail(),
            "timezone" => "America\/Chicago",
            "phone" => "123-456-7989",
            "organization" => "organization",
            "position" => "position",
        ];

        $user = $this->repository->create($data);

        $result = $this->repository->delete($user->id);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_update_a_user_password()
    {
        $data = [
            "password" => "password",
            "language" => "en_us",
            "firstName" => "first",
            "lastName" => "last",
            "emailAddress" => $this->faker->safeEmail(),
            "userName" => $this->faker->safeEmail(),
            "timezone" => "America\/Chicago",
            "phone" => "123-456-7989",
            "organization" => "organization",
            "position" => "position",
        ];

        $user = $this->repository->create($data);

        $updatedUser = $this->repository->updatePassword($user, 'new password');

        $this->assertInstanceOf(BookedUser::class, $updatedUser);
    }
}
