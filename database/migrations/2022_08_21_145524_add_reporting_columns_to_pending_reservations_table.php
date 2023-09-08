<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pending_reservations', function (Blueprint $table) {
            $table->string('invoice_amount')->nullable();
            $table->string('instance_cost')->nullable();
            $table->string('rate_paid')->nullable();
            $table->float('escac_total')->nullable();
            $table->float('coscs_total')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pending_reservations', function (Blueprint $table) {});
    }
};
