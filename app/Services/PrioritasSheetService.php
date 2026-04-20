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
     * Cache 5 menit untuk performa.
     */
    public function getDashboardData(): array
    {
        return Cache::remember('prioritas_dashboard_data', 300, function () {
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

                // ── Struktur kolom AKTUAL (8 kolom/grup, TIDAK ada kolom Σ Total):
                // A=0: Unit Kerja
                // RKAP    : B(1) C(2) D(3) E(4)=Biaya1-4 | F(5) G(6) H(7) I(8)=Paket1-4
                // Dokumen : J(9) K(10)L(11)M(12)=Biaya1-4 | N(13)O(14)P(15)Q(16)=Paket1-4
                // Tekpol  : R(17)S(18)T(19)U(20)=Biaya1-4 | V(21)W(22)X(23)Y(24)=Paket1-4
                // HPS     : Z(25)AA(26)AB(27)AC(28)=Biaya1-4| AD(29)AE(30)AF(31)AG(32)=Paket1-4
                // SPPBJ   : AH(33)AI(34)AJ(35)AK(36)=Biaya1-4| AL(37)AM(38)AN(39)AO(40)=Paket1-4
                // % Prog  : AP(41)=DiajBiaya AQ(42)=DiajPaket AR(43)=BelumBiaya AS(44)=BelumPaket
                //           AT(45)=HPSBiaya  AU(46)=HPSPaket  AV(47)=SPPBJBiaya AW(48)=SPPBJPaket

                $records[] = [
                    'unit_kerja'         => $row[0] ?? '',

                    // RKAP 2026 - Σ Biaya (Rp) cols 1-4
                    'rkap_biaya_1'       => $this->parseNum($row[1] ?? ''),
                    'rkap_biaya_2'       => $this->parseNum($row[2] ?? ''),
                    'rkap_biaya_3'       => $this->parseNum($row[3] ?? ''),
                    'rkap_biaya_4'       => $this->parseNum($row[4] ?? ''),
                    // RKAP - Σ Paket breakdown 1-4
                    'rkap_paket_1'       => $this->parseInt($row[5] ?? ''),
                    'rkap_paket_2'       => $this->parseInt($row[6] ?? ''),
                    'rkap_paket_3'       => $this->parseInt($row[7] ?? ''),
                    'rkap_paket_4'       => $this->parseInt($row[8] ?? ''),

                    // Dokumen di Unit - Σ Biaya (Rp) cols 1-4
                    'doku_biaya_1'       => $this->parseNum($row[9] ?? ''),
                    'doku_biaya_2'       => $this->parseNum($row[10] ?? ''),
                    'doku_biaya_3'       => $this->parseNum($row[11] ?? ''),
                    'doku_biaya_4'       => $this->parseNum($row[12] ?? ''),
                    // Dokumen - Σ Paket breakdown 1-4
                    'doku_paket_1'       => $this->parseInt($row[13] ?? ''),
                    'doku_paket_2'       => $this->parseInt($row[14] ?? ''),
                    'doku_paket_3'       => $this->parseInt($row[15] ?? ''),
                    'doku_paket_4'       => $this->parseInt($row[16] ?? ''),

                    // Sudah Diajukan Unit (Bag Tekpol) - Σ Biaya (Rp) 1-4
                    'tekpol_biaya_1'     => $this->parseNum($row[17] ?? ''),
                    'tekpol_biaya_2'     => $this->parseNum($row[18] ?? ''),
                    'tekpol_biaya_3'     => $this->parseNum($row[19] ?? ''),
                    'tekpol_biaya_4'     => $this->parseNum($row[20] ?? ''),
                    // Tekpol - Σ Paket breakdown 1-4
                    'tekpol_paket_1'     => $this->parseInt($row[21] ?? ''),
                    'tekpol_paket_2'     => $this->parseInt($row[22] ?? ''),
                    'tekpol_paket_3'     => $this->parseInt($row[23] ?? ''),
                    'tekpol_paket_4'     => $this->parseInt($row[24] ?? ''),

                    // HPS/Pengadaan - Σ Biaya (Rp) 1-4
                    'hps_biaya_1'        => $this->parseNum($row[25] ?? ''),
                    'hps_biaya_2'        => $this->parseNum($row[26] ?? ''),
                    'hps_biaya_3'        => $this->parseNum($row[27] ?? ''),
                    'hps_biaya_4'        => $this->parseNum($row[28] ?? ''),
                    // HPS - Σ Paket breakdown 1-4
                    'hps_paket_1'        => $this->parseInt($row[29] ?? ''),
                    'hps_paket_2'        => $this->parseInt($row[30] ?? ''),
                    'hps_paket_3'        => $this->parseInt($row[31] ?? ''),
                    'hps_paket_4'        => $this->parseInt($row[32] ?? ''),

                    // SPPBJ/Kontrak - Σ Biaya (Rp) 1-4
                    'sppbj_biaya_1'      => $this->parseNum($row[33] ?? ''),
                    'sppbj_biaya_2'      => $this->parseNum($row[34] ?? ''),
                    'sppbj_biaya_3'      => $this->parseNum($row[35] ?? ''),
                    'sppbj_biaya_4'      => $this->parseNum($row[36] ?? ''),
                    // SPPBJ - Σ Paket breakdown 1-4
                    'sppbj_paket_1'      => $this->parseInt($row[37] ?? ''),
                    'sppbj_paket_2'      => $this->parseInt($row[38] ?? ''),
                    'sppbj_paket_3'      => $this->parseInt($row[39] ?? ''),
                    'sppbj_paket_4'      => $this->parseInt($row[40] ?? ''),

                    // % Progress Thd RKAP setahun — starts at index 41
                    'pct_diaj_biaya'     => $this->parseFloat($row[41] ?? ''),
                    'pct_diaj_paket'     => $this->parseFloat($row[42] ?? ''),
                    'pct_belum_biaya'    => $this->parseFloat($row[43] ?? ''),
                    'pct_belum_paket'    => $this->parseFloat($row[44] ?? ''),
                    'pct_hps_biaya'      => $this->parseFloat($row[45] ?? ''),
                    'pct_hps_paket'      => $this->parseFloat($row[46] ?? ''),
                    'pct_sppbj_biaya'    => $this->parseFloat($row[47] ?? ''),
                    'pct_sppbj_paket'    => $this->parseFloat($row[48] ?? ''),

                    '_is_subtotal'       => $this->isSubtotalRow($row[0] ?? ''),
                ];
            }

            return [
                'title'   => $title,
                'date'    => $date,
                'records' => $records,
            ];
        });
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
}
