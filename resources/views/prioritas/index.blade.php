@extends('layouts.app')

@section('title', 'Dashboard Investasi — Monitoring Prioritas')

@section('content')

@php
    $records = $data['records'] ?? [];
    $title   = $data['title'] ?? 'Monitoring Investasi Prioritas';
    $date    = $data['date']  ?? '';

    // ── Aggregate totals dari records (skip subtotal rows) ───────────────
    $dataRows = array_filter($records, fn($r) => !$r['_is_subtotal']);

    $totalRkap    = array_sum(array_map(fn($r) => ($r['rkap_biaya_1'] + $r['rkap_biaya_2'] + $r['rkap_biaya_3'] + $r['rkap_biaya_4']), $dataRows));
    $totalDoku    = array_sum(array_map(fn($r) => ($r['doku_biaya_1'] + $r['doku_biaya_2'] + $r['doku_biaya_3'] + $r['doku_biaya_4']), $dataRows));
    $totalDiaj    = array_sum(array_map(fn($r) => ($r['tekpol_biaya_1'] + $r['tekpol_biaya_2'] + $r['tekpol_biaya_3'] + $r['tekpol_biaya_4']), $dataRows));
    $totalHps     = array_sum(array_map(fn($r) => ($r['hps_biaya_1'] + $r['hps_biaya_2'] + $r['hps_biaya_3'] + $r['hps_biaya_4']), $dataRows));
    $totalSppbj   = array_sum(array_map(fn($r) => ($r['sppbj_biaya_1'] + $r['sppbj_biaya_2'] + $r['sppbj_biaya_3'] + $r['sppbj_biaya_4']), $dataRows));

    $pctDiaj  = $totalRkap > 0 ? round(($totalDiaj / $totalRkap) * 100, 1) : 0;
    $pctHps   = $totalRkap > 0 ? round(($totalHps  / $totalRkap) * 100, 1) : 0;
    $pctSppbj = $totalRkap > 0 ? round(($totalSppbj/ $totalRkap) * 100, 1) : 0;

    // Paket totals
    $totalRkapPaket  = array_sum(array_map(fn($r) => (($r['rkap_paket_1']??0) + ($r['rkap_paket_2']??0) + ($r['rkap_paket_3']??0) + ($r['rkap_paket_4']??0)), $dataRows));
    $totalDiajPaket  = array_sum(array_map(fn($r) => (($r['tekpol_paket_1']??0) + ($r['tekpol_paket_2']??0) + ($r['tekpol_paket_3']??0) + ($r['tekpol_paket_4']??0)), $dataRows));
    $totalHpsPaket   = array_sum(array_map(fn($r) => (($r['hps_paket_1']??0) + ($r['hps_paket_2']??0) + ($r['hps_paket_3']??0) + ($r['hps_paket_4']??0)), $dataRows));
    $totalSppbjPaket = array_sum(array_map(fn($r) => (($r['sppbj_paket_1']??0) + ($r['sppbj_paket_2']??0) + ($r['sppbj_paket_3']??0) + ($r['sppbj_paket_4']??0)), $dataRows));

    $pctDiajPaket  = $totalRkapPaket > 0 ? round(($totalDiajPaket / $totalRkapPaket) * 100, 1) : 0;
    $pctHpsPaket   = $totalRkapPaket > 0 ? round(($totalHpsPaket  / $totalRkapPaket) * 100, 1) : 0;
    $pctSppbjPaket = $totalRkapPaket > 0 ? round(($totalSppbjPaket/ $totalRkapPaket) * 100, 1) : 0;

    // Unit kerja chart data (non-subtotal)
    $chartUnits    = [];
    $chartDiaj     = [];
    $chartHps      = [];
    $chartSppbj    = [];
    foreach ($dataRows as $r) {
        if (empty($r['unit_kerja'])) continue;
        $chartUnits[]  = addslashes($r['unit_kerja']);
        $chartDiaj[]   = $r['pct_diaj_biaya']  ?? 0;
        $chartHps[]    = $r['pct_hps_biaya']   ?? 0;
        $chartSppbj[]  = $r['pct_sppbj_biaya'] ?? 0;
    }

    $fmtRp = fn($v) => 'Rp ' . number_format($v / 1_000_000, 0, ',', '.') . ' Jt';
@endphp

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════════
   ROOT TOKENS — Fresh Blue Theme
   ═══════════════════════════════════════════════════════════ */
:root {
    --blue-50:  #eff6ff;
    --blue-100: #dbeafe;
    --blue-200: #bfdbfe;
    --blue-300: #93c5fd;
    --blue-400: #60a5fa;
    --blue-500: #3b82f6;
    --blue-600: #2563eb;
    --blue-700: #1d4ed8;
    --blue-800: #1e40af;
    --blue-900: #1e3a8a;

    --cyan-400: #22d3ee;
    --cyan-500: #06b6d4;

    --bg-page  : #f0f7ff;
    --bg-card  : #ffffff;
    --bg-glass : rgba(255,255,255,0.72);
    --border   : rgba(37,99,235,.12);
    --shadow-sm: 0 2px 8px rgba(30,64,175,.07);
    --shadow-md: 0 6px 24px rgba(30,64,175,.12);
    --shadow-lg: 0 12px 40px rgba(30,64,175,.16);

    --text-head : #0f172a;
    --text-body : #1e293b;
    --text-muted: #64748b;

    --rkap-clr   : #f97316;
    --doku-clr   : #3b82f6;
    --tekpol-clr : #10b981;
    --hps-clr    : #8b5cf6;
    --sppbj-clr  : #f43f5e;
    --pct-clr    : #06b6d4;

    --radius-sm: 8px;
    --radius-md: 14px;
    --radius-lg: 20px;
}

/* ═══════════════════════════════════════════════════════════
   RESET / BASE
   ═══════════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }

#sidebar, header, footer { display: none !important; }

body {
    background: var(--bg-page) !important;
    color: var(--text-body) !important;
    font-family: 'Inter', system-ui, sans-serif;
    overflow-x: hidden;
    min-height: 100vh;
}

#main-content {
    background: transparent !important;
    margin-left: 0 !important;
    width: 100vw !important;
    padding: 0 !important;
    min-height: 100vh;
}

main {
    padding: 0 !important;
    margin: 0 !important;
}

/* ═══════════════════════════════════════════════════════════
   TOP HEADER BAR
   ═══════════════════════════════════════════════════════════ */
.page-header {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 40%, #2563eb 70%, #0ea5e9 100%);
    padding: 1.25rem 2rem 1.5rem;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.page-header::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0; right: 0;
    height: 40px;
    background: var(--bg-page);
    clip-path: ellipse(55% 100% at 50% 100%);
}

.header-inner {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-brand { display: flex; align-items: center; gap: 1rem; }

.brand-icon {
    width: 52px; height: 52px;
    background: rgba(255,255,255,.18);
    border: 2px solid rgba(255,255,255,.3);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(8px);
    flex-shrink: 0;
}

.brand-icon svg { width: 28px; height: 28px; color: #fff; }

.header-brand h1 {
    font-size: 1.35rem;
    font-weight: 900;
    color: #fff;
    margin: 0;
    letter-spacing: -0.03em;
    line-height: 1.2;
    text-shadow: 0 2px 8px rgba(0,0,0,.2);
}

.header-brand p {
    font-size: 0.72rem;
    color: rgba(255,255,255,.75);
    margin: 0.2rem 0 0;
    font-weight: 400;
}

.header-date-badge {
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    backdrop-filter: blur(6px);
    margin-top: 0.5rem;
}

.header-actions { display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; }

.btn-header {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.5rem 1.1rem;
    border-radius: 8px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    border: none;
}

.btn-header-ghost {
    background: rgba(255,255,255,.12);
    border: 1.5px solid rgba(255,255,255,.3);
    color: #fff;
}
.btn-header-ghost:hover { background: rgba(255,255,255,.22); color: #fff; }

.btn-header-solid {
    background: #fff;
    color: var(--blue-700);
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
}
.btn-header-solid:hover { background: var(--blue-50); transform: translateY(-1px); }

/* ═══════════════════════════════════════════════════════════
   PAGE BODY
   ═══════════════════════════════════════════════════════════ */
.page-body {
    padding: 0 1.75rem 2rem;
    max-width: 100%;
}

/* ═══════════════════════════════════════════════════════════
   KPI CARDS ROW
   ═══════════════════════════════════════════════════════════ */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
    margin-top: -1.5rem;
    margin-bottom: 1.25rem;
    position: relative;
    z-index: 2;
}

.kpi-card {
    background: var(--bg-card);
    border-radius: var(--radius-md);
    padding: 1rem 1.1rem;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

.kpi-card-accent {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3.5px;
    border-radius: var(--radius-md) var(--radius-md) 0 0;
}

.kpi-glow {
    position: absolute;
    top: -20px; right: -20px;
    width: 80px; height: 80px;
    border-radius: 50%;
    opacity: 0.07;
}

.kpi-label {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted);
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.kpi-icon {
    width: 22px; height: 22px;
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
}

.kpi-value {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--text-head);
    letter-spacing: -0.02em;
    line-height: 1.1;
    font-family: 'JetBrains Mono', monospace;
}

.kpi-sub {
    display: flex; align-items: center; gap: 0.35rem;
    margin-top: 0.5rem;
}

.kpi-pct-bar {
    flex: 1;
    height: 4px;
    background: var(--blue-100);
    border-radius: 2px;
    overflow: hidden;
}
.kpi-pct-fill {
    height: 100%;
    border-radius: 2px;
    transition: width 1s ease;
}

.kpi-pct-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: var(--text-muted);
    white-space: nowrap;
}

/* ═══════════════════════════════════════════════════════════
   CHART SECTION
   ═══════════════════════════════════════════════════════════ */
.section-title {
    font-size: 0.62rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--blue-600);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.section-title::before {
    content: '';
    display: inline-block;
    width: 3px; height: 14px;
    background: linear-gradient(to bottom, var(--blue-500), var(--cyan-500));
    border-radius: 2px;
}

.charts-row {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
    align-items: stretch;
}

.charts-left-col {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.charts-left-col > .chart-card {
    flex: 1;
    justify-content: center;
}

.chart-card {
    background: var(--bg-card);
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    padding: 1rem 1.15rem;
    display: flex;
    flex-direction: column;
    min-height: 280px;
}

.chart-card-title {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-head);
    margin-bottom: 0.1rem;
    flex-shrink: 0;
}
.chart-card-sub {
    font-size: 0.6rem;
    color: var(--text-muted);
    margin-bottom: 0.6rem;
    flex-shrink: 0;
}

.donut-wrap {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    min-height: 0;
}

.donut-legend { flex: 1; display: flex; flex-direction: column; gap: 0.55rem; }

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.legend-dot {
    width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0;
}
.legend-text { font-size: 0.63rem; font-weight: 600; color: var(--text-body); flex: 1; line-height: 1.3; }
.legend-val  { font-size: 0.7rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; }

/* ═══════════════════════════════════════════════════════════
   TABLE SECTION
   ═══════════════════════════════════════════════════════════ */
.table-card {
    background: var(--bg-card);
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.table-toolbar {
    padding: 0.8rem 1.25rem;
    background: linear-gradient(to right, #f8faff, #fff);
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.toolbar-left { display: flex; align-items: center; gap: 1rem; }

.live-badge {
    display: flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.7rem;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 20px;
    font-size: 0.62rem;
    font-weight: 700;
    color: #166534;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.live-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #22c55e;
    animation: pulse-green 2s infinite;
}

@keyframes pulse-green {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(1.4); }
}

.tab-switcher {
    display: flex;
    background: var(--blue-50);
    border: 1.5px solid var(--blue-200);
    padding: 3px;
    border-radius: 10px;
    gap: 2px;
}

.tab-btn {
    padding: 5px 14px;
    border-radius: 8px;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--blue-600);
    background: transparent;
    border: none;
    user-select: none;
}
.tab-btn.active {
    background: var(--blue-600);
    color: #fff;
    box-shadow: 0 2px 8px rgba(37,99,235,.3);
}

.btn-sm {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.4rem 0.9rem;
    border-radius: 7px;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    cursor: pointer;
    transition: all 0.2s;
    border: 1.5px solid var(--border);
    background: #fff;
    color: var(--text-muted);
}
.btn-sm:hover { background: var(--blue-50); border-color: var(--blue-300); color: var(--blue-700); }
.btn-sm svg  { width: 14px; height: 14px; }

/* ── TABLE ──────────────────────────────────────────────── */
.table-scroller {
    overflow: auto;
    max-height: 50vh;
}

#prioritas-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 10.5px;
}

#prioritas-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 9px 7px;
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    z-index: 30;
    white-space: nowrap;
}

/* Stage Headers */
.h-rkap    { border-top: 3px solid var(--rkap-clr)   !important; background: #fff7ed !important; color: #9a3412 !important; }
.h-doku    { border-top: 3px solid var(--doku-clr)   !important; background: #eff6ff !important; color: #1e40af !important; }
.h-tekpol  { border-top: 3px solid var(--tekpol-clr) !important; background: #ecfdf5 !important; color: #065f46 !important; }
.h-hps     { border-top: 3px solid var(--hps-clr)    !important; background: #f5f3ff !important; color: #5b21b6 !important; }
.h-sppbj   { border-top: 3px solid var(--sppbj-clr)  !important; background: #fff1f2 !important; color: #9f1239 !important; }
.h-pct     { border-top: 3px solid var(--pct-clr)    !important; background: #ecfeff !important; color: #164e63 !important; }

#prioritas-table thead tr:nth-child(2) th { top: 36px; }
#prioritas-table thead tr:nth-child(3) th { top: 68px; font-size: 9px; }

/* Sticky Unit Kerja Column */
#prioritas-table .sticky-col {
    position: sticky; left: 0; z-index: 40;
    background: #fff;
    min-width: 210px; max-width: 210px;
    border-right: 2px solid var(--blue-200);
    text-align: left;
    padding-left: 1rem;
    box-shadow: 3px 0 8px rgba(37,99,235,.06);
    font-weight: 500;
    color: var(--text-head);
}
#prioritas-table thead .sticky-col { z-index: 50; background: #f8fafc; }

/* Row Styles */
#prioritas-table tbody tr:nth-child(even) td { background: #fafcff; }
#prioritas-table tbody tr:nth-child(odd)  td { background: #fff; }
#prioritas-table tbody tr:hover td { background: #eff6ff !important; }

.row-highlight              { background: #eff6ff !important; font-weight: 800; }
.row-highlight td           { color: var(--blue-800) !important; border-bottom: 2px solid var(--blue-200) !important; background: #eff6ff !important; }
.row-highlight .sticky-col  { background: #eff6ff !important; }

/* Cell Styles */
.val-num  { font-family: 'JetBrains Mono', monospace; text-align: right; letter-spacing: -0.02em; }
.val-dim  { color: #cbd5e1; }

/* Progress Badges */
.badge-pct {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 5px;
    font-weight: 700;
    font-size: 9.5px;
    min-width: 46px;
    text-align: center;
    border: 1px solid transparent;
}
.badge-green { background: #dcfce7; color: #166534; border-color: #86efac; }
.badge-amber { background: #fef3c7; color: #92400e; border-color: #fde68a; }
.badge-red   { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }

/* Widths */
.w-biaya { min-width: 100px; width: 100px; }
.w-paket { min-width: 55px;  width: 55px;  text-align: center !important; }
.w-pct   { min-width: 72px;  width: 72px;  text-align: center !important; }

/* Tab Logic */
#prioritas-table.tab-biaya .tab-content-paket { display: none !important; }
#prioritas-table.tab-paket .tab-content-biaya { display: none !important; }
.tab-hidden { display: none !important; }

/* ── PRINT COMPATIBILITY ────────────────────────────────── */
@media print {
    @page { size: landscape; margin: 10mm; }
    body * { visibility: hidden !important; }
    .table-card, .table-card * { visibility: visible !important; }
    .table-card { position: absolute; left: 0; top: 0; width: 100vw; border: none; box-shadow: none; margin: 0; padding: 0; }
    .table-toolbar { display: none !important; }
    .table-scroller { max-height: none !important; overflow: visible !important; border: none; }
    
    #print-header { display: block !important; margin-bottom: 20px !important; }

    #prioritas-table { width: 100%; border-collapse: collapse; font-size: 7.5pt !important; }
    #prioritas-table th, #prioritas-table td { border: 1px solid #cbd5e1 !important; color: #000 !important; padding: 4px !important; }
    #prioritas-table th { background: #f8fafc !important; }
    #prioritas-table th.sticky-col, #prioritas-table td.sticky-col { position: static !important; }
    .badge-pct { border: 1px solid #000 !important; padding: 1px 3px !important; font-size: 6.5pt !important; }
}

/* RESPONSIVE */
@media (max-width: 1280px) {
    .kpi-row { grid-template-columns: repeat(3, 1fr); }
    .charts-row { grid-template-columns: 1fr; }
}
@media (max-width: 900px) {
    .kpi-row { grid-template-columns: repeat(2, 1fr); }
    .page-body { padding: 0 1rem 2rem; }
}
</style>
@endpush

{{-- ═══════════════════════════════════════════════════════
     PAGE HEADER
     ═══════════════════════════════════════════════════════ --}}
<div class="page-header">
    <div class="header-inner">
        <div class="header-brand">
            <div class="brand-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h1>Monitoring Investasi Prioritas</h1>
                <p>PTPN IV Regional I — Progress Realisasi Nilai &amp; Paket Investasi Real-Time</p>
                @if($date)
                    <span class="header-date-badge">📅 Per: {{ $date }}</span>
                @endif
            </div>
        </div>

        <div class="header-actions">
            <a href="{{ route('dashboard') }}" class="btn-header btn-header-ghost">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Dashboard Utama
            </a>
            <a href="{{ route('prioritas.index', ['refresh' => 1]) }}" class="btn-header btn-header-solid">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh Data
            </a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     PAGE BODY
     ═══════════════════════════════════════════════════════ --}}
<div class="page-body">

    @if(isset($error))
    <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1rem;color:#991b1b;font-size:0.8rem;font-weight:600;">
        ⚠️ {{ $error }}
    </div>
    @endif

    {{-- ── KPI CARDS ─────────────────────────────────────────── --}}
    <div class="kpi-row">
        {{-- RKAP Total --}}
        <div class="kpi-card">
            <div class="kpi-card-accent" style="background: linear-gradient(to right, #f97316, #fb923c);"></div>
            <div class="kpi-glow" style="background: #f97316;"></div>
            <div class="kpi-label">
                <div class="kpi-icon" style="background:#fff7ed;"><svg style="width:13px;height:13px;color:#f97316" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                RKAP 2026
            </div>
            <div class="kpi-value">{{ $fmtRp($totalRkap) }}</div>
            <div class="kpi-sub">
                <span class="kpi-pct-label" style="color:#f97316">Anggaran setahun</span>
            </div>
        </div>

        {{-- Dokumen di Unit --}}
        <div class="kpi-card">
            <div class="kpi-card-accent" style="background: linear-gradient(to right, #3b82f6, #60a5fa);"></div>
            <div class="kpi-glow" style="background: #3b82f6;"></div>
            <div class="kpi-label">
                <div class="kpi-icon" style="background:#eff6ff;"><svg style="width:13px;height:13px;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                Dokumen di Unit
            </div>
            <div class="kpi-value">{{ $fmtRp($totalDoku) }}</div>
            <div class="kpi-sub">
                <div class="kpi-pct-bar">
                    <div class="kpi-pct-fill" style="background:#3b82f6; width:{{ min(100, $totalRkap > 0 ? round(($totalDoku/$totalRkap)*100) : 0) }}%"></div>
                </div>
                <span class="kpi-pct-label">{{ $totalRkap > 0 ? round(($totalDoku/$totalRkap)*100, 1) : 0 }}% dari RKAP</span>
            </div>
        </div>

        {{-- Sudah Diajukan --}}
        <div class="kpi-card">
            <div class="kpi-card-accent" style="background: linear-gradient(to right, #10b981, #34d399);"></div>
            <div class="kpi-glow" style="background: #10b981;"></div>
            <div class="kpi-label">
                <div class="kpi-icon" style="background:#ecfdf5;"><svg style="width:13px;height:13px;color:#10b981" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                Sudah Diajukan
            </div>
            <div class="kpi-value">{{ $fmtRp($totalDiaj) }}</div>
            <div class="kpi-sub">
                <div class="kpi-pct-bar">
                    <div class="kpi-pct-fill" style="background:#10b981; width:{{ min(100, $pctDiaj) }}%"></div>
                </div>
                <span class="kpi-pct-label">{{ $pctDiaj }}% dari RKAP</span>
            </div>
        </div>

        {{-- HPS / Pengadaan --}}
        <div class="kpi-card">
            <div class="kpi-card-accent" style="background: linear-gradient(to right, #8b5cf6, #a78bfa);"></div>
            <div class="kpi-glow" style="background: #8b5cf6;"></div>
            <div class="kpi-label">
                <div class="kpi-icon" style="background:#f5f3ff;"><svg style="width:13px;height:13px;color:#8b5cf6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg></div>
                HPS / Pengadaan
            </div>
            <div class="kpi-value">{{ $fmtRp($totalHps) }}</div>
            <div class="kpi-sub">
                <div class="kpi-pct-bar">
                    <div class="kpi-pct-fill" style="background:#8b5cf6; width:{{ min(100, $pctHps) }}%"></div>
                </div>
                <span class="kpi-pct-label">{{ $pctHps }}% dari RKAP</span>
            </div>
        </div>

        {{-- SPPBJ / Kontrak --}}
        <div class="kpi-card">
            <div class="kpi-card-accent" style="background: linear-gradient(to right, #f43f5e, #fb7185);"></div>
            <div class="kpi-glow" style="background: #f43f5e;"></div>
            <div class="kpi-label">
                <div class="kpi-icon" style="background:#fff1f2;"><svg style="width:13px;height:13px;color:#f43f5e" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg></div>
                SPPBJ / Kontrak
            </div>
            <div class="kpi-value">{{ $fmtRp($totalSppbj) }}</div>
            <div class="kpi-sub">
                <div class="kpi-pct-bar">
                    <div class="kpi-pct-fill" style="background:#f43f5e; width:{{ min(100, $pctSppbj) }}%"></div>
                </div>
                <span class="kpi-pct-label">{{ $pctSppbj }}% dari RKAP</span>
            </div>
        </div>
    </div>

    {{-- ── DATA TABLE ────────────────────────────────────────── --}}
    <div class="section-title">Data Detail per Unit Kerja</div>
    <div class="table-card" style="margin-bottom: 1.5rem;">
        
        <!-- PRINT HEADER (Hanya Tampil Saat Print PDF) -->
        <div id="print-header" style="display: none; text-align: center;">
            <h2 style="margin:0; font-size:18pt; font-weight:800; color:#1e293b; text-transform:uppercase; letter-spacing:1px;">Laporan Progress Realisasi Investasi</h2>
            <p id="print-subtitle" style="margin:4px 0 0; font-size:10pt; color:#475569; font-weight:600;">PTPN IV Regional 1 - Tahun 2026</p>
            <div style="margin-top:10px; border-bottom:2px solid #cbd5e1;"></div>
        </div>

        <div class="table-toolbar">
            <div class="toolbar-left">
                <div class="live-badge">
                    <div class="live-dot"></div>
                    Data Real-Time
                </div>
                <div class="tab-switcher">
                    <div class="tab-btn active" onclick="switchTab('biaya')">Monitoring Biaya</div>
                    <div class="tab-btn" onclick="switchTab('paket')">Monitoring Paket</div>
                </div>
            </div>
            <div class="toolbar-right" style="display:flex; align-items:center; gap:0.6rem;">
                <button onclick="togglePctCols()" class="btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Toggle %
                </button>
                <div style="position:relative;" id="export-dropdown-wrapper">
                    <button onclick="toggleExportMenu()" class="btn-sm" style="background:var(--blue-600); color:#fff; border-color:var(--blue-700);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export PDF
                    </button>
                    <div id="exportMenu" class="tab-hidden" style="position:absolute; right:0; top:110%; background:#fff; border:1px solid var(--border); box-shadow:var(--shadow-md); border-radius:8px; z-index:99; display:flex; flex-direction:column; min-width:140px; overflow:hidden;">
                        <a onclick="exportPDF('biaya')" style="padding:0.6rem 1rem; font-size:0.65rem; font-weight:700; cursor:pointer; border-bottom:1px solid #f1f5f9; color:var(--text-body);">Tabel Biaya</a>
                        <a onclick="exportPDF('paket')" style="padding:0.6rem 1rem; font-size:0.65rem; font-weight:700; cursor:pointer; border-bottom:1px solid #f1f5f9; color:var(--text-body);">Tabel Paket</a>
                        <a onclick="exportPDF('semua')" style="padding:0.6rem 1rem; font-size:0.65rem; font-weight:700; cursor:pointer; color:var(--text-body);">Tabel Keduanya</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-scroller">
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
                            <th class="w-biaya tab-content-biaya">I</th><th class="w-biaya tab-content-biaya">II</th><th class="w-biaya tab-content-biaya">III</th><th class="w-biaya tab-content-biaya">IV</th>
                            <th class="w-paket tab-content-paket">I</th><th class="w-paket tab-content-paket">II</th><th class="w-paket tab-content-paket">III</th><th class="w-paket tab-content-paket">IV</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $row)
                        @php
                            $isSub    = $row['_is_subtotal'];
                            $rowClass = $isSub ? 'row-highlight' : '';

                            $fmtBiaya = fn($v) => ($v === null || $v == 0)
                                ? '<span class="val-dim">—</span>'
                                : number_format($v, 0, ',', '.');

                            $fmtPaket = fn($v) => ($v === null || $v == 0)
                                ? '<span class="val-dim">—</span>'
                                : $v;

                            $getBadge = function($v) use ($isSub) {
                                if ($v === null || $v == 0) return '<span class="val-dim">—</span>';
                                if ($isSub) return '<strong>'.number_format($v, 1, ',', '.').'%</strong>';
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

    {{-- ── CHARTS ROW ────────────────────────────────────────── --}}
    <div class="section-title">Visualisasi Progress</div>
    <div class="charts-row">

        <div class="charts-left-col">
            {{-- Donut Chart: Komposisi Biaya --}}
            <div class="chart-card">
                <div class="chart-card-title">Progress Realisasi Biaya</div>
                <div class="chart-card-sub">Distribusi tahap realisasi biaya keseluruhan</div>
                <div class="donut-wrap">
                    <canvas id="donutChartBiaya" width="150" height="150" style="flex-shrink:0;"></canvas>
                    <div class="donut-legend">
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#10b981;"></div>
                            <div class="legend-text">Sudah Diajukan</div>
                            <div class="legend-val" style="color:#10b981;">{{ $pctDiaj }}%</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#8b5cf6;"></div>
                            <div class="legend-text">HPS/Pengadaan</div>
                            <div class="legend-val" style="color:#8b5cf6;">{{ $pctHps }}%</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#f43f5e;"></div>
                            <div class="legend-text">SPPBJ/Kontrak</div>
                            <div class="legend-val" style="color:#f43f5e;">{{ $pctSppbj }}%</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#e2e8f0;"></div>
                            <div class="legend-text">Belum Diajukan</div>
                            <div class="legend-val" style="color:#94a3b8;">{{ max(0, round(100 - $pctDiaj, 1)) }}%</div>
                        </div>

                        <div style="margin-top:.6rem;padding-top:.6rem;border-top:1px dashed #cbd5e1;">
                            <div style="font-size:.6rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">Total Biaya (RKAP)</div>
                            <div style="font-size:.8rem;font-weight:800;color:var(--text-head);">{{ $fmtRp($totalRkap) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Donut Chart: Komposisi Paket --}}
            <div class="chart-card">
                <div class="chart-card-title">Progress Realisasi Paket</div>
                <div class="chart-card-sub">Distribusi pengadaan paket keseluruhan</div>
                <div class="donut-wrap">
                    <canvas id="donutChartPaket" width="150" height="150" style="flex-shrink:0;"></canvas>
                    <div class="donut-legend">
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#10b981;"></div>
                            <div class="legend-text">Sudah Diajukan</div>
                            <div class="legend-val" style="color:#10b981;">{{ $pctDiajPaket }}%</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#8b5cf6;"></div>
                            <div class="legend-text">HPS/Pengadaan</div>
                            <div class="legend-val" style="color:#8b5cf6;">{{ $pctHpsPaket }}%</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#f43f5e;"></div>
                            <div class="legend-text">SPPBJ/Kontrak</div>
                            <div class="legend-val" style="color:#f43f5e;">{{ $pctSppbjPaket }}%</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#e2e8f0;"></div>
                            <div class="legend-text">Belum Diajukan</div>
                            <div class="legend-val" style="color:#94a3b8;">{{ max(0, round(100 - $pctDiajPaket, 1)) }}%</div>
                        </div>

                        <div style="margin-top:.6rem;padding-top:.6rem;border-top:1px dashed #cbd5e1;">
                            <div style="font-size:.6rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">Total Pengadaan</div>
                            <div style="font-size:.8rem;font-weight:800;color:var(--text-head);">{{ $totalSppbjPaket }} / {{ $totalRkapPaket }} <span style="font-size:.65rem;font-weight:600;color:var(--text-muted);">SPPBJ</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Horizontal Bar Chart: Progress per Unit --}}
        <div class="chart-card">
            <div class="chart-card-title">Progress % Biaya per Unit Kerja</div>
            <div class="chart-card-sub">Perbandingan % Diajukan, HPS, dan SPPBJ terhadap RKAP per unit</div>
            <div style="flex:1;min-height:0;overflow-y:auto;overflow-x:hidden;">
                <canvas id="barChart"></canvas>
            </div>
        </div>

    </div>

</div>{{-- /page-body --}}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
// ── Tab Switcher ──────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.innerText.toLowerCase().includes(tab)) btn.classList.add('active');
    });
    const table = document.getElementById('prioritas-table');
    table.classList.remove('tab-biaya', 'tab-paket');
    table.classList.add('tab-' + tab);
}

function togglePctCols() {
    document.querySelectorAll('.pct-col').forEach(c => c.classList.toggle('tab-hidden'));
}

// ── Export Dropdown Logic ─────────────────────────────────
function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    if (menu.classList.contains('tab-hidden')) {
        menu.classList.remove('tab-hidden');
    } else {
        menu.classList.add('tab-hidden');
    }
}
document.addEventListener('click', function(e) {
    if (!document.getElementById('export-dropdown-wrapper').contains(e.target)) {
        document.getElementById('exportMenu').classList.add('tab-hidden');
    }
});

// ── Export PDF Logic ──────────────────────────────────────
function exportPDF(mode) {
    document.getElementById('exportMenu').classList.add('tab-hidden');
    
    // 1. Clone seluruh DOM table-card untuk menghindari konflik layout (sidebar/viewport overflow)
    const originalCard = document.querySelector('.table-card');
    const cloneCard = originalCard.cloneNode(true);
    
    // 2. Buat container rahasia di pojok (0,0) agar tidak ada offset putih 
    const tempContainer = document.createElement('div');
    tempContainer.style.position = 'absolute';
    tempContainer.style.top = '0';
    tempContainer.style.left = '0';
    tempContainer.style.width = 'max-content'; // Paksa melebar sempurna tanpa terpotong
    tempContainer.style.background = '#fff';
    tempContainer.style.zIndex = '-9999';
    
    tempContainer.appendChild(cloneCard);
    document.body.appendChild(tempContainer);
    
    // 3. Seting elemen-elemen di dalam clone
    const cloneHeader = cloneCard.querySelector('#print-header');
    const cloneToolbar = cloneCard.querySelector('.table-toolbar');
    const cloneScroller = cloneCard.querySelector('.table-scroller');
    const cloneTable = cloneCard.querySelector('#prioritas-table');
    const cloneSubtitle = cloneCard.querySelector('#print-subtitle');
    
    cloneHeader.style.display = 'block';
    cloneToolbar.style.display = 'none';
    cloneScroller.style.maxHeight = 'none';
    cloneScroller.style.overflow = 'visible';
    
    // 4. Logika filter kolom
    cloneTable.className = '';
    if (mode === 'biaya') {
        cloneTable.classList.add('tab-biaya');
        cloneSubtitle.innerText = 'Distribusi Berdasarkan Anggaran Biaya (Rp) - PTPN IV Regional 1 Tahun 2026';
    } else if (mode === 'paket') {
        cloneTable.classList.add('tab-paket');
        cloneSubtitle.innerText = 'Distribusi Berdasarkan Jumlah Pengadaan Paket - PTPN IV Regional 1 Tahun 2026';
    } else {
        cloneSubtitle.innerText = 'Distribusi Komprehensif (Biaya & Paket) - PTPN IV Regional 1 Tahun 2026';
    }

    // 5. Eksekusi html2pdf
    const opt = {
        margin:       0.3,
        filename:     `Laporan_Investasi_${mode}_2026.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true, scrollX: 0, scrollY: 0 },
        jsPDF:        { unit: 'in', format: mode === 'semua' ? 'a3' : 'a4', orientation: 'landscape' }
    };

    html2pdf().set(opt).from(tempContainer).save().then(() => {
        // Hapus elemen clone bersih setelah PDF tercipta
        document.body.removeChild(tempContainer);
    });
}

// ── Chart.js Global Defaults ─────────────────────────────
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#64748b';

// ── Donut Charts ───────────────────────────────────────────
(function() {
    // Biaya Chart
    const pctDiajBiaya  = {{ $pctDiaj }};
    const pctHpsBiaya   = {{ $pctHps }};
    const pctSppbjBiaya = {{ $pctSppbj }};
    const pctBelumBiaya = Math.max(0, 100 - pctDiajBiaya);

    const ctxBiaya = document.getElementById('donutChartBiaya').getContext('2d');
    new Chart(ctxBiaya, {
        type: 'doughnut',
        data: {
            labels: ['Sudah Diajukan', 'HPS/Pengadaan', 'SPPBJ/Kontrak', 'Belum Diajukan'],
            datasets: [{
                data: [pctDiajBiaya, pctHpsBiaya, pctSppbjBiaya, pctBelumBiaya],
                backgroundColor: ['#10b981', '#8b5cf6', '#f43f5e', '#e2e8f0'],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed.toFixed(1)}%` }
                }
            },
            animation: { animateRotate: true, duration: 1200, easing: 'easeInOutQuart' }
        },
        plugins: [{
            id: 'centerTextBiaya',
            afterDraw(chart) {
                const { width, height, ctx } = chart;
                ctx.save();
                ctx.font = 'bold 22px JetBrains Mono, monospace';
                ctx.fillStyle = '#1e293b';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(`${pctSppbjBiaya}%`, width / 2, height / 2 - 8);
                ctx.font = '600 10px Inter, sans-serif';
                ctx.fillStyle = '#94a3b8';
                ctx.fillText('SPPBJ', width / 2, height / 2 + 12);
                ctx.restore();
            }
        }]
    });

    // Paket Chart
    const pctDiajPaket  = {{ $pctDiajPaket }};
    const pctHpsPaket   = {{ $pctHpsPaket }};
    const pctSppbjPaket = {{ $pctSppbjPaket }};
    const pctBelumPaket = Math.max(0, 100 - pctDiajPaket);

    const ctxPaket = document.getElementById('donutChartPaket').getContext('2d');
    new Chart(ctxPaket, {
        type: 'doughnut',
        data: {
            labels: ['Sudah Diajukan', 'HPS/Pengadaan', 'SPPBJ/Kontrak', 'Belum Diajukan'],
            datasets: [{
                data: [pctDiajPaket, pctHpsPaket, pctSppbjPaket, pctBelumPaket],
                backgroundColor: ['#10b981', '#8b5cf6', '#f43f5e', '#e2e8f0'],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed.toFixed(1)}%` }
                }
            },
            animation: { animateRotate: true, duration: 1200, easing: 'easeInOutQuart' }
        },
        plugins: [{
            id: 'centerTextPaket',
            afterDraw(chart) {
                const { width, height, ctx } = chart;
                ctx.save();
                ctx.font = 'bold 22px JetBrains Mono, monospace';
                ctx.fillStyle = '#1e293b';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(`${pctSppbjPaket}%`, width / 2, height / 2 - 8);
                ctx.font = '600 10px Inter, sans-serif';
                ctx.fillStyle = '#94a3b8';
                ctx.fillText('SPPBJ', width / 2, height / 2 + 12);
                ctx.restore();
            }
        }]
    });
})();

// ── Horizontal Bar Chart ──────────────────────────────────
(function() {
    const units  = @json($chartUnits);
    const diaj   = @json($chartDiaj);
    const hps    = @json($chartHps);
    const sppbj  = @json($chartSppbj);

    if (!units.length) return;

    // Set canvas height dynamically: 32px per row + header space so elements are visible
    const canvas = document.getElementById('barChart');
    canvas.style.height = Math.max(280, units.length * 36 + 60) + 'px';
    canvas.style.width  = '100%';

    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: units,
            datasets: [
                {
                    label: 'Diajukan (%)',
                    data: diaj,
                    backgroundColor: 'rgba(16,185,129,0.72)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 3,
                    barThickness: 8,
                },
                {
                    label: 'HPS (%)',
                    data: hps,
                    backgroundColor: 'rgba(139,92,246,0.72)',
                    borderColor: '#8b5cf6',
                    borderWidth: 1,
                    borderRadius: 3,
                    barThickness: 8,
                },
                {
                    label: 'SPPBJ (%)',
                    data: sppbj,
                    backgroundColor: 'rgba(244,63,94,0.72)',
                    borderColor: '#f43f5e',
                    borderWidth: 1,
                    borderRadius: 3,
                    barThickness: 8,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: {
                    min: 0, max: 100,
                    ticks: { callback: v => v + '%', font: { size: 9 } },
                    grid: { color: 'rgba(226,232,240,.6)' }
                },
                y: {
                    ticks: { font: { size: 9.5 }, autoSkip: false, padding: 4 },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { boxWidth: 9, padding: 14, font: { size: 10.5, weight: '600' } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${(ctx.parsed.x ?? 0).toFixed(1)}%`
                    }
                }
            },
            animation: { duration: 900, easing: 'easeOutQuart' }
        }
    });
})();

// Init
document.addEventListener('DOMContentLoaded', () => switchTab('biaya'));
</script>
@endpush

@endsection
