<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pending_reservations', function (Blueprint $table) {
            $table->string('studio')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pending_reservations', function (Blueprint $table) {});
    }
};