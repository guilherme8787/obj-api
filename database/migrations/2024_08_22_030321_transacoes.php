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
            $table->char('forma_pagamento', 1);
            $table->decimal('valor', 15, 2);
            $table->decimal('saldo_apos_transacao', 15, 2);
            $table->timestamp('data_transacao')->useCurrent();
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
