<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingFieldsToTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('billing_name')->after('personal_team')->nullable();
            $table->string('billing_address')->after('billing_name')->nullable();
            $table->string('billing_country')->after('billing_address')->nullable();
            $table->string('billing_code')->after('billing_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['billing_name', 'billing_address', 'billing_country', 'billing_code']);
        });
    }
}