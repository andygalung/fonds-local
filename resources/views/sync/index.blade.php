@extends('layouts.app')

@section('title', 'Sinkronisasi')
@section('page-title', 'Sinkronisasi Google Sheets')
@section('page-subtitle', 'Kelola dan pantau sinkronisasi data investasi')

@section('content')

<div class="max-w-4xl mx-auto space-y-5">

    {{-- ─── Status Alert ───────────────────────────────────────────────────── --}}
    @if(session('success'))
    <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            <p class="font-semibold">{{ session('success') }}</p>
            @if(session('output'))
            <pre class="mt-2 text-xs bg-emerald-100 rounded-lg p-3 overflow-x-auto whitespace-pre-wrap text-emerald-700">{{ session('output') }}</pre>
            @endif
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl p-4">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            <p class="font-semibold">{{ session('error') }}</p>
            @if(session('output'))
            <pre class="mt-2 text-xs bg-red-100 rounded-lg p-3 overflow-x-auto whitespace-pre-wrap text-red-700">{{ session('output') }}</pre>
            @endif
        </div>
    </div>
    @endif

    {{-- ─── Main Sync Card ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-900 to-primary-800 px-6 py-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-bold text-lg">Sinkronisasi Google Sheets</h2>
                    <p class="text-white/70 text-sm mt-0.5">Data diambil dari spreadsheet PTPN IV Regional 1</p>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100">
            <div class="px-6 py-4 text-center">
                <p class="text-2xl font-bold text-slate-800">{{ number_format($totalData) }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Total Data</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="text-sm font-semibold text-slate-700">{{ $lastSyncAt }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Sinkronisasi Terakhir</p>
            </div>
            <div class="px-6 py-4 text-center">
                <span class="inline-flex items-center gap-1.5 text-emerald-600 text-sm font-semibold">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Otomatis Aktif
                </span>
                <p class="text-xs text-slate-500 mt-0.5">Setiap 5 menit</p>
            </div>
        </div>

        <!-- Config Info -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">Konfigurasi Sumber Data</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-slate-500 w-24 flex-shrink-0">Sheet ID:</span>
                    <code class="bg-white border border-slate-200 px-2 py-1 rounded font-mono text-primary-700 truncate">
                        {{ env('GOOGLE_SHEET_ID', 'Belum dikonfigurasi') }}
                    </code>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-slate-500 w-24 flex-shrink-0">Range:</span>
                    <code class="bg-white border border-slate-200 px-2 py-1 rounded font-mono text-primary-700">
                        {{ env('GOOGLE_SHEET_RANGE', 'Sheet1!A1:AN1000') }}
                    </code>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-slate-500 w-24 flex-shrink-0">Credentials:</span>
                    @php
                        $credPath = base_path(env('GOOGLE_CREDENTIALS_PATH', 'storage/app/google-credentials.json'));
                        $credExists = file_exists($credPath);
                    @endphp
                    @if($credExists)
                        <span class="text-emerald-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            File ditemukan
                        </span>
                    @else
                        <span class="text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            File tidak ditemukan
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Warning jika credentials belum ada --}}
        @if(!$credExists)
        <div class="mx-6 mt-4 flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold text-sm">File Google Credentials Belum Ditemukan!</p>
                <p class="text-sm mt-1">Letakkan file <code class="bg-amber-100 px-1 rounded font-mono">google-credentials.json</code>
                   dari Google Cloud Console ke path:</p>
                <code class="block mt-1 bg-amber-100 px-2 py-1.5 rounded font-mono text-xs break-all">
                    {{ $credPath }}
                </code>
            </div>
        </div>
        @endif

        <!-- Action -->
        <div class="px-6 py-5">
            <form method="POST" action="{{ route('sync.run') }}" id="sync-form">
                @csrf
                <button type="submit" id="sync-btn"
                        class="flex items-center gap-2.5 bg-gradient-to-r from-primary-700 to-primary-900 text-white px-6 py-3 rounded-xl text-sm font-semibold hover:from-primary-600 hover:to-primary-800 transition shadow-md hover:shadow-lg active:scale-95"
                        @if(!$credExists) disabled @endif>
                    <svg id="sync-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span id="sync-text">🔄 Sinkronisasi Sekarang</span>
                </button>
            </form>
            <p class="text-xs text-slate-400 mt-2">Proses sinkronisasi mungkin memerlukan beberapa detik tergantung jumlah data.</p>
        </div>
    </div>

    {{-- ─── Artisan Info ────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <h3 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Menjalankan via Terminal
        </h3>
        <div class="space-y-2">
            <div class="bg-slate-900 text-emerald-400 rounded-xl p-4 font-mono text-xs overflow-x-auto">
                <p class="text-slate-500 mb-1"># Sinkronisasi normal</p>
                <p>php artisan sync:sheet</p>
            </div>
            <div class="bg-slate-900 text-emerald-400 rounded-xl p-4 font-mono text-xs overflow-x-auto">
                <p class="text-slate-500 mb-1"># Preview data tanpa menyimpan</p>
                <p>php artisan sync:sheet --dry-run</p>
            </div>
            <div class="bg-slate-900 text-emerald-400 rounded-xl p-4 font-mono text-xs overflow-x-auto">
                <p class="text-slate-500 mb-1"># Jalankan scheduler</p>
                <p>php artisan schedule:run</p>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.getElementById('sync-form')?.addEventListener('submit', function () {
    const btn = document.getElementById('sync-btn');
    const icon = document.getElementById('sync-icon');
    const text = document.getElementById('sync-text');

    btn.disabled = true;
    btn.classList.add('opacity-75');
    icon.classList.add('animate-spin');
    text.textContent = 'Sedang sinkronisasi...';
});
</script>
@endpush
