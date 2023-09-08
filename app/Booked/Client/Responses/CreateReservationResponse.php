<?php

namespace App\Booked\Client\Responses;

use Illuminate\Contracts\Support\Arrayable;

class CreateReservationResponse extends BookedResponse implements Arrayable
{
    public function referenceNumber(): string
    {
        return $this->json('referenceNumber');
    }

    public function pendingApproval(): bool
    {
        return $this->json('isPendingApproval');
    }

    public function toArray(): array
    {
        return [
            'referenceNumber' => $this->referenceNumber(),
            'pendingApproval' => $this->pendingApproval(),
        ];
    }
}
