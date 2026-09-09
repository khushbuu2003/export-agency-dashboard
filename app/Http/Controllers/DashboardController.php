<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Investor;
use App\Models\Transfer;
use App\Models\Pricing;
use App\Models\TaxRate;
use App\Models\Customer;
use App\Models\ExportTransaction;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Stock / Inventory query for Dashboard embedded module
        $stockQuery = Stock::query();

        if ($request->filled('stock_search')) {
            $search = $request->stock_search;
            $stockQuery->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $stockQuery->where('category', $request->category);
        }

        $stocks = $stockQuery->latest()->paginate(10, ['*'], 'stock_page');
        $categories = Stock::distinct()->pluck('category')->filter();

        // 2. Supplier & Cotton query for Dashboard embedded module
        $supplierQuery = Supplier::query();

        if ($request->filled('supplier_search')) {
            $search = $request->supplier_search;
            $supplierQuery->where(function($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('cotton_type', 'like', "%{$search}%")
                  ->orWhere('gst_pan', 'like', "%{$search}%");
            });
        }

        $suppliers = $supplierQuery->latest()->paginate(10, ['*'], 'supplier_page');

        // 3. Summary KPI Card metrics
        $totalStockCount = Stock::count();
        $totalStockQty = Stock::sum('total_qty');
        
        $totalReservedQty = Stock::sum('reserved_qty');
        $availableStockQty = max(0, $totalStockQty - $totalReservedQty);

        // Supplier Metrics
        $totalSuppliersCount = Supplier::count();
        $totalSupplierPurchased = Supplier::sum('total_purchased');
        $totalSupplierPaid = Supplier::sum('total_paid');
        $supplierBalanceDue = max(0, $totalSupplierPurchased - $totalSupplierPaid);

        $totalInvestorsCount = Investor::count();
        $totalInvestmentValue = Investor::sum('investment_amount');

        $totalTransfersCount = Transfer::count();
        $pendingTransfersCount = Transfer::where('status', 'Pending')->count();

        $totalExportValue = ExportTransaction::sum('total_value');
        $totalTaxCollected = ExportTransaction::sum('tax_amount');

        // Sender Party Name
        $senderPartyName = Transfer::select('sender_party')
            ->groupBy('sender_party')
            ->orderByRaw('COUNT(*) DESC')
            ->pluck('sender_party')
            ->first() ?? 'Apex Logistics Hub';

        // Monthly Sales / Exports Data for chart
        $monthlyExportData = ExportTransaction::selectRaw("strftime('%Y-%m', export_date) as month, SUM(total_value) as total, SUM(tax_amount) as tax_total, COUNT(*) as tx_count")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $chartLabels = [];
        $chartValues = [];
        foreach ($monthlyExportData as $row) {
            $formattedMonth = date("M Y", strtotime($row->month . "-01"));
            $chartLabels[] = $formattedMonth;
            $chartValues[] = (float) $row->total;
        }

        // Recent export transactions
        $recentTransactions = ExportTransaction::with(['customer', 'stock', 'taxRate'])
            ->latest('export_date')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'stocks',
            'categories',
            'suppliers',
            'totalStockCount',
            'totalStockQty',
            'availableStockQty',
            'totalSuppliersCount',
            'totalSupplierPurchased',
            'totalSupplierPaid',
            'supplierBalanceDue',
            'totalInvestorsCount',
            'totalInvestmentValue',
            'totalTransfersCount',
            'pendingTransfersCount',
            'totalExportValue',
            'totalTaxCollected',
            'senderPartyName',
            'chartLabels',
            'chartValues',
            'recentTransactions'
        ));
    }
}
