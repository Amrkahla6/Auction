<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuctionDetialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auction_detials', function (Blueprint $table) {
            $table->id();
            $table->string('param_name_ar')->nullable();
            $table->string('param_name_en')->nullable();
            $table->string('param_value')->nullable();
            $table->unsignedBigInteger('auction_id')->nullable();
            $table->unsignedBigInteger('cat_id')->nullable();

            $table->foreign('auction_id')->references('id')->on('auctions')->onDelete('cascade');
            $table->foreign('cat_id')->references('id')->on('categories')->onDelete('cascade');
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
        Schema::dropIfExists('auction_detials');
    }
}
