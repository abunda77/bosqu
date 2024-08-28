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
        Schema::create('post_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body')->comment('Content of the post');
            $table->string('feature_image')->comment('URL of featured image');
            $table->string('slug')->unique();
            $table->foreignId('admin_id')->constrained('admins');
            $table->enum('status', ['draft', 'published', 'private']);
            $table->timestamps();
        });

        Schema::create('post_post_categories', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('post_posts');
            $table->foreignId('category_id')->constrained('post_categories');
            $table->primary(['post_id', 'category_id']);
        });

        Schema::create('post_post_tags', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('post_posts');
            $table->foreignId('tag_id')->constrained('post_tags');
            $table->primary(['post_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('post_post_tags');
      
        
        Schema::dropIfExists('post_post_categories');
        
        Schema::dropIfExists('post_posts');
    }
};
