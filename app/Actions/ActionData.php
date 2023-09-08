<?php

namespace App\Actions;

use Arr;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;

class ActionData implements Arrayable, ArrayAccess
{
    public array $original;

    public function __construct(public array $data = [])
    {
        $this->original = $data;
    }

    public function has(string ...$keys): bool
    {
        return Arr::has($this->data, $keys);
    }

    public function get($key)
    {
        return $this->data[$key];
    }

    public function all(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function pull($key)
    {
        return Arr::pull($this->data, $key);
    }

    public function only(array|string $keys): array
    {
        return Arr::only($this->data, $keys);
    }

    public function except(array|string $keys): array
    {
        return Arr::except($this->data, $keys);
    }

    public function pullIf(bool $condition, $key)
    {
        if ($condition) {
            return $this->pull($key);
        }

        return $this->get($key);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}
