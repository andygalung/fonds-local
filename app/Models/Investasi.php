<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Investasi extends Model
{
    use HasFactory;

    protected $table = 'investasi';

    protected $fillable = [
        'no',
        'regional',
        'kode_sinusa',
        'rekening_besar',
        'komoditi',
        'unit_kerja',
        'program_kerja',
        'nama_investasi',
        'status_anggaran',
        'no_wbs',
        'ppab_dpbb',
        'no_ppab_dpbb',
        'uraian_pekerjaan',
        'stasiun_kerja',
        'pr',
        'tgl_doc_terima',
        'tgl_anggaran',
        'nilai_rkap',
        'nilai_pengajuan',
        'nilai_ph',
        'tanggal_upload_ips',
        'no_pk',
        'no_kontrak',
        'nilai_kontrak',
        'penyedia',
        'tgl_mulai_kontrak',
        'tgl_selesai_kontrak',
        'count_days',
        'no_bast_1',
        'tgl_bast_1',
        'no_bast_2',
        'tgl_bast_2',
        'progress_fisik',
        'no_addendum',
        'jangka_waktu_add',
        'prioritas',
        'nomor_po',
        'sa_gr_tanggal',
        'keterangan',
        'status_paket',
    ];

    protected $casts = [
        'tgl_doc_terima'    => 'date',
        'tgl_anggaran'      => 'date',
        'tanggal_upload_ips'=> 'date',
        'tgl_mulai_kontrak' => 'date',
        'tgl_selesai_kontrak'=> 'date',
        'tgl_bast_1'        => 'date',
        'tgl_bast_2'        => 'date',
        'sa_gr_tanggal'     => 'date',
        'nilai_rkap'        => 'integer',
        'nilai_pengajuan'   => 'integer',
        'nilai_ph'          => 'integer',
        'nilai_kontrak'     => 'integer',
        'count_days'        => 'integer',
        'progress_fisik'    => 'float',
        'no'                => 'integer',
    ];

    // ─── Scopes ───────────────────────────────────────────────────────────

    public function scopeFilterByRegional(Builder $query, ?string $regional): Builder
    {
        return $regional ? $query->where('regional', $regional) : $query;
    }

    public function scopeFilterByUnitKerja(Builder $query, ?string $unitKerja): Builder
    {
        return $unitKerja ? $query->where('unit_kerja', $unitKerja) : $query;
    }

    public function scopeFilterByTahun(Builder $query, ?string $tahun): Builder
    {
        return $tahun ? $query->whereYear('tgl_anggaran', $tahun) : $query;
    }

    public function scopeFilterByStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status_paket', $status) : $query;
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (!$keyword) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('nama_investasi', 'like', "%{$keyword}%")
              ->orWhere('kode_sinusa', 'like', "%{$keyword}%")
              ->orWhere('unit_kerja', 'like', "%{$keyword}%")
              ->orWhere('penyedia', 'like', "%{$keyword}%")
              ->orWhere('no_kontrak', 'like', "%{$keyword}%")
              ->orWhere('no_wbs', 'like', "%{$keyword}%")
              ->orWhere('status_paket', 'like', "%{$keyword}%")
              ->orWhere('rekening_besar', 'like', "%{$keyword}%")
              ->orWhere('komoditi', 'like', "%{$keyword}%");
        });
    }
}
