<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index()
    {
        $taxRates = TaxRate::latest()->paginate(10);
        return view('tax_rates.index', compact('taxRates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        TaxRate::create($validated);
        return redirect()->route('tax-rates.index')->with('success', 'Tax rate rule added.');
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $taxRate->update($validated);
        return redirect()->route('tax-rates.index')->with('success', 'Tax rate rule updated.');
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();
        return redirect()->route('tax-rates.index')->with('success', 'Tax rate rule deleted.');
    }
}
