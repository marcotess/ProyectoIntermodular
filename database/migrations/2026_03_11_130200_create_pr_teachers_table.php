<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pr_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pr_id')->constrained('prs')->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['pr_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pr_teachers');
    }
};
