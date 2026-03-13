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
        Schema::table('str', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('tanggal_berakhir');
        });

        Schema::table('sip', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('tanggal_berakhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('str', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });

        Schema::table('sip', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
};
