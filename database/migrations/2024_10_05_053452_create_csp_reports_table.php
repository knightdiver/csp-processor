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
        Schema::create('csp_reports', function (Blueprint $table) {
            $table->id();
            $table->string('document_uri');
            $table->string('referrer')->nullable();
            $table->string('violated_directive');
            $table->string('blocked_uri');
            $table->string('source_file')->nullable();
            $table->integer('line_number')->nullable();
            $table->integer('column_number')->nullable();
            $table->foreignId('domain_id')->constrained('domains')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csp_reports');
    }
};
