<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('issues')->where('status_id', 5)->update(['status_id' => 6]);
        DB::table('issues')->where('status_id', 8)->update(['status_id' => 7]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse remapping is possible without the original status audit history.
    }
};
