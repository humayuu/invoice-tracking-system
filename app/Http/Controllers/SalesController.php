<?php

namespace App\Http\Controllers;

use App\Exports\SaleInvoiceExport;
use App\Exports\SalesExport;
use App\Http\Requests\StoreSaleRequest;
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
     * @param  array<int, array{item_name: string, quantity: mixed, price: mixed}>  $items
     * @return array<int, array{item_name: string, quantity: float, price: float, total: float}>
     */
    protected function normalizedLineItems(array $items): array
    {
        $normalized = [];
        foreach ($items as $item) {
            $qty = round((float) $item['quantity'], 2);
            $price = round((float) $item['price'], 2);
            $normalized[] = [
                'item_name' => $item['item_name'],
                'quantity' => $qty,
                'price' => $price,
                'total' => round($qty * $price, 2),
            ];
        }

        return $normalized;
    }

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
                    $name = $sale->client->name;
                    $initials = function_exists('mb_strtoupper')
                        ? mb_strtoupper(mb_substr($name, 0, 2))
                        : strtoupper(substr($name, 0, 2));

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
                        <button type="button" class="btn btn-sm btn-danger border rounded-3 btn-global-delete-confirm"
                            data-bs-toggle="modal" data-bs-target="#globalDeleteModal"
                            data-delete-url="'.route('sales.destroy', $sale->id).'"
                            data-delete-title="Delete invoice?"
                            data-delete-message="'.e('Permanently delete invoice '.$sale->invoice_no.'?').'"
                            title="Delete invoice">
                            <i class="fa-solid fa-trash"></i>
                        </button>';

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
    public function store(StoreSaleRequest $request)
    {

        try {
            DB::beginTransaction();

            $client = Client::where('user_id', Auth::id())->findOrFail($request->client_id);
            $dueDate = Carbon::parse($request->invoice_date)->addDays($client->credit_period);
            $lineItems = $this->normalizedLineItems($request->items);
            $amount = collect($lineItems)->sum('total');
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

            foreach ($lineItems as $item) {
                $sale->salesItems()->create([
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            DB::commit();

            return redirect()->back()->with([
                'success' => 'Invoice created successfully!',
                'flash_action' => 'created',
            ]);

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
            return redirect()->route('sales.show', $sale)
                ->with('error', 'Paid invoices cannot be edited.');
        }
        $clients = Client::where('user_id', Auth::id())->get();
        $sale->load(['client', 'salesItems']);

        return view('sales.edit', compact('sale', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSaleRequest $request, Sale $sale)
    {
        abort_if($sale->user_id !== Auth::id(), 403);
        abort_if($sale->status === 'paid', 403);

        try {
            DB::beginTransaction();

            $client = Client::where('user_id', Auth::id())->findOrFail($request->client_id);
            $dueDate = Carbon::parse($request->invoice_date)->addDays($client->credit_period);
            $lineItems = $this->normalizedLineItems($request->items);
            $amount = collect($lineItems)->sum('total');
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

            foreach ($lineItems as $item) {
                $sale->salesItems()->create([
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            DB::commit();

            return redirect()->back()->with([
                'success' => 'Invoice updated successfully!',
                'flash_action' => 'updated',
            ]);

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

        return redirect()->back()->with([
            'success' => 'Invoice deleted successfully!',
            'flash_action' => 'deleted',
        ]);
    }

    /**
     * For Update Sale invoice Status
     */
    public function saleStatus(Sale $sale)
    {
        abort_if($sale->user_id !== Auth::id(), 403);

        $sale->update(['status' => 'paid']);

        return redirect()->back()->with([
            'success' => 'Status updated successfully!',
            'flash_action' => 'updated',
        ]);
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
        $sale = Sale::findOrFail($id);
        abort_if($sale->user_id !== Auth::id(), 403);

        $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $sale->invoice_no);
        $invoice = 'invoice-'.$safeName.'-'.now()->format('d-m-Y').'.xlsx';

        return Excel::download(new SaleInvoiceExport($id), $invoice);
    }

    /**
     * For generate Invoice Pdf
     */
    public function invoicePdf($id)
    {
        $sale = Sale::with(['salesItems', 'client'])->findOrFail($id);
        abort_if($sale->user_id !== Auth::id(), 403);

        $pdf = Pdf::loadView('pdf.invoice', compact('sale'));

        $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $sale->invoice_no);

        return $pdf->download('invoice-'.$safeName.'.pdf');
    }
}
