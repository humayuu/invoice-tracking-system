<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::with(['salesItems', 'client'])
            ->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->get();

        $salesCount = $sales->count();

        return view('sales.index', compact('sales', 'salesCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all();

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

            $sale = Sale::create([
                'user_id' => Auth::id(),
                'client_id' => $request->client_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $dueDate,
                'po_no' => $request->po_no,
                'invoice_no' => Sale::generateInvoiceNo(),
                'note' => $request->note,
                'amount' => $amount,
                'status' => 'pending',
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
        $clients = Client::all();
        abort_if($sale->user_id !== Auth::id(), 403);
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

            $sale->update([
                'client_id' => $request->client_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $dueDate,
                'po_no' => $request->po_no,
                'note' => $request->note,
                'amount' => $amount,
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
        $sale = Sale::find($id);
        abort_if($sale->user_id !== Auth::id(), 403);

        $sale->update(['status' => 'paid']);

        return redirect()->route('sales.index')->with('success', 'Status updated successfully!');

    }
}
