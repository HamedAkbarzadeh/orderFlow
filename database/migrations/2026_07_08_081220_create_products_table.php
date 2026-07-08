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
        Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique()->nullable();
        $table->text('introduction')->nullable();
        $table->text('image')->nullable();
        $table->integer('price');
        $table->integer('discount')->default(0);
        $table->tinyInteger('status')->default(0);
        $table->tinyInteger('marketable')->default(1)->comment('0 => not marketable, 1 => marketable');
        $table->integer('sold_number')->default(0);
        $table->integer('marketable_number')->default(0);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
