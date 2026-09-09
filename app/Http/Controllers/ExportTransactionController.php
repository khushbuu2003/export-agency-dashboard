<?php

namespace App\Http\Controllers;

use App\Models\ExportTransaction;
use App\Models\Customer;
use App\Models\Stock;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class ExportTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = ExportTransaction::with(['customer', 'stock', 'taxRate']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('destination_port', 'like', "%{$search}%");
        }
        $transactions = $query->latest('export_date')->paginate(10);
        $customers = Customer::all();
        $stocks = Stock::all();
        $taxRates = TaxRate::all();

        return view('export_transactions.index', compact('transactions', 'customers', 'stocks', 'taxRates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_code' => 'required|string|unique:export_transactions,transaction_code',
            'customer_id' => 'required|exists:customers,id',
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'shipping_cost' => 'required|numeric|min:0',
            'payment_status' => 'required|string',
            'export_date' => 'required|date',
            'destination_port' => 'required|string|max:255',
        ]);

        $subtotal = $validated['quantity'] * $validated['unit_price'];
        $taxAmount = 0.00;

        if (!empty($validated['tax_rate_id'])) {
            $taxRateObj = TaxRate::find($validated['tax_rate_id']);
            if ($taxRateObj) {
                $taxAmount = ($subtotal * $taxRateObj->rate) / 100;
            }
        }

        $totalValue = $subtotal + $taxAmount + $validated['shipping_cost'];

        ExportTransaction::create(array_merge($validated, [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_value' => $totalValue,
        ]));

        return redirect()->route('export-transactions.index')->with('success', 'Export transaction created successfully.');
    }

    public function update(Request $request, ExportTransaction $exportTransaction)
    {
        $validated = $request->validate([
            'transaction_code' => 'required|string|unique:export_transactions,transaction_code,' . $exportTransaction->id,
            'customer_id' => 'required|exists:customers,id',
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'shipping_cost' => 'required|numeric|min:0',
            'payment_status' => 'required|string',
            'export_date' => 'required|date',
            'destination_port' => 'required|string|max:255',
        ]);

        $subtotal = $validated['quantity'] * $validated['unit_price'];
        $taxAmount = 0.00;

        if (!empty($validated['tax_rate_id'])) {
            $taxRateObj = TaxRate::find($validated['tax_rate_id']);
            if ($taxRateObj) {
                $taxAmount = ($subtotal * $taxRateObj->rate) / 100;
            }
        }

        $totalValue = $subtotal + $taxAmount + $validated['shipping_cost'];

        $exportTransaction->update(array_merge($validated, [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_value' => $totalValue,
        ]));

        return redirect()->route('export-transactions.index')->with('success', 'Export transaction updated successfully.');
    }

    public function destroy(ExportTransaction $exportTransaction)
    {
        $exportTransaction->delete();
        return redirect()->route('export-transactions.index')->with('success', 'Export transaction deleted successfully.');
    }
}
