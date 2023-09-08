<?php /** @noinspection PhpDocSignatureInspection */

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Lioneagle\LeUtils\Casts\DateTimeCast;
use Lioneagle\LeUtils\Traits\HasUuid;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $external_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereExternalId($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder|User whereTwoFactorSecret($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @property string $uuid
 * @property string|null $phone
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property ?\Carbon\Carbon $existing_membership_expiry
 * @property bool $organisation
 * @method static \Lioneagle\LeUtils\Query\Builder|User whereOrganisation($value)
 * @property \Carbon\Carbon|null|null $member_since
 * @method static \Lioneagle\LeUtils\Query\Builder|User whereMemberSince($value)
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Cashier\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Lioneagle\LeUtils\Query\Builder|User uuid(string $uuid)
 * @method static \Lioneagle\LeUtils\Query\Builder|User uuidOrFail(string $uuid)
 * @method static \Lioneagle\LeUtils\Query\Builder|User wherePhone($value)
 * @method static \Lioneagle\LeUtils\Query\Builder|User wherePmLastFour($value)
 * @method static \Lioneagle\LeUtils\Query\Builder|User wherePmType($value)
 * @method static \Lioneagle\LeUtils\Query\Builder|User whereStripeId($value)
 * @method static \Lioneagle\LeUtils\Query\Builder|User whereTrialEndsAt($value)
 * @method static \Lioneagle\LeUtils\Query\Builder|User whereUuid($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PendingReservation[] $pendingReservations
 * @property-read int|null $pending_reservations_count
 * @method static \Lioneagle\LeUtils\Query\Builder|User whereExistingMembershipExpiry($value)
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasUuid;
    use Billable;

    protected $rememberTokenName = null;

    protected $casts = [
        'existing_membership_expiry' => DateTimeCast::class,
        'organisation' => 'boolean',
        'member_since' => DateTimeCast::class,
    ];

    public function taxRates(): array
    {
        return [
            config('brandx.tax_rate'),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\App\Models\PendingReservation
     */
    public function pendingReservations(): HasMany
    {
        return $this->hasMany(PendingReservation::class)->orderBy('start');
    }

    public function memberExpiry(): ?Carbon
    {
        if ($this->isExistingMember() && ! $this->subscribed()) {
            return $this->existing_membership_expiry;
        }

        $end = $this->subscription()?->asStripeSubscription()->current_period_end;

        return $end ? Carbon::parse($end) : null;
    }

    public function isMember(): bool
    {
        if ($this->subscribed()) {
            return true;
        }

        return $this->existing_membership_expiry?->greaterThan(Carbon::now()) ?? false;
    }

    public function isExistingMember(): bool
    {
        return ! is_null($this->existing_membership_expiry);
    }

    public function memberSince(): ?Carbon
    {
        return $this->member_since ?? $this->subscription()?->created_at;
    }

    public function memberRenewalDate(): ?Carbon
    {
        if ($this->existing_membership_expiry && ! $this->subscription()) {
            return null;
        }

        return $this->memberExpiry();
    }
}
