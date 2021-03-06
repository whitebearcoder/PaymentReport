<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeKeyInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_key_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constraind('users');
            $table->foreignId('brand_id')->constraind('brands');
            $table->foreignId('agent_id')->constraind('brands')->nullable();
            $table->timestamp('date');
            $table->float('sum');
            $table->string('received');
            $table->string('comments');
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
        Schema::dropIfExists('income_key_ins');
    }
}
