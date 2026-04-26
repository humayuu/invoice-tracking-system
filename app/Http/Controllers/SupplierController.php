<?php

namespace App\Http\Controllers;

use App\Exports\PendingOverdueStatementExport;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Purchase;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $suppliers = Supplier::where('user_id', Auth::id())
            ->orderBy('id', 'DESC');
        if ($request->ajax()) {
            return DataTables::of($suppliers)
                ->addIndexColumn()

                ->addColumn('credit_period', function ($supplier) {
                    return '<span class="badge bg-primary fs-6">'.$supplier->credit_period.' days</span>';
                })

                ->addColumn('action', function ($supplier) {
                    return '
                    <div class="d-flex justify-content-center gap-1">

                        <a href="'.route('supplier.wise.invoices', $supplier->id).'" class="btn btn-sm px-3 btn-dark">
                            Invoices
                        </a>

                        <a href="'.route('supplier.show', $supplier->id).'" class="btn btn-sm btn-dark">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        <a href="'.route('supplier.edit', $supplier->id).'" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <button type="button" class="btn btn-sm btn-danger btn-global-delete-confirm"
                            data-bs-toggle="modal" data-bs-target="#globalDeleteModal"
                            data-delete-url="'.route('supplier.destroy', $supplier->id).'"
                            data-delete-title="Delete supplier?"
                            data-delete-message="'.e('Permanently delete supplier “'.$supplier->name.'”?').'"
                            title="Delete supplier">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </div>
                ';
                })

                ->rawColumns(['credit_period', 'action'])
                ->make(true);
        }

        $supplierCount = $suppliers->count();

        return view('suppliers.index', compact('supplierCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        Supplier::create($validated);

        return redirect()->back()->with([
            'success' => 'Supplier Create Successfully',
            'flash_action' => 'created',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        abort_if($supplier->user_id !== Auth::id(), 403);

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        abort_if($supplier->user_id !== Auth::id(), 403);

        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        abort_if($supplier->user_id !== Auth::id(), 403);

        $supplier->update($request->validated());

        return redirect()->back()->with([
            'success' => 'Supplier updated successfully.',
            'flash_action' => 'updated',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        abort_if($supplier->user_id !== Auth::id(), 403);

        $supplier->delete();

        return redirect()->route('supplier.index')->with([
            'success' => 'Supplier Deleted successfully.',
            'flash_action' => 'deleted',
        ]);

    }

    /**
     * Pending & overdue purchases for a supplier (statement view).
     */
    public function supplierWiseInvoices(int $id)
    {
        $supplier = Supplier::where('user_id', Auth::id())->findOrFail($id);

        $invoices = $this->pendingOverduePurchasesForSupplier($supplier);

        return view('suppliers.supplier_wise', compact('supplier', 'invoices'));
    }

    public function supplierWiseInvoicesPdf(int $id)
    {
        $supplier = Supplier::where('user_id', Auth::id())->findOrFail($id);
        $invoices = $this->pendingOverduePurchasesForSupplier($supplier);

        $pdf = Pdf::loadView('pdf.pending_overdue_statement', [
            'documentTitle' => 'Purchase statement',
            'partyName' => $supplier->name,
            'creditPeriodDays' => $supplier->credit_period,
            'statusLine' => 'Status: Pending & overdue invoices only',
            'invoices' => $invoices,
        ]);
        $pdf->setPaper('a4', 'landscape');
        $safe = preg_replace('/[^A-Za-z0-9._-]+/', '-', $supplier->name) ?: 'supplier';

        return $pdf->download('supplier-statement-'.$safe.'-'.now()->format('d-m-Y').'.pdf');
    }

    public function supplierWiseInvoicesExport(int $id)
    {
        $supplier = Supplier::where('user_id', Auth::id())->findOrFail($id);
        $invoices = $this->pendingOverduePurchasesForSupplier($supplier);
        $safe = preg_replace('/[^A-Za-z0-9._-]+/', '-', $supplier->name) ?: 'supplier';
        $filename = 'supplier-'.$safe.'-pending-overdue-'.now()->format('d-m-Y').'.xlsx';

        return Excel::download(
            new PendingOverdueStatementExport($supplier->name, $invoices),
            $filename
        );
    }

    /**
     * @return Collection<int, Purchase>
     */
    private function pendingOverduePurchasesForSupplier(Supplier $supplier)
    {
        return Purchase::query()
            ->where('user_id', Auth::id())
            ->where('supplier_id', $supplier->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->with(['purchaseItems', 'supplier'])
            ->orderBy('due_date')
            ->orderByDesc('id')
            ->get();
    }
}
