<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $userId = Auth::id();

        $totalSales = (float) Sale::where('user_id', $userId)->sum('amount');
        $totalPurchases = (float) Purchase::where('user_id', $userId)->sum('amount');
        $activeClients = Client::where('user_id', $userId)->count();
        $activeSuppliers = Supplier::where('user_id', $userId)->count();

        $salesRecent = Sale::query()
            ->where('user_id', $userId)
            ->with(['client:id,name'])
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        $purchasesRecent = Purchase::query()
            ->where('user_id', $userId)
            ->with(['supplier:id,name'])
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        $recentRows = collect();
        foreach ($salesRecent as $sale) {
            $recentRows->push([
                'date' => $sale->invoice_date,
                'party' => $sale->client->name ?? '—',
                'amount' => (float) $sale->amount,
                'type' => 'sale',
                'invoice_no' => $sale->invoice_no,
                'status' => $sale->status,
                'url' => route('sales.show', $sale),
                'sort' => $sale->invoice_date->format('Y-m-d').sprintf('%010d', $sale->id).'a',
            ]);
        }
        foreach ($purchasesRecent as $purchase) {
            $recentRows->push([
                'date' => $purchase->invoice_date,
                'party' => $purchase->supplier->name ?? '—',
                'amount' => (float) $purchase->amount,
                'type' => 'purchase',
                'invoice_no' => $purchase->invoice_no,
                'status' => $purchase->status,
                'url' => route('purchase.show', $purchase),
                'sort' => $purchase->invoice_date->format('Y-m-d').sprintf('%010d', $purchase->id).'b',
            ]);
        }

        $recentInvoices = $recentRows->sortByDesc('sort')->take(10)->values()->map(function (array $row) {
            unset($row['sort']);

            return $row;
        });

        return view('dashboard', compact(
            'totalSales',
            'totalPurchases',
            'activeClients',
            'activeSuppliers',
            'recentInvoices',
        ));
    }
}
