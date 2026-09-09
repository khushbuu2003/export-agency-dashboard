<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $stocks = $query->latest()->paginate(10);
        $categories = Stock::distinct()->pluck('category')->filter();

        return view('stocks.index', compact('stocks', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:stocks,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'total_qty' => 'required|integer|min:0',
            'reserved_qty' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        Stock::create($validated);

        return redirect()->route('stocks.index')->with('success', 'Stock item created successfully.');
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:stocks,code,' . $stock->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'total_qty' => 'required|integer|min:0',
            'reserved_qty' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        $stock->update($validated);

        return redirect()->route('stocks.index')->with('success', 'Stock item updated successfully.');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Stock item deleted successfully.');
    }
}
