<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingReservations extends Migration
{
    public function up()
    {
        Schema::create('pending_reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();

            $table->foreignId('user_id')->constrained('users');

            $table->dateTime('start');
            $table->dateTime('end');
            $table->integer('attendees');
            $table->boolean('generating_income');
            $table->boolean('funded');
            $table->boolean('performance');

            $table->text('description');

            $table->string('reference_number');
            $table->dateTime('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pending_reservations');
    }
}
