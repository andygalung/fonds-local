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
        Schema::create('investasi', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable();
            $table->string('regional', 100)->nullable();
            $table->string('kode_sinusa', 100)->nullable();
            $table->string('rekening_besar', 200)->nullable();
            $table->string('komoditi', 100)->nullable();
            $table->string('unit_kerja', 200)->nullable();
            $table->string('program_kerja', 500)->nullable();
            $table->string('nama_investasi', 500)->nullable();
            $table->string('status_anggaran', 100)->nullable();
            $table->string('no_wbs', 100)->nullable();
            $table->string('ppab_dpbb', 100)->nullable();
            $table->string('no_ppab_dpbb', 200)->nullable();
            $table->text('uraian_pekerjaan')->nullable();
            $table->string('stasiun_kerja', 200)->nullable();
            $table->string('pr', 100)->nullable();
            $table->date('tgl_doc_terima')->nullable();
            $table->date('tgl_anggaran')->nullable();
            $table->bigInteger('nilai_rkap')->nullable();
            $table->bigInteger('nilai_pengajuan')->nullable();
            $table->bigInteger('nilai_ph')->nullable();
            $table->date('tanggal_upload_ips')->nullable();
            $table->string('no_pk', 200)->nullable();
            $table->string('no_kontrak', 200)->nullable();
            $table->bigInteger('nilai_kontrak')->nullable();
            $table->string('penyedia', 300)->nullable();
            $table->date('tgl_mulai_kontrak')->nullable();
            $table->date('tgl_selesai_kontrak')->nullable();
            $table->integer('count_days')->nullable();
            $table->string('no_bast_1', 200)->nullable();
            $table->date('tgl_bast_1')->nullable();
            $table->string('no_bast_2', 200)->nullable();
            $table->date('tgl_bast_2')->nullable();
            $table->float('progress_fisik', 5, 2)->nullable();
            $table->string('no_addendum', 200)->nullable();
            $table->string('jangka_waktu_add', 200)->nullable();
            $table->string('prioritas', 100)->nullable();
            $table->string('nomor_po', 200)->nullable();
            $table->date('sa_gr_tanggal')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status_paket', 100)->nullable();
            $table->timestamps();

            // Composite index untuk updateOrInsert
            $table->index(['kode_sinusa', 'no_kontrak'], 'idx_kode_sinusa_kontrak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investasi');
    }
};
