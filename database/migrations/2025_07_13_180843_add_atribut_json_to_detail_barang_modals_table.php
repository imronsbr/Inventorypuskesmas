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
        Schema::table('detail_barang_modals', function (Blueprint $table) {
            // Add JSON column for storing various attributes
            $table->json('atribut')->nullable()->after('nie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_barang_modals', function (Blueprint $table) {
            $table->dropColumn('atribut');
        });
    }
};
