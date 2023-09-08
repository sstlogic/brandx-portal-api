<?php

namespace App\Actions;

use App\Booked\Client\Responses\CreateReservationResponse;
use App\Booked\Repositories\AttributeRepository;
use App\Booked\Repositories\ReservationRepository;
use App\Models\PendingReservation;
use App\Models\User;
use App\Services\ReportingService;
use App\Services\ReservationPrice\ReservationPriceService;
use Carbon\Carbon;
use DB;

class CreatePendingReservationAction extends BaseAction
{
    public function __construct(
        private ReservationRepository $repository,
        private AttributeRepository   $attributeRepository
    ) {}

    public function execute(array $data): PendingReservation
    {
        return DB::transaction(function () use ($data) {
            $this->setData($data);

            if ($this->data->has('interval')) {
                $reservation = $this->createRepeatingBooking();
            } else {
                $reservation = $this->createBooking();
            }

            return $reservation;
        });
    }

    private function createBooking()
    {
        $pendingReservation = PendingReservation::make([
            'user_id' => $this->getUser()->id,
            'start' => $this->getDateFromData('start'),
            'end' => $this->getDateFromData('end'),
            'attendees' => $this->data['attendees'],
            'generating_income' => $this->data['generatingIncome'],
            'funded' => $this->data['funded'],
            'performance' => $this->data['performance'],
            'description' => $this->data['description'],
            'resource_id' => $this->data['resource'],
            'resource_name' => $this->data['resource_name'],
        ]);

        $pendingReservation->fill([
            'instance_cost' => app(ReservationPriceService::class)->calculateFor($pendingReservation)->price / 100,
            'rate_paid' => app(ReservationPriceService::class)->calculateFor($pendingReservation)->rate / 100,
            'escac_total' => app(ReportingService::class)->escacRevenueForReservations(
                    collect([$pendingReservation])
                ) / 100,
            'coscs_total' => app(ReportingService::class)->coscsRevenueForReservations(
                    collect([$pendingReservation])
                ) / 100,
            'studio' => $pendingReservation->getLocation(),
        ]);

        $reference = $this
            ->createBookedReservation($pendingReservation)
            ->referenceNumber();

        $pendingReservation->reference_number = $reference;
        $pendingReservation->save();

        return $pendingReservation;
    }

    private function createRepeatingBooking()
    {
        $start = $this
            ->getDateFromData('start')
            ->copy()
            ->setTimeFrom($this->getDateFromData('start_time'))
            ->shiftTimezone('Australia/Sydney');

        $end = $this
            ->getDateFromData('start')
            ->copy()
            ->setTimeFrom($this->getDateFromData('end_time'))
            ->shiftTimezone('Australia/Sydney');

        $pendingReservation = PendingReservation::make([
            'user_id' => $this->getUser()->id,
            'start' => $this->getDateFromData('start'),
            'end' => $this->getDateFromData('end'),
            'attendees' => $this->data['attendees'],
            'generating_income' => $this->data['generatingIncome'],
            'funded' => $this->data['funded'],
            'performance' => $this->data['performance'],
            'description' => $this->data['description'],
            'interval' => $this->data['interval'],
            'interval_type' => $this->data['interval_type'],
            'start_time' => $start,
            'end_time' => $end,
            'weekly_days' => $this->data['weekly_days'],
            'resource_id' => $this->data['resource'],
            'resource_name' => $this->data['resource_name'],
        ]);

        $pendingReservation->fill([
            'instance_cost' => app(ReservationPriceService::class)
                    ->calculateFor($pendingReservation)->price / $pendingReservation->durationInHours() / 100,
            'rate_paid' => app(ReservationPriceService::class)->calculateFor($pendingReservation)->rate / 100,
            'escac_total' => app(ReportingService::class)
                    ->escacRevenueForReservations(collect([$pendingReservation])) / 100,
            'coscs_total' => app(ReportingService::class)
                    ->coscsRevenueForReservations(collect([$pendingReservation])) / 100,
            'studio' => $pendingReservation->getLocation(),
        ]);

        $reference = $this
            ->createRepeatingBookedReservation($pendingReservation)
            ->referenceNumber();

        $pendingReservation->reference_number = $reference;
        $pendingReservation->save();

        return $pendingReservation;
    }

    private function createBookedReservation(PendingReservation $pendingReservation): CreateReservationResponse
    {
        return $this->repository->create([
            'userId' => $this->getUser()->external_id,
            'resourceId' => $this->data['resource'],
            'description' => $this->data['description'],
            'startDateTime' => $this->getDateFromData('start')->format('c'),
            'endDateTime' => $this->getDateFromData('end')->format('c'),
            'customAttributes' => $this->attributes($pendingReservation),
        ]);
    }

    private function createRepeatingBookedReservation(PendingReservation $pendingReservation): CreateReservationResponse
    {
        $start = $this
            ->getDateFromData('start')
            ->copy()
            ->setTimeFrom($this->getDateFromData('start_time'))
            ->shiftTimezone('Australia/Sydney');

        $end = $this
            ->getDateFromData('start')
            ->copy()
            ->setTimeFrom($this->getDateFromData('end_time'))
            ->shiftTimezone('Australia/Sydney');

        return $this->repository->create([
            'userId' => $this->getUser()->external_id,
            'resourceId' => $this->data['resource'],
            'description' => $this->data['description'],
            'startDateTime' => $start->format('c'),
            'endDateTime' => $end->format('c'),
            'recurrenceRule' => [
                'type' => $this->getIntervalTypeKey(),
                'interval' => $this->data['interval'],
                'monthlyType' => $this->getMonthlyType(),
                'weekdays' => $this->getWeeklyDays(),
                'repeatTerminationDate' => $this->getDateFromData('end'),
            ],
            'customAttributes' => $this->attributes($pendingReservation),
        ]);
    }

    private function getUser(): User
    {
        return User::where('uuid', $this->data['user'])->firstOrFail();
    }

    private function getIntervalTypeKey(): string
    {
        $types = [
            'weekly' => 'weekly',
            'daily' => 'daily',
            'monthly-day' => 'monthly',
            'monthly-date' => 'monthly',
        ];

        return $types[$this->data['interval_type']];
    }

    private function getMonthlyType(): ?string
    {
        $types = [
            'monthly-date' => 'dayOfMonth',
            'monthly-day' => 'dayOfWeek',
        ];

        return $types[$this->data['interval_type']] ?? null;
    }

    private function getWeeklyDays()
    {
        $days = $this->data['weekly_days'] ?? [];

        return collect($days)->map(fn (int $day) => $day + 1)->all();
    }

    private function attributes(PendingReservation $pendingReservation): array
    {
        $attributes = [
            'number_participants' => $this->data['attendees'],
            'time_created' => Carbon::now()->setTimezone('Australia/Sydney')->format('d/m/Y g:ia'),
            'paid_status' => 'not paid',
            'instance_cost' => $pendingReservation->instance_cost,
            'rate_paid' => $pendingReservation->rate_paid,
            'studio' => $pendingReservation->studio,
        ];

        return collect($attributes)
            ->map(function ($attribute, $key) {
                return [
                    'attributeId' => $this->attributeRepository->reservationAttributeId($key),
                    'attributeValue' => $attribute,
                ];
            })
            ->reject(function ($array) {
                return ! $array['attributeId'];
            })
            ->values()
            ->all();
    }
}
