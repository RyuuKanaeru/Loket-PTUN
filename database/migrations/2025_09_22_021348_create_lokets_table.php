<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lokets', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->default('Loket'); // nama loket, bisa diubah lewat UI
            $table->integer('nomor_terakhir')->default(0); // nomor terakhir yang dipanggil
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('lokets');
    }
};