@extends('layouts.app')

@section('title', 'Pricing Rules')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-rose-500/10 text-rose-400 text-xs font-semibold uppercase tracking-wider">Tariffs & Price Lists</span>
            <h1 class="text-2xl font-bold font-heading text-white mt-1">Export Pricing Rules</h1>
            <p class="text-slate-400 text-xs mt-0.5">Manage base export prices, multi-currency rates, minimum order quantities (MOQ), and effective dates.</p>
        </div>

        <button onclick="openModal('addPricingModal')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white font-semibold text-sm shadow-lg shadow-rose-600/20 flex items-center gap-2 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i> New Pricing Rule
        </button>
    </div>

    <!-- Data Table -->
    <div class="glass-card rounded-2xl overflow-hidden border border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-800/80 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/80">
                    <tr>
                        <th class="py-4 px-5">Stock Item Code</th>
                        <th class="py-4 px-5">Product Name</th>
                        <th class="py-4 px-5 text-right">Base Export Price</th>
                        <th class="py-4 px-5 text-center">Currency</th>
                        <th class="py-4 px-5 text-right">Min Order Qty (MOQ)</th>
                        <th class="py-4 px-5">Effective Date</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($pricings as $pr)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-mono font-bold text-xs">
                                    {{ $pr->stock->code ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-4 px-5 font-bold text-white">{{ $pr->stock->name ?? 'N/A' }}</td>
                            <td class="py-4 px-5 text-right font-bold text-rose-400">${{ number_format($pr->base_export_price, 2) }}</td>
                            <td class="py-4 px-5 text-center">
                                <span class="px-2.5 py-1 rounded-full bg-slate-800 border border-slate-700 text-slate-300 text-xs font-semibold">{{ $pr->currency }}</span>
                            </td>
                            <td class="py-4 px-5 text-right font-medium text-slate-300">{{ number_format($pr->min_order_qty) }}</td>
                            <td class="py-4 px-5 text-slate-400 text-xs">{{ date('d M Y', strtotime($pr->effective_date)) }}</td>
                            <td class="py-4 px-5 text-right space-x-2">
                                <button onclick='editPricing({{ json_encode($pr) }})' class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="confirmDeletePricing({{ $pr->id }})" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">No pricing rules defined.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $pricings->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add Pricing Rule -->
<div id="addPricingModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg overflow-hidden border border-slate-700">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold font-heading text-white">Add Export Pricing Rule</h3>
            <button onclick="closeModal('addPricingModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('pricings.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Select Stock Item</label>
                <select name="stock_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-rose-500">
                    @foreach($stocks as $st)
                        <option value="{{ $st->id }}">{{ $st->code }} - {{ $st->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Base Export Price ($)</label>
                    <input type="number" step="0.01" name="base_export_price" value="25.00" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Currency</label>
                    <select name="currency" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-rose-500">
                        <option value="USD">USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="GBP">GBP (£)</option>
                        <option value="AED">AED (Dh)</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Min Order Qty (MOQ)</label>
                    <input type="number" name="min_order_qty" value="100" min="1" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Effective Date</label>
                    <input type="date" name="effective_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-rose-500">
                </div>
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('addPricingModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold">Save Pricing</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Pricing -->
<div id="editPricingModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg overflow-hidden border border-slate-700">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold font-heading text-white">Edit Export Pricing Rule</h3>
            <button onclick="closeModal('editPricingModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="editPricingForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Select Stock Item</label>
                <select id="edit_pr_stock_id" name="stock_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-rose-500">
                    @foreach($stocks as $st)
                        <option value="{{ $st->id }}">{{ $st->code }} - {{ $st->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Base Export Price ($)</label>
                    <input type="number" step="0.01" id="edit_pr_price" name="base_export_price" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Currency</label>
                    <select id="edit_pr_currency" name="currency" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-rose-500">
                        <option value="USD">USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="GBP">GBP (£)</option>
                        <option value="AED">AED (Dh)</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Min Order Qty (MOQ)</label>
                    <input type="number" id="edit_pr_moq" name="min_order_qty" min="1" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Effective Date</label>
                    <input type="date" id="edit_pr_date" name="effective_date" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-rose-500">
                </div>
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('editPricingModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold">Update Pricing</button>
            </div>
        </form>
    </div>
</div>

<form id="deletePricingForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function editPricing(pr) {
        document.getElementById('editPricingForm').action = "/pricings/" + pr.id;
        document.getElementById('edit_pr_stock_id').value = pr.stock_id;
        document.getElementById('edit_pr_price').value = pr.base_export_price;
        document.getElementById('edit_pr_currency').value = pr.currency;
        document.getElementById('edit_pr_moq').value = pr.min_order_qty;
        document.getElementById('edit_pr_date').value = pr.effective_date;
        openModal('editPricingModal');
    }

    function confirmDeletePricing(id) {
        if (confirm("Are you sure you want to delete this pricing rule?")) {
            const form = document.getElementById('deletePricingForm');
            form.action = "/pricings/" + id;
            form.submit();
        }
    }
</script>
@endsection
