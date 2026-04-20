@php
    $currentSort = request('sort');
    $currentDir  = request('dir', 'asc');
    $nextDir     = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
    $isActive    = $currentSort === $column;
@endphp
<a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'dir' => $nextDir, 'page' => 1]) }}"
   class="inline-flex items-center gap-1 hover:text-gold-300 transition {{ $isActive ? 'text-gold-300' : '' }}">
    {{ $label }}
    <span class="text-xs opacity-70">
        @if($isActive)
            {{ $currentDir === 'asc' ? '↑' : '↓' }}
        @else
            ↕
        @endif
    </span>
</a>
