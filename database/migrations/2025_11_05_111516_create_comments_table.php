<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            //Kommenteerida saavad ainult sisseloginud kasutajad -> FK requires (foreign key)
            $table->foreignId('user_id') ->constrained()->cascadeOnDelete();

            $table->text('body');
            $table->enum('status', ['pending', 'approved','hidden', 'spam'])->default('pending') -> index();
            $table->string('ip_address',45)->nullable();
            $table->timestamps();

            $table->softDeletes();

            $table->index(['post_id', 'status', 'created_at']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
