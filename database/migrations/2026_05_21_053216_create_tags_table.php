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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string("slug")->unique();
            $table->string("name")->unique();
            $table->timestamps();
        });

        // polymorphic pivot table 
        Schema::create("taggables", function(Blueprint $table){
            $table->id();
            $table->foreignId("tag_id")->constrained()->onDelete("cascade");
            $table->morphs("taggable");
            $table->unique(['tag_id', "taggable_id", "taggable_type"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');
    }
};