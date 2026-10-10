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
        Schema::create('issues', function (Blueprint $table) {
            $table->id('issue_id');
            $table->string('issue_number', 50)->unique();
            $table->date('date_received');
            $table->foreignId('importer_id')->constrained('importers', 'importer_id')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('port_id')->constrained('ports', 'port_id')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('concern');
            $table->text('benefits_availed')->nullable();
            $table->foreignId('assigned_manager')->nullable()->constrained('users', 'user_id')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedTinyInteger('status_id')->default(1);
            $table->dateTime('date_resolved')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users', 'user_id')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
