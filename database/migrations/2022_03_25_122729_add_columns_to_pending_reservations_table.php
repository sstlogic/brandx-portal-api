<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPendingReservationsTable extends Migration
{
    public function up()
    {
        Schema::table('pending_reservations', function (Blueprint $table) {
            $table->string('interval_type')->nullable();
            $table->integer('interval')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->text('weekly_days')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pending_reservations', function (Blueprint $table) {
            $table->dropColumn(['interval_type', 'interval', 'start_time', 'end_time', 'weekly_days']);
        });
    }
}
