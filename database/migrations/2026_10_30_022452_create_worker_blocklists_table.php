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
        Schema::create('worker_blocklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('worker_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('passport_number')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('contact_number')->nullable();
            $table->date('date_birth')->nullable();
            $table->string('place_birth')->nullable();
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->enum('match_confidence', ['high', 'medium', 'low'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->timestamp('blocked_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['agency_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_blocklists');
    }
};
