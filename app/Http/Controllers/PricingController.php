<?php

namespace App\Http\Controllers;

use App\Models\Pricing;
use App\Models\Stock;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index(Request $request)
    {
        $query = Pricing::with('stock');
        $pricings = $query->latest()->paginate(10);
        $stocks = Stock::all();
        return view('pricings.index', compact('pricings', 'stocks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'base_export_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'min_order_qty' => 'required|integer|min:1',
            'effective_date' => 'required|date',
        ]);

        Pricing::create($validated);
        return redirect()->route('pricings.index')->with('success', 'Pricing rule added successfully.');
    }

    public function update(Request $request, Pricing $pricing)
    {
        $validated = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'base_export_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'min_order_qty' => 'required|integer|min:1',
            'effective_date' => 'required|date',
        ]);

        $pricing->update($validated);
        return redirect()->route('pricings.index')->with('success', 'Pricing rule updated successfully.');
    }

    public function destroy(Pricing $pricing)
    {
        $pricing->delete();
        return redirect()->route('pricings.index')->with('success', 'Pricing rule removed successfully.');
    }
}
