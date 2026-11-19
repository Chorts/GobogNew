<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gobog_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('tenan_id')->nullable()->constrained('tenans')->restrictOnDelete();
            $table->enum('tipe', ['penjualan', 'pengembalian', 'tambah_stok', 'kurangi_stok']);
            $table->unsignedInteger('jumlah');
            $table->double('harga_satuan');
            $table->double('total_nominal');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tipe', 'created_at']);
            $table->index(['tenan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gobog_transactions');
    }
};
