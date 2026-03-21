<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::all();

        return view('clients.index', compact('clients'));
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
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'nullable|string|min:11|max:15',
            'address' => 'required|string|max:500',
        ]);

        $validated['user_id'] = Auth::id();

        Client::create($validated);

        return redirect()->back()->with('success', 'Client Create Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'credit_period' => 'required|integer|in:15,30,45,60',
            'email' => 'nullable|email|unique:clients,email,'.$client->id,
            'phone' => 'nullable|string|min:11|max:15',
            'address' => 'required|string|max:500',
        ]);

        $client->update($validated);

        return redirect()->route('client.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('client.index')->with('success', 'Client Deleted successfully.');

    }
}
