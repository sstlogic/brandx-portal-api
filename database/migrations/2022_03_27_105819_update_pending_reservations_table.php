<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePendingReservationsTable extends Migration
{
    public function up()
    {
        Schema::table('pending_reservations', function (Blueprint $table) {
            $table->string('resource_name');
            $table->integer('resource_id');
        });
    }

    public function down()
    {
        Schema::table('pending_reservations', function (Blueprint $table) {
            $table->dropColumn(['resource_name', 'resource_id']);
        });
    }
}
