<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('change_user_id');
            $table->text('changelog')->nullable()->default(null);
            $table->foreign('change_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('history_posts', function (Blueprint $table) {
            $table->dropColumn('change_user_id');
            $table->dropColumn('changelog');
        });
    }
};
