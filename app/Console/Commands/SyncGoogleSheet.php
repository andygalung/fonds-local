<?php

namespace App\Console\Commands;

use App\Models\Investasi;
use App\Services\GoogleSheetService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncGoogleSheet extends Command
{
    protected $signature = 'sync:sheet {--dry-run : Tampilkan data tanpa menyimpan ke database}';

    protected $description = 'Sinkronisasi data investasi dari Google Sheets ke database MySQL';

    public function handle(GoogleSheetService $sheetService): int
    {
        $this->info('🔄 Memulai sinkronisasi dari Google Sheets...');
        $startTime = now();

        try {
            $rows = $sheetService->getData();

            if (empty($rows)) {
                $this->warn('⚠️  Tidak ada data yang ditemukan di Google Sheets.');
                return self::SUCCESS;
            }

            $this->info("📊 Ditemukan " . count($rows) . " baris data.");

            if ($this->option('dry-run')) {
                $this->info('🔍 Dry-run mode aktif. Data tidak akan disimpan.');
                $this->table(
                    ['No', 'Kode Sinusa', 'No Kontrak', 'Nama Investasi', 'Nilai RKAP'],
                    collect($rows)->take(15)->map(fn($r) => [
                        $r['no'] ?? '-',
                        $r['kode_sinusa'] ?? '-',
                        $r['no_kontrak'] ?? '-',
                        Str::limit($r['nama_investasi'] ?? '-', 50),
                        number_format($r['nilai_rkap'] ?? 0),
                    ])->toArray()
                );
                return self::SUCCESS;
            }

            $inserted = 0;
            $updated  = 0;
            $skipped  = 0;

            DB::beginTransaction();

            foreach ($rows as $row) {
                // Skip hanya jika baris benar-benar kosong (tidak ada nama investasi DAN kode sinusa)
                if (empty($row['nama_investasi']) && empty($row['kode_sinusa'])) {
                    $skipped++;
                    continue;
                }

                // Tentukan unique key secara bertingkat:
                // 1. kode_sinusa + no_kontrak (ideal)
                // 2. kode_sinusa saja (jika belum ada kontrak)
                // 3. no_wbs (fallback)
                // 4. nama_investasi (last resort)
                if (!empty($row['kode_sinusa']) && !empty($row['no_kontrak'])) {
                    $uniqueKey = [
                        'kode_sinusa' => $row['kode_sinusa'],
                        'no_kontrak'  => $row['no_kontrak'],
                    ];
                } elseif (!empty($row['kode_sinusa'])) {
                    $uniqueKey = [
                        'kode_sinusa' => $row['kode_sinusa'],
                        'no_kontrak'  => null,
                    ];
                } elseif (!empty($row['no_wbs'])) {
                    $uniqueKey = [
                        'no_wbs'     => $row['no_wbs'],
                        'no_kontrak' => $row['no_kontrak'] ?? null,
                    ];
                } else {
                    $uniqueKey = [
                        'nama_investasi' => $row['nama_investasi'] ?? null,
                        'no_kontrak'     => $row['no_kontrak'] ?? null,
                    ];
                }

                $existing = Investasi::where($uniqueKey)->first();

                if ($existing) {
                    $existing->fill($row)->save();
                    $updated++;
                } else {
                    Investasi::create(array_merge($uniqueKey, $row));
                    $inserted++;
                }
            }

            DB::commit();

            $elapsed = now()->diffInSeconds($startTime);
            $this->info("✅ Sinkronisasi selesai dalam {$elapsed} detik.");
            $this->table(
                ['Status', 'Jumlah'],
                [
                    ['✅ Inserted (Baru)',         $inserted],
                    ['🔄 Updated (Diperbarui)',    $updated],
                    ['⏭️  Skipped (Baris Kosong)', $skipped],
                    ['📊 Total Diproses',          count($rows)],
                ]
            );

            Log::info('SyncGoogleSheet selesai', [
                'inserted' => $inserted,
                'updated'  => $updated,
                'skipped'  => $skipped,
                'elapsed'  => $elapsed,
            ]);

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            Log::error('SyncGoogleSheet error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return self::FAILURE;
        }
    }
}
