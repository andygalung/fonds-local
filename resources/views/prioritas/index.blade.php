@extends('layouts.app')

@section('title', 'Dashboard Prioritas PKS')
@section('page-title', 'Dashboard Prioritas Nilai & Paket Investasi')
@section('page-subtitle', 'OFF FARM REKG 045 PKS dan PPIS PT Perkebunan Nusantara IV Regional I')

@section('content')

{{-- ─── Fullscreen Nav Menu & Controls ────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-3">
    {{-- On-screen Navigation Menu --}}
    <div class="flex items-center gap-1 bg-white/80 backdrop-blur-md border border-slate-200 p-1 rounded-xl shadow-sm">
        <a href="{{ route('dashboard') }}" class="nav-btn px-3 py-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            DASHBOARD
        </a>
        <a href="{{ route('investasi.index') }}" class="nav-btn px-3 py-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            INVESTASI
        </a>
        <a href="{{ route('prioritas.index') }}" class="nav-btn px-3 py-1.5 text-xs font-bold text-primary-700 bg-primary-50 rounded-lg flex items-center gap-1.5 transition shadow-sm border border-primary-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
            PRIORITAS PKS
        </a>
    </div>

    <div class="flex items-center gap-3">
        <div class="text-right">
            <h2 class="text-[13px] font-bold text-slate-800 uppercase tracking-wide leading-tight">{{ $data['title'] ?? 'DASHBOARD PRIORITAS' }}</h2>
            <p class="text-[10px] text-slate-500 font-medium">Data: {{ $data['date'] ?? '-' }} • {{ count($data['records']) }} unit kerja</p>
        </div>

        {{-- Controls --}}
        <div class="flex gap-2">
            <button id="btn-toggle-pct" onclick="togglePctCols()"
                    class="flex items-center gap-1.5 text-xs font-semibold bg-white text-slate-600 px-3 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span id="btn-toggle-pct-label">Sembunyikan %</span>
            </button>
            <a href="{{ route('prioritas.index', ['refresh' => 1]) }}"
               id="btn-refresh"
               class="flex items-center gap-1.5 text-xs font-semibold bg-primary-800 text-white px-3 py-2 rounded-lg border border-primary-900 hover:bg-primary-700 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </a>
        </div>
    </div>
</div>

{{-- ─── Error Alert ────────────────────────────────────────────────────────── --}}
@if($error)
<div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="text-sm font-semibold text-red-700">Gagal memuat data</p>
        <p class="text-xs text-red-600 mt-0.5">{{ $error }}</p>
    </div>
</div>
@endif

{{-- ─── Legend ─────────────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center gap-3 mb-3 text-xs">
    <span class="flex items-center gap-1.5"><span class="w-4 h-3 rounded inline-block bg-amber-100 border border-amber-300"></span> RKAP 2026</span>
    <span class="flex items-center gap-1.5"><span class="w-4 h-3 rounded inline-block bg-sky-100 border border-sky-300"></span> Dokumen di Unit</span>
    <span class="flex items-center gap-1.5"><span class="w-4 h-3 rounded inline-block bg-teal-100 border border-teal-300"></span> Sudah Diajukan (Tekpol)</span>
    <span class="flex items-center gap-1.5"><span class="w-4 h-3 rounded inline-block bg-violet-100 border border-violet-300"></span> HPS/Pengadaan</span>
    <span class="flex items-center gap-1.5"><span class="w-4 h-3 rounded inline-block bg-rose-100 border border-rose-300"></span> SPPBJ/Kontrak</span>
    <span class="flex items-center gap-1.5"><span class="w-4 h-3 rounded inline-block bg-slate-700 border border-slate-800"></span> % Progress RKAP</span>
    <span class="ml-auto text-slate-400 font-mono">Biaya dalam satuan Rp (ribu)</span>
</div>

{{-- ─── Main Table ─────────────────────────────────────────────────────────── --}}
@if(empty($data['records']))
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-16 text-center">
    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <p class="text-slate-500 font-medium">Tidak ada data</p>
    <p class="text-slate-400 text-xs mt-1">Coba klik Refresh Data untuk memuat ulang dari Google Sheets</p>
</div>
@else

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex-1 flex flex-col" style="min-height: 0;">
    <div class="overflow-x-auto flex-1 h-full" style="overflow-y: auto;">
        <table id="prioritas-table" class="text-xs border-collapse" style="min-width: 3600px; table-layout: fixed;">
            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- HEADER ROW 1: Master Group Labels                             --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <thead class="sticky top-0 z-30">
                <tr class="text-white text-center font-bold text-[10px]">
                    {{-- Unit Kerja --}}
                    <th rowspan="3"
                        class="px-2 py-2 text-left align-middle bg-primary-900 border border-white/20 min-w-[160px] w-[160px] sticky left-0 z-40">
                        Unit Kerja<br><span class="font-normal">(Pabrik / Kebun)</span>
                    </th>

                    {{-- RKAP 2026 Rekg 045 Setahun (Prioritas) --}}
                    <th colspan="8"
                        class="px-2 py-1 bg-amber-600 border border-white/20 text-center">
                        RKAP 2026 Rekg 045 Setahun (Prioritas)
                    </th>

                    {{-- Dokumen di Unit --}}
                    <th colspan="8"
                        class="px-2 py-1 bg-sky-700 border border-white/20 text-center">
                        Dokumen di Unit
                    </th>

                    {{-- Sudah diajukan Unit (Bag Tekpol) --}}
                    <th colspan="8"
                        class="px-2 py-1 bg-teal-700 border border-white/20 text-center">
                        Sudah diajukan Unit (Bag Tekpol)
                    </th>

                    {{-- HPS/Pengadaan --}}
                    <th colspan="8"
                        class="px-2 py-1 bg-violet-700 border border-white/20 text-center">
                        HPS/Pengadaan
                    </th>

                    {{-- SPPBJ/Kontrak --}}
                    <th colspan="8"
                        class="px-2 py-1 bg-rose-700 border border-white/20 text-center">
                        SPPBJ/Kontrak
                    </th>

                    {{-- % Progress Thd RKAP setahun --}}
                    <th colspan="8" class="pct-col px-2 py-1 bg-slate-700 border border-white/20 text-center w-[480px]">
                        % Progress Thd RKAP setahun (%)
                    </th>
                </tr>

                {{-- ── HEADER ROW 2: Sub-group labels ─────────────────────── --}}
                {{-- Per grup: Σ Biaya(4 cols) | Σ Paket(4 cols) = 8 cols/grup --}}
                <tr class="text-white text-center text-[10px]">
                    {{-- RKAP: 4 biaya + 4 paket --}}
                    <th colspan="4" class="px-1 py-1 bg-amber-600/90 border border-white/20">Σ Biaya (Rp)</th>
                    <th colspan="4" class="px-1 py-1 bg-amber-700 border border-white/20">Σ Paket</th>
                    {{-- Dokumen --}}
                    <th colspan="4" class="px-1 py-1 bg-sky-700/90 border border-white/20">Σ Biaya (Rp)</th>
                    <th colspan="4" class="px-1 py-1 bg-sky-800 border border-white/20">Σ Paket</th>
                    {{-- Tekpol --}}
                    <th colspan="4" class="px-1 py-1 bg-teal-700/90 border border-white/20">Σ Biaya (Rp)</th>
                    <th colspan="4" class="px-1 py-1 bg-teal-800 border border-white/20">Σ Paket</th>
                    {{-- HPS --}}
                    <th colspan="4" class="px-1 py-1 bg-violet-700/90 border border-white/20">Σ Biaya (Rp)</th>
                    <th colspan="4" class="px-1 py-1 bg-violet-800 border border-white/20">Σ Paket</th>
                    {{-- SPPBJ --}}
                    <th colspan="4" class="px-1 py-1 bg-rose-700/90 border border-white/20">Σ Biaya (Rp)</th>
                    <th colspan="4" class="px-1 py-1 bg-rose-800 border border-white/20">Σ Paket</th>
                    {{-- % Progress --}}
                    <th colspan="2" class="pct-col px-1 py-1 bg-slate-700/90 border border-white/20">Diajukan Unit</th>
                    <th colspan="2" class="pct-col px-1 py-1 bg-slate-600 border border-white/20">Belum Diajukan</th>
                    <th colspan="2" class="pct-col px-1 py-1 bg-slate-700/90 border border-white/20">HPS/Pengadaan</th>
                    <th colspan="2" class="pct-col px-1 py-1 bg-slate-600 border border-white/20">SPPBJ/Kontrak</th>
                </tr>

                {{-- ── HEADER ROW 3: Column numbers ────────────────────────── --}}
                {{-- RKAP: Biaya 1,2,3,4 | Paket 1,2,3,4 (8 cols, no Σ total) --}}
                <tr class="text-white text-center text-[10px] font-bold">
                    {{-- RKAP Biaya 1-4 --}}
                    <th class="px-1 py-1.5 bg-amber-600 border border-white/20 min-w-[90px]">1</th>
                    <th class="px-1 py-1.5 bg-amber-600 border border-white/20 min-w-[90px]">2</th>
                    <th class="px-1 py-1.5 bg-amber-600 border border-white/20 min-w-[90px]">3</th>
                    <th class="px-1 py-1.5 bg-amber-600 border border-white/20 min-w-[70px]">4</th>
                    {{-- RKAP Paket 1-4 --}}
                    <th class="px-1 py-1.5 bg-amber-700 border border-white/20 min-w-[45px]">1</th>
                    <th class="px-1 py-1.5 bg-amber-700 border border-white/20 min-w-[45px]">2</th>
                    <th class="px-1 py-1.5 bg-amber-700 border border-white/20 min-w-[45px]">3</th>
                    <th class="px-1 py-1.5 bg-amber-700 border border-white/20 min-w-[45px]">4</th>
                    {{-- Dokumen Biaya 1-4 --}}
                    <th class="px-1 py-1.5 bg-sky-700 border border-white/20 min-w-[90px]">1</th>
                    <th class="px-1 py-1.5 bg-sky-700 border border-white/20 min-w-[90px]">2</th>
                    <th class="px-1 py-1.5 bg-sky-700 border border-white/20 min-w-[90px]">3</th>
                    <th class="px-1 py-1.5 bg-sky-700 border border-white/20 min-w-[70px]">4</th>
                    {{-- Dokumen Paket 1-4 --}}
                    <th class="px-1 py-1.5 bg-sky-800 border border-white/20 min-w-[45px]">1</th>
                    <th class="px-1 py-1.5 bg-sky-800 border border-white/20 min-w-[45px]">2</th>
                    <th class="px-1 py-1.5 bg-sky-800 border border-white/20 min-w-[45px]">3</th>
                    <th class="px-1 py-1.5 bg-sky-800 border border-white/20 min-w-[45px]">4</th>
                    {{-- Tekpol Biaya 1-4 --}}
                    <th class="px-1 py-1.5 bg-teal-700 border border-white/20 min-w-[90px]">1</th>
                    <th class="px-1 py-1.5 bg-teal-700 border border-white/20 min-w-[90px]">2</th>
                    <th class="px-1 py-1.5 bg-teal-700 border border-white/20 min-w-[90px]">3</th>
                    <th class="px-1 py-1.5 bg-teal-700 border border-white/20 min-w-[70px]">4</th>
                    {{-- Tekpol Paket 1-4 --}}
                    <th class="px-1 py-1.5 bg-teal-800 border border-white/20 min-w-[45px]">1</th>
                    <th class="px-1 py-1.5 bg-teal-800 border border-white/20 min-w-[45px]">2</th>
                    <th class="px-1 py-1.5 bg-teal-800 border border-white/20 min-w-[45px]">3</th>
                    <th class="px-1 py-1.5 bg-teal-800 border border-white/20 min-w-[45px]">4</th>
                    {{-- HPS Biaya 1-4 --}}
                    <th class="px-1 py-1.5 bg-violet-700 border border-white/20 min-w-[90px]">1</th>
                    <th class="px-1 py-1.5 bg-violet-700 border border-white/20 min-w-[90px]">2</th>
                    <th class="px-1 py-1.5 bg-violet-700 border border-white/20 min-w-[90px]">3</th>
                    <th class="px-1 py-1.5 bg-violet-700 border border-white/20 min-w-[70px]">4</th>
                    {{-- HPS Paket 1-4 --}}
                    <th class="px-1 py-1.5 bg-violet-800 border border-white/20 min-w-[45px]">1</th>
                    <th class="px-1 py-1.5 bg-violet-800 border border-white/20 min-w-[45px]">2</th>
                    <th class="px-1 py-1.5 bg-violet-800 border border-white/20 min-w-[45px]">3</th>
                    <th class="px-1 py-1.5 bg-violet-800 border border-white/20 min-w-[45px]">4</th>
                    {{-- SPPBJ Biaya 1-4 --}}
                    <th class="px-1 py-1.5 bg-rose-700 border border-white/20 min-w-[90px]">1</th>
                    <th class="px-1 py-1.5 bg-rose-700 border border-white/20 min-w-[90px]">2</th>
                    <th class="px-1 py-1.5 bg-rose-700 border border-white/20 min-w-[90px]">3</th>
                    <th class="px-1 py-1.5 bg-rose-700 border border-white/20 min-w-[70px]">4</th>
                    {{-- SPPBJ Paket 1-4 --}}
                    <th class="px-1 py-1.5 bg-rose-800 border border-white/20 min-w-[45px]">1</th>
                    <th class="px-1 py-1.5 bg-rose-800 border border-white/20 min-w-[45px]">2</th>
                    <th class="px-1 py-1.5 bg-rose-800 border border-white/20 min-w-[45px]">3</th>
                    <th class="px-1 py-1.5 bg-rose-800 border border-white/20 min-w-[45px]">4</th>
                    {{-- % Progress --}}
                    <th class="pct-col px-1 py-1.5 bg-slate-700 border border-white/20 min-w-[55px]">Biaya</th>
                    <th class="pct-col px-1 py-1.5 bg-slate-700 border border-white/20 min-w-[55px]">Paket</th>
                    <th class="pct-col px-1 py-1.5 bg-slate-600 border border-white/20 min-w-[55px]">Biaya</th>
                    <th class="pct-col px-1 py-1.5 bg-slate-600 border border-white/20 min-w-[55px]">Paket</th>
                    <th class="pct-col px-1 py-1.5 bg-slate-700 border border-white/20 min-w-[55px]">Biaya</th>
                    <th class="pct-col px-1 py-1.5 bg-slate-700 border border-white/20 min-w-[55px]">Paket</th>
                    <th class="pct-col px-1 py-1.5 bg-slate-600 border border-white/20 min-w-[55px]">Biaya</th>
                    <th class="pct-col px-1 py-1.5 bg-slate-600 border border-white/20 min-w-[55px]">Paket</th>
                </tr>
            </thead>

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- BODY                                                          --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <tbody>
            @foreach($data['records'] as $row)
            @php
                $isSub  = $row['_is_subtotal'];
                $rowBg  = $isSub ? 'bg-primary-900 text-white font-bold' : 'bg-white hover:bg-slate-50';
                $numCls = $isSub ? 'text-white' : 'text-slate-700';
                $txtCls = $isSub ? 'text-white' : 'text-slate-800';
                $bdr    = 'border border-slate-200';

                // Helper: format bilangan besar ke ribuan
                $fmtBiaya = function($v) {
                    if ($v === null) return '<span class="text-slate-300">—</span>';
                    return '<span>'.number_format($v, 0, ',', '.').'</span>';
                };
                $fmtPaket = function($v) {
                    if ($v === null) return '<span class="text-slate-300">—</span>';
                    return '<span>'.number_format($v, 0, ',', '.').'</span>';
                };
                $fmtPct = function($v) use ($isSub) {
                    if ($v === null) return '<span class="text-slate-300">—</span>';
                    $color = $v >= 80 ? 'text-emerald-600' : ($v >= 50 ? 'text-amber-600' : 'text-red-500');
                    $cls = $isSub ? 'text-white font-bold' : $color . ' font-semibold';
                    return '<span class="'.$cls.'">'.number_format($v, 2, ',', '.').'%</span>';
                };
            @endphp
            <tr class="{{ $rowBg }} transition-colors duration-100">
                {{-- Unit Kerja --}}
                <td class="{{ $bdr }} {{ $txtCls }} px-2 py-1.5 sticky left-0 z-10 {{ $isSub ? 'bg-primary-900' : 'bg-white' }} whitespace-nowrap font-{{ $isSub ? 'bold' : 'medium' }}">
                    {{ $row['unit_kerja'] ?: '—' }}
                </td>

                {{-- ── RKAP: Biaya 1–4 | Paket 1–4 ─────────────────────────── --}}
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-amber-50/60 {{ $isSub ? '!bg-amber-800' : '' }}">{!! $fmtBiaya($row['rkap_biaya_1']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-amber-50/60 {{ $isSub ? '!bg-amber-800' : '' }}">{!! $fmtBiaya($row['rkap_biaya_2']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-amber-50/60 {{ $isSub ? '!bg-amber-800' : '' }}">{!! $fmtBiaya($row['rkap_biaya_3']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-amber-50/60 {{ $isSub ? '!bg-amber-800' : '' }}">{!! $fmtBiaya($row['rkap_biaya_4']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-amber-100/60 {{ $isSub ? '!bg-amber-900' : '' }}">{!! $fmtPaket($row['rkap_paket_1']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-amber-100/60 {{ $isSub ? '!bg-amber-900' : '' }}">{!! $fmtPaket($row['rkap_paket_2']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-amber-100/60 {{ $isSub ? '!bg-amber-900' : '' }}">{!! $fmtPaket($row['rkap_paket_3']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-amber-100/60 {{ $isSub ? '!bg-amber-900' : '' }}">{!! $fmtPaket($row['rkap_paket_4']) !!}</td>

                {{-- ── Dokumen: Biaya 1–4 | Paket 1–4 ───────────────────────── --}}
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-sky-50/60 {{ $isSub ? '!bg-sky-800' : '' }}">{!! $fmtBiaya($row['doku_biaya_1']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-sky-50/60 {{ $isSub ? '!bg-sky-800' : '' }}">{!! $fmtBiaya($row['doku_biaya_2']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-sky-50/60 {{ $isSub ? '!bg-sky-800' : '' }}">{!! $fmtBiaya($row['doku_biaya_3']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-sky-50/60 {{ $isSub ? '!bg-sky-800' : '' }}">{!! $fmtBiaya($row['doku_biaya_4']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-sky-100/60 {{ $isSub ? '!bg-sky-900' : '' }}">{!! $fmtPaket($row['doku_paket_1']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-sky-100/60 {{ $isSub ? '!bg-sky-900' : '' }}">{!! $fmtPaket($row['doku_paket_2']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-sky-100/60 {{ $isSub ? '!bg-sky-900' : '' }}">{!! $fmtPaket($row['doku_paket_3']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-sky-100/60 {{ $isSub ? '!bg-sky-900' : '' }}">{!! $fmtPaket($row['doku_paket_4']) !!}</td>

                {{-- ── Tekpol: Biaya 1–4 | Paket 1–4 ────────────────────────── --}}
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-teal-50/60 {{ $isSub ? '!bg-teal-800' : '' }}">{!! $fmtBiaya($row['tekpol_biaya_1']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-teal-50/60 {{ $isSub ? '!bg-teal-800' : '' }}">{!! $fmtBiaya($row['tekpol_biaya_2']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-teal-50/60 {{ $isSub ? '!bg-teal-800' : '' }}">{!! $fmtBiaya($row['tekpol_biaya_3']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-teal-50/60 {{ $isSub ? '!bg-teal-800' : '' }}">{!! $fmtBiaya($row['tekpol_biaya_4']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-teal-100/60 {{ $isSub ? '!bg-teal-900' : '' }}">{!! $fmtPaket($row['tekpol_paket_1']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-teal-100/60 {{ $isSub ? '!bg-teal-900' : '' }}">{!! $fmtPaket($row['tekpol_paket_2']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-teal-100/60 {{ $isSub ? '!bg-teal-900' : '' }}">{!! $fmtPaket($row['tekpol_paket_3']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-teal-100/60 {{ $isSub ? '!bg-teal-900' : '' }}">{!! $fmtPaket($row['tekpol_paket_4']) !!}</td>

                {{-- ── HPS: Biaya 1–4 | Paket 1–4 ───────────────────────────── --}}
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-violet-50/60 {{ $isSub ? '!bg-violet-800' : '' }}">{!! $fmtBiaya($row['hps_biaya_1']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-violet-50/60 {{ $isSub ? '!bg-violet-800' : '' }}">{!! $fmtBiaya($row['hps_biaya_2']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-violet-50/60 {{ $isSub ? '!bg-violet-800' : '' }}">{!! $fmtBiaya($row['hps_biaya_3']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-violet-50/60 {{ $isSub ? '!bg-violet-800' : '' }}">{!! $fmtBiaya($row['hps_biaya_4']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-violet-100/60 {{ $isSub ? '!bg-violet-900' : '' }}">{!! $fmtPaket($row['hps_paket_1']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-violet-100/60 {{ $isSub ? '!bg-violet-900' : '' }}">{!! $fmtPaket($row['hps_paket_2']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-violet-100/60 {{ $isSub ? '!bg-violet-900' : '' }}">{!! $fmtPaket($row['hps_paket_3']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-violet-100/60 {{ $isSub ? '!bg-violet-900' : '' }}">{!! $fmtPaket($row['hps_paket_4']) !!}</td>

                {{-- ── SPPBJ: Biaya 1–4 | Paket 1–4 ──────────────────────────── --}}
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-rose-50/60 {{ $isSub ? '!bg-rose-800' : '' }}">{!! $fmtBiaya($row['sppbj_biaya_1']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-rose-50/60 {{ $isSub ? '!bg-rose-800' : '' }}">{!! $fmtBiaya($row['sppbj_biaya_2']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-rose-50/60 {{ $isSub ? '!bg-rose-800' : '' }}">{!! $fmtBiaya($row['sppbj_biaya_3']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-right bg-rose-50/60 {{ $isSub ? '!bg-rose-800' : '' }}">{!! $fmtBiaya($row['sppbj_biaya_4']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-rose-100/60 {{ $isSub ? '!bg-rose-900' : '' }}">{!! $fmtPaket($row['sppbj_paket_1']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-rose-100/60 {{ $isSub ? '!bg-rose-900' : '' }}">{!! $fmtPaket($row['sppbj_paket_2']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-rose-100/60 {{ $isSub ? '!bg-rose-900' : '' }}">{!! $fmtPaket($row['sppbj_paket_3']) !!}</td>
                <td class="{{ $bdr }} {{ $numCls }} px-1.5 py-1 text-center bg-rose-100/60 {{ $isSub ? '!bg-rose-900' : '' }}">{!! $fmtPaket($row['sppbj_paket_4']) !!}</td>

                {{-- ── % Progress Columns ────────────────────────────────── --}}
                <td class="pct-col {{ $bdr }} px-1.5 py-1 text-center bg-slate-50 {{ $isSub ? '!bg-slate-700' : '' }}">{!! $fmtPct($row['pct_diaj_biaya']) !!}</td>
                <td class="pct-col {{ $bdr }} px-1.5 py-1 text-center bg-slate-50 {{ $isSub ? '!bg-slate-700' : '' }}">{!! $fmtPct($row['pct_diaj_paket']) !!}</td>
                <td class="pct-col {{ $bdr }} px-1.5 py-1 text-center bg-slate-100/80 {{ $isSub ? '!bg-slate-600' : '' }}">{!! $fmtPct($row['pct_belum_biaya']) !!}</td>
                <td class="pct-col {{ $bdr }} px-1.5 py-1 text-center bg-slate-100/80 {{ $isSub ? '!bg-slate-600' : '' }}">{!! $fmtPct($row['pct_belum_paket']) !!}</td>
                <td class="pct-col {{ $bdr }} px-1.5 py-1 text-center bg-slate-50 {{ $isSub ? '!bg-slate-700' : '' }}">{!! $fmtPct($row['pct_hps_biaya']) !!}</td>
                <td class="pct-col {{ $bdr }} px-1.5 py-1 text-center bg-slate-50 {{ $isSub ? '!bg-slate-700' : '' }}">{!! $fmtPct($row['pct_hps_paket']) !!}</td>
                <td class="pct-col {{ $bdr }} px-1.5 py-1 text-center bg-slate-100/80 {{ $isSub ? '!bg-slate-600' : '' }}">{!! $fmtPct($row['pct_sppbj_biaya']) !!}</td>
                <td class="pct-col {{ $bdr }} px-1.5 py-1 text-center bg-slate-100/80 {{ $isSub ? '!bg-slate-600' : '' }}">{!! $fmtPct($row['pct_sppbj_paket']) !!}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif

@endsection

@push('styles')
<style>
    /* ── Fullscreen specific overrides ── */
    #sidebar, header, footer { display: none !important; }
    #main-content { margin-left: 0 !important; height: 100vh !important; display: flex !important; flex-direction: column !important; }
    main { padding: 12px 16px !important; flex: 1; display: flex; flex-direction: column; min-height: 0;}
    body { background-color: #f8fafc; }

    /* Make sticky header work correctly */
    #prioritas-table thead th { position: sticky; }
    #prioritas-table thead tr:nth-child(1) th { top: 0; }
    #prioritas-table thead tr:nth-child(2) th { top: 28px; }
    #prioritas-table thead tr:nth-child(3) th { top: 52px; }

    /* Override for the rowspan unit-kerja header */
    #prioritas-table thead tr:nth-child(1) th:first-child {
        top: 0;
        height: 84px; /* 3 rows height */
    }

    /* Frozen first column */
    #prioritas-table tbody td:first-child {
        position: sticky;
        left: 0;
        z-index: 10;
        box-shadow: 2px 0 4px rgba(0,0,0,0.05); /* add small shadow to show depth */
    }
    #prioritas-table thead th:first-child {
        box-shadow: 2px 0 4px rgba(0,0,0,0.05);
    }

    /* Subtotal row styles */
    .subtotal-row td { font-weight: 700; }

    /* Hover feedback */
    #prioritas-table tbody tr:not(.subtotal-row):hover td {
        background-color: rgba(219, 234, 254, 0.4) !important;
    }

    /* Custom scrollbar for table */
    #prioritas-table::-webkit-scrollbar { width: 8px; height: 8px; }
    #prioritas-table::-webkit-scrollbar-track { background: #f1f5f9; }
    #prioritas-table::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 9999px; }
    #prioritas-table::-webkit-scrollbar-thumb:hover { background: #64748b; }

    /* PCT col hidden */
    .pct-hidden .pct-col { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
let pctVisible = true;

function togglePctCols() {
    pctVisible = !pctVisible;
    const table = document.getElementById('prioritas-table');
    if (pctVisible) {
        table.classList.remove('pct-hidden');
        document.getElementById('btn-toggle-pct-label').textContent = 'Sembunyikan %';
    } else {
        table.classList.add('pct-hidden');
        document.getElementById('btn-toggle-pct-label').textContent = 'Tampilkan %';
    }
}
</script>
@endpush
