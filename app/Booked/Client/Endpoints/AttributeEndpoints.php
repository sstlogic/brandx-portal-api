<?php

namespace App\Booked\Client\Endpoints;

use App\Booked\Repositories\AttributeRepository;
use Illuminate\Support\Collection;

trait AttributeEndpoints
{
    public function getCategoryAttributes(int $category): Collection
    {
        return $this->get("/Attributes/Category/$category")->collect('attributes');
    }

    public function createReservationAttribute(string $attributeName)
    {
        return $this->post('/Attributes/', [
            'label' => $attributeName,
            'categoryId' => AttributeRepository::RESERVATION_CATEGORY,
            'type' => 1,
            'adminOnly' => true,
        ]);
    }
}
