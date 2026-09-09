@extends('layouts.app')

@section('title', 'Export Agency Dashboard')

@section('content')
<div class="space-y-8">
    
    <!-- Dashboard Top Bar Banner -->
    <div class="light-card rounded-2xl p-6 relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gradient-to-r from-white via-indigo-50/40 to-slate-50 border-slate-200">
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-gauge-high"></i> Master Agency Dashboard
            </span>
            <h1 class="text-2xl font-bold font-heading text-slate-900">Export Agency Management System</h1>
            <p class="text-slate-500 text-sm mt-1">Direct management for Stock Inventory, Cotton Suppliers, Purchases, Payments (₹ Rupees), and Due Reminders.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <!-- Send Test Email Button -->
            <form action="{{ route('suppliers.send-test-email') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm shadow-sm flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-paper-plane text-amber-400"></i> Generate Test Email
                </button>
            </form>

            <a href="{{ route('emails.preview') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm shadow-md shadow-purple-600/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-eye text-purple-200"></i> Preview Email in Browser
            </a>

            <button onclick="openModal('addStockModal')" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-plus"></i> Add Stock Item
            </button>
            <button onclick="openModal('addSupplierModal')" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-user-plus"></i> Add Supplier
            </button>
        </div>
    </div>

    <!-- Clean Streamlined Supplier Summary Cards (In Rupees ₹) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- 1. Total Suppliers -->
        <div class="light-card rounded-2xl p-5 hover:border-indigo-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Suppliers</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-truck-field text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-indigo-700 font-heading">{{ $totalSuppliersCount }} <span class="text-xs font-normal text-slate-500">registered</span></div>
                <div class="text-xs text-slate-500 mt-1">Active cotton & material vendors</div>
            </div>
        </div>

        <!-- 2. Total Purchased (₹ Rupees) -->
        <div class="light-card rounded-2xl p-5 hover:border-blue-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Purchased</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-cart-shopping text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-slate-900 font-heading">₹ {{ number_format($totalSupplierPurchased, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Cumulative supplier procurement</div>
            </div>
        </div>

        <!-- 3. Total Paid (₹ Rupees) -->
        <div class="light-card rounded-2xl p-5 hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Paid</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-emerald-600 font-heading">₹ {{ number_format($totalSupplierPaid, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Settled supplier payouts</div>
            </div>
        </div>

        <!-- 4. Supplier Balance Due (₹ Rupees) -->
        <div class="light-card rounded-2xl p-5 hover:border-amber-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Supplier Balance Due</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-amber-600 font-heading">₹ {{ number_format($supplierBalanceDue, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Pending supplier balance</div>
            </div>
        </div>

    </div>

    <!-- ================================================================= -->
    <!-- SECTION 1: EMBEDDED SUPPLIER & COTTON MANAGEMENT MODULE -->
    <!-- ================================================================= -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold uppercase tracking-wider">Sourcing & Cotton Module</span>
                </div>
                <h2 class="text-xl font-bold font-heading text-slate-900 mt-1">Supplier Management</h2>
                <p class="text-slate-500 text-xs mt-0.5">Manage raw cotton suppliers, company names, contact persons, mobile numbers, GST/PAN numbers, cotton types, payment terms (7, 15, 30, 45 days), total purchased, total paid, and due alerts.</p>
            </div>

            <button onclick="openModal('addSupplierModal')" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 flex items-center gap-2 transition-all self-start sm:self-auto">
                <i class="fa-solid fa-plus"></i> Add New Supplier
            </button>
        </div>

        <!-- Filter & Search Toolbar for Suppliers -->
        <div class="light-card rounded-2xl p-4 flex items-center justify-between">
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-3 w-full sm:w-96">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="supplier_search" value="{{ request('supplier_search') }}" placeholder="Search supplier name, company, cotton type, GST..." class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold">Search Supplier</button>
                @if(request()->has('supplier_search'))
                    <a href="{{ route('dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Supplier Data Table (Rupees ₹ + Due Status) -->
        <div class="light-card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-4 px-5">Supplier & Company Name</th>
                            <th class="py-4 px-5">Contact & Mobile</th>
                            <th class="py-4 px-5">Cotton Type</th>
                            <th class="py-4 px-5">Payment Term & Due Date</th>
                            <th class="py-4 px-5 text-right">Total Purchased</th>
                            <th class="py-4 px-5 text-right">Total Paid</th>
                            <th class="py-4 px-5 text-right">Balance Due</th>
                            <th class="py-4 px-5 text-center">Overdue Status</th>
                            <th class="py-4 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($suppliers as $sup)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900">{{ $sup->company_name }}</div>
                                    <div class="text-xs font-semibold text-indigo-600">{{ $sup->supplier_name }}</div>
                                    @if($sup->gst_pan)
                                        <div class="text-[11px] text-slate-500 font-mono mt-0.5">GST/PAN: {{ $sup->gst_pan }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-semibold text-slate-800">{{ $sup->contact_person }}</div>
                                    <div class="text-xs text-slate-500"><i class="fa-solid fa-phone text-slate-400 mr-1"></i>{{ $sup->mobile }}</div>
                                </td>
                                <td class="py-4 px-5">
                                    <span class="px-2.5 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold">
                                        <i class="fa-solid fa-leaf mr-1 text-emerald-600"></i>{{ $sup->cotton_type ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-xs">
                                    <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $sup->payment_terms }}</span>
                                    <div class="text-slate-500 mt-1">Due: {{ date('d M Y', strtotime($sup->dueDate)) }}</div>
                                </td>
                                <td class="py-4 px-5 text-right font-bold text-slate-900">₹ {{ number_format($sup->total_purchased, 2) }}</td>
                                <td class="py-4 px-5 text-right font-bold text-emerald-600">₹ {{ number_format($sup->total_paid, 2) }}</td>
                                <td class="py-4 px-5 text-right font-bold {{ $sup->balanceDue() > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                    ₹ {{ number_format($sup->balanceDue(), 2) }}
                                </td>
                                <td class="py-4 px-5 text-center">
                                    @if($sup->isOverdue())
                                        <span class="px-2.5 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold animate-pulse">
                                            ⚠️ Overdue
                                        </span>
                                    @elseif($sup->balanceDue() <= 0)
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                                            Settled
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold">
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right space-x-2">
                                    <button onclick='editSupplier({{ json_encode($sup) }})' class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <button onclick="confirmDeleteSupplier({{ $sup->id }})" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition-all">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">No supplier records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $suppliers->appends(request()->except('supplier_page'))->links() }}
            </div>
        </div>
    </div>


    <!-- ================================================================= -->
    <!-- SECTION 2: EMBEDDED STOCK & INVENTORY MANAGEMENT MODULE -->
    <!-- ================================================================= -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-200">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold uppercase tracking-wider">Main Inventory Module</span>
                </div>
                <h2 class="text-xl font-bold font-heading text-slate-900 mt-1">Stock & Inventory Management</h2>
                <p class="text-slate-500 text-xs mt-0.5">Manage stock items, item codes (e.g. EXP-1001), total quantity, reserved quantity, available quantity, unit cost (₹), and status.</p>
            </div>

            <button onclick="openModal('addStockModal')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 flex items-center gap-2 transition-all self-start sm:self-auto">
                <i class="fa-solid fa-plus"></i> Add Stock Item
            </button>
        </div>

        <!-- Filter & Search Toolbar for Stock -->
        <div class="light-card rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 sm:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="stock_search" value="{{ request('stock_search') }}" placeholder="Search code (EXP-1001), name..." class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-sm text-slate-900 focus:outline-none focus:border-emerald-500">
                </div>

                <select name="category" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-emerald-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold transition-all">Filter Stock</button>
                @if(request()->hasAny(['stock_search', 'category']))
                    <a href="{{ route('dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Reset Filter</a>
                @endif
            </form>
        </div>

        <!-- Stock Data Table (Rupees ₹) -->
        <div class="light-card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-4 px-5">Stock/Item Code</th>
                            <th class="py-4 px-5">Product Name</th>
                            <th class="py-4 px-5">Product Category</th>
                            <th class="py-4 px-5 text-right">Total Qty</th>
                            <th class="py-4 px-5 text-right">Reserved Qty</th>
                            <th class="py-4 px-5 text-right">Available Qty</th>
                            <th class="py-4 px-5 text-right">Unit Cost</th>
                            <th class="py-4 px-5 text-center">Status</th>
                            <th class="py-4 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($stocks as $stock)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-5">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 font-mono font-bold text-xs">
                                        {{ $stock->code }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 font-bold text-slate-900">{{ $stock->name }}</td>
                                <td class="py-4 px-5">
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs">
                                        {{ $stock->category }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-right font-medium text-slate-800">{{ number_format($stock->total_qty) }} <span class="text-xs text-slate-400">{{ $stock->unit }}</span></td>
                                <td class="py-4 px-5 text-right font-medium text-amber-600">{{ number_format($stock->reserved_qty) }}</td>
                                <td class="py-4 px-5 text-right font-bold text-emerald-600">{{ number_format($stock->availableQty()) }}</td>
                                <td class="py-4 px-5 text-right font-semibold text-slate-800">₹ {{ number_format($stock->unit_cost, 2) }}</td>
                                <td class="py-4 px-5 text-center">
                                    @if($stock->status === 'In Stock')
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">In Stock</span>
                                    @elseif($stock->status === 'Low Stock')
                                        <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold">Low Stock</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-semibold">Out of Stock</span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right space-x-2">
                                    <button onclick='editStock({{ json_encode($stock) }})' class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <button onclick="confirmDeleteStock({{ $stock->id }}, '{{ $stock->code }}')" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">No stock items found in inventory.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $stocks->appends(request()->except('stock_page'))->links() }}
            </div>
        </div>
    </div>

    <!-- Recent Export Transactions Table (Rupees ₹) -->
    <div class="light-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold font-heading text-slate-900">Recent Export Orders</h2>
                <p class="text-slate-500 text-xs mt-0.5">Latest international export shipments recorded</p>
            </div>
            <a href="{{ route('export-transactions.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1.5">
                View All Transactions <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Invoice Code</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Product Stock</th>
                        <th class="py-3 px-4">Destination Port</th>
                        <th class="py-3 px-4 text-right">Total Value</th>
                        <th class="py-3 px-4 text-center">Payment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-brand-600">{{ $tx->transaction_code }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $tx->customer->company_name ?? 'N/A' }}</td>
                            <td class="py-3.5 px-4 text-slate-700">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-xs font-mono text-slate-600 mr-1.5">{{ $tx->stock->code ?? '' }}</span>
                                {{ $tx->stock->name ?? 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> {{ $tx->destination_port }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-600">₹ {{ number_format($tx->total_value, 2) }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if($tx->payment_status === 'Paid')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">Paid</span>
                                @elseif($tx->payment_status === 'Pending')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold">Pending</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold">{{ $tx->payment_status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">No export transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ========================================== -->
<!-- MODALS FOR STOCK MANAGEMENT ON DASHBOARD   -->
<!-- ========================================== -->

<!-- Modal: Add Stock Item -->
<div id="addStockModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="light-card rounded-2xl w-full max-w-lg overflow-hidden shadow-xl border border-slate-200">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-lg font-bold font-heading text-slate-900">Add New Stock Item</h3>
            <button onclick="closeModal('addStockModal')" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('stocks.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Stock/Item Code</label>
                    <input type="text" name="code" placeholder="e.g. EXP-1006" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 font-mono focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Product Category</label>
                    <input type="text" name="category" placeholder="e.g. Textile" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Product Name</label>
                <input type="text" name="name" placeholder="e.g. Cotton Fabric" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Total Qty</label>
                    <input type="number" name="total_qty" value="1000" min="0" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Reserved Qty</label>
                    <input type="number" name="reserved_qty" value="0" min="0" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Unit</label>
                    <input type="text" name="unit" value="Meters" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Unit Cost (₹ Rupees)</label>
                    <input type="number" step="0.01" name="unit_cost" value="500.00" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                        <option value="In Stock">In Stock</option>
                        <option value="Low Stock">Low Stock</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-200">
                <button type="button" onclick="closeModal('addStockModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold">Save Stock Item</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Stock Item -->
<div id="editStockModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="light-card rounded-2xl w-full max-w-lg overflow-hidden shadow-xl border border-slate-200">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-lg font-bold font-heading text-slate-900">Edit Stock Item</h3>
            <button onclick="closeModal('editStockModal')" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="editStockForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Stock/Item Code</label>
                    <input type="text" id="edit_code" name="code" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 font-mono focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Product Category</label>
                    <input type="text" id="edit_category" name="category" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Product Name</label>
                <input type="text" id="edit_name" name="name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Total Qty</label>
                    <input type="number" id="edit_total_qty" name="total_qty" min="0" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Reserved Qty</label>
                    <input type="number" id="edit_reserved_qty" name="reserved_qty" min="0" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Unit</label>
                    <input type="text" id="edit_unit" name="unit" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Unit Cost (₹ Rupees)</label>
                    <input type="number" step="0.01" id="edit_unit_cost" name="unit_cost" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select id="edit_status" name="status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
                        <option value="In Stock">In Stock</option>
                        <option value="Low Stock">Low Stock</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-200">
                <button type="button" onclick="closeModal('editStockModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold">Update Item</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteStockForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>


<!-- ========================================== -->
<!-- MODALS FOR SUPPLIER MANAGEMENT ON DASHBOARD-->
<!-- ========================================== -->

<!-- Modal: Add Supplier -->
<div id="addSupplierModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="light-card rounded-2xl w-full max-w-2xl overflow-hidden shadow-xl border border-slate-200">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-lg font-bold font-heading text-slate-900">Add Supplier & Cotton Entry</h3>
            <button onclick="closeModal('addSupplierModal')" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('suppliers.store') }}" method="POST" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Supplier Name</label>
                    <input type="text" name="supplier_name" placeholder="e.g. Rajesh Sharma" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Company Name</label>
                    <input type="text" name="company_name" placeholder="e.g. Gujarat Long Staple Cotton Mills" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" placeholder="e.g. Rajesh Sharma" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Mobile / Phone Number</label>
                    <input type="text" name="mobile" placeholder="e.g. +91 98250 11223" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">GST / PAN Number</label>
                    <input type="text" name="gst_pan" placeholder="e.g. 24AAACG1234F1Z5" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 font-mono focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cotton Type</label>
                    <input type="text" name="cotton_type" placeholder="e.g. Shankar-6, MCU-5, Organic" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- PAYMENT DUE DAYS SELECTION (REQUIRED) -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-indigo-700 mb-1">Payment Term Selection (Due Days)</label>
                    <select name="payment_due_days" required class="w-full bg-indigo-50/50 border border-indigo-300 rounded-xl px-3 py-2 text-sm font-bold text-indigo-900 focus:outline-none focus:border-indigo-600">
                        <option value="7" selected>7 days</option>
                        <option value="15">15 days</option>
                        <option value="30">30 days</option>
                        <option value="45">45 days</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Purchase / Entry Date</label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Total Purchased Amount (₹ Rupees)</label>
                    <input type="number" step="0.01" name="total_purchased" value="150000.00" min="0" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Total Paid Amount (₹ Rupees)</label>
                    <input type="number" step="0.01" name="total_paid" value="100000.00" min="0" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Bank Details (Account, IFSC / SWIFT)</label>
                <textarea name="bank_details" rows="2" placeholder="e.g. HDFC Bank, A/C: 50200012345678, IFSC: HDFC0000123" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Full Address</label>
                <textarea name="address" rows="2" placeholder="Factory / Office Address..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-200">
                <button type="button" onclick="closeModal('addSupplierModal')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold">Save Supplier & Entry</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Supplier -->
<div id="editSupplierModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="light-card rounded-2xl w-full max-w-2xl overflow-hidden shadow-xl border border-slate-200">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-lg font-bold font-heading text-slate-900">Edit Supplier Details</h3>
            <button onclick="closeModal('editSupplierModal')" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="editSupplierForm" method="POST" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Supplier Name</label>
                    <input type="text" id="edit_sup_name" name="supplier_name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Company Name</label>
                    <input type="text" id="edit_sup_company" name="company_name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Contact Person</label>
                    <input type="text" id="edit_sup_contact" name="contact_person" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Mobile / Phone Number</label>
                    <input type="text" id="edit_sup_mobile" name="mobile" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">GST / PAN Number</label>
                    <input type="text" id="edit_sup_gst_pan" name="gst_pan" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 font-mono focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cotton Type</label>
                    <input type="text" id="edit_sup_cotton" name="cotton_type" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-indigo-700 mb-1">Payment Term Selection (Due Days)</label>
                    <select id="edit_sup_days" name="payment_due_days" required class="w-full bg-indigo-50/50 border border-indigo-300 rounded-xl px-3 py-2 text-sm font-bold text-indigo-900 focus:border-indigo-600">
                        <option value="7">7 days</option>
                        <option value="15">15 days</option>
                        <option value="30">30 days</option>
                        <option value="45">45 days</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Purchase / Entry Date</label>
                    <input type="date" id="edit_sup_date" name="purchase_date" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Total Purchased Amount (₹ Rupees)</label>
                    <input type="number" step="0.01" id="edit_sup_purchased" name="total_purchased" min="0" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Total Paid Amount (₹ Rupees)</label>
                    <input type="number" step="0.01" id="edit_sup_paid" name="total_paid" min="0" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Bank Details</label>
                <textarea id="edit_sup_bank" name="bank_details" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-indigo-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Full Address</label>
                <textarea id="edit_sup_address" name="address" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-indigo-500"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-200">
                <button type="button" onclick="closeModal('editSupplierModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold">Update Supplier</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteSupplierForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // Stock Modal Helper Functions
    function editStock(stock) {
        document.getElementById('editStockForm').action = "/stocks/" + stock.id;
        document.getElementById('edit_code').value = stock.code;
        document.getElementById('edit_name').value = stock.name;
        document.getElementById('edit_category').value = stock.category;
        document.getElementById('edit_total_qty').value = stock.total_qty;
        document.getElementById('edit_reserved_qty').value = stock.reserved_qty;
        document.getElementById('edit_unit').value = stock.unit;
        document.getElementById('edit_unit_cost').value = stock.unit_cost;
        document.getElementById('edit_status').value = stock.status;
        openModal('editStockModal');
    }

    function confirmDeleteStock(id, code) {
        if (confirm("Are you sure you want to delete stock item (" + code + ")?")) {
            const form = document.getElementById('deleteStockForm');
            form.action = "/stocks/" + id;
            form.submit();
        }
    }

    // Supplier Modal Helper Functions
    function editSupplier(sup) {
        document.getElementById('editSupplierForm').action = "/suppliers/" + sup.id;
        document.getElementById('edit_sup_name').value = sup.supplier_name;
        document.getElementById('edit_sup_company').value = sup.company_name;
        document.getElementById('edit_sup_contact').value = sup.contact_person;
        document.getElementById('edit_sup_mobile').value = sup.mobile;
        document.getElementById('edit_sup_gst_pan').value = sup.gst_pan || '';
        document.getElementById('edit_sup_cotton').value = sup.cotton_type || '';
        document.getElementById('edit_sup_days').value = sup.payment_due_days || 7;
        document.getElementById('edit_sup_date').value = sup.purchase_date || '';
        document.getElementById('edit_sup_purchased').value = sup.total_purchased;
        document.getElementById('edit_sup_paid').value = sup.total_paid;
        document.getElementById('edit_sup_bank').value = sup.bank_details || '';
        document.getElementById('edit_sup_address').value = sup.address || '';
        openModal('editSupplierModal');
    }

    function confirmDeleteSupplier(id) {
        if (confirm("Are you sure you want to delete this supplier profile?")) {
            const form = document.getElementById('deleteSupplierForm');
            form.action = "/suppliers/" + id;
            form.submit();
        }
    }
</script>
@endsection
