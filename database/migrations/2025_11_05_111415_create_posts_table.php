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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            //Auor võib hiljem olla kustutatud -> set null
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            //Katekooria pole kohustuslik
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title' ,180);
            $table->string('slug' ,220)->unique();
            $table->text('excerpt')->nullable(); //Sissejuhatav tekst
            $table->longText('body'); //Artikli sisu

            //Kasuta enum või string + kontroll. MySQL sobib enum
            $table->enum('status', ['draft','review', 'published', 'archived'])->default('draft') ->index();


            $table->dateTime('published_at')->nullable()->index();
            $table->string('featured_image', 255)->nullable();
            $table->unsignedSmallInteger('reading_time')->nullable();
            $table->timestamps();

            $table->softDeletes();

            //Avaliku vaate jaoks kiiremad päringud
            $table->index(['status', 'published_at']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
