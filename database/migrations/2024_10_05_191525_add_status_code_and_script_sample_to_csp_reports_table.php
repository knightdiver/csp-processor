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
            // Check if the columns exist before trying to drop them
            if (Schema::hasColumn('csp_reports', 'status_code')) {
                $table->dropColumn('status_code');
            }
            if (Schema::hasColumn('csp_reports', 'script_sample')) {
                $table->dropColumn('script_sample');
            }
            if (Schema::hasColumn('csp_reports', 'effective_directive')) {
                $table->dropColumn('effective_directive');
            }
            if (Schema::hasColumn('csp_reports', 'original_policy')) {
                $table->dropColumn('original_policy');
            }
        });
    }
}
