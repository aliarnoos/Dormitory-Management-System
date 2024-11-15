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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); 
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('IQD');
            $table->date('date');
            $table->string('status')->default('pending');
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Recreate it
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
