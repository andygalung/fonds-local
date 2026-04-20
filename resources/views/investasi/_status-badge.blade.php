@php
    $status = $status ?? '';
    $lower  = strtolower($status);
    $class  = match(true) {
        str_contains($lower, 'selesai') || str_contains($lower, 'bast') || str_contains($lower, 'lunas') => 'bg-emerald-100 text-emerald-700',
        str_contains($lower, 'proses') || str_contains($lower, 'berjalan') || str_contains($lower, 'pelaksanaan') => 'bg-blue-100 text-blue-700',
        str_contains($lower, 'batal') || str_contains($lower, 'gagal') => 'bg-red-100 text-red-700',
        str_contains($lower, 'tender') || str_contains($lower, 'pengadaan') || str_contains($lower, 'ph') => 'bg-amber-100 text-amber-700',
        str_contains($lower, 'kontrak') => 'bg-violet-100 text-violet-700',
        $status !== '' => 'bg-slate-100 text-slate-600',
        default => 'bg-slate-100 text-slate-400',
    };
@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $class }}">
    {{ $status ?: 'N/A' }}
</span>
