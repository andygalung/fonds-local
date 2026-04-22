<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PrioritasSheetService
{
    protected GoogleClient $client;
    protected Sheets $sheetsService;
    protected string $spreadsheetId;

    // Nama sheet yang ingin diambil
    protected string $sheetName = "Copy of Progress Investasi PKS (Data OH) Prioritas";

    public function __construct()
    {
        $this->spreadsheetId = config('google.sheet_id');
        $this->initClient();
    }

    protected function initClient(): void
    {
        $credentialsPath = base_path(config('google.credentials_path'));

        $this->client = new GoogleClient();
        $this->client->setApplicationName(config('google.application_name'));
        $this->client->setScopes(config('google.scopes'));
        $this->client->setAuthConfig($credentialsPath);
        $this->client->setAccessType('offline');

        $this->sheetsService = new Sheets($this->client);
    }

    /**
     * Ambil raw data dari sheet (semua baris, termasuk header merger).
     * Return array of raw rows [row_index => [col_values...]].
     */
    public function getRawRows(string $range = null): array
    {
        $range = $range ?? "'{$this->sheetName}'!A1:BH500";

        try {
            $response = $this->sheetsService->spreadsheets_values->get(
                $this->spreadsheetId,
                $range,
                ['valueRenderOption' => 'FORMATTED_VALUE']
            );

            return $response->getValues() ?? [];
        } catch (\Exception $e) {
            Log::error('PrioritasSheetService::getRawRows error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ambil data lengkap yang sudah di-parse ke format tabel dashboard.
     * Baris 1-4 = header/judul, Baris 5-6 = kolom header, Baris 7+ = data.
     * Default tanpa cache agar perubahan sheet langsung terlihat.
     * Bisa aktifkan cache via env PRIORITAS_CACHE_SECONDS (> 0).
     */
    public function getDashboardData(): array
    {
        $cacheSeconds = (int) env('PRIORITAS_CACHE_SECONDS', 0);

        if ($cacheSeconds > 0) {
            return Cache::remember('prioritas_dashboard_data', $cacheSeconds, function () {
                return $this->buildDashboardData();
            });
        }

        return $this->buildDashboardData();
    }

    protected function buildDashboardData(): array
    {
        $rows = $this->getRawRows("'{$this->sheetName}'!A1:BH500");

        if (empty($rows)) {
            return [
                'title'   => '',
                'date'    => '',
                'headers' => [],
                'groups'  => [],
                'records' => [],
            ];
        }

        // Baris 1 = judul utama (A1)
        $title = $rows[0][0] ?? '';

        // Baris 2 = tanggal (A2)
        $date = $rows[1][0] ?? '';

        // Cari start index per grup dari header sheet (row index 3),
        // agar tahan terhadap pergeseran/penambahan kolom.
        $groupRow = $rows[3] ?? [];
        $subGroupRow = $rows[4] ?? [];
        $colStart = [
            'rkap'   => $this->findHeaderStart($groupRow, ['rkap', 'prioritas']) ?? 1,
            'doku'   => $this->findHeaderStart($groupRow, ['dokumen', 'unit']) ?? 9,
            'tekpol' => $this->findHeaderStart($groupRow, ['diajukan', 'tekpol']) ?? 17,
            'hps'    => $this->findHeaderStart($groupRow, ['hps', 'pengadaan']) ?? 25,
            'sppbj'  => $this->findHeaderStart($groupRow, ['sppbj', 'kontrak']) ?? 33,
            'pct'    => $this->findHeaderStart($groupRow, ['progress', 'rkap']) ?? 41,
        ];
        $hasSplitTekpolProgress = $this->hasTekpolProgressSplit($subGroupRow, $colStart['pct']);

        // Baris 4-6 = header grup & sub-header
        // Berdasarkan screenshot, struktur:
        // Row 4 (index 3): Unit Kerja | RKAP 2026 Rekg 045 Setahun (Prioritas) | Dokumen di Unit | Sudah diajukan Unit (Bag Tekpol) | HPS/Pengadaan | SPPBJ/Kontrak | %Progress Thd RKAP setahun (%)
        // Row 5 (index 4): sub-header tiap grup
        // Row 6 (index 5): sub-sub-header (1,2,3,4 dll)
        // Row 7+ (index 6+): data

        // Parse data records (dari baris ke-7, index 6)
        $records = [];
        for ($i = 6; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row) || (empty($row[0]) && empty($row[1]))) {
                continue; // skip baris kosong
            }

            // Struktur dibaca relatif dari start setiap grup:
            // +0..+3 = Biaya(1..4), +4..+7 = Paket(1..4)
            // Khusus progress: +0 DiajBiaya, +1 DiajPaket, +2 BelumBiaya, +3 BelumPaket, dst.

            $records[] = [
                'unit_kerja'         => $row[0] ?? '',

                // RKAP 2026 - Σ Biaya (Rp) cols 1-4
                'rkap_biaya_1'       => $this->parseNum($row[$colStart['rkap'] + 0] ?? ''),
                'rkap_biaya_2'       => $this->parseNum($row[$colStart['rkap'] + 1] ?? ''),
                'rkap_biaya_3'       => $this->parseNum($row[$colStart['rkap'] + 2] ?? ''),
                'rkap_biaya_4'       => $this->parseNum($row[$colStart['rkap'] + 3] ?? ''),
                // RKAP - Σ Paket breakdown 1-4
                'rkap_paket_1'       => $this->parseInt($row[$colStart['rkap'] + 4] ?? ''),
                'rkap_paket_2'       => $this->parseInt($row[$colStart['rkap'] + 5] ?? ''),
                'rkap_paket_3'       => $this->parseInt($row[$colStart['rkap'] + 6] ?? ''),
                'rkap_paket_4'       => $this->parseInt($row[$colStart['rkap'] + 7] ?? ''),

                // Dokumen di Unit - Σ Biaya (Rp) cols 1-4
                'doku_biaya_1'       => $this->parseNum($row[$colStart['doku'] + 0] ?? ''),
                'doku_biaya_2'       => $this->parseNum($row[$colStart['doku'] + 1] ?? ''),
                'doku_biaya_3'       => $this->parseNum($row[$colStart['doku'] + 2] ?? ''),
                'doku_biaya_4'       => $this->parseNum($row[$colStart['doku'] + 3] ?? ''),
                // Dokumen - Σ Paket breakdown 1-4
                'doku_paket_1'       => $this->parseInt($row[$colStart['doku'] + 4] ?? ''),
                'doku_paket_2'       => $this->parseInt($row[$colStart['doku'] + 5] ?? ''),
                'doku_paket_3'       => $this->parseInt($row[$colStart['doku'] + 6] ?? ''),
                'doku_paket_4'       => $this->parseInt($row[$colStart['doku'] + 7] ?? ''),

                // Sudah Diajukan Unit (Bag Tekpol) - Σ Biaya (Rp) 1-4
                'tekpol_biaya_1'     => $this->parseNum($row[$colStart['tekpol'] + 0] ?? ''),
                'tekpol_biaya_2'     => $this->parseNum($row[$colStart['tekpol'] + 1] ?? ''),
                'tekpol_biaya_3'     => $this->parseNum($row[$colStart['tekpol'] + 2] ?? ''),
                'tekpol_biaya_4'     => $this->parseNum($row[$colStart['tekpol'] + 3] ?? ''),
                // Tekpol - Σ Paket breakdown 1-4
                'tekpol_paket_1'     => $this->parseInt($row[$colStart['tekpol'] + 4] ?? ''),
                'tekpol_paket_2'     => $this->parseInt($row[$colStart['tekpol'] + 5] ?? ''),
                'tekpol_paket_3'     => $this->parseInt($row[$colStart['tekpol'] + 6] ?? ''),
                'tekpol_paket_4'     => $this->parseInt($row[$colStart['tekpol'] + 7] ?? ''),

                // HPS/Pengadaan - Σ Biaya (Rp) 1-4
                'hps_biaya_1'        => $this->parseNum($row[$colStart['hps'] + 0] ?? ''),
                'hps_biaya_2'        => $this->parseNum($row[$colStart['hps'] + 1] ?? ''),
                'hps_biaya_3'        => $this->parseNum($row[$colStart['hps'] + 2] ?? ''),
                'hps_biaya_4'        => $this->parseNum($row[$colStart['hps'] + 3] ?? ''),
                // HPS - Σ Paket breakdown 1-4
                'hps_paket_1'        => $this->parseInt($row[$colStart['hps'] + 4] ?? ''),
                'hps_paket_2'        => $this->parseInt($row[$colStart['hps'] + 5] ?? ''),
                'hps_paket_3'        => $this->parseInt($row[$colStart['hps'] + 6] ?? ''),
                'hps_paket_4'        => $this->parseInt($row[$colStart['hps'] + 7] ?? ''),

                // SPPBJ/Kontrak - Σ Biaya (Rp) 1-4
                'sppbj_biaya_1'      => $this->parseNum($row[$colStart['sppbj'] + 0] ?? ''),
                'sppbj_biaya_2'      => $this->parseNum($row[$colStart['sppbj'] + 1] ?? ''),
                'sppbj_biaya_3'      => $this->parseNum($row[$colStart['sppbj'] + 2] ?? ''),
                'sppbj_biaya_4'      => $this->parseNum($row[$colStart['sppbj'] + 3] ?? ''),
                // SPPBJ - Σ Paket breakdown 1-4
                'sppbj_paket_1'      => $this->parseInt($row[$colStart['sppbj'] + 4] ?? ''),
                'sppbj_paket_2'      => $this->parseInt($row[$colStart['sppbj'] + 5] ?? ''),
                'sppbj_paket_3'      => $this->parseInt($row[$colStart['sppbj'] + 6] ?? ''),
                'sppbj_paket_4'      => $this->parseInt($row[$colStart['sppbj'] + 7] ?? ''),

                // % Progress Thd RKAP setahun
                // Format baru (10 kolom): Posisi, Diterima, Tekpol, HPS, SPPBJ (masing-masing Biaya/Paket)
                // Format lama (8 kolom): Diajukan, Belum/Posisi, HPS, SPPBJ
                'pct_belum_biaya'    => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 0] ?? '')
                    : $this->parseFloat($row[$colStart['pct'] + 2] ?? ''),
                'pct_belum_paket'    => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 1] ?? '')
                    : $this->parseFloat($row[$colStart['pct'] + 3] ?? ''),

                'pct_diterima_biaya' => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 2] ?? '')
                    : null,
                'pct_diterima_paket' => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 3] ?? '')
                    : null,

                'pct_tekpol_biaya'   => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 4] ?? '')
                    : $this->parseFloat($row[$colStart['pct'] + 0] ?? ''),
                'pct_tekpol_paket'   => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 5] ?? '')
                    : $this->parseFloat($row[$colStart['pct'] + 1] ?? ''),

                'pct_hps_biaya'      => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 6] ?? '')
                    : $this->parseFloat($row[$colStart['pct'] + 4] ?? ''),
                'pct_hps_paket'      => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 7] ?? '')
                    : $this->parseFloat($row[$colStart['pct'] + 5] ?? ''),
                'pct_sppbj_biaya'    => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 8] ?? '')
                    : $this->parseFloat($row[$colStart['pct'] + 6] ?? ''),
                'pct_sppbj_paket'    => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 9] ?? '')
                    : $this->parseFloat($row[$colStart['pct'] + 7] ?? ''),

                // Backward compatibility untuk view/chart lama:
                'pct_diaj_biaya'     => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 4] ?? '')
                    : $this->parseFloat($row[$colStart['pct'] + 0] ?? ''),
                'pct_diaj_paket'     => $hasSplitTekpolProgress
                    ? $this->parseFloat($row[$colStart['pct'] + 5] ?? '')
                    : $this->parseFloat($row[$colStart['pct'] + 1] ?? ''),

                '_is_subtotal'       => $this->isSubtotalRow($row[0] ?? ''),
            ];
        }

        return [
            'title'   => $title,
            'date'    => $date,
            'records' => $records,
        ];
    }

    /**
     * Clear cache agar data segar diambil kembali.
     */
    public function clearCache(): void
    {
        Cache::forget('prioritas_dashboard_data');
    }

    protected function isSubtotalRow(string $label): bool
    {
        $label = strtolower(trim($label));
        return str_contains($label, 'jumlah') || str_contains($label, 'total');
    }

    protected function parseNum(string $value): ?float
    {
        if (trim($value) === '' || $value === '-') return null;
        $cleaned = preg_replace('/[^\d.,\-]/', '', $value);
        $cleaned = str_replace('.', '', $cleaned);
        $cleaned = str_replace(',', '.', $cleaned);
        return is_numeric($cleaned) ? (float)$cleaned : null;
    }

    protected function parseInt(string $value): ?int
    {
        if (trim($value) === '' || $value === '-') return null;
        $cleaned = preg_replace('/[^\d]/', '', $value);
        return $cleaned !== '' ? (int)$cleaned : null;
    }

    protected function parseFloat(string $value): ?float
    {
        if (trim($value) === '' || $value === '-') return null;
        $cleaned = str_replace(['%', ' '], '', $value);
        $cleaned = str_replace(',', '.', $cleaned);
        return is_numeric($cleaned) ? (float)$cleaned : null;
    }

    protected function findHeaderStart(array $row, array $needles): ?int
    {
        foreach ($row as $idx => $cell) {
            $text = $this->normalizeText((string) $cell);
            if ($text === '') {
                continue;
            }

            $matchedAll = true;
            foreach ($needles as $needle) {
                if (!str_contains($text, $this->normalizeText($needle))) {
                    $matchedAll = false;
                    break;
                }
            }

            if ($matchedAll) {
                return (int) $idx;
            }
        }

        return null;
    }

    protected function hasTekpolProgressSplit(array $row, int $pctStart): bool
    {
        $window = array_slice($row, $pctStart, 12);
        $text = $this->normalizeText(implode(' ', array_map(fn($v) => (string) $v, $window)));
        return str_contains($text, 'diterima') && str_contains($text, 'tekpol');
    }

    protected function normalizeText(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/\s+/', ' ', $value);
        return $value ?? '';
    }
}
