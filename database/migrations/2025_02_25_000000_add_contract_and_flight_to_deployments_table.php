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
        Schema::table('deployments', function (Blueprint $table) {
            $table->date('end_of_contract_date')->nullable()->after('date_deployed');
            $table->boolean('has_left_country')->default(false)->after('end_of_contract_date');
            $table->string('flight_number')->nullable()->after('has_left_country');
            $table->date('flight_date')->nullable()->after('flight_number');
            $table->string('airline')->nullable()->after('flight_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn([
                'end_of_contract_date',
                'has_left_country',
                'flight_number',
                'flight_date',
                'airline',
            ]);
        });
    }
};
