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
        Schema::create('worker_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained();
            $table->string('contact_number')->nullable();
            $table->date('date_hired')->nullable();
            $table->string('address')->nullable();
            $table->date('date_birth')->nullable();
            $table->string('place_birth')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('passport_place_issue')->nullable();
            $table->date('passport_date_issue')->nullable();
            $table->date('passport_date_expired')->nullable();
            $table->string('elementary')->nullable();
            $table->string('high_school')->nullable();
            $table->string('vocational')->nullable();
            $table->string('college')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('religion')->nullable();
            $table->enum('civil_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->float('height', 8, 2)->nullable();
            $table->float('weight', 8, 2)->nullable();
            $table->text('objectives')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
