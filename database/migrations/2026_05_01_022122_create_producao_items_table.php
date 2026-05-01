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
        Schema::create('producao_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producao_id')->constrained('producoes')->cascadeOnDelete();
            $table->foreignId('produto_id')->constrained('produtos');
            $table->decimal('quantidade_produzida', 14, 4);
            $table->string('unidade', 10)->nullable();
            $table->timestamps();

            $table->unique(['producao_id', 'produto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producao_items');
    }
};
