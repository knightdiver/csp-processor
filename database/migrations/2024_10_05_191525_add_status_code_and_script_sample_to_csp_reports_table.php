<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusCodeAndScriptSampleToCspReportsTable extends Migration
{
    public function up()
    {
        Schema::table('csp_reports', function (Blueprint $table) {
            // Add new columns
            $table->integer('status_code')->after('blocked_uri')->nullable();
            $table->text('script_sample')->after('status_code')->nullable();
            $table->string('effective_directive')->after('violated_directive')->nullable(); // New column
            $table->text('original_policy')->after('effective_directive')->nullable(); // New column
        });
    }

    public function down()
    {
        Schema::table('csp_reports', function (Blueprint $table) {
            // Drop the newly added columns
            $table->dropColumn(['status_code', 'script_sample', 'effective_directive', 'original_policy']);
        });
    }
}
