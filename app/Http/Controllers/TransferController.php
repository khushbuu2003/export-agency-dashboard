<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Stock;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $query = Transfer::with('stock');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('transfer_code', 'like', "%{$search}%")
                  ->orWhere('sender_party', 'like', "%{$search}%")
                  ->orWhere('receiver_party', 'like', "%{$search}%");
        }
        $transfers = $query->latest()->paginate(10);
        $stocks = Stock::all();
        return view('transfers.index', compact('transfers', 'stocks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transfer_code' => 'required|string|unique:transfers,transfer_code',
            'stock_id' => 'required|exists:stocks,id',
            'qty' => 'required|integer|min:1',
            'sender_party' => 'required|string|max:255',
            'receiver_party' => 'required|string|max:255',
            'transfer_date' => 'required|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        Transfer::create($validated);
        return redirect()->route('transfers.index')->with('success', 'Transfer order created successfully.');
    }

    public function update(Request $request, Transfer $transfer)
    {
        $validated = $request->validate([
            'transfer_code' => 'required|string|unique:transfers,transfer_code,' . $transfer->id,
            'stock_id' => 'required|exists:stocks,id',
            'qty' => 'required|integer|min:1',
            'sender_party' => 'required|string|max:255',
            'receiver_party' => 'required|string|max:255',
            'transfer_date' => 'required|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $transfer->update($validated);
        return redirect()->route('transfers.index')->with('success', 'Transfer order updated successfully.');
    }

    public function destroy(Transfer $transfer)
    {
        $transfer->delete();
        return redirect()->route('transfers.index')->with('success', 'Transfer order deleted successfully.');
    }
}
