<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseExport;
use App\Exports\PurchaseInvoiceExport;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\Purchase;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
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

    public function index(Request $request)
    {
        $purchases = Purchase::with('supplier')
            ->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->select(['id', 'invoice_no', 'invoice_date', 'amount', 'status', 'supplier_id']);

        if ($request->ajax()) {
            return DataTables::of($purchases)
                ->addIndexColumn()
                ->addColumn('invoice_no', fn ($purchase) => '#'.$purchase->invoice_no)
                ->addColumn('invoice_date', fn ($purchase) => $purchase->invoice_date->format('d-m-Y'))
                ->addColumn('supplier', function ($purchase) {
                    $name = $purchase->supplier->name;
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
                ->addColumn('amount', fn ($purchase) => 'Rs. '.number_format($purchase->amount))
                ->addColumn('status', function ($purchase) {
                    $badgeClass = match ($purchase->status) {
                        'paid' => 'bg-success-subtle text-success-emphasis',
                        'pending' => 'bg-warning-subtle text-warning-emphasis',
                        'overdue' => 'bg-danger-subtle text-danger-emphasis',
                        default => 'bg-secondary-subtle text-secondary-emphasis',
                    };

                    return '<span class="badge rounded-pill '.$badgeClass.' fs-6">'
                        .ucfirst($purchase->status).'</span>';
                })
                ->addColumn('action', function ($purchase) {
                    $buttons = '';

                    if ($purchase->status !== 'paid') {
                        $buttons .= '
                            <form action="'.route('purchase.status', $purchase->id).'"
                                  method="POST" class="d-inline">
                                '.csrf_field().method_field('PUT').'
                                <button type="submit" class="btn btn-sm btn-success rounded-3 me-1">
                                    <i class="fa-solid fa-check me-1"></i>Mark Paid
                                </button>
                            </form>
                            <a href="'.route('purchase.edit', $purchase->id).'"
                               class="btn btn-sm btn-primary border rounded-3 me-1">
                                <i class="fa-solid fa-pen"></i>
                            </a>';
                    }

                    $buttons .= '
                        <a href="'.route('purchase.show', $purchase->id).'"
                           class="btn btn-sm btn-dark border rounded-3 me-1">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <form action="'.route('purchase.destroy', $purchase->id).'"
                              method="POST" class="d-inline">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger border rounded-3">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>';

                    return $buttons;
                })
                ->rawColumns(['supplier', 'status', 'action'])
                ->make(true);
        }

        $purchasesCount = $purchases->count();

        return view('purchase.index', compact('purchasesCount'));
    }

    public function create()
    {
        $suppliers = Supplier::where('user_id', Auth::id())->get();

        return view('purchase.create', compact('suppliers'));
    }

    public function store(StorePurchaseRequest $request)
    {
        try {
            DB::beginTransaction();

            $supplier = Supplier::where('user_id', Auth::id())->findOrFail($request->supplier_id);
            $dueDate = Carbon::parse($request->invoice_date)->addDays($supplier->credit_period);
            $lineItems = $this->normalizedLineItems($request->items);
            $amount = collect($lineItems)->sum('total');
            $status = Carbon::today()->gte($dueDate) ? 'overdue' : 'pending';

            $purchase = Purchase::create([
                'user_id' => Auth::id(),
                'supplier_id' => $request->supplier_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $dueDate,
                'po_no' => $request->po_no,
                'invoice_no' => Purchase::generateInvoiceNo(),
                'note' => $request->note,
                'amount' => $amount,
                'status' => $status,
            ]);

            foreach ($lineItems as $item) {
                $purchase->purchaseItems()->create([
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            DB::commit();

            return redirect()->back()->with([
                'success' => 'Purchase invoice created successfully!',
                'flash_action' => 'created',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }

    public function show(Purchase $purchase)
    {
        abort_if($purchase->user_id !== Auth::id(), 403);
        $purchase->load(['supplier', 'purchaseItems']);

        return view('purchase.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        abort_if($purchase->user_id !== Auth::id(), 403);

        if ($purchase->status === 'paid') {
            return redirect()->route('purchase.show', $purchase)
                ->with('error', 'Paid invoices cannot be edited.');
        }

        $suppliers = Supplier::where('user_id', Auth::id())->get();
        $purchase->load(['supplier', 'purchaseItems']);

        return view('purchase.edit', compact('purchase', 'suppliers'));
    }

    public function update(StorePurchaseRequest $request, Purchase $purchase)
    {
        abort_if($purchase->user_id !== Auth::id(), 403);
        abort_if($purchase->status === 'paid', 403);

        try {
            DB::beginTransaction();

            $supplier = Supplier::where('user_id', Auth::id())->findOrFail($request->supplier_id);
            $dueDate = Carbon::parse($request->invoice_date)->addDays($supplier->credit_period);
            $lineItems = $this->normalizedLineItems($request->items);
            $amount = collect($lineItems)->sum('total');
            $status = Carbon::today()->gte($dueDate) ? 'overdue' : 'pending';

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $dueDate,
                'po_no' => $request->po_no,
                'note' => $request->note,
                'amount' => $amount,
                'status' => $status,
            ]);

            $purchase->purchaseItems()->delete();

            foreach ($lineItems as $item) {
                $purchase->purchaseItems()->create([
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            DB::commit();

            return redirect()->back()->with([
                'success' => 'Purchase invoice updated successfully!',
                'flash_action' => 'updated',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }

    public function destroy(Purchase $purchase)
    {
        abort_if($purchase->user_id !== Auth::id(), 403);

        DB::transaction(function () use ($purchase) {
            $purchase->purchaseItems()->delete();
            $purchase->delete();
        });

        return redirect()->back()->with([
            'success' => 'Purchase invoice deleted successfully!',
            'flash_action' => 'deleted',
        ]);
    }

    public function purchaseStatus(Purchase $purchase)
    {
        abort_if($purchase->user_id !== Auth::id(), 403);
        $purchase->update(['status' => 'paid']);

        return redirect()->back()->with([
            'success' => 'Status updated successfully!',
            'flash_action' => 'updated',
        ]);
    }

    public function export()
    {
        $filename = 'purchases-'.now()->format('d-m-Y').'.xlsx';

        return Excel::download(new PurchaseExport, $filename);
    }

    public function invoiceExport($id)
    {
        $purchase = Purchase::findOrFail($id);
        abort_if($purchase->user_id !== Auth::id(), 403);

        $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $purchase->invoice_no);
        $invoice = 'purchase-invoice-'.$safeName.'-'.now()->format('d-m-Y').'.xlsx';

        return Excel::download(new PurchaseInvoiceExport($id), $invoice);
    }

    public function invoicePdf($id)
    {
        $purchase = Purchase::with(['purchaseItems', 'supplier'])->findOrFail($id);
        abort_if($purchase->user_id !== Auth::id(), 403);

        $pdf = Pdf::loadView('pdf.purchase_invoice', compact('purchase'));

        $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $purchase->invoice_no);

        return $pdf->download('purchase-invoice-'.$safeName.'.pdf');
    }
}
