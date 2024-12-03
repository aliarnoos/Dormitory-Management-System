<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apartment_prices', function (Blueprint $table) {
            $table->id();
            $table->string('apartment_type');
            $table->string('ug_semester_price');
            $table->string('app_semester_price');
            $table->string('winter_price');
            $table->string('summer_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartment_prices');
    }
};
