<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    protected GoogleClient $client;
    protected Sheets $sheetsService;
    protected string $spreadsheetId;
    protected string $range;

    // Mapping: nama header sheet → field database (snake_case)
    protected array $columnMap = [
        'No'                                  => 'no',
        'Regional'                            => 'regional',
        'Kode Sinusa'                         => 'kode_sinusa',
        'Rekening Besar'                      => 'rekening_besar',
        'Komoditi'                            => 'komoditi',
        'Unit Kerja'                          => 'unit_kerja',
        'Program Kerja/Impact'                => 'program_kerja',
        'Nama Investasi Sesuai SINUSA'        => 'nama_investasi',
        'Status Anggaran'                     => 'status_anggaran',
        'No. WBS'                             => 'no_wbs',
        'PPAB/DPBB'                           => 'ppab_dpbb',
        'No. PPAB/DPBB'                       => 'no_ppab_dpbb',
        'Uraian Pekerjaan Judul PPAB/DPBB'   => 'uraian_pekerjaan',
        'Stasiun Kerja'                       => 'stasiun_kerja',
        'PR'                                  => 'pr',
        'Tgl Doc Terima'                      => 'tgl_doc_terima',
        'Tgl Anggaran'                        => 'tgl_anggaran',
        'Nilai RKAP'                          => 'nilai_rkap',
        'Nilai Pengajuan'                     => 'nilai_pengajuan',
        'Nilai PH'                            => 'nilai_ph',
        'Tanggal Upload IPS'                  => 'tanggal_upload_ips',
        'No. PK'                              => 'no_pk',
        'No. Kontrak'                         => 'no_kontrak',
        'Nilai Kontrak'                       => 'nilai_kontrak',
        'Penyedia'                            => 'penyedia',
        'Tgl Mulai Kontrak'                   => 'tgl_mulai_kontrak',
        'Tgl Selesai Kontrak'                 => 'tgl_selesai_kontrak',
        'Count Days'                          => 'count_days',
        'No. BAST I / Tagihan 95%'            => 'no_bast_1',
        'Tgl BAST I'                          => 'tgl_bast_1',
        'No. BAST II'                         => 'no_bast_2',
        'Tgl BAST II'                         => 'tgl_bast_2',
        'Progress Fisik Pekerjaan (%)'        => 'progress_fisik',
        'No. Addendum'                        => 'no_addendum',
        'Jangka Waktu Add'                    => 'jangka_waktu_add',
        'Prioritas'                           => 'prioritas',
        'Nomor PO'                            => 'nomor_po',
        'SA/GR Tanggal'                       => 'sa_gr_tanggal',
        'Keterangan'                          => 'keterangan',
        'Status Paket Pekerjaan'              => 'status_paket',
    ];

    // Field yang berisi nilai tanggal
    protected array $dateFields = [
        'tgl_doc_terima',
        'tgl_anggaran',
        'tanggal_upload_ips',
        'tgl_mulai_kontrak',
        'tgl_selesai_kontrak',
        'tgl_bast_1',
        'tgl_bast_2',
        'sa_gr_tanggal',
    ];

    // Field yang berisi nilai numerik (currency)
    protected array $numericFields = [
        'nilai_rkap',
        'nilai_pengajuan',
        'nilai_ph',
        'nilai_kontrak',
        'count_days',
        'no',
    ];

    public function __construct()
    {
        $this->spreadsheetId = config('google.sheet_id');
        $this->range = config('google.sheet_range');
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
     * Ambil semua data dari Google Sheets.
     * Return array of associative arrays (field => value).
     */
    public function getData(): array
    {
        try {
            $response = $this->sheetsService->spreadsheets_values->get(
                $this->spreadsheetId,
                $this->range
            );

            $rows = $response->getValues();

            if (empty($rows)) {
                return [];
            }

            // Row pertama = header
            $headers = array_shift($rows);

            $records = [];
            foreach ($rows as $row) {
                $mapped = $this->mapRowToArray($headers, $row);
                if (!empty($mapped)) {
                    $records[] = $mapped;
                }
            }

            return $records;
        } catch (\Exception $e) {
            Log::error('GoogleSheetService::getData error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mapping satu row ke associative array dengan field database.
     */
    protected function mapRowToArray(array $headers, array $row): array
    {
        $data = [];

        foreach ($headers as $index => $header) {
            $header = trim($header);

            if (!isset($this->columnMap[$header])) {
                continue;
            }

            $field = $this->columnMap[$header];
            $value = isset($row[$index]) ? trim((string) $row[$index]) : null;
            $value = ($value === '' || $value === '-') ? null : $value;

            if ($value !== null) {
                if (in_array($field, $this->dateFields)) {
                    $value = $this->parseDate($value);
                } elseif (in_array($field, $this->numericFields)) {
                    $value = $this->parseNumeric($value);
                } elseif ($field === 'progress_fisik') {
                    $value = $this->parseProgress($value);
                }
            }

            $data[$field] = $value;
        }

        return $data;
    }

    /**
     * Parse berbagai format tanggal ke format Y-m-d.
     * Mendukung: d/m/Y, d-m-Y, Y-m-d, d Month Y (Indonesia).
     */
    protected function parseDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $indonesianMonths = [
            'januari' => '01', 'februari' => '02', 'maret' => '03',
            'april' => '04', 'mei' => '05', 'juni' => '06',
            'juli' => '07', 'agustus' => '08', 'september' => '09',
            'oktober' => '10', 'november' => '11', 'desember' => '12',
        ];

        $lower = strtolower(trim($value));

        // Format: "12 Januari 2024" atau "12 Jan 2024"
        foreach ($indonesianMonths as $name => $num) {
            $shortName = substr($name, 0, 3);
            if (str_contains($lower, $name) || str_contains($lower, $shortName)) {
                $lower = str_replace([$name, $shortName], '-' . $num . '-', $lower);
                break;
            }
        }

        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'd-m-y', 'm/d/Y', 'j-n-Y', 'j/n/Y'];

        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, trim($lower));
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }

        // Try strtotime fallback
        $ts = strtotime($value);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }

        return null;
    }

    /**
     * Parse nilai numerik: strip Rp, titik, koma.
     */
    protected function parseNumeric(?string $value): ?int
    {
        if (!$value) {
            return null;
        }

        // Hapus Rp, spasi, titik ribuan
        $cleaned = preg_replace('/[Rp\s\.]/', '', $value);
        // Ganti koma desimal dengan titik
        $cleaned = str_replace(',', '.', $cleaned);

        if (!is_numeric($cleaned)) {
            return null;
        }

        return (int) round((float) $cleaned);
    }

    /**
     * Parse persentase progress: "75.5%" → 75.5
     */
    protected function parseProgress(?string $value): ?float
    {
        if (!$value) {
            return null;
        }

        $cleaned = str_replace(['%', ' '], '', $value);
        $cleaned = str_replace(',', '.', $cleaned);

        if (!is_numeric($cleaned)) {
            return null;
        }

        return (float) $cleaned;
    }
}
