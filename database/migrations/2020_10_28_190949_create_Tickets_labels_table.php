<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('Tickets_labels')) {
            Schema::create('Tickets_labels', function (Blueprint $table) {
                $table->foreignId('Tickets_id')->constrained('Tickets');
                $table->foreignId('label_id')->constrained('labels');
                $table->primary(['Tickets_id', 'label_id']);
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
        Schema::dropIfExists('Tickets_labels');
    }
}
