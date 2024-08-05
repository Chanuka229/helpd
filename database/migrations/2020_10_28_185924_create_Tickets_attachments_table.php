<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('Tickets_attachments')) {
            Schema::create('Tickets_attachments', function (Blueprint $table) {
                $table->foreignId('Tickets_reply_id')->constrained('Tickets_replies');
                $table->foreignId('file_id')->constrained('files');
                $table->primary(['Tickets_reply_id', 'file_id']);
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
        Schema::dropIfExists('Tickets_attachments');
    }
}
