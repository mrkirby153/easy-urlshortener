<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUrlsIncreaseSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('shortened_urls', function(Blueprint $table){
            $table->string('long_url', 4096)->change();
            $table->string('title', 4096)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('shortened_urls', function(Blueprint $table){
            $table->string('long_url', 255)->change();
            $table->string('title', 255)->change();
        });
    }
}
