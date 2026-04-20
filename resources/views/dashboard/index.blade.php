@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard SIIAR')
@section('page-subtitle', 'Sistem Informasi Investasi & Aset Regional — PTPN IV')

@section('content')

{{-- ─── Summary Cards ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-7">

    {{-- Total RKAP --}}
    <div class="stat-card bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4">
        <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-md">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Nilai RKAP</p>
            <p class="text-xl font-bold text-slate-800 mt-0.5 truncate">
                Rp {{ $totalRkap > 0 ? number_format($totalRkap / 1000000000, 1) . ' M' : '0' }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">{{ number_format($totalRkap) }}</p>
        </div>
    </div>

    {{-- Total Kontrak --}}
    <div class="stat-card bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4">
        <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-gold-500 to-gold-700 flex items-center justify-center shadow-md">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Nilai Kontrak</p>
            <p class="text-xl font-bold text-slate-800 mt-0.5 truncate">
                Rp {{ $totalKontrak > 0 ? number_format($totalKontrak / 1000000000, 1) . ' M' : '0' }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">{{ number_format($totalKontrak) }}</p>
        </div>
    </div>

    {{-- Total Proyek --}}
    <div class="stat-card bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4">
        <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center shadow-md">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Proyek</p>
            <p class="text-xl font-bold text-slate-800 mt-0.5">{{ number_format($totalProyek) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">paket pekerjaan</p>
        </div>
    </div>

    {{-- Avg Progress --}}
    <div class="stat-card bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4">
        <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-violet-500 to-violet-700 flex items-center justify-center shadow-md">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Rata-rata Progress</p>
            <p class="text-xl font-bold text-slate-800 mt-0.5">{{ number_format($avgProgress ?? 0, 1) }}%</p>
            <div class="mt-1 w-full bg-slate-200 rounded-full h-1.5">
                <div class="bg-violet-500 h-1.5 rounded-full" style="width: {{ min($avgProgress ?? 0, 100) }}%"></div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Charts Row 1 ──────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-5">

    {{-- Tren per Tahun --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-semibold text-slate-800">Tren Nilai Investasi per Tahun</h2>
                <p class="text-xs text-slate-500 mt-0.5">RKAP vs Kontrak (dalam Miliar Rp)</p>
            </div>
            <span class="text-xs bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full font-medium">Line Chart</span>
        </div>
        <div class="relative h-64">
            <canvas id="trenChart"></canvas>
        </div>
    </div>

    {{-- Per Regional --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-semibold text-slate-800">Total Nilai per Regional</h2>
                <p class="text-xs text-slate-500 mt-0.5">Nilai kontrak total (dalam Miliar Rp)</p>
            </div>
            <span class="text-xs bg-gold-50 text-gold-700 px-2.5 py-1 rounded-full font-medium">Bar Chart</span>
        </div>
        <div class="relative h-64">
            <canvas id="regionalChart"></canvas>
        </div>
    </div>
</div>

{{-- ─── Charts Row 2 ──────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    {{-- Progress per Unit Kerja --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-semibold text-slate-800">Progress Fisik per Unit Kerja</h2>
                <p class="text-xs text-slate-500 mt-0.5">Rata-rata progress fisik pekerjaan (%)</p>
            </div>
            <span class="text-xs bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full font-medium">Horizontal Bar</span>
        </div>
        <div class="relative" style="height: {{ max(200, count($progressPerUnit) * 36) }}px; max-height: 420px;">
            <canvas id="progressChart"></canvas>
        </div>
    </div>

    {{-- Status Breakdown --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="mb-4">
            <h2 class="font-semibold text-slate-800">Status Paket</h2>
            <p class="text-xs text-slate-500 mt-0.5">Distribusi status pekerjaan</p>
        </div>
        <div class="relative h-52">
            <canvas id="statusChart"></canvas>
        </div>
        <!-- Legend -->
        <div class="mt-3 space-y-1.5">
            @foreach($statusBreakdown->take(6) as $item)
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-600 truncate max-w-[160px]">{{ $item->status_paket ?: 'Tidak diketahui' }}</span>
                <span class="font-semibold text-slate-800 ml-2">{{ number_format($item->jumlah) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ─── Quick Link ─────────────────────────────────────────────────────────── --}}
<div class="flex justify-end">
    <a href="{{ route('investasi.index') }}"
       class="inline-flex items-center gap-2 bg-primary-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-primary-700 transition shadow-md hover:shadow-lg">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Lihat Semua Data Investasi
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
</div>

@endsection

@push('scripts')
<script>
const formatMiliar = (v) => 'Rp ' + (v / 1000000000).toFixed(1) + ' M';
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { labels: { font: { family: 'Inter', size: 11 }, boxWidth: 12, padding: 12 } }
    }
};

// ─── Chart 1: Tren per Tahun ───────────────────────────────────────────────
const trenData = @json($trenTahunan);
new Chart(document.getElementById('trenChart'), {
    type: 'line',
    data: {
        labels: trenData.map(d => d.tahun ?? '-'),
        datasets: [
            {
                label: 'Nilai RKAP',
                data: trenData.map(d => (d.total_rkap ?? 0) / 1e9),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.08)',
                borderWidth: 2.5,
                pointRadius: 5,
                pointBackgroundColor: '#2563eb',
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Nilai Kontrak',
                data: trenData.map(d => (d.total_kontrak ?? 0) / 1e9),
                borderColor: '#d97706',
                backgroundColor: 'rgba(217,119,6,0.08)',
                borderWidth: 2.5,
                pointRadius: 5,
                pointBackgroundColor: '#d97706',
                tension: 0.4,
                fill: true,
            }
        ]
    },
    options: {
        ...chartDefaults,
        plugins: { ...chartDefaults.plugins, tooltip: { callbacks: { label: (c) => formatMiliar(c.raw * 1e9) } } },
        scales: {
            x: { grid: { display: false } },
            y: {
                grid: { color: '#f1f5f9' },
                ticks: { callback: (v) => 'Rp ' + v.toFixed(0) + ' M' }
            }
        }
    }
});

// ─── Chart 2: Per Regional ────────────────────────────────────────────────
const regionalData = @json($perRegional);
const regionalColors = ['#1e40af','#2563eb','#3b82f6','#60a5fa','#93c5fd','#bfdbfe','#dbeafe'];
new Chart(document.getElementById('regionalChart'), {
    type: 'bar',
    data: {
        labels: regionalData.map(d => d.regional ?? '-'),
        datasets: [{
            label: 'Total Nilai Kontrak',
            data: regionalData.map(d => (d.total_kontrak ?? 0) / 1e9),
            backgroundColor: regionalData.map((_, i) => regionalColors[i % regionalColors.length]),
            borderRadius: 6,
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            ...chartDefaults.plugins,
            legend: { display: false },
            tooltip: { callbacks: { label: (c) => formatMiliar(c.raw * 1e9) + ' (' + (regionalData[c.dataIndex]?.jumlah ?? 0) + ' proyek)' } }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
            y: {
                grid: { color: '#f1f5f9' },
                ticks: { callback: (v) => 'Rp ' + v + ' M', font: { size: 10 } }
            }
        }
    }
});

// ─── Chart 3: Progress per Unit Kerja ────────────────────────────────────
const progressData = @json($progressPerUnit);
new Chart(document.getElementById('progressChart'), {
    type: 'bar',
    data: {
        labels: progressData.map(d => d.unit_kerja ?? '-'),
        datasets: [{
            label: 'Avg Progress (%)',
            data: progressData.map(d => d.avg_progress ?? 0),
            backgroundColor: progressData.map(d => {
                const v = d.avg_progress ?? 0;
                if (v >= 80) return '#10b981';
                if (v >= 50) return '#f59e0b';
                return '#ef4444';
            }),
            borderRadius: 4,
        }]
    },
    options: {
        ...chartDefaults,
        indexAxis: 'y',
        plugins: {
            ...chartDefaults.plugins,
            legend: { display: false },
            tooltip: { callbacks: { label: (c) => c.raw.toFixed(1) + '%' } }
        },
        scales: {
            x: {
                grid: { color: '#f1f5f9' },
                min: 0, max: 100,
                ticks: { callback: (v) => v + '%', font: { size: 10 } }
            },
            y: {
                grid: { display: false },
                ticks: { font: { size: 10 } }
            }
        }
    }
});

// ─── Chart 4: Status Donut ────────────────────────────────────────────────
const statusData = @json($statusBreakdown);
const statusColors = ['#1e40af','#d97706','#10b981','#8b5cf6','#ef4444','#06b6d4','#64748b'];
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusData.map(d => d.status_paket ?? '-'),
        datasets: [{
            data: statusData.map(d => d.jumlah),
            backgroundColor: statusData.map((_, i) => statusColors[i % statusColors.length]),
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            ...chartDefaults.plugins,
            legend: { display: false },
            tooltip: { callbacks: { label: (c) => c.label + ': ' + c.raw + ' proyek' } }
        },
        cutout: '65%',
    }
});
</script>
@endpush
