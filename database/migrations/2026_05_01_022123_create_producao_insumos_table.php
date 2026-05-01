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
        Schema::create('producao_insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producao_id')->constrained('producoes')->cascadeOnDelete();
            $table->foreignId('insumo_id')->constrained('insumos');
            $table->decimal('quantidade_consumida', 14, 4);
            $table->string('unidade', 10)->nullable();
            $table->decimal('custo_unitario', 14, 4)->default(0);
            $table->decimal('custo_total', 14, 4)->default(0);
            $table->timestamps();

            $table->unique(['producao_id', 'insumo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producao_insumos');
    }
};
