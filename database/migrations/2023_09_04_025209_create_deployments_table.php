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
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->date('date_deployed');
            $table->string('position');
            $table->string('country');
            $table->string('status');
            $table->foreignId('agency_id')->constrained();
            $table->foreignId('worker_id')->constrained();
            $table->foreignId('foreign_agency_id')->constrained();
            $table->foreignId('handler_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployments');
    }
};
