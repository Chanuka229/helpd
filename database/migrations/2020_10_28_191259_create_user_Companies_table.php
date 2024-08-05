<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_Companies')) {
            Schema::create('user_Companies', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained('users');
                $table->foreignId('Companies_id')->constrained('Companies');
                $table->primary(['user_id', 'Companies_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_Companies');
    }
}
