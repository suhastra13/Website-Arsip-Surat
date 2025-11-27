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
        Schema::create('surat', function (Blueprint $table) {
            $table->id();

            // tipe surat: masuk / keluar
            $table->enum('tipe', ['masuk', 'keluar']);

            // kategori
            $table->unsignedBigInteger('kategori_id');

            // kode arsip internal (bisa auto-generate, mis: SM-2025-0001)
            $table->string('kode_arsip')->unique();

            // data utama surat
            $table->string('no_surat');
            $table->date('tanggal_surat');

            // gunakan salah satu tergantung tipe
            $table->date('tanggal_terima')->nullable(); // untuk surat masuk
            $table->date('tanggal_keluar')->nullable(); // untuk surat keluar

            // asal & tujuan
            $table->string('asal_surat')->nullable();    // lebih relevan utk surat masuk
            $table->string('tujuan_surat')->nullable();  // lebih relevan utk surat keluar

            $table->string('perihal');
            $table->text('ringkasan')->nullable();
            $table->string('penandatangan')->nullable();

            // tingkat penting: biasa / penting / sangat_penting
            $table->enum('tingkat_penting', ['biasa', 'penting', 'sangat_penting'])->default('biasa');

            // file yang di-upload
            $table->string('file_path'); // path di storage

            // pencatat (user)
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // foreign key
            $table->foreign('kategori_id')->references('id')->on('kategori_surat')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat');
    }
};
