<?php

namespace Tests\Services\ReservationPrice;

use App\Models\PendingReservation;
use App\Services\ReservationPrice\ReservationPriceService;
use Carbon\Carbon;
use Tests\Feature\FeatureTestCase;

class ReservationPriceServiceTest extends FeatureTestCase
{
    /** @test */
    public function it_calculates_solo_rate()
    {
        $reservation = new PendingReservation(
            [
                'start' => Carbon::now(),
                'end' => Carbon::now()->addHours(2),
                'attendees' => 1,
                'generatingIncome' => false,
                'funded' => false,
                'performance' => false,
            ]
        );

        $price = app(ReservationPriceService::class)->calculate($reservation);

        $this->assertSame(config('brandx.prices.solo.hourly'), $price->rate);
    }

    /** @test */
    public function it_calculates_unfunded_rate()
    {
        $reservation = new PendingReservation(
            [
                'start' => Carbon::now(),
                'end' => Carbon::now()->addHours(2),
                'attendees' => 2,
                'generatingIncome' => false,
                'funded' => false,
                'performance' => false,
            ]
        );

        $price = app(ReservationPriceService::class)->calculate($reservation);

        $this->assertSame(config('brandx.prices.unfunded.hourly'), $price->rate);
    }

    /** @test */
    public function it_calculates_funded_rate()
    {
        $reservation = new PendingReservation(
            [
                'start' => Carbon::now(),
                'end' => Carbon::now()->addHours(2),
                'attendees' => 2,
                'generatingIncome' => false,
                'funded' => true,
                'performance' => false,
            ]
        );

        $price = app(ReservationPriceService::class)->calculate($reservation);

        $this->assertSame(config('brandx.prices.funded.hourly'), $price->rate);
    }

    /** @test */
    public function it_calculates_performance_rate()
    {
        $reservation = new PendingReservation(
            [
                'start' => Carbon::now(),
                'end' => Carbon::now()->addHours(2),
                'attendees' => 1,
                'generatingIncome' => false,
                'funded' => false,
                'performance' => true,
            ]
        );

        $price = app(ReservationPriceService::class)->calculate($reservation);

        $this->assertSame(config('brandx.prices.performance.hourly'), $price->rate);
    }

    /** @test */
    public function it_calculates_commercial_rate()
    {
        $reservation = new PendingReservation(
            [
                'start' => Carbon::now(),
                'end' => Carbon::now()->addHours(2),
                'attendees' => 1,
                'generating_income' => true,
                'funded' => false,
                'performance' => false,
            ]
        );

        $price = app(ReservationPriceService::class)->calculate($reservation);

        $this->assertSame(config('brandx.prices.commercial.hourly'), $price->rate);
    }

    /** @test */
    public function it_calculates_solo_day_rate()
    {
        $reservation = new PendingReservation(
            [
                'start' => Carbon::now(),
                'end' => Carbon::now()->addHours(9),
                'attendees' => 1,
                'generatingIncome' => false,
                'funded' => false,
                'performance' => false,
            ]
        );

        $price = app(ReservationPriceService::class)->calculate($reservation);

        $this->assertSame(config('brandx.prices.solo.daily'), $price->rate);
    }

    /** @test */
    public function it_calculates_solo_week_rate()
    {
        $reservation = new PendingReservation(
            [
                'start' => Carbon::now(),
                'end' => Carbon::now()->addDays(14),
                'attendees' => 1,
                'generatingIncome' => false,
                'funded' => false,
                'performance' => false,
                'interval' => 1,
                'interval_type' => 'weekly',
                'weekly_days' => [0, 1, 2],
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addHours(9),
            ]
        );

        $price = app(ReservationPriceService::class)->calculate($reservation);

        $this->assertSame(config('brandx.prices.solo.weekly'), $price->rate);
    }
}

