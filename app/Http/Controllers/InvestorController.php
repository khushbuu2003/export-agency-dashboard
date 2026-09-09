<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index(Request $request)
    {
        $query = Investor::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        $investors = $query->latest()->paginate(10);
        return view('investors.index', compact('investors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'investment_amount' => 'required|numeric|min:0',
            'amount_received' => 'required|numeric|min:0',
            'share_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|string',
        ]);

        Investor::create($validated);
        return redirect()->route('investors.index')->with('success', 'Investor profile added successfully.');
    }

    public function update(Request $request, Investor $investor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'investment_amount' => 'required|numeric|min:0',
            'amount_received' => 'required|numeric|min:0',
            'share_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|string',
        ]);

        $investor->update($validated);
        return redirect()->route('investors.index')->with('success', 'Investor profile updated successfully.');
    }

    public function destroy(Investor $investor)
    {
        $investor->delete();
        return redirect()->route('investors.index')->with('success', 'Investor removed successfully.');
    }
}
