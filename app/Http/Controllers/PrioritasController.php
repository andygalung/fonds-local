<?php

namespace App\Http\Controllers;

use App\Services\PrioritasSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrioritasController extends Controller
{
    public function __construct(
        protected PrioritasSheetService $service
    ) {}

    public function index(Request $request)
    {
        $error = null;
        $data  = ['title' => '', 'date' => '', 'records' => []];

        try {
            // Paksa refresh jika ada parameter ?refresh=1
            if ($request->boolean('refresh')) {
                $this->service->clearCache();
            }

            $data = $this->service->getDashboardData();
        } catch (\Exception $e) {
            Log::error('PrioritasController::index error: ' . $e->getMessage());
            $error = 'Gagal memuat data dari Google Sheets. ' . $e->getMessage();
        }

        return view('prioritas.index', compact('data', 'error'));
    }

    /**
     * Endpoint AJAX untuk refresh data (invalidate cache).
     */
    public function refresh()
    {
        try {
            $this->service->clearCache();
            $data = $this->service->getDashboardData();
            return response()->json(['success' => true, 'record_count' => count($data['records'])]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
