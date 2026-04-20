@extends('layouts.app')

@section('title', 'Data Investasi')
@section('page-title', 'Data Investasi & Aset')
@section('page-subtitle', 'Sistem Informasi Investasi & Aset Regional — PTPN IV')

@section('content')

{{-- ─── Filter & Search Bar ────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('investasi.index') }}" id="filter-form">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-slate-600 mb-1">🔍 Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama investasi, kode, penyedia..."
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
            </div>
            {{-- Regional --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Regional</label>
                <select name="regional" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400">
                    <option value="">Semua</option>
                    @foreach($regionals as $r)
                        <option value="{{ $r }}" @selected(request('regional') === $r)>{{ $r }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Unit Kerja --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Unit Kerja</label>
                <select name="unit_kerja" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400">
                    <option value="">Semua</option>
                    @foreach($unitKerjas as $u)
                        <option value="{{ $u }}" @selected(request('unit_kerja') === $u)>{{ $u }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Tahun --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Tahun</label>
                <select name="tahun" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400">
                    <option value="">Semua</option>
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" @selected(request('tahun') == $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Status Paket --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Status Paket</label>
                <select name="status" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400">
                    <option value="">Semua</option>
                    @foreach($statusList as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex items-center gap-2 mt-3 flex-wrap">
            <button type="submit" class="flex items-center gap-1.5 bg-primary-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707l-6.414 6.414A1 1 0 0014 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 018 21v-7.586a1 1 0 00-.293-.707L1.293 6.707A1 1 0 011 6V4z"/></svg>
                Filter
            </button>
            <a href="{{ route('investasi.index') }}" class="flex items-center gap-1.5 bg-slate-100 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-200 transition">
                Reset
            </a>
            <span class="ml-auto text-xs text-slate-500">
                Menampilkan <strong>{{ $investasi->firstItem() ?? 0 }}–{{ $investasi->lastItem() ?? 0 }}</strong>
                dari <strong>{{ $investasi->total() }}</strong> data
            </span>
        </div>
    </div>

    {{-- Hidden sort fields --}}
    <input type="hidden" name="sort" value="{{ request('sort') }}">
    <input type="hidden" name="dir" value="{{ request('dir') }}">
</form>

{{-- ─── View Toggle Tabs ───────────────────────────────────────────────────── --}}
<div class="flex items-center gap-2 mb-3">
    <button id="tab-ringkasan" onclick="switchTab('ringkasan')"
            class="tab-btn active-tab px-4 py-2 rounded-lg text-sm font-medium transition">
        📋 Ringkasan
    </button>
    <button id="tab-lengkap" onclick="switchTab('lengkap')"
            class="tab-btn inactive-tab px-4 py-2 rounded-lg text-sm font-medium transition">
        📊 Detail Lengkap (Semua Kolom)
    </button>
    <input type="hidden" id="active-tab-input" name="tab" value="{{ request('tab', 'ringkasan') }}">
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB: RINGKASAN                                                              --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="view-ringkasan" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-primary-900 to-primary-800 text-white">
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider whitespace-nowrap sticky left-0 bg-primary-900 z-10">No</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider whitespace-nowrap">@include('investasi._sort-th', ['label'=>'Kode Sinusa','column'=>'kode_sinusa'])</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Regional</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Unit Kerja</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama Investasi</th>
                    <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider whitespace-nowrap">@include('investasi._sort-th', ['label'=>'Nilai RKAP','column'=>'nilai_rkap'])</th>
                    <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider whitespace-nowrap">@include('investasi._sort-th', ['label'=>'Nilai Kontrak','column'=>'nilai_kontrak'])</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Penyedia</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider whitespace-nowrap">No. Kontrak</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider whitespace-nowrap">@include('investasi._sort-th', ['label'=>'Progress','column'=>'progress_fisik'])</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Tgl Mulai</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Tgl Selesai</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider whitespace-nowrap">@include('investasi._sort-th', ['label'=>'Status','column'=>'status_paket'])</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($investasi as $item)
                <tr class="table-row-hover cursor-pointer" onclick="openDetail({{ $item->id }})">
                    <td class="px-3 py-2.5 text-slate-500 text-xs whitespace-nowrap sticky left-0 bg-white">{{ $investasi->firstItem() + $loop->index }}</td>
                    <td class="px-3 py-2.5 font-mono text-xs text-primary-700 whitespace-nowrap">{{ $item->kode_sinusa ?? '-' }}</td>
                    <td class="px-3 py-2.5 text-xs whitespace-nowrap">{{ $item->regional ?? '-' }}</td>
                    <td class="px-3 py-2.5 text-xs whitespace-nowrap max-w-[130px] truncate" title="{{ $item->unit_kerja }}">{{ $item->unit_kerja ?? '-' }}</td>
                    <td class="px-3 py-2.5 max-w-xs">
                        <p class="text-xs font-medium text-slate-800 line-clamp-2 leading-snug">{{ $item->nama_investasi ?? '-' }}</p>
                        @if($item->no_wbs)<p class="text-xs text-slate-400 mt-0.5 font-mono">WBS: {{ $item->no_wbs }}</p>@endif
                    </td>
                    <td class="px-3 py-2.5 text-xs text-right whitespace-nowrap font-medium">
                        @if($item->nilai_rkap) <span class="text-slate-700">{{ number_format($item->nilai_rkap) }}</span>
                        @else <span class="text-slate-400">—</span> @endif
                    </td>
                    <td class="px-3 py-2.5 text-xs text-right whitespace-nowrap font-medium">
                        @if($item->nilai_kontrak) <span class="text-gold-700">{{ number_format($item->nilai_kontrak) }}</span>
                        @else <span class="text-slate-400">—</span> @endif
                    </td>
                    <td class="px-3 py-2.5 text-xs max-w-[130px] truncate" title="{{ $item->penyedia }}">{{ $item->penyedia ?? '-' }}</td>
                    <td class="px-3 py-2.5 text-xs font-mono whitespace-nowrap text-slate-600">{{ $item->no_kontrak ?? '-' }}</td>
                    <td class="px-3 py-2.5 whitespace-nowrap">
                        @php $p = $item->progress_fisik ?? 0; @endphp
                        <div class="flex items-center gap-1.5">
                            <div class="w-14 bg-slate-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $p >= 80 ? 'bg-emerald-500' : ($p >= 50 ? 'bg-gold-500' : 'bg-red-400') }}"
                                     style="width:{{ min($p,100) }}%"></div>
                            </div>
                            <span class="text-xs font-medium {{ $p >= 80 ? 'text-emerald-600' : ($p >= 50 ? 'text-gold-600' : 'text-red-500') }}">{{ number_format($p,1) }}%</span>
                        </div>
                    </td>
                    <td class="px-3 py-2.5 text-xs text-slate-500 whitespace-nowrap">{{ $item->tgl_mulai_kontrak?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-3 py-2.5 text-xs text-slate-500 whitespace-nowrap">{{ $item->tgl_selesai_kontrak?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-3 py-2.5 whitespace-nowrap">@include('investasi._status-badge', ['status' => $item->status_paket])</td>
                </tr>
                @empty
                @include('investasi._empty')
                @endforelse
            </tbody>
        </table>
    </div>
    @include('investasi._pagination', ['data' => $investasi])
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB: DETAIL LENGKAP (Semua 40 Kolom)                                       --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="view-lengkap" class="hidden bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto" style="max-height: 75vh; overflow-y: auto;">
        <table class="text-xs whitespace-nowrap border-collapse" style="min-width: 3200px;">
            <thead class="sticky top-0 z-20">
                <tr class="bg-gradient-to-r from-primary-900 to-primary-800 text-white">
                    {{-- Group 1: Identifikasi --}}
                    <th colspan="9" class="px-3 py-1.5 text-center text-xs font-bold border-r border-white/20 bg-primary-900">📁 IDENTIFIKASI PROYEK</th>
                    {{-- Group 2: Anggaran --}}
                    <th colspan="6" class="px-3 py-1.5 text-center text-xs font-bold border-r border-white/20 bg-primary-800">💰 ANGGARAN</th>
                    {{-- Group 3: Kontrak --}}
                    <th colspan="7" class="px-3 py-1.5 text-center text-xs font-bold border-r border-white/20 bg-primary-900">📄 KONTRAK & PENYEDIA</th>
                    {{-- Group 4: BAST --}}
                    <th colspan="6" class="px-3 py-1.5 text-center text-xs font-bold border-r border-white/20 bg-primary-800">✅ BAST & PROGRESS</th>
                    {{-- Group 5: Lainnya --}}
                    <th colspan="6" class="px-3 py-1.5 text-center text-xs font-bold bg-primary-900">📌 LAINNYA</th>
                </tr>
                <tr class="bg-primary-800 text-white text-xs">
                    {{-- Identifikasi --}}
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[40px]">No</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[110px]">Kode Sinusa</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[90px]">Regional</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[130px]">Rekening Besar</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[90px]">Komoditi</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[130px]">Unit Kerja</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[150px]">Program Kerja</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[200px]">Nama Investasi</th>
                    <th class="px-2 py-2 text-left border-r border-white/20 min-w-[100px]">Status Anggaran</th>
                    {{-- Anggaran --}}
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[100px]">No. WBS</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[80px]">PPAB/DPBB</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[130px]">No. PPAB/DPBB</th>
                    <th class="px-2 py-2 text-right border-r border-white/10 min-w-[120px]">Nilai RKAP</th>
                    <th class="px-2 py-2 text-right border-r border-white/10 min-w-[120px]">Nilai Pengajuan</th>
                    <th class="px-2 py-2 text-right border-r border-white/20 min-w-[120px]">Nilai PH</th>
                    {{-- Kontrak --}}
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[130px]">No. Kontrak</th>
                    <th class="px-2 py-2 text-right border-r border-white/10 min-w-[130px]">Nilai Kontrak</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[160px]">Penyedia</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[95px]">Tgl Mulai</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[95px]">Tgl Selesai</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[70px]">Count Days</th>
                    <th class="px-2 py-2 text-left border-r border-white/20 min-w-[90px]">Nomor PO</th>
                    {{-- BAST --}}
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[130px]">No. BAST I</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[90px]">Tgl BAST I</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[130px]">No. BAST II</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[90px]">Tgl BAST II</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[70px]">Progress (%)</th>
                    <th class="px-2 py-2 text-left border-r border-white/20 min-w-[90px]">SA/GR Tgl</th>
                    {{-- Lainnya --}}
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[100px]">PR</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[100px]">No. PK</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[100px]">No. Addendum</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[100px]">Jangka Add</th>
                    <th class="px-2 py-2 text-left border-r border-white/10 min-w-[80px]">Prioritas</th>
                    <th class="px-2 py-2 text-left min-w-[110px]">Status Paket</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($investasi as $item)
                <tr class="table-row-hover hover:bg-blue-50/50">
                    <td class="px-2 py-2 text-slate-500 border-r border-slate-100">{{ $investasi->firstItem() + $loop->index }}</td>
                    <td class="px-2 py-2 font-mono text-primary-700 border-r border-slate-100">{{ $item->kode_sinusa ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->regional ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100 max-w-[130px] truncate" title="{{ $item->rekening_besar }}">{{ $item->rekening_besar ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->komoditi ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100 max-w-[130px] truncate" title="{{ $item->unit_kerja }}">{{ $item->unit_kerja ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100 max-w-[150px] truncate" title="{{ $item->program_kerja }}">{{ $item->program_kerja ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100 max-w-[200px]"><span class="line-clamp-2 font-medium text-slate-800">{{ $item->nama_investasi ?? '-' }}</span></td>
                    <td class="px-2 py-2 border-r border-slate-200">{{ $item->status_anggaran ?? '-' }}</td>

                    <td class="px-2 py-2 font-mono border-r border-slate-100">{{ $item->no_wbs ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->ppab_dpbb ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->no_ppab_dpbb ?? '-' }}</td>
                    <td class="px-2 py-2 text-right border-r border-slate-100 font-medium text-slate-700">{{ $item->nilai_rkap ? number_format($item->nilai_rkap) : '-' }}</td>
                    <td class="px-2 py-2 text-right border-r border-slate-100 font-medium text-slate-700">{{ $item->nilai_pengajuan ? number_format($item->nilai_pengajuan) : '-' }}</td>
                    <td class="px-2 py-2 text-right border-r border-slate-200 font-medium text-slate-700">{{ $item->nilai_ph ? number_format($item->nilai_ph) : '-' }}</td>

                    <td class="px-2 py-2 font-mono border-r border-slate-100">{{ $item->no_kontrak ?? '-' }}</td>
                    <td class="px-2 py-2 text-right border-r border-slate-100 font-medium text-gold-700">{{ $item->nilai_kontrak ? number_format($item->nilai_kontrak) : '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100 max-w-[160px] truncate" title="{{ $item->penyedia }}">{{ $item->penyedia ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->tgl_mulai_kontrak?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->tgl_selesai_kontrak?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-2 py-2 text-center border-r border-slate-100">{{ $item->count_days ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-200">{{ $item->nomor_po ?? '-' }}</td>

                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->no_bast_1 ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->tgl_bast_1?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->no_bast_2 ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->tgl_bast_2?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-2 py-2 text-center border-r border-slate-100">
                        @php $p = $item->progress_fisik ?? 0; @endphp
                        <span class="font-bold {{ $p >= 80 ? 'text-emerald-600' : ($p >= 50 ? 'text-gold-600' : 'text-red-500') }}">
                            {{ number_format($p,1) }}%
                        </span>
                    </td>
                    <td class="px-2 py-2 border-r border-slate-200">{{ $item->sa_gr_tanggal?->format('d/m/Y') ?? '-' }}</td>

                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->pr ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->no_pk ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->no_addendum ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->jangka_waktu_add ?? '-' }}</td>
                    <td class="px-2 py-2 border-r border-slate-100">{{ $item->prioritas ?? '-' }}</td>
                    <td class="px-2 py-2">@include('investasi._status-badge', ['status' => $item->status_paket])</td>
                </tr>
                @empty
                <tr><td colspan="34" class="py-16 text-center text-slate-400">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('investasi._pagination', ['data' => $investasi])
</div>

{{-- ─── Detail Modal ───────────────────────────────────────────────────────── --}}
<div id="detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDetail()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-primary-900 to-primary-800">
            <h3 class="text-white font-semibold">Detail Investasi</h3>
            <button onclick="closeDetail()" class="text-white/70 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="detail-content" class="flex-1 overflow-y-auto p-6">
            <div class="text-center py-8 text-slate-400">Memuat...</div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .active-tab   { background:#1e40af; color:#fff; }
    .inactive-tab { background:#f1f5f9; color:#475569; }
    .inactive-tab:hover { background:#e2e8f0; }
</style>
@endpush

@push('scripts')
<script>
// ─── Tab Switch ──────────────────────────────────────────────────────────────
function switchTab(name) {
    ['ringkasan','lengkap'].forEach(t => {
        document.getElementById('view-' + t).classList.add('hidden');
        document.getElementById('tab-' + t).classList.remove('active-tab');
        document.getElementById('tab-' + t).classList.add('inactive-tab');
    });
    document.getElementById('view-' + name).classList.remove('hidden');
    document.getElementById('tab-' + name).classList.add('active-tab');
    document.getElementById('tab-' + name).classList.remove('inactive-tab');
    localStorage.setItem('investasi-tab', name);
}

// Restore last active tab
const savedTab = localStorage.getItem('investasi-tab') || 'ringkasan';
switchTab(savedTab);

// ─── Auto-submit on filter change ────────────────────────────────────────────
document.querySelectorAll('#filter-form select').forEach(sel => {
    sel.addEventListener('change', () => document.getElementById('filter-form').submit());
});

// ─── Detail Modal ─────────────────────────────────────────────────────────────
const allData = @json($investasi->items());

function openDetail(id) {
    const item = allData.find(r => r.id === id);
    if (!item) return;
    const modal = document.getElementById('detail-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';

    const fmt = (v) => v ? parseInt(v).toLocaleString('id-ID') : '-';
    const fmtDate = (v) => v ? new Date(v).toLocaleDateString('id-ID',{day:'2-digit',month:'2-digit',year:'numeric'}) : '-';
    const prog = item.progress_fisik ?? 0;
    const progColor = prog >= 80 ? '#10b981' : prog >= 50 ? '#d97706' : '#ef4444';

    document.getElementById('detail-content').innerHTML = `
        <div class="mb-4">
            <p class="text-xs text-slate-500 font-mono">${item.kode_sinusa || '-'} &bull; WBS: ${item.no_wbs || '-'}</p>
            <h4 class="text-lg font-bold text-slate-800 mt-1">${item.nama_investasi || 'Tanpa Nama'}</h4>
            <div class="flex flex-wrap gap-2 mt-2">
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">${item.regional || '-'}</span>
                <span class="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded-full">${item.unit_kerja || '-'}</span>
                <span class="text-xs bg-gold-100 text-gold-700 px-2 py-1 rounded-full">${item.komoditi || '-'}</span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-5 p-4 bg-slate-50 rounded-xl">
            <div class="flex justify-between items-center mb-1.5">
                <span class="text-sm font-semibold text-slate-700">Progress Fisik</span>
                <span class="text-xl font-bold" style="color:${progColor}">${prog.toFixed(1)}%</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-3">
                <div class="h-3 rounded-full transition-all" style="width:${Math.min(prog,100)}%;background:${progColor}"></div>
            </div>
        </div>

        <!-- Grid Detail -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            ${section('💰 Anggaran', [
                ['Status Anggaran', item.status_anggaran],
                ['Nilai RKAP', 'Rp ' + fmt(item.nilai_rkap)],
                ['Nilai Pengajuan', 'Rp ' + fmt(item.nilai_pengajuan)],
                ['Nilai PH', 'Rp ' + fmt(item.nilai_ph)],
                ['Tgl Anggaran', fmtDate(item.tgl_anggaran)],
                ['PPAB/DPBB', item.ppab_dpbb],
                ['No. PPAB/DPBB', item.no_ppab_dpbb],
            ])}
            ${section('📄 Kontrak', [
                ['No. Kontrak', item.no_kontrak],
                ['Nilai Kontrak', 'Rp ' + fmt(item.nilai_kontrak)],
                ['Penyedia', item.penyedia],
                ['Tgl Mulai', fmtDate(item.tgl_mulai_kontrak)],
                ['Tgl Selesai', fmtDate(item.tgl_selesai_kontrak)],
                ['Count Days', item.count_days + ' hari'],
                ['Nomor PO', item.nomor_po],
            ])}
            ${section('✅ BAST', [
                ['No. BAST I', item.no_bast_1],
                ['Tgl BAST I', fmtDate(item.tgl_bast_1)],
                ['No. BAST II', item.no_bast_2],
                ['Tgl BAST II', fmtDate(item.tgl_bast_2)],
                ['SA/GR Tanggal', fmtDate(item.sa_gr_tanggal)],
            ])}
            ${section('📌 Lainnya', [
                ['PR', item.pr],
                ['No. PK', item.no_pk],
                ['Stasiun Kerja', item.stasiun_kerja],
                ['No. Addendum', item.no_addendum],
                ['Jangka Waktu Add', item.jangka_waktu_add],
                ['Prioritas', item.prioritas],
                ['Status Paket', item.status_paket],
                ['Keterangan', item.keterangan],
            ])}
        </div>
    `;
}

function section(title, rows) {
    const rowsHtml = rows.map(([label, val]) => `
        <div class="flex justify-between gap-3 py-1.5 border-b border-slate-100">
            <span class="text-slate-500 text-xs flex-shrink-0">${label}</span>
            <span class="text-slate-800 text-xs font-medium text-right">${val || '-'}</span>
        </div>
    `).join('');
    return `<div class="bg-white border border-slate-100 rounded-xl p-4">
        <p class="text-xs font-bold text-slate-700 mb-2">${title}</p>
        ${rowsHtml}
    </div>`;
}

function closeDetail() {
    const modal = document.getElementById('detail-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDetail(); });
</script>
@endpush
