<?php

namespace App\Booked\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

abstract class BookedModel implements Arrayable
{
    protected array $dates = [];

    public function __construct(
        private array $attributes
    ) {}

    public static function make(array $attributes): static
    {
        return new static($attributes);
    }

    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    public function __set(string $key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function toArray(): array
    {
        return [
            ...$this->attributes,
            ...$this->getDateAttributes(),
        ];
    }

    public function clone(): static
    {
        return clone $this;
    }

    private function getAttribute(string $key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->isAttributeDate($key)
                ? $this->castToDate($key)
                : $this->attributes[$key];
        }

        return null;
    }

    private function isAttributeDate($key): bool
    {
        return in_array($key, $this->dates) || array_key_exists($key, $this->dates);
    }

    private function castToDate(string $attribute): ?Carbon
    {
        $value = $this->attributes[$attribute];

        if (! $value) {
            return null;
        }

        return Carbon::parse($value);
    }

    private function getDateAttributes(): Collection
    {
        return collect($this->dates)
            ->mapWithKeys(
                fn (string $key) => [$key => $this->castToDate($key)?->setTimezone('Australia/Sydney')->format('c')]
            );
    }

    private function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }
}
