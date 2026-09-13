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
        Schema::create('issue_actions', function (Blueprint $table) {
            $table->id('action_id');
            $table->foreignId('issue_id')->constrained('issues', 'issue_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('action_taken');
            $table->string('coordinated_office')->nullable();
            $table->date('action_date');
            $table->foreignId('performed_by')->constrained('users', 'user_id')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_actions');
    }
};

