@if($data->hasPages())
<div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-3">
    <p class="text-xs text-slate-500">
        Halaman <strong>{{ $data->currentPage() }}</strong> dari <strong>{{ $data->lastPage() }}</strong>
        &mdash; Total: <strong>{{ number_format($data->total()) }}</strong> data
    </p>
    <div class="flex items-center gap-1">
        @if($data->onFirstPage())
            <span class="px-3 py-1.5 text-xs rounded-lg text-slate-400 bg-slate-100 cursor-not-allowed">← Prev</span>
        @else
            <a href="{{ $data->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-100 transition">← Prev</a>
        @endif

        @foreach($data->getUrlRange(max(1, $data->currentPage()-2), min($data->lastPage(), $data->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}"
               class="px-3 py-1.5 text-xs rounded-lg transition {{ $page == $data->currentPage() ? 'bg-primary-800 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                {{ $page }}
            </a>
        @endforeach

        @if($data->hasMorePages())
            <a href="{{ $data->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-100 transition">Next →</a>
        @else
            <span class="px-3 py-1.5 text-xs rounded-lg text-slate-400 bg-slate-100 cursor-not-allowed">Next →</span>
        @endif
    </div>
</div>
@endif
