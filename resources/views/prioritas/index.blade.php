@extends('layouts.app')

@section('title', 'Dashboard Investasi')

@section('content')

@php
    $records = $data['records'] ?? [];
@endphp

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --slate-950: #0f172a;
        --slate-900: #1e293b;
        --slate-800: #334155;
        --blue-500: #3b82f6;
        --emerald-500: #10b981;
        --amber-500: #f59e0b;
        --rose-500: #f43f5e;
        --indigo-500: #6366f1;
        --text-100: #f1f5f9;
        --text-400: #94a3b8;
        --border-subtle: rgba(255, 255, 255, 0.05);
    }

    body {
        background-color: var(--slate-950) !important;
        color: var(--text-100) !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow-x: hidden !important;
    }

    /* Fullscreen Mode - Hide App UI */
    #sidebar, header, footer { display: none !important; }

    #main-content {
        background-color: var(--slate-950) !important;
        margin-left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }

    main {
        padding: 1.5rem !important;
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        min-height: 0 !important;
    }

    /* Page Header */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .brand-box {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .brand-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--blue-500), var(--indigo-500));
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.2);
    }

    .brand-text h1 {
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        margin: 0;
        background: linear-gradient(to right, #fff, #94a3b8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .brand-text p {
        font-size: 0.8rem;
        color: var(--text-400);
        margin: 0;
    }

    /* Action Buttons */
    .btn-action {
        background: var(--slate-900);
        border: 1px solid var(--slate-800);
        color: #fff;
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .btn-action:hover {
        background: var(--slate-800);
        transform: translateY(-1px);
    }

    .btn-primary-blue {
        background: var(--blue-500);
        border: none;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-primary-blue:hover {
        background: #2563eb;
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
    }

    /* Main Table Container */
    .data-card {
        background: var(--slate-900);
        border-radius: 20px;
        border: 1px solid var(--slate-800);
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }

    .table-header-toolbar {
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-subtle);
    }

    .table-scroller {
        flex: 1;
        overflow: auto;
        position: relative;
    }

    /* Table Professional Styling */
    #prioritas-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        font-size: 11px;
    }

    /* Header Grouping & Coloring */
    #prioritas-table thead th {
        background: var(--slate-900);
        color: var(--text-400);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 10px;
        border-right: 1px solid var(--border-subtle);
        border-bottom: 1px solid var(--border-subtle);
        position: sticky;
        top: 0;
        z-index: 30;
    }

    /* Colored accents for each stage section */
    .stage-rkap { border-top: 3px solid var(--amber-500) !important; color: var(--amber-500) !important; }
    .stage-doku { border-top: 3px solid var(--blue-500) !important; color: var(--blue-500) !important; }
    .stage-tekpol { border-top: 3px solid var(--emerald-500) !important; color: var(--emerald-500) !important; }
    .stage-hps { border-top: 3px solid var(--indigo-500) !important; color: var(--indigo-500) !important; }
    .stage-sppbj { border-top: 3px solid var(--rose-500) !important; color: var(--rose-500) !important; }
    .stage-pct { border-top: 3px solid var(--text-400) !important; color: var(--text-100) !important; }

    #prioritas-table thead tr:nth-child(2) th { top: 43px; }
    #prioritas-table thead tr:nth-child(3) th { top: 78px; font-size: 9px; color: var(--text-400); }

    /* Sticky Column */
    #prioritas-table .sticky-col {
        position: sticky;
        left: 0;
        z-index: 40;
        background: var(--slate-900);
        min-width: 220px;
        max-width: 220px;
        border-right: 2px solid var(--slate-800);
        text-align: left;
        padding-left: 1.5rem;
    }

    #prioritas-table thead .sticky-col { z-index: 50; }

    /* Table Body */
    #prioritas-table td {
        padding: 10px;
        border-right: 1px solid var(--border-subtle);
        border-bottom: 1px solid var(--border-subtle);
        white-space: nowrap;
        color: var(--text-100);
    }

    #prioritas-table tbody tr:hover td {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }

    /* Subtotal / Regional Rows */
    .row-total {
        background-color: rgba(59, 130, 246, 0.1) !important;
        font-weight: 800;
    }
    .row-total td { color: var(--blue-500) !important; }
    .row-total .sticky-col { background-color: #1e293b !important; }

    /* Data Formatting */
    .num-font { font-family: 'JetBrains Mono', monospace; text-align: right; }
    .dim-zero { color: #334155; }
    
    /* Progress Pills */
    .pct-pill {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 10px;
        min-width: 55px;
        text-align: center;
    }
    .pill-high { background: rgba(16, 185, 129, 0.1); color: var(--emerald-500); border: 1px solid rgba(16, 185, 129, 0.2); }
    .pill-mid { background: rgba(245, 158, 11, 0.1); color: var(--amber-500); border: 1px solid rgba(245, 158, 11, 0.2); }
    .pill-low { background: rgba(244, 63, 94, 0.1); color: var(--rose-500); border: 1px solid rgba(244, 63, 94, 0.2); }

    /* Column Sizing */
    .w-biaya { min-width: 100px; width: 100px; }
    .w-paket { min-width: 45px; width: 45px; }
    .w-pct { min-width: 80px; width: 80px; text-align: center; }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: var(--slate-950); }
    ::-webkit-scrollbar-thumb { background: var(--slate-800); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--slate-700); }
</style>
@endpush

<div class="dashboard-header">
    <div class="brand-box">
        <div class="brand-icon">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div class="brand-text">
            <h1>INVESTASI</h1>
            <p>Monitoring harian progres realisasi investasi unit kerja.</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="btn-action">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <a href="{{ route('prioritas.index', ['refresh' => 1]) }}" class="btn-action btn-primary-blue">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refresh Data
        </a>
    </div>
</div>

<div class="data-card">
    <div class="table-header-toolbar">
        <div class="flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Detail Monitoring Unit Kerja</span>
        </div>
        <button onclick="togglePctCols()" class="btn-action">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Toggle Persentase
        </button>
    </div>

    <div class="table-scroller">
        <table id="prioritas-table">
            <thead>
                <tr>
                    <th rowspan="3" class="sticky-col">Unit Kerja</th>
                    <th colspan="8" class="stage-rkap">RKAP 2026 Setahun</th>
                    <th colspan="8" class="stage-doku">Dokumen di Unit</th>
                    <th colspan="8" class="stage-tekpol">Sudah Diajukan (Tekpol)</th>
                    <th colspan="8" class="stage-hps">HPS / Pengadaan</th>
                    <th colspan="8" class="stage-sppbj">SPPBJ / Kontrak</th>
                    <th colspan="8" class="stage-pct pct-col">Progress Thd RKAP (%)</th>
                </tr>
                <tr>
                    @for($i=0; $i<5; $i++)
                        <th colspan="4">Σ Biaya (Rp)</th>
                        <th colspan="4">Σ Paket</th>
                    @endfor
                    <th colspan="2" class="pct-col">Diajukan</th>
                    <th colspan="2" class="pct-col">Belum</th>
                    <th colspan="2" class="pct-col">HPS</th>
                    <th colspan="2" class="pct-col">SPPBJ</th>
                </tr>
                <tr>
                    @for($g=0; $g<5; $g++)
                        <th class="w-biaya">1</th><th class="w-biaya">2</th><th class="w-biaya">3</th><th class="w-biaya">4</th>
                        <th class="w-paket">1</th><th class="w-paket">2</th><th class="w-paket">3</th><th class="w-paket">4</th>
                    @endfor
                    <th class="w-pct pct-col">Biaya</th><th class="w-pct pct-col">Paket</th>
                    <th class="w-pct pct-col">Biaya</th><th class="w-pct pct-col">Paket</th>
                    <th class="w-pct pct-col">Biaya</th><th class="w-pct pct-col">Paket</th>
                    <th class="w-pct pct-col">Biaya</th><th class="w-pct pct-col">Paket</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $row)
                    @php
                        $isSub = $row['_is_subtotal'];
                        $rowClass = $isSub ? 'row-total' : '';
                        
                        $fmtBiaya = fn($v) => ($v === null || $v == 0) ? '<span class="dim-zero">0</span>' : number_format($v, 0, ',', '.');
                        $fmtPaket = fn($v) => ($v === null || $v == 0) ? '<span class="dim-zero">0</span>' : $v;
                        
                        $getPillClass = function($v) {
                            if ($v >= 80) return 'pill-high';
                            if ($v >= 50) return 'pill-mid';
                            return 'pill-low';
                        };

                        $fmtPct = function($v) use ($isSub, $getPillClass) {
                            if ($v === null || $v == 0) return '<span class="dim-zero">0%</span>';
                            if ($isSub) return number_format($v, 1, ',', '.') . '%';
                            return '<span class="pct-pill '.$getPillClass($v).'">'.number_format($v, 1, ',', '.').'%</span>';
                        };
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="sticky-col">{{ $row['unit_kerja'] ?: '—' }}</td>
                        
                        {{-- RKAP --}}
                        <td class="num-font">{!! $fmtBiaya($row['rkap_biaya_1']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['rkap_biaya_2']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['rkap_biaya_3']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['rkap_biaya_4']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['rkap_paket_1']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['rkap_paket_2']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['rkap_paket_3']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['rkap_paket_4']) !!}</td>

                        {{-- Dokumen --}}
                        <td class="num-font">{!! $fmtBiaya($row['doku_biaya_1']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['doku_biaya_2']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['doku_biaya_3']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['doku_biaya_4']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['doku_paket_1']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['doku_paket_2']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['doku_paket_3']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['doku_paket_4']) !!}</td>

                        {{-- Tekpol --}}
                        <td class="num-font">{!! $fmtBiaya($row['tekpol_biaya_1']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['tekpol_biaya_2']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['tekpol_biaya_3']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['tekpol_biaya_4']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['tekpol_paket_1']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['tekpol_paket_2']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['tekpol_paket_3']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['tekpol_paket_4']) !!}</td>

                        {{-- HPS --}}
                        <td class="num-font">{!! $fmtBiaya($row['hps_biaya_1']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['hps_biaya_2']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['hps_biaya_3']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['hps_biaya_4']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['hps_paket_1']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['hps_paket_2']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['hps_paket_3']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['hps_paket_4']) !!}</td>

                        {{-- SPPBJ --}}
                        <td class="num-font">{!! $fmtBiaya($row['sppbj_biaya_1']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['sppbj_biaya_2']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['sppbj_biaya_3']) !!}</td>
                        <td class="num-font">{!! $fmtBiaya($row['sppbj_biaya_4']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['sppbj_paket_1']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['sppbj_paket_2']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['sppbj_paket_3']) !!}</td>
                        <td class="num-font">{!! $fmtPaket($row['sppbj_paket_4']) !!}</td>

                        {{-- Progress --}}
                        <td class="w-pct pct-col">{!! $fmtPct($row['pct_diaj_biaya']) !!}</td>
                        <td class="w-pct pct-col">{!! $fmtPct($row['pct_diaj_paket']) !!}</td>
                        <td class="w-pct pct-col">{!! $fmtPct($row['pct_belum_biaya']) !!}</td>
                        <td class="w-pct pct-col">{!! $fmtPct($row['pct_belum_paket']) !!}</td>
                        <td class="w-pct pct-col">{!! $fmtPct($row['pct_hps_biaya']) !!}</td>
                        <td class="w-pct pct-col">{!! $fmtPct($row['pct_hps_paket']) !!}</td>
                        <td class="w-pct pct-col">{!! $fmtPct($row['pct_sppbj_biaya']) !!}</td>
                        <td class="w-pct pct-col">{!! $fmtPct($row['pct_sppbj_paket']) !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    function togglePctCols() {
        const cols = document.querySelectorAll('.pct-col');
        const isHidden = cols[0].style.display === 'none';
        cols.forEach(c => c.style.display = isHidden ? '' : 'none');
    }
</script>
@endpush

@endsection
