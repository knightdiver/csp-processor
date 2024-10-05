<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusCodeAndScriptSampleToCspReportsTable extends Migration
{
    public function up()
    {
        Schema::table('csp_reports', function (Blueprint $table) {
            $table->integer('status_code')->nullable()->after('blocked_uri'); // Adding status_code after blocked_uri
            $table->text('script_sample')->nullable()->after('status_code'); // Adding script_sample after status_code
        });
    }

    public function down()
    {
        Schema::table('csp_reports', function (Blueprint $table) {
            $table->dropColumn(['status_code', 'script_sample']);
        });
    }
}

