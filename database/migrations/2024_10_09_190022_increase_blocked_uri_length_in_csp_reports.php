<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('csp_reports', function (Blueprint $table) {
            $table->string('blocked_uri', 2048)->change();
        });
    }

    public function down()
    {
        Schema::table('csp_reports', function (Blueprint $table) {
            $table->string('blocked_uri', 255)->change(); // Revert to previous length if necessary
        });
    }
};
