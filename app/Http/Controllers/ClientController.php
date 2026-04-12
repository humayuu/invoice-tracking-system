<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clients = Client::where('user_id', Auth::id())
            ->orderBy('id', 'DESC');
        if ($request->ajax()) {
            return DataTables::of($clients)
                ->addIndexColumn()

                ->addColumn('credit_period', function ($client) {
                    return '<span class="badge bg-primary fs-6">'.$client->credit_period.' days</span>';
                })

                ->addColumn('action', function ($client) {
                    return '
                    <div class="d-flex justify-content-center gap-1">

                     <a target="_blank" href="'.route('client.wise.invoices', $client->id).'" class="btn btn-sm px-3 btn-dark">
                            Invoices
                        </a>

                        <a href="'.route('client.show', $client->id).'" class="btn btn-sm btn-dark">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        <a href="'.route('client.edit', $client->id).'" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <form method="POST" action="'.route('client.destroy', $client->id).'" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\')">
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

        $clientCount = $clients->count();

        return view('clients.index', compact('clientCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'credit_period' => 'required|in:15,30,45,60',
            'email' => [
                'nullable',
                'email',
                Rule::unique('clients', 'email')->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'phone' => 'nullable|string|min:11|max:15',
            'address' => 'required|string|max:500',
        ]);

        $validated['user_id'] = Auth::id();

        Client::create($validated);

        return redirect()->back()->with([
            'success' => 'Client Create Successfully',
            'flash_action' => 'created',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        abort_if($client->user_id !== Auth::id(), 403);

        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        abort_if($client->user_id !== Auth::id(), 403);

        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        abort_if($client->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'credit_period' => 'required|integer|in:15,30,45,60',
            'email' => [
                'nullable',
                'email',
                Rule::unique('clients', 'email')
                    ->where(fn ($q) => $q->where('user_id', Auth::id()))
                    ->ignore($client->id),
            ],
            'phone' => 'nullable|string|min:11|max:15',
            'address' => 'required|string|max:500',
        ]);

        $client->update($validated);

        return redirect()->back()->with([
            'success' => 'Client updated successfully.',
            'flash_action' => 'updated',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        abort_if($client->user_id !== Auth::id(), 403);

        $client->delete();

        return redirect()->back()->with([
            'success' => 'Client Deleted successfully.',
            'flash_action' => 'deleted',
        ]);

    }

    /**
     * For Client Wise Invoice
     */
    public function clientWiseInvoices($id)
    {
        $client = Client::where('user_id', Auth::id())->findOrFail($id);

        $invoices = Sale::where('user_id', Auth::id())
            ->where('client_id', $client->id)
            ->with('client')
            ->orderByDesc('id')
            ->get();

        return view('clients.client_wise', compact('client', 'invoices'));
    }
}
