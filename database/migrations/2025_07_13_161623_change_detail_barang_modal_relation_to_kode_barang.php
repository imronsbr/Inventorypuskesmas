<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detail_barang_modals', function (Blueprint $table) {
            // Add new column for kode_barang only if it doesn't exist
            if (!Schema::hasColumn('detail_barang_modals', 'kode_barang')) {
                $table->string('kode_barang')->nullable()->after('id');
            }
        });

        // Migrate existing data only if kode_barang is empty
        $this->migrateExistingData();

        Schema::table('detail_barang_modals', function (Blueprint $table) {
            // Make kode_barang not nullable after data migration
            if (Schema::hasColumn('detail_barang_modals', 'kode_barang')) {
                $table->string('kode_barang')->nullable(false)->change();
                
                // Add foreign key constraint for kode_barang
                $table->foreign('kode_barang')->references('kode_barang')->on('master_barang_modals')->onDelete('cascade');
            }
            
            // Drop foreign key constraint for old column if exists
            if (Schema::hasColumn('detail_barang_modals', 'master_barang_modal_id')) {
                $table->dropForeign(['master_barang_modal_id']);
                
                // Drop the old column
                $table->dropColumn('master_barang_modal_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_barang_modals', function (Blueprint $table) {
            // Add back the old column
            if (!Schema::hasColumn('detail_barang_modals', 'master_barang_modal_id')) {
                $table->foreignId('master_barang_modal_id')->nullable()->after('id');
            }
        });

        // Migrate data back
        $this->migrateDataBack();

        Schema::table('detail_barang_modals', function (Blueprint $table) {
            // Drop foreign key constraint for kode_barang if exists
            if (Schema::hasColumn('detail_barang_modals', 'kode_barang')) {
                $table->dropForeign(['kode_barang']);
                
                // Drop the kode_barang column
                $table->dropColumn('kode_barang');
            }
            
            // Add back the old foreign key constraint
            if (Schema::hasColumn('detail_barang_modals', 'master_barang_modal_id')) {
                $table->foreign('master_barang_modal_id')->references('id')->on('master_barang_modals')->onDelete('cascade');
                
                // Make master_barang_modal_id not nullable
                $table->foreignId('master_barang_modal_id')->nullable(false)->change();
            }
        });
    }

    /**
     * Migrate existing data from master_barang_modal_id to kode_barang
     */
    private function migrateExistingData()
    {
        // Only migrate if both columns exist and kode_barang is empty
        if (Schema::hasColumn('detail_barang_modals', 'master_barang_modal_id') && 
            Schema::hasColumn('detail_barang_modals', 'kode_barang')) {
            
            $details = DB::table('detail_barang_modals')
                ->join('master_barang_modals', 'detail_barang_modals.master_barang_modal_id', '=', 'master_barang_modals.id')
                ->whereNull('detail_barang_modals.kode_barang')
                ->select('detail_barang_modals.id', 'master_barang_modals.kode_barang')
                ->get();

            foreach ($details as $detail) {
                DB::table('detail_barang_modals')
                    ->where('id', $detail->id)
                    ->update(['kode_barang' => $detail->kode_barang]);
            }
        }
    }

    /**
     * Migrate data back from kode_barang to master_barang_modal_id
     */
    private function migrateDataBack()
    {
        if (Schema::hasColumn('detail_barang_modals', 'kode_barang') && 
            Schema::hasColumn('detail_barang_modals', 'master_barang_modal_id')) {
            
            $details = DB::table('detail_barang_modals')
                ->join('master_barang_modals', 'detail_barang_modals.kode_barang', '=', 'master_barang_modals.kode_barang')
                ->select('detail_barang_modals.id', 'master_barang_modals.id as master_id')
                ->get();

            foreach ($details as $detail) {
                DB::table('detail_barang_modals')
                    ->where('id', $detail->id)
                    ->update(['master_barang_modal_id' => $detail->master_id]);
            }
        }
    }
};
