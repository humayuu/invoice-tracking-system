<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

                        <a href="'.route('supplier.show', $supplier->id).'" class="btn btn-sm btn-dark">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        <a href="'.route('supplier.edit', $supplier->id).'" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <form method="POST" action="'.route('supplier.destroy', $supplier->id).'" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\')">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'credit_period' => 'required|in:15,30,45,60',
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'nullable|string|min:11|max:15',
            'address' => 'required|string|max:500',
        ]);

        $validated['user_id'] = Auth::id();

        Supplier::create($validated);

        return redirect()->back()->with('success', 'Supplier Create Successfully');
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
    public function update(Request $request, Supplier $supplier)
    {
        abort_if($supplier->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'credit_period' => 'required|integer|in:15,30,45,60',
            'email' => 'nullable|email|unique:clients,email,'.$supplier->id,
            'phone' => 'nullable|string|min:11|max:15',
            'address' => 'required|string|max:500',
        ]);

        $supplier->update($validated);

        return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        abort_if($supplier->user_id !== Auth::id(), 403);

        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier Deleted successfully.');

    }
}
