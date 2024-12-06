<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collection_id');
            $table->text('prompt');
            $table->text('response');
            $table->timestamps();
            $table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
