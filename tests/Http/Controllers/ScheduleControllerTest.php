<?php

namespace Tests\Http\Controllers;

use Tests\Feature\FeatureTestCase;

class ScheduleControllerTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_all_schedules()
    {
        $response = $this->getJson(route('schedules.index'));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_get_a_single_schedule()
    {
        $response = $this->getJson(route('schedules.show', ['schedule' => 1]));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                ],
            ]);
    }
}

