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
        Schema::create('compra_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();
            $table->foreignId('insumo_id')->constrained('insumos');
            $table->decimal('quantidade', 14, 4);
            $table->string('unidade', 10)->nullable();
            $table->decimal('valor_unitario', 14, 4);
            $table->decimal('valor_total', 14, 4);
            $table->timestamps();

            $table->unique(['compra_id', 'insumo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compra_items');
    }
};
