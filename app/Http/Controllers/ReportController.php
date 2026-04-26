<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartySummaryPdfRequest;
use App\Support\OutstandingSummary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $salesRows = OutstandingSummary::forClients(Auth::id());
        $purchaseRows = OutstandingSummary::forSuppliers(Auth::id());

        $salesPending = $salesRows->sum('pending_total');
        $salesOverdue = $salesRows->sum('overdue_total');
        $purchasePending = $purchaseRows->sum('pending_total');
        $purchaseOverdue = $purchaseRows->sum('overdue_total');

        return view('reports.index', compact(
            'salesRows',
            'purchaseRows',
            'salesPending',
            'salesOverdue',
            'purchasePending',
            'purchaseOverdue'
        ));
    }

    public function salesSummaryPdf(PartySummaryPdfRequest $request)
    {
        return $this->partySummaryPdf(
            $request,
            OutstandingSummary::forClients(Auth::id()),
            'Sales summary',
            'Client',
            'sales-summary',
        );
    }

    public function purchaseSummaryPdf(Request $request)
    {
        return $this->partySummaryPdf(
            $request,
            OutstandingSummary::forSuppliers(Auth::id()),
            'Purchase summary',
            'Supplier',
            'purchase-summary',
        );
    }

    /**
     * @param  Collection<int, object>  $rows
     */
    private function partySummaryPdf(PartySummaryPdfRequest $request, Collection $rows, string $documentTitle, string $partyColumnLabel, string $filePrefix)
    {
        $generatedAt = now()->format('d M Y').' at '.now()->format('H:i');

        $pdf = Pdf::loadView('pdf.party_outstanding_summary', [
            'documentTitle' => $documentTitle,
            'partyColumnLabel' => $partyColumnLabel,
            'rows' => $rows,
            'generatedAt' => $generatedAt,
            'preparedBy' => Auth::user()->name,
            'totalPending' => $rows->sum('pending_total'),
            'totalOverdue' => $rows->sum('overdue_total'),
            'totalNotYetDue' => $rows->sum('not_yet_due'),
        ]);

        $pdf->setPaper('a4', 'landscape');
        $filename = $filePrefix.'-'.now()->format('Y-m-d-His').'.pdf';

        if ($request->boolean('preview')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
