<?php

namespace App\Models;

use App\Actions\GetRepeatingScheduleSlotsAction;
use App\Booked\Enums\ReservationLocation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lioneagle\LeUtils\Casts\DateTimeCast;
use Lioneagle\LeUtils\Query\Builder;
use Lioneagle\LeUtils\Traits\HasUuid;

/**
 * App\Models\PendingReservation
 *
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $reference_number
 * @property string|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|PendingReservation newModelQuery()
 * @method static Builder|PendingReservation newQuery()
 * @method static Builder|PendingReservation query()
 * @method static Builder|PendingReservation uuid(string $uuid)
 * @method static Builder|PendingReservation uuidOrFail(string $uuid)
 * @method static Builder|PendingReservation whereCreatedAt($value)
 * @method static Builder|PendingReservation whereDeletedAt($value)
 * @method static Builder|PendingReservation whereId($value)
 * @method static Builder|PendingReservation wherePaidAt($value)
 * @method static Builder|PendingReservation whereReferenceNumber($value)
 * @method static Builder|PendingReservation whereUpdatedAt($value)
 * @method static Builder|PendingReservation whereUserId($value)
 * @method static Builder|PendingReservation whereUuid($value)
 * @mixin \Eloquent
 * @property \Carbon\Carbon|null $start
 * @property \Carbon\Carbon|null $end
 * @property int $attendees
 * @property bool $generating_income
 * @property bool $funded
 * @property bool $performance
 * @property string $description
 * @method static Builder|PendingReservation whereAttendees($value)
 * @method static Builder|PendingReservation whereDescription($value)
 * @method static Builder|PendingReservation whereEnd($value)
 * @method static Builder|PendingReservation whereFunded($value)
 * @method static Builder|PendingReservation whereGeneratingIncome($value)
 * @method static Builder|PendingReservation wherePerformance($value)
 * @method static Builder|PendingReservation whereStart($value)
 * @method static Builder|PendingReservation notPaid()
 * @property string|null $interval_type
 * @property int|null $interval
 * @property \Carbon\Carbon|null|null $start_time
 * @property \Carbon\Carbon|null|null $end_time
 * @property array|null $weekly_days
 * @property string $resource_name
 * @property int $resource_id
 * @method static Builder|PendingReservation whereEndTime($value)
 * @method static Builder|PendingReservation whereInterval($value)
 * @method static Builder|PendingReservation whereIntervalType($value)
 * @method static Builder|PendingReservation whereResourceId($value)
 * @method static Builder|PendingReservation whereResourceName($value)
 * @method static Builder|PendingReservation whereStartTime($value)
 * @method static Builder|PendingReservation whereWeeklyDays($value)
 * @property string|null $payment_reference
 * @property string|null $payment_status
 * @property string|null $invoice_id
 * @method static Builder|PendingReservation whereInvoiceId($value)
 * @method static Builder|PendingReservation wherePaymentReference($value)
 * @method static Builder|PendingReservation wherePaymentStatus($value)
 * @property string|null $invoice_amount
 * @property string|null $instance_cost
 * @property string|null $rate_paid
 * @property int|null $escac_total
 * @property int|null $coscs_total
 * @method static Builder|PendingReservation whereCoscsTotal($value)
 * @method static Builder|PendingReservation whereEscacTotal($value)
 * @method static Builder|PendingReservation whereInstanceCost($value)
 * @method static Builder|PendingReservation whereInvoiceAmount($value)
 * @method static Builder|PendingReservation whereRatePaid($value)
 * @property string|null $studio
 * @method static Builder|PendingReservation whereStudio($value)
 * @property-read \App\Models\User $user
 */
class PendingReservation extends Model
{
    use HasUuid;
    use HasFactory;

    const PAID = 'paid';

    protected $casts = [
        'start' => DateTimeCast::class,
        'end' => DateTimeCast::class,
        'start_time' => DateTimeCast::class,
        'end_time' => DateTimeCast::class,
        'weekly_days' => 'array',
        'generating_income' => 'boolean',
        'funded' => 'boolean',
        'performance' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isSolo(): bool
    {
        return (int) $this->attendees === 1;
    }

    public function isForPerformance(): bool
    {
        return $this->performance;
    }

    public function isFunded(): bool
    {
        return $this->funded;
    }

    public function isCommercial(): bool
    {
        return (bool) $this->generating_income;
    }

    public function durationInMinutes(): int
    {
        return $this->start->diffInMinutes($this->end);
    }

    public function rawDurationInHours(): float
    {
        return round($this->durationInMinutes() / 60, 2);
    }

    public function durationInHours(): float
    {
        if ($this->isRepeating()) {
            return $this->repeatingInstances()->sum(
                fn (PendingReservation $reservation) => $reservation->rawDurationInHours()
            );
        }

        return $this->rawDurationInHours();
    }

    public function scopeNotPaid(EloquentBuilder $builder)
    {
        return $builder->where('paid_at', null);
    }

    public function isRepeating(): bool
    {
        return ! empty($this->interval_type);
    }

    public function isRepeatingWeekly(): bool
    {
        return $this->interval_type === 'weekly';
    }

    public function isRepeatingWeeklyOnMultipleDays(): bool
    {
        return $this->isRepeatingWeekly() && count($this->weekly_days) > 0;
    }

    public function isRepeatingDaily(): bool
    {
        return $this->interval_type === 'daily';
    }

    public function repeatingInstances()
    {
        $dates = app(GetRepeatingScheduleSlotsAction::class)->dates([
            ...$this->toArray(),
            'start_date' => $this->start->copy(),
            'end_date' => $this->end->copy(),
        ]);

        return $dates->map(fn (Carbon $date) => $this->createReservation($date, $this->start_time, $this->end_time));
    }

    public function prettyIntervalType()
    {
        $types = [
            'weekly' => 'weekly',
            'daily' => 'daily',
            'monthly-day' => 'monthly (on the day)',
            'monthly-date' => 'monthly (on the date)',
        ];

        return $types[$this->interval_type];
    }

    public function prettyInterval()
    {
        $types = [
            'weekly' => 'week',
            'daily' => 'day',
            'monthly-day' => 'month',
            'monthly-date' => 'month',
        ];

        $key = $types[$this->interval_type];

        return $this->interval > 1 ? "$this->interval $key" . "s" : $this->interval . ' ' . $key;
    }

    public function prettyStart()
    {
        return $this->isRepeating()
            ? $this->repeatingInstances()->first()->start
            : $this->start;
    }

    public function prettyEnd()
    {
        return $this->isRepeating()
            ? $this->repeatingInstances()->first()->end
            : $this->end;
    }

    public function getLocation(): ?ReservationLocation
    {
        return ReservationLocation::fromResourceName($this->resource_name);
    }

    private function createReservation(Carbon $date, Carbon $startTime, Carbon $endTime)
    {
        $start = $date
            ->copy()
            ->setTImezone('Australia/Sydney')
            ->setTimeFrom($startTime->setTImezone('Australia/Sydney'));

        $end = $date
            ->copy()
            ->setTImezone('Australia/Sydney')
            ->setTimeFrom($endTime->setTImezone('Australia/Sydney'));

        return new PendingReservation(
            [
                'start' => $start,
                'end' => $end,
                'attendees' => $this->attendees,
                'generating_income' => $this->generating_income,
                'funded' => $this->funded,
                'performance' => $this->performance,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'interval' => $this->interval,
                'interval_type' => $this->interval_type,
                'weekly_days' => $this->weekly_days,
            ]
        );
    }
}
