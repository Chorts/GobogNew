<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gobog_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('total_stok')->default(0);
            $table->unsignedBigInteger('stok_tersedia')->default(0);
            $table->unsignedBigInteger('stok_beredar')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gobog_stocks');
    }
};
