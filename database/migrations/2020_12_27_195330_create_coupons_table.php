<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->json('discount'); // {"type": "fixed" / "percentage", amount: N00} // amount is in cents or percentage points
            $table->json('usage');// {"max_user":Integer, "max_total":Integer}
            $table->json('valid_categories_ids'); // [1,2,N]
            $table->json('valid_products_ids'); // [1,2,N]

            $table->datetime('starts_at');
            $table->datetime('ends_at')->nullable();
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
        Schema::dropIfExists('coupons');
    }
}
