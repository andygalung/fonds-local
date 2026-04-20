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

                // ── Struktur kolom per grup (9 kolom/grup):
                // Biaya(1), Biaya(2), Biaya(3), Biaya(4), Σ Paket Total, Paket(1), Paket(2), Paket(3), Paket(4)
                // A=0: Unit Kerja
                // Grup 1 RKAP  : B(1)  C(2)  D(3)  E(4) | F(5)=ΣPaket | G(6) H(7) I(8) J(9)
                // Grup 2 Doku  : K(10) L(11) M(12) N(13) | O(14)=ΣPaket| P(15) Q(16) R(17) S(18)
                // Grup 3 Tekpol: T(19) U(20) V(21) W(22) | X(23)=ΣPaket| Y(24) Z(25) AA(26) AB(27)
                // Grup 4 HPS   : AC(28)AD(29)AE(30)AF(31)| AG(32)=ΣPaket|AH(33)AI(34)AJ(35)AK(36)
                // Grup 5 SPPBJ : AL(37)AM(38)AN(39)AO(40)| AP(41)=ΣPaket|AQ(42)AR(43)AS(44)AT(45)
                // % Progress   : AU(46)AV(47)AW(48)AX(49)AY(50)AZ(51)BA(52)BB(53)

                $records[] = [
                    'unit_kerja'         => $row[0] ?? '',

                    // RKAP 2026 - Σ Biaya (Rp) cols 1-4
                    'rkap_biaya_1'       => $this->parseNum($row[1] ?? ''),
                    'rkap_biaya_2'       => $this->parseNum($row[2] ?? ''),
                    'rkap_biaya_3'       => $this->parseNum($row[3] ?? ''),
                    'rkap_biaya_4'       => $this->parseNum($row[4] ?? ''),
                    // RKAP - Σ Paket Total
                    'rkap_paket_total'   => $this->parseInt($row[5] ?? ''),
                    // RKAP - Σ Paket breakdown
                    'rkap_paket_1'       => $this->parseInt($row[6] ?? ''),
                    'rkap_paket_2'       => $this->parseInt($row[7] ?? ''),
                    'rkap_paket_3'       => $this->parseInt($row[8] ?? ''),
                    'rkap_paket_4'       => $this->parseInt($row[9] ?? ''),

                    // Dokumen di Unit - Σ Biaya (Rp) cols 1-4
                    'doku_biaya_1'       => $this->parseNum($row[10] ?? ''),
                    'doku_biaya_2'       => $this->parseNum($row[11] ?? ''),
                    'doku_biaya_3'       => $this->parseNum($row[12] ?? ''),
                    'doku_biaya_4'       => $this->parseNum($row[13] ?? ''),
                    // Dokumen - Σ Paket Total
                    'doku_paket_total'   => $this->parseInt($row[14] ?? ''),
                    // Dokumen - Σ Paket breakdown
                    'doku_paket_1'       => $this->parseInt($row[15] ?? ''),
                    'doku_paket_2'       => $this->parseInt($row[16] ?? ''),
                    'doku_paket_3'       => $this->parseInt($row[17] ?? ''),
                    'doku_paket_4'       => $this->parseInt($row[18] ?? ''),

                    // Sudah Diajukan Unit (Bag Tekpol) - Σ Biaya (Rp)
                    'tekpol_biaya_1'     => $this->parseNum($row[19] ?? ''),
                    'tekpol_biaya_2'     => $this->parseNum($row[20] ?? ''),
                    'tekpol_biaya_3'     => $this->parseNum($row[21] ?? ''),
                    'tekpol_biaya_4'     => $this->parseNum($row[22] ?? ''),
                    // Tekpol - Σ Paket Total
                    'tekpol_paket_total' => $this->parseInt($row[23] ?? ''),
                    // Tekpol - Σ Paket breakdown
                    'tekpol_paket_1'     => $this->parseInt($row[24] ?? ''),
                    'tekpol_paket_2'     => $this->parseInt($row[25] ?? ''),
                    'tekpol_paket_3'     => $this->parseInt($row[26] ?? ''),
                    'tekpol_paket_4'     => $this->parseInt($row[27] ?? ''),

                    // HPS/Pengadaan - Σ Biaya (Rp)
                    'hps_biaya_1'        => $this->parseNum($row[28] ?? ''),
                    'hps_biaya_2'        => $this->parseNum($row[29] ?? ''),
                    'hps_biaya_3'        => $this->parseNum($row[30] ?? ''),
                    'hps_biaya_4'        => $this->parseNum($row[31] ?? ''),
                    // HPS - Σ Paket Total
                    'hps_paket_total'    => $this->parseInt($row[32] ?? ''),
                    // HPS - Σ Paket breakdown
                    'hps_paket_1'        => $this->parseInt($row[33] ?? ''),
                    'hps_paket_2'        => $this->parseInt($row[34] ?? ''),
                    'hps_paket_3'        => $this->parseInt($row[35] ?? ''),
                    'hps_paket_4'        => $this->parseInt($row[36] ?? ''),

                    // SPPBJ/Kontrak - Σ Biaya (Rp)
                    'sppbj_biaya_1'      => $this->parseNum($row[37] ?? ''),
                    'sppbj_biaya_2'      => $this->parseNum($row[38] ?? ''),
                    'sppbj_biaya_3'      => $this->parseNum($row[39] ?? ''),
                    'sppbj_biaya_4'      => $this->parseNum($row[40] ?? ''),
                    // SPPBJ - Σ Paket Total
                    'sppbj_paket_total'  => $this->parseInt($row[41] ?? ''),
                    // SPPBJ - Σ Paket breakdown
                    'sppbj_paket_1'      => $this->parseInt($row[42] ?? ''),
                    'sppbj_paket_2'      => $this->parseInt($row[43] ?? ''),
                    'sppbj_paket_3'      => $this->parseInt($row[44] ?? ''),
                    'sppbj_paket_4'      => $this->parseInt($row[45] ?? ''),

                    // % Progress Thd RKAP setahun (%)
                    'pct_diaj_biaya'     => $this->parseFloat($row[46] ?? ''),
                    'pct_diaj_paket'     => $this->parseFloat($row[47] ?? ''),
                    'pct_belum_biaya'    => $this->parseFloat($row[48] ?? ''),
                    'pct_belum_paket'    => $this->parseFloat($row[49] ?? ''),
                    'pct_hps_biaya'      => $this->parseFloat($row[50] ?? ''),
                    'pct_hps_paket'      => $this->parseFloat($row[51] ?? ''),
                    'pct_sppbj_biaya'    => $this->parseFloat($row[52] ?? ''),
                    'pct_sppbj_paket'    => $this->parseFloat($row[53] ?? ''),

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
