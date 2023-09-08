<?php

namespace App\Console\Commands;

use App\Booked\Repositories\AttributeRepository;
use Cache;
use Illuminate\Console\Command;

class CreateReservationAttributeCommand extends Command
{
    protected $signature = 'reservation-attributes:create {name}';

    protected $description = 'Command description';

    public function handle(
        AttributeRepository $attributeRepository
    ) {
        $name = $this->argument('name');

        Cache::forget('booked:reservations:attributes');

        $id = $attributeRepository->reservationAttributeId($name);

        if ($id) {
            $this->warn("The attribute '$name' already exist.");
            $this->info("id: $id");
            $this->newLine();
        } else {
            $attributeRepository->createReservationAttribute($this->argument('name'));
            $this->line("Attribute '$name' created.");
        }
    }
}
