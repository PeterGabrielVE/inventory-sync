<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryLog;
use Carbon\Carbon;

class InventoryLogController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryLog::query();

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from . ' 00:00:00');
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        return $query->with('product:id,name')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($log) {
                $old = $log->old_stock ?? 0;
                $new = $log->new_stock ?? 0;
                $delta = $new - $old;

                return [
                    'id' => $log->id,
                    'product_id' => $log->product_id,
                    'product_name' => $log->product->name ?? 'N/A',
                    'old_stock' => $old,
                    'new_stock' => $new,
                    'delta' => $delta,
                    'type' => $delta > 0 ? 'Entrada' : ($delta < 0 ? 'Salida' : 'Sin cambio'),
                    'source' => $log->source ?? '',
                    'note' => $log->note ?? '',
                    'created_at' => Carbon::parse($log->created_at)->format('d-m-Y H:i:s'),
                ];
            });
    }
}
