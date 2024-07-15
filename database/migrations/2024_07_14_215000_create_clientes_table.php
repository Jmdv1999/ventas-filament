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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_identificacion', ['V', 'E', 'J', 'G']);
            $table->string('identificacion', length: 20)->unique();
            $table->string('nombre', length: 50);
            $table->text('direccion');
            $table->string('email', length: 100);
            $table->string('telefono', length: 13);
            $table->date('nacimiento');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
