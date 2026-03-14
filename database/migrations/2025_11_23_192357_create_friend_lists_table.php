<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        Schema::create('friend_lists', function (Blueprint $table) { 
            $table->id();
            
            
            $table->foreignId("friend_id")->constrained('users')->onDelete('cascade');
            $table->foreignId("user_id")->constrained('users')->onDelete('cascade');
            $table->timestamps(); 

            // Add index to prevent duplicates
            $table->unique(['user_id', 'friend_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friend_lists');
    }
};