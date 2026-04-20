<tr>
    <td colspan="13" class="px-6 py-16 text-center">
        <div class="flex flex-col items-center gap-3">
            <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-slate-500 font-medium">Tidak ada data ditemukan</p>
            <p class="text-slate-400 text-sm">Coba ubah filter atau lakukan sinkronisasi Google Sheets</p>
            <a href="{{ route('sync.index') }}"
               class="mt-2 inline-flex items-center gap-2 bg-primary-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                🔄 Sync Sekarang
            </a>
        </div>
    </td>
</tr>
