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
        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained();// 'worker_id',
            $table->string('level'); // 'level',
            $table->string('title'); // 'title',
            $table->string('description')->nullable(); // 'description',
            $table->date('from_date')->nullable(); // 'from_date',
            $table->date('to_date')->nullable(); // 'to_date',
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education');
    }
};
