<?php

namespace App\Http\Controllers;

use App\Exports\SaleInvoiceExport;
use App\Exports\SalesExport;
use App\Models\Client;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sales = Sale::with('client')
            ->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->select(['id', 'invoice_no', 'invoice_date', 'amount', 'status', 'client_id']);
        if ($request->ajax()) {

            return DataTables::of($sales)
                ->addIndexColumn()

                ->addColumn('invoice_no', function ($sale) {
                    return '#'.$sale->invoice_no;
                })

                ->addColumn('invoice_date', function ($sale) {
                    return $sale->invoice_date->format('d-m-Y');
                })

                ->addColumn('client', function ($sale) {
                    $initials = strtoupper(substr($sale->client->name, 0, 2));
                    $name = $sale->client->name;

                    return '
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary
                                        d-flex align-items-center justify-content-center fw-bold"
                                 style="width:34px; height:34px; font-size:13px;">
                                '.$initials.'
                            </div>
                            <span>'.$name.'</span>
                        </div>';
                })

                // Amount format
                ->addColumn('amount', function ($sale) {
                    return 'Rs. '.number_format($sale->amount);
                })

                // Status badge
                ->addColumn('status', function ($sale) {
                    $badgeClass = match ($sale->status) {
                        'paid' => 'bg-success-subtle text-success-emphasis',
                        'pending' => 'bg-warning-subtle text-warning-emphasis',
                        'overdue' => 'bg-danger-subtle text-danger-emphasis',
                        default => 'bg-secondary-subtle text-secondary-emphasis',
                    };

                    return '<span class="badge rounded-pill '.$badgeClass.' fs-6">'
                           .ucfirst($sale->status).'</span>';
                })

                // Action buttons
                ->addColumn('action', function ($sale) {
                    $buttons = '';

                    if ($sale->status !== 'paid') {
                        $buttons .= '
                            <form action="'.route('sales.status', $sale->id).'"
                                  method="POST" class="d-inline">
                                '.csrf_field().method_field('PUT').'
                                <button type="submit" class="btn btn-sm btn-success rounded-3 me-1">
                                    <i class="fa-solid fa-check me-1"></i>Mark Paid
                                </button>
                            </form>
                            <a href="'.route('sales.edit', $sale->id).'"
                               class="btn btn-sm btn-primary border rounded-3 me-1">
                                <i class="fa-solid fa-pen"></i>
                            </a>';
                    }

                    $buttons .= '
                        <a href="'.route('sales.show', $sale->id).'"
                           class="btn btn-sm btn-dark border rounded-3 me-1">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <form action="'.route('sales.destroy', $sale->id).'"
                              method="POST" class="d-inline">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger border rounded-3">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>';

                    return $buttons;
                })
                ->rawColumns(['client', 'status', 'action'])
                ->make(true);
        }

        $salesCount = $sales->count();

        return view('sales.index', compact('salesCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where('user_id', Auth::id())->get();

        return view('sales.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'po_no' => 'nullable|string|max:50',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.sub_total' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $client = Client::findOrFail($request->client_id);
            $dueDate = Carbon::parse($request->invoice_date)->addDays($client->credit_period);
            $amount = collect($request->items)->sum('sub_total');
            $status = Carbon::today()->gte($dueDate) ? 'overdue' : 'pending';

            $sale = Sale::create([
                'user_id' => Auth::id(),
                'client_id' => $request->client_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $dueDate,
                'po_no' => $request->po_no,
                'invoice_no' => Sale::generateInvoiceNo(),
                'note' => $request->note,
                'amount' => $amount,
                'status' => $status,
            ]);

            foreach ($request->items as $item) {
                $sale->salesItems()->create([
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['sub_total'],
                ]);
            }

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        abort_if($sale->user_id !== Auth::id(), 403);
        $sale->load(['client', 'salesItems']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        abort_if($sale->user_id !== Auth::id(), 403);

        if ($sale->status === 'paid') {
            return redirect()->back();
        }
        $clients = Client::where('user_id', Auth::id())->get();
        $sale->load(['client', 'salesItems']);

        return view('sales.edit', compact('sale', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        abort_if($sale->user_id !== Auth::id(), 403);

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'po_no' => 'nullable|string|max:50',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.sub_total' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $client = Client::findOrFail($request->client_id);
            $dueDate = Carbon::parse($request->invoice_date)->addDays($client->credit_period);
            $amount = collect($request->items)->sum('sub_total');
            $status = Carbon::today()->gte($dueDate) ? 'overdue' : 'pending';

            $sale->update([
                'client_id' => $request->client_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $dueDate,
                'po_no' => $request->po_no,
                'note' => $request->note,
                'amount' => $amount,
                'status' => $status,
            ]);

            $sale->salesItems()->delete();

            foreach ($request->items as $item) {
                $sale->salesItems()->create([
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['sub_total'],
                ]);
            }

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Invoice updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        abort_if($sale->user_id !== Auth::id(), 403);

        DB::transaction(function () use ($sale) {
            $sale->salesItems()->delete();
            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Invoice deleted successfully!');
    }

    /**
     * For Update Sale invoice Status
     */
    public function saleStatus($id)
    {
        $sale = Sale::findOrFail($id);
        abort_if($sale->user_id !== Auth::id(), 403);

        $sale->update(['status' => 'paid']);

        return redirect()->route('sales.index')->with('success', 'Status updated successfully!');

    }

    /**
     * For Export Excel File
     */
    public function export()
    {
        $filename = 'sales-'.now()->format('d-m-Y').'.xlsx';

        return Excel::download(new SalesExport, $filename);
    }

    /**
     * For Export Excel File Invoice
     */
    public function invoiceExport($id)
    {
        $invoice = 'invoice-'.now()->format('d-m-Y').'.xlsx';

        return Excel::download(new SaleInvoiceExport($id), $invoice);
    }

    /**
     * For generate Invoice Pdf
     */
    public function invoicePdf($id)
    {
        $sale = Sale::with(['salesItems', 'client'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.invoice', compact('sale'));

        return $pdf->download('invoice.pdf');
    }
}
