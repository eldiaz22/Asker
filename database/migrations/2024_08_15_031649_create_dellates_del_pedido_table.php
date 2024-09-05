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
            Schema::create('detalle_pedidos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_pedido')->constrained('pedidos')->onDelete('cascade');
                $table->foreignId('id_producto')->constrained('productos')->onDelete('cascade');
                $table->integer('cantidad');
                $table->text('nota')->nullable();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pedidos');
    }
};