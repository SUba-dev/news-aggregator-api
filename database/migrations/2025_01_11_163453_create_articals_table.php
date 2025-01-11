<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articals', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('source', 100)->nullable();
            $table->unsignedBigInteger('news_source_id'); // Foreign key referencing news_sources table
            $table->unsignedBigInteger('category_id')->default('1'); // General news
            $table->string('title', 150);
            $table->string('author', 150)->nullable();
            $table->text('description');
            $table->text('content');
            $table->string('url')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->unique(['source', 'news_source_id', 'title', 'published_at']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('news_source_id')->references('id')->on('news_sources')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('articals', function (Blueprint $table) {
            $table->dropForeign(['news_source_id']);  
            $table->dropForeign(['category_id']);    
        });
        Schema::dropIfExists('articals');

        Schema::dropIfExists('news_sources');
        Schema::dropIfExists('categories');
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
