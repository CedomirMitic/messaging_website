<?php

// database/migrations/..._create_posts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            // Assumes authenticated users create posts
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title'); 
            $table->text('content');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }
    // ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
