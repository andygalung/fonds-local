<?php

namespace App\Http\Controllers;

use App\Models\Investasi;
use Illuminate\Http\Request;

class InvestasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Investasi::query();

        // ─── Search ────────────────────────────────────────────────────────
        $query->search($request->input('search'));

        // ─── Filters ───────────────────────────────────────────────────────
        $query->filterByRegional($request->input('regional'));
        $query->filterByUnitKerja($request->input('unit_kerja'));
        $query->filterByTahun($request->input('tahun'));
        $query->filterByStatus($request->input('status'));

        // ─── Sorting ───────────────────────────────────────────────────────
        $sortBy  = $request->input('sort', 'id');
        $sortDir = $request->input('dir', 'asc');

        if (in_array($sortBy, ['no', 'nama_investasi', 'nilai_rkap', 'nilai_kontrak', 'progress_fisik', 'tgl_anggaran', 'status_paket'])) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $investasi = $query->paginate(15)->appends($request->query());

        // ─── Filter Options ────────────────────────────────────────────────
        $regionals   = Investasi::select('regional')->distinct()->whereNotNull('regional')->orderBy('regional')->pluck('regional');
        $unitKerjas  = Investasi::select('unit_kerja')->distinct()->whereNotNull('unit_kerja')->orderBy('unit_kerja')->pluck('unit_kerja');
        $statusList  = Investasi::select('status_paket')->distinct()->whereNotNull('status_paket')->orderBy('status_paket')->pluck('status_paket');
        $tahunList   = Investasi::selectRaw('YEAR(tgl_anggaran) as tahun')
                        ->whereNotNull('tgl_anggaran')
                        ->distinct()
                        ->orderBy('tahun', 'desc')
                        ->pluck('tahun');

        return view('investasi.index', compact(
            'investasi',
            'regionals',
            'unitKerjas',
            'statusList',
            'tahunList'
        ));
    }
}
