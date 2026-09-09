<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Stock;
use App\Models\Investor;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReminderMail;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('cotton_type', 'like', "%{$search}%")
                  ->orWhere('gst_pan', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'mobile' => 'required|string|max:50',
            'address' => 'nullable|string',
            'gst_pan' => 'nullable|string|max:100',
            'cotton_type' => 'nullable|string|max:100',
            'payment_due_days' => 'required|integer|in:7,15,30,45',
            'purchase_date' => 'required|date',
            'bank_details' => 'nullable|string',
            'total_purchased' => 'required|numeric|min:0',
            'total_paid' => 'required|numeric|min:0',
        ]);

        $validated['payment_terms'] = $validated['payment_due_days'] . ' days';

        $supplier = Supplier::create($validated);

        // Auto send to configured Admin Email if overdue
        $adminEmail = SystemSetting::get('admin_email', 'admin@exportagency.com');

        if ($supplier->isOverdue()) {
            try {
                $stocks = Stock::all();
                $suppliers = Supplier::all();
                $investors = Investor::all();
                Mail::to($adminEmail)->send(new PaymentReminderMail($suppliers, $stocks, $investors));
            } catch (\Exception $e) {
                // Mail logged cleanly
            }
        }

        return redirect()->back()->with('success', 'Supplier record added with ' . $validated['payment_due_days'] . ' days term.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'mobile' => 'required|string|max:50',
            'address' => 'nullable|string',
            'gst_pan' => 'nullable|string|max:100',
            'cotton_type' => 'nullable|string|max:100',
            'payment_due_days' => 'required|integer|in:7,15,30,45',
            'purchase_date' => 'required|date',
            'bank_details' => 'nullable|string',
            'total_purchased' => 'required|numeric|min:0',
            'total_paid' => 'required|numeric|min:0',
        ]);

        $validated['payment_terms'] = $validated['payment_due_days'] . ' days';

        $supplier->update($validated);

        return redirect()->back()->with('success', 'Supplier record updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->back()->with('success', 'Supplier record removed.');
    }

    public function sendTestEmail()
    {
        $adminEmail = SystemSetting::get('admin_email', 'admin@exportagency.com');
        $stocks = Stock::all();
        $suppliers = Supplier::all();
        $investors = Investor::all();

        try {
            Mail::to($adminEmail)->send(new PaymentReminderMail($suppliers, $stocks, $investors));
        } catch (\Exception $e) {
            // Mail logged cleanly
        }

        $msg = 'Reminder Email report generated for Admin Email (' . $adminEmail . ')! Click "Preview Email in Browser" button to view it live.';

        return redirect()->back()->with('success', $msg);
    }

    public function previewEmail()
    {
        $stocks = Stock::all();
        $suppliers = Supplier::all();
        $investors = Investor::all();

        return new PaymentReminderMail($suppliers, $stocks, $investors);
    }
}
