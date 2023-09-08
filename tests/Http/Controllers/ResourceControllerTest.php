<?php

namespace Tests\Http\Controllers;

use Tests\Feature\FeatureTestCase;

class ResourceControllerTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_all_resources()
    {
        $response = $this->getJson(route('resources.index'));

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
    public function it_can_get_a_single_resource()
    {
        $response = $this->getJson(route('resources.show', ['resource' => 1]));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                ],
            ]);
    }
}

