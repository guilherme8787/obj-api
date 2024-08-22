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
        Schema::create('transacoes', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_conta');
            $table->text('forma_pagamento');
            $table->decimal('valor', 15, 2);
            $table->decimal('saldo_apos_transacao', 15, 2);
            $table->timestamps();
            $table->foreign('numero_conta')->references('numero_conta')->on('contas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacoes');
    }
};
