<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeValueNullableOnMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('metas', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });
        DB::table('metas')->where('value','=','null')->update(['value' => null]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('metas')->whereNull('value')->update(['value' => 'null']);
        Schema::table('metas', function (Blueprint $table) {
            $table->text('value')->nullable(false)->change();
        });
        //
    }
}
