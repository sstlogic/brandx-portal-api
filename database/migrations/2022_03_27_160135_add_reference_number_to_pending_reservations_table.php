<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceNumberToPendingReservationsTable extends Migration
{
    public function up()
    {
        Schema::table('pending_reservations', function (Blueprint $table) {
            $table->string('payment_reference')->nullable();
            $table->string('payment_status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pending_reservations', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_status']);
        });
    }
}
