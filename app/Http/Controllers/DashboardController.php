<?php

namespace App\Http\Controllers;

use App\Models\Investasi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ─── Summary Cards ─────────────────────────────────────────────────
        $totalRkap      = Investasi::sum('nilai_rkap');
        $totalKontrak   = Investasi::sum('nilai_kontrak');
        $totalProyek    = Investasi::count();
        $avgProgress    = Investasi::whereNotNull('progress_fisik')->avg('progress_fisik');

        // ─── Chart 1: Tren Nilai Investasi per Tahun (Line Chart) ──────────
        $trenTahunan = Investasi::select(
                DB::raw('YEAR(tgl_anggaran) as tahun'),
                DB::raw('SUM(nilai_rkap) as total_rkap'),
                DB::raw('SUM(nilai_kontrak) as total_kontrak')
            )
            ->whereNotNull('tgl_anggaran')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        // ─── Chart 2: Total Nilai per Regional (Bar Chart) ─────────────────
        $perRegional = Investasi::select(
                'regional',
                DB::raw('SUM(nilai_kontrak) as total_kontrak'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->whereNotNull('regional')
            ->groupBy('regional')
            ->orderByDesc('total_kontrak')
            ->get();

        // ─── Chart 3: Avg Progress per Unit Kerja (Horizontal Bar) ─────────
        $progressPerUnit = Investasi::select(
                'unit_kerja',
                DB::raw('ROUND(AVG(progress_fisik), 1) as avg_progress'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->whereNotNull('unit_kerja')
            ->whereNotNull('progress_fisik')
            ->groupBy('unit_kerja')
            ->orderByDesc('avg_progress')
            ->limit(20)
            ->get();

        // ─── Status Breakdown ───────────────────────────────────────────────
        $statusBreakdown = Investasi::select('status_paket', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('status_paket')
            ->groupBy('status_paket')
            ->orderByDesc('jumlah')
            ->get();

        return view('dashboard.index', compact(
            'totalRkap',
            'totalKontrak',
            'totalProyek',
            'avgProgress',
            'trenTahunan',
            'perRegional',
            'progressPerUnit',
            'statusBreakdown'
        ));
    }
}
