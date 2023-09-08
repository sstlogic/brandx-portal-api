<?php

namespace Tests\Actions;

use App\Actions\CreatePendingReservationAction;
use App\Actions\DeleteReservationAction;
use App\Models\User;
use Carbon\Carbon;
use Tests\Feature\FeatureTestCase;

class CreatePendingReservationActionTest extends FeatureTestCase
{
    /** @test */
    public function it_can_create_a_single_reservation()
    {
        $user = $this->getUser();

        $data = [
            'user' => $user->uuid,
            'resource' => 16,
            'description' => 'foo',
            'start' => Carbon::now()->setTimezone('Australia/Sydney')->setHour(13)->setMinute((0))->format('c'),
            'end' => Carbon::now()->setTimezone('Australia/Sydney')->setHour(15)->setMinute((0))->format('c'),
            'attendees' => 1,
            'generatingIncome' => false,
            'funded' => false,
            'performance' => false,
        ];

        $pendingReservation = app(CreatePendingReservationAction::class)->execute($data);
        $deletion = app(DeleteReservationAction::class)->execute($pendingReservation->reference_number);

        $this->assertNotNull($pendingReservation->reference_number);

        $this->assertTrue($deletion->successful());
    }

    /** @test */
    public function it_creates_a_monthly_reservation()
    {
        $user = $this->getUser();

        $data = [
            'user' => $user->uuid,
            'resource' => 16,
            'description' => 'foo',
            'start' => Carbon::now()->setTimezone('Australia/Sydney')->startOfDay()->format('c'),
            'end' => Carbon::now()->setTimezone('Australia/Sydney')->addMonths(4)->startOfDay()->format('c'),
            'interval_type' => 'monthly-date',
            'interval' => 1,
            'start_time' => Carbon::now()->setTimezone('Australia/Sydney')->setHour(9)->setMinute((0))->format('c'),
            'end_time' => Carbon::now()->setTimezone('Australia/Sydney')->setHour(10)->setMinute((0))->format('c'),
            'attendees' => 1,
            'generatingIncome' => false,
            'funded' => false,
            'performance' => false,
        ];

        $pendingReservation = app(CreatePendingReservationAction::class)->execute($data);
        // $deletion = app(DeleteReservationAction::class)->execute($pendingReservation->reference_number);

        $this->assertNotNull($pendingReservation->reference_number);
        // $this->assertTrue($deletion->successful());
    }

    protected function getUser(): User
    {
        $user = $this->postJson('/login', [
            'email' => 'james@lioneagle.solutions',
            'password' => 'password',
        ])->json();

        return User::where('uuid', $user['data']['uuid'])->first();
    }
}

