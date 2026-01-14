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
        Schema::create('coffees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('brand');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock');
            // Rutas cortas en storage y URL remotas; no guardamos blobs/base64 en BD
            $table->string('image_path', 2048)->nullable();
            $table->string('image_url', 2048)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coffees');
    }
};
