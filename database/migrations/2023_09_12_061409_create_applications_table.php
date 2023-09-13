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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->nullable()->constrained();
            $table->foreignId('agency_id')->constrained(); // 'agency_id',
            $table->string('first_name')->nullable(); // 'first_name',
            $table->string('last_name')->nullable(); // 'last_name',
            $table->string('middle_name')->nullable(); // 'middle_name',
            $table->string('contact_number')->nullable(); // 'contact_number',
            $table->string('email')->nullable(); // 'email',
            $table->text('cover_letter')->nullable(); // 'cover_letter',
            $table->string('accepted_terms_and_condition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
