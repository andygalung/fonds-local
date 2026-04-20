<?php

namespace App\Http\Controllers;

use App\Models\Investasi;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    public function index()
    {
        $totalData  = Investasi::count();
        $lastSync   = Investasi::max('updated_at');
        $lastSyncAt = $lastSync ? \Carbon\Carbon::parse($lastSync)->diffForHumans() : 'Belum pernah';

        return view('sync.index', compact('totalData', 'lastSyncAt'));
    }

    public function run(Request $request)
    {
        try {
            $exitCode = Artisan::call('sync:sheet');

            $output = Artisan::output();

            if ($exitCode === 0) {
                return redirect()->route('sync.index')->with([
                    'success' => 'Sinkronisasi berhasil!',
                    'output'  => $output,
                ]);
            } else {
                return redirect()->route('sync.index')->with([
                    'error'  => 'Sinkronisasi gagal. Periksa log untuk detail.',
                    'output' => $output,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('SyncController::run error: ' . $e->getMessage());
            return redirect()->route('sync.index')->with([
                'error' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
}
