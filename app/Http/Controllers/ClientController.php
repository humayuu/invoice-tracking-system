<?php

namespace App\Http\Controllers;

use App\Exports\PendingOverdueStatementExport;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
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

                     <a href="'.route('client.wise.invoices', $client->id).'" class="btn btn-sm px-3 btn-dark">
                            Invoices
                        </a>

                        <a href="'.route('client.show', $client->id).'" class="btn btn-sm btn-dark">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        <a href="'.route('client.edit', $client->id).'" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <button type="button" class="btn btn-sm btn-danger btn-global-delete-confirm"
                            data-bs-toggle="modal" data-bs-target="#globalDeleteModal"
                            data-delete-url="'.route('client.destroy', $client->id).'"
                            data-delete-title="Delete client?"
                            data-delete-message="'.e('Permanently delete client “'.$client->name.'”?').'"
                            title="Delete client">
                            <i class="fa-solid fa-trash"></i>
                        </button>

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
    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();
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
    public function update(UpdateClientRequest $request, Client $client)
    {
        abort_if($client->user_id !== Auth::id(), 403);

        $client->update($request->validated());

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
     * Pending & overdue sales for a client (statement view).
     */
    public function clientWiseInvoices($id)
    {
        $client = Client::where('user_id', Auth::id())->findOrFail($id);

        $invoices = $this->pendingOverdueSalesForClient($client);

        return view('clients.client_wise', compact('client', 'invoices'));
    }

    public function clientWiseInvoicesPdf(int $id)
    {
        $client = Client::where('user_id', Auth::id())->findOrFail($id);
        $invoices = $this->pendingOverdueSalesForClient($client);

        $pdf = Pdf::loadView('pdf.pending_overdue_statement', [
            'documentTitle' => 'Invoice statement',
            'partyName' => $client->name,
            'creditPeriodDays' => $client->credit_period,
            'statusLine' => 'Status: Pending & overdue invoices only',
            'invoices' => $invoices,
        ]);
        $pdf->setPaper('a4', 'landscape');
        $safe = preg_replace('/[^A-Za-z0-9._-]+/', '-', $client->name) ?: 'client';

        return $pdf->download('client-statement-'.$safe.'-'.now()->format('d-m-Y').'.pdf');
    }

    public function clientWiseInvoicesExport(int $id)
    {
        $client = Client::where('user_id', Auth::id())->findOrFail($id);
        $invoices = $this->pendingOverdueSalesForClient($client);
        $safe = preg_replace('/[^A-Za-z0-9._-]+/', '-', $client->name) ?: 'client';
        $filename = 'client-'.$safe.'-pending-overdue-'.now()->format('d-m-Y').'.xlsx';

        return Excel::download(
            new PendingOverdueStatementExport($client->name, $invoices),
            $filename
        );
    }

    /**
     * @return Collection<int, Sale>
     */
    private function pendingOverdueSalesForClient(Client $client)
    {
        return Sale::query()
            ->where('user_id', Auth::id())
            ->where('client_id', $client->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->with(['salesItems', 'client'])
            ->orderBy('due_date')
            ->orderByDesc('id')
            ->get();
    }
}
