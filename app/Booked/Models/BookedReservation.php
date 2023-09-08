<?php

namespace App\Booked\Models;

/**
 * @property \Carbon\Carbon $startDate
 * @property \Carbon\Carbon $endDate
 * @property string $referenceNumber
 * @property ?array $customAttributes
 * @property int $userId
 */
class BookedReservation extends BookedModel
{
    protected array $dates = [
        'startDate',
        'endDate',
        // 'bufferedStartDate',
        // 'bufferedEndDate',
    ];

    public function userId()
    {
        return $this->owner['userId'];
    }

    public function mergeCustomAttributes(array $attributes)
    {
        if (! $this->customAttributes) {
            $this->customAttributes = [];
        }

        $attributesToMerge = collect($attributes);
        $existing = collect($this->customAttributes);

        $existing = collect($existing)
            ->map(function (array $attribute) use ($attributesToMerge) {
                if ($override = $attributesToMerge->firstWhere('attributeId', $attribute['id'])) {
                    return $override;
                } else {
                    return [
                        'attributeId' => $attribute['id'],
                        'attributeValue' => $attribute['value'],
                    ];
                }
            });

        collect($attributesToMerge)->each(function (array $attribute) use ($existing) {
            if (! $existing->firstWhere('id', $attribute['attributeId'])) {
                $existing->push($attribute);
            }
        });

        return $existing;
    }
}
