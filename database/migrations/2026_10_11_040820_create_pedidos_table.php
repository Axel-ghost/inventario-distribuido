<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('solicitante_id')->constrained('users');
            $table->foreignId('revisor_id')->nullable()->constrained('users');
            $table->enum('estado', ['solicitado', 'en_revision', 'aprobado', 'rechazado', 'finalizado'])->default('solicitado');
            $table->text('comentario_admin')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};