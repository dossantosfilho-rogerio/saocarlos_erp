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
        Schema::create('insumo_lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained('insumos')->cascadeOnDelete();
            $table->foreignId('compra_item_id')->nullable()->constrained('compra_items')->nullOnDelete();
            $table->timestamp('data_entrada');
            $table->decimal('quantidade_entrada', 14, 4);
            $table->decimal('quantidade_saldo', 14, 4);
            $table->decimal('custo_unitario', 14, 4);
            $table->timestamps();

            $table->index(['insumo_id', 'data_entrada', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumo_lotes');
    }
};
