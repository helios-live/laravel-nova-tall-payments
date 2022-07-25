<?php

use IdeaToCode\LaravelNovaTallPayments\Models\Price;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $keys = array_keys(Price::$period_map[0]);
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('slug')->unique(); //
            $table->string('name'); //
            $table->json('payload');
            $table->integer('amount'); // in cents
            $table->enum('billing_period', $keys);
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
        Schema::dropIfExists('prices');
    }
}