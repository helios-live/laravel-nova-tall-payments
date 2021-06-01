<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeColumnsToTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('stripe_customer_id')->after('personal_team')->nullable();
            $table->string('stripe_pm_id')->after('stripe_customer_id')->nullable();
            $table->json('stripe_card_data')->after('stripe_pm_id')->nullable();
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
            $table->dropColumn(['stripe_customer_id', 'stripe_pm_id', 'stripe_card_data',]);
        });
    }
}
