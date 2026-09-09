@extends('layouts.app')

@section('title', 'Stock & Inventory Management')

@section('content')
<div class="space-y-6">
    
    <!-- Module Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold uppercase tracking-wider">Main Module</span>
                <span class="text-slate-400 text-xs">• Inventory Master</span>
            </div>
            <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Stock Management</h1>
            <p class="text-slate-500 text-xs mt-0.5">Manage export stock items, item codes (e.g. EXP-1001), quantities, reserved items, and costs.</p>
        </div>

        <button onclick="openModal('addStockModal')" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 flex items-center gap-2 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i> Add New Stock Item
        </button>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="light-card rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('stocks.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 sm:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code (EXP-1001), name..." class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-sm text-slate-900 focus:outline-none focus:border-emerald-500">
            </div>

            <select name="category" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-emerald-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold transition-all">Filter</button>
            @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('stocks.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Stock Data Table -->
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
                            <td class="py-4 px-5 text-right font-semibold text-slate-800">${{ number_format($stock->unit_cost, 2) }}</td>
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
        
        <!-- Pagination Links -->
        <div class="p-4 border-t border-slate-100">
            {{ $stocks->links() }}
        </div>
    </div>
</div>

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
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Unit Cost ($)</label>
                    <input type="number" step="0.01" name="unit_cost" value="10.00" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500">
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
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Unit Cost ($)</label>
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

@endsection

@section('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

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
</script>
@endsection
