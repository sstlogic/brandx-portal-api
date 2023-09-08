<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateReportingAttributesCommand extends Command
{
    protected $signature = 'reporting-attributes:create';

    protected $description = 'Command description';

    public function handle()
    {
        $attributes = [
            'invoice_amount',
            'instance_cost',
            'rate_paid',
            'invoice_id',
            'payment_status',
            'payment_reference',
            'paid_at',
            'escac_total',
            'coscs_total',
            'studio',
        ];

        collect($attributes)
            ->each(fn (string $attribute) => $this->call(CreateReservationAttributeCommand::class, [
                'name' => $attribute,
            ]));
    }
}
