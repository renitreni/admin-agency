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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained();
            $table->foreignId('worker_id')->constrained(); // 'worker_id',
            $table->foreignId('foreign_agency_id')->nullable()->constrained(); // 'foreign_agency_id',
            $table->text('source'); // 'source',
            $table->string('created_by')->nullable(); // 'created_by',
            $table->string('updated_by')->nullable(); // 'updated_by',
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
