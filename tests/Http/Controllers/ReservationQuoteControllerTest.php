<?php

namespace Tests\Http\Controllers;

use Carbon\Carbon;
use Tests\Feature\FeatureTestCase;

class ReservationQuoteControllerTest extends FeatureTestCase
{
    /** @test */
    public function it_calculates_price()
    {
        $data = [
            'start' => Carbon::now(),
            'end' => Carbon::now()->addHours(2),
            'attendees' => 1,
            'generatingIncome' => false,
            'funded' => false,
            'performance' => false,
        ];

        $response = $this->postJson(route('reservations.quote'), $data);

        $response->assertSuccessful()
            ->assertJson([
                'rate' => config('brandx.prices.solo.hourly'),
                'price' => config('brandx.prices.solo.hourly') * 2,
                'duration' => 2,
                'discount' => false,
                'discountType' => null,
            ]);
    }
}

