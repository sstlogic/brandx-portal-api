<?php

namespace App\Actions;

use Arr;
use Carbon\Carbon;
use Closure;
use DB;

abstract class BaseAction
{
    protected ActionData $data;

    protected function setData(array $data): static
    {
        $this->data = new ActionData($data);

        return $this;
    }

    protected function dataHas($key): bool
    {
        return Arr::has($this->data, $key);
    }

    protected function dataMissing($key): bool
    {
        return ! $this->dataHas($key);
    }

    protected function whenDataHas($key, Closure $closure, bool $pull = true): static
    {
        if ($this->data->has($key)) {
            $closure($this->data->pullIf($pull, $key));
        }

        return $this;
    }

    protected function getDateFromData(string $date): Carbon
    {
        $raw = $this->data[$date];

        return $raw instanceof Carbon ? $raw : Carbon::parse($raw);
    }

    protected function transaction(Closure $callback): static
    {
        DB::transaction($callback);

        return $this;
    }
}
