<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('antrians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loket_id')->constrained()->cascadeOnDelete(); 
            $table->integer('nomor'); // nomor antrian
            $table->enum('status', ['menunggu', 'dipanggil'])->default('menunggu'); 
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('antrians');
    }
};