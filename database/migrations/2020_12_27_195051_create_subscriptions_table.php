<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->string('manager')->nullable();
            $table->unsignedBigInteger('owner_id');
            $table->string('owner_type');

            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->string('affiliate_type')->nullable();

            $table->unsignedBigInteger('price_id');
            $table->integer('current_price'); // in cents
            $table->integer('base_price'); // in cents
            $table->json('coupon')->nullable();
            $table->enum('status', ['New', 'Waiting', 'Active', 'Suspended', 'Canceled', 'Ended'])->default('New');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
