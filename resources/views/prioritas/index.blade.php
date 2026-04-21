@extends('layouts.app')

@section('title', 'Dashboard Investasi')

@section('content')

@php
    $records = $data['records'] ?? [];
@endphp

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --bg-main: #f8fafc;
        --bg-card: #ffffff;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        
        /* Stage Colors - Professional Light Palette */
        --rkap-bg: #fff7ed; --rkap-text: #9a3412; --rkap-border: #f97316;
        --doku-bg: #eff6ff; --doku-text: #1e40af; --doku-border: #3b82f6;
        --tekpol-bg: #ecfdf5; --tekpol-text: #065f46; --tekpol-border: #10b981;
        --hps-bg: #f5f3ff; --hps-text: #5b21b6; --hps-border: #8b5cf6;
        --sppbj-bg: #fff1f2; --sppbj-text: #9f1239; --sppbj-border: #f43f5e;
    }

    body {
        background-color: var(--bg-main) !important;
        color: var(--text-main) !important;
        font-family: 'Inter', sans-serif;
        overflow-x: hidden !important;
    }

    /* Hide Sidebar/Header for Max Space */
    #sidebar, header, footer { display: none !important; }

    #main-content {
        background-color: var(--bg-main) !important;
        margin-left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }

    main {
        padding: 1rem !important;
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        min-height: 0 !important;
    }

    /* Header Styling */
    .dashboard-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        background: #fff;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .nav-brand { display: flex; align-items: center; gap: 0.75rem; }
    .nav-brand h1 { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em; }
    .nav-brand p { font-size: 0.7rem; color: var(--text-muted); margin: 0; }
    
    .btn-pill {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s;
        border: 1px solid var(--border-color);
        background: #fff;
        color: var(--text-main);
    }

    .btn-pill:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .btn-blue { background: #2563eb; color: #fff; border: none; }
    .btn-blue:hover { background: #1d4ed8; }

    /* Table Grid Container */
    .grid-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }

    .grid-toolbar {
        padding: 0.75rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fafafa;
        border-bottom: 1px solid var(--border-color);
    }

    .grid-scroller {
        flex: 1;
        overflow: auto;
        position: relative;
    }

    /* Professional Table Styling */
    #prioritas-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        font-size: 11px;
    }

    #prioritas-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        padding: 10px 8px;
        border-right: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        z-index: 30;
    }

    /* Stage Headers Highlighting */
    .h-rkap { border-top: 4px solid var(--rkap-border) !important; background: var(--rkap-bg) !important; color: var(--rkap-text) !important; }
    .h-doku { border-top: 4px solid var(--doku-border) !important; background: var(--doku-bg) !important; color: var(--doku-text) !important; }
    .h-tekpol { border-top: 4px solid var(--tekpol-border) !important; background: var(--tekpol-bg) !important; color: var(--tekpol-text) !important; }
    .h-hps { border-top: 4px solid var(--hps-border) !important; background: var(--hps-bg) !important; color: var(--hps-text) !important; }
    .h-sppbj { border-top: 4px solid var(--sppbj-border) !important; background: var(--sppbj-bg) !important; color: var(--sppbj-text) !important; }
    .h-pct { border-top: 4px solid #94a3b8 !important; background: #f1f5f9 !important; }

    #prioritas-table thead tr:nth-child(2) th { top: 38px; }
    #prioritas-table thead tr:nth-child(3) th { top: 70px; font-size: 9px; }

    /* Sticky Column Unit Kerja */
    #prioritas-table .sticky-col {
        position: sticky;
        left: 0;
        z-index: 40;
        background: #fff;
        min-width: 220px;
        max-width: 220px;
        border-right: 2px solid #cbd5e1;
        text-align: left;
        padding-left: 1.25rem;
        box-shadow: 2px 0 5px rgba(0,0,0,0.02);
    }

    #prioritas-table thead .sticky-col { z-index: 50; background: #f8fafc; }

    /* Zebra Striping for Readability */
    #prioritas-table tbody tr:nth-child(even) td { background-color: #fdfdfd; }
    #prioritas-table tbody tr:nth-child(odd) td { background-color: #fff; }
    #prioritas-table tbody tr:hover td { background-color: #f1f5f9 !important; }

    /* Subtotal / Regional Styling */
    .row-highlight { background-color: #f1f5f9 !important; font-weight: 800; }
    .row-highlight td { color: #1e40af !important; border-bottom: 2px solid #cbd5e1 !important; }
    .row-highlight .sticky-col { background-color: #f1f5f9 !important; }

    /* Cell Data Styling */
    .val-num { font-family: 'JetBrains Mono', monospace; text-align: right; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }
    .val-dim { color: #cbd5e1; }
    
    /* Progress Badges */
    .badge-pct {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 10px;
        min-width: 50px;
        text-align: center;
        border: 1px solid transparent;
    }
    .badge-green { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
    .badge-amber { background: #fef3c7; color: #92400e; border-color: #fde68a; }
    .badge-red { background: #fee2e2; color: #991b1b; border-color: #fecaca; }

    /* Width definitions to prevent overlap */
    .w-biaya { min-width: 110px; width: 110px; }
    .w-paket { min-width: 60px; width: 60px; text-align: center !important; }
    .w-pct { min-width: 80px; width: 80px; text-align: center !important; }

    /* Tab Switcher Styling */
    .tab-switcher {
        display: flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
    }
    .tab-btn {
        padding: 6px 16px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .tab-btn.active {
        background: #fff;
        color: #2563eb;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    [data-tab-content] { display: none; }
    [data-tab-content].active { display: table-cell; }
    .tab-hidden { display: none !important; }

    /* New Tab Logic */
    #prioritas-table.tab-biaya .tab-content-paket { display: none !important; }
    #prioritas-table.tab-paket .tab-content-biaya { display: none !important; }
</style>
@endpush

<div class="dashboard-nav">
    <div class="nav-brand">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div>
            <h1>MONITORING INVESTASI</h1>
            <p>Progres harian realisasi nilai & paket investasi unit kerja.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('dashboard') }}" class="btn-pill">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Dashboard
        </a>
        <a href="{{ route('prioritas.index', ['refresh' => 1]) }}" class="btn-pill btn-blue">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refresh Data
        </a>
    </div>
</div>

<div class="grid-card">
    <div class="grid-toolbar">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Data Real-Time</span>
            </div>
            
            <div class="tab-switcher">
                <div class="tab-btn active" onclick="switchTab('biaya')">Monitoring Biaya</div>
                <div class="tab-btn" onclick="switchTab('paket')">Monitoring Paket</div>
            </div>
        </div>
        <button onclick="togglePctCols()" class="btn-pill">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Toggle %
        </button>
    </div>

    <div class="grid-scroller">
        <table id="prioritas-table" class="tab-biaya">
            <thead>
                <tr>
                    <th rowspan="3" class="sticky-col">Unit Kerja</th>
                    <th colspan="4" class="h-rkap stage-header">RKAP 2026 Setahun</th>
                    <th colspan="4" class="h-doku stage-header">Dokumen di Unit</th>
                    <th colspan="4" class="h-tekpol stage-header">Sudah Diajukan</th>
                    <th colspan="4" class="h-hps stage-header">HPS / Pengadaan</th>
                    <th colspan="4" class="h-sppbj stage-header">SPPBJ / Kontrak</th>
                    <th colspan="4" class="h-pct pct-col stage-header">Progress (%)</th>
                </tr>
                <tr>
                    @for($i=0; $i<5; $i++)
                        <th colspan="4" class="tab-content-biaya">Σ Biaya (Rp)</th>
                        <th colspan="4" class="tab-content-paket">Σ Paket</th>
                    @endfor
                    <th colspan="1" rowspan="2" class="pct-col tab-content-biaya">Diajukan</th>
                    <th colspan="1" rowspan="2" class="pct-col tab-content-paket">Diajukan</th>
                    <th colspan="1" rowspan="2" class="pct-col tab-content-biaya">Belum</th>
                    <th colspan="1" rowspan="2" class="pct-col tab-content-paket">Belum</th>
                    <th colspan="1" rowspan="2" class="pct-col tab-content-biaya">HPS</th>
                    <th colspan="1" rowspan="2" class="pct-col tab-content-paket">HPS</th>
                    <th colspan="1" rowspan="2" class="pct-col tab-content-biaya">SPPBJ</th>
                    <th colspan="1" rowspan="2" class="pct-col tab-content-paket">SPPBJ</th>
                </tr>
                <tr>
                    @for($g=0; $g<5; $g++)
                        <th class="w-biaya tab-content-biaya">1</th><th class="w-biaya tab-content-biaya">2</th><th class="w-biaya tab-content-biaya">3</th><th class="w-biaya tab-content-biaya">4</th>
                        <th class="w-paket tab-content-paket">1</th><th class="w-paket tab-content-paket">2</th><th class="w-paket tab-content-paket">3</th><th class="w-paket tab-content-paket">4</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($records as $row)
                    @php
                        $isSub = $row['_is_subtotal'];
                        $rowClass = $isSub ? 'row-highlight' : '';
                        
                        $fmtBiaya = fn($v) => ($v === null || $v == 0) ? '<span class="val-dim">0</span>' : number_format($v, 0, ',', '.');
                        $fmtPaket = fn($v) => ($v === null || $v == 0) ? '<span class="val-dim">0</span>' : $v;
                        
                        $getBadge = function($v) use ($isSub) {
                            if ($v === null || $v == 0) return '<span class="val-dim">0%</span>';
                            if ($isSub) return number_format($v, 1, ',', '.') . '%';
                            $cls = ($v >= 80) ? 'badge-green' : (($v >= 50) ? 'badge-amber' : 'badge-red');
                            return '<span class="badge-pct '.$cls.'">'.number_format($v, 1, ',', '.').'%</span>';
                        };
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="sticky-col">{{ $row['unit_kerja'] ?: '—' }}</td>
                        
                        @php
                            $stages = [
                                ['rkap_biaya_1','rkap_biaya_2','rkap_biaya_3','rkap_biaya_4','rkap_paket_1','rkap_paket_2','rkap_paket_3','rkap_paket_4'],
                                ['doku_biaya_1','doku_biaya_2','doku_biaya_3','doku_biaya_4','doku_paket_1','doku_paket_2','doku_paket_3','doku_paket_4'],
                                ['tekpol_biaya_1','tekpol_biaya_2','tekpol_biaya_3','tekpol_biaya_4','tekpol_paket_1','tekpol_paket_2','tekpol_paket_3','tekpol_paket_4'],
                                ['hps_biaya_1','hps_biaya_2','hps_biaya_3','hps_biaya_4','hps_paket_1','hps_paket_2','hps_paket_3','hps_paket_4'],
                                ['sppbj_biaya_1','sppbj_biaya_2','sppbj_biaya_3','sppbj_biaya_4','sppbj_paket_1','sppbj_paket_2','sppbj_paket_3','sppbj_paket_4'],
                            ];
                        @endphp

                        @foreach($stages as $stageCols)
                            @for($i=0; $i<4; $i++)
                                <td class="val-num tab-content-biaya">{!! $fmtBiaya($row[$stageCols[$i]]) !!}</td>
                            @endfor
                            @for($i=4; $i<8; $i++)
                                <td class="val-num w-paket tab-content-paket">{!! $fmtPaket($row[$stageCols[$i]]) !!}</td>
                            @endfor
                        @endforeach

                        {{-- Progress Columns --}}
                        <td class="w-pct pct-col tab-content-biaya">{!! $getBadge($row['pct_diaj_biaya']) !!}</td>
                        <td class="w-pct pct-col tab-content-paket">{!! $getBadge($row['pct_diaj_paket']) !!}</td>
                        <td class="w-pct pct-col tab-content-biaya">{!! $getBadge($row['pct_belum_biaya']) !!}</td>
                        <td class="w-pct pct-col tab-content-paket">{!! $getBadge($row['pct_belum_paket']) !!}</td>
                        <td class="w-pct pct-col tab-content-biaya">{!! $getBadge($row['pct_hps_biaya']) !!}</td>
                        <td class="w-pct pct-col tab-content-paket">{!! $getBadge($row['pct_hps_paket']) !!}</td>
                        <td class="w-pct pct-col tab-content-biaya">{!! $getBadge($row['pct_sppbj_biaya']) !!}</td>
                        <td class="w-pct pct-col tab-content-paket">{!! $getBadge($row['pct_sppbj_paket']) !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tab) {
        // Update buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
            const btnText = btn.innerText.toLowerCase();
            if(btnText.includes(tab)) btn.classList.add('active');
        });

        // Update table class
        const table = document.getElementById('prioritas-table');
        table.classList.remove('tab-biaya', 'tab-paket');
        table.classList.add('tab-' + tab);

        // Update stage headers colspan
        // When in single tab mode, each stage has 4 columns instead of 8
        const headers = document.querySelectorAll('.stage-header');
        headers.forEach(h => {
            h.setAttribute('colspan', '4');
        });
    }

    function togglePctCols() {
        const cols = document.querySelectorAll('.pct-col');
        cols.forEach(c => c.classList.toggle('tab-hidden'));
    }

    // Initialize state
    document.addEventListener('DOMContentLoaded', () => {
        // Ensure initial colspan is correct
        switchTab('biaya');
    });
</script>
@endpush

@endsection
