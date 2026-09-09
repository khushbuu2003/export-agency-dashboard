@extends('layouts.app')

@section('title', 'Tax Rates & Export Duties')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-teal-500/10 text-teal-400 text-xs font-semibold uppercase tracking-wider">Customs & Fiscal Compliance</span>
            <h1 class="text-2xl font-bold font-heading text-white mt-1">Tax Rates & Duties</h1>
            <p class="text-slate-400 text-xs mt-0.5">Manage customs export duty rates, tax exemption categories, and surcharge fees.</p>
        </div>

        <button onclick="openModal('addTaxModal')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-semibold text-sm shadow-lg shadow-teal-600/20 flex items-center gap-2 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i> Add Tax Rate
        </button>
    </div>

    <!-- Data Table -->
    <div class="glass-card rounded-2xl overflow-hidden border border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-800/80 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/80">
                    <tr>
                        <th class="py-4 px-5">Rule Name</th>
                        <th class="py-4 px-5">Category / Type</th>
                        <th class="py-4 px-5 text-right">Tax Rate (%)</th>
                        <th class="py-4 px-5">Description</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($taxRates as $tax)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-5 font-bold text-white">{{ $tax->name }}</td>
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-full bg-slate-800 border border-slate-700 text-teal-300 text-xs font-semibold">
                                    {{ $tax->type }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-right font-bold text-teal-400">{{ number_format($tax->rate, 2) }}%</td>
                            <td class="py-4 px-5 text-slate-400 text-xs">{{ $tax->description ?? 'N/A' }}</td>
                            <td class="py-4 px-5 text-right space-x-2">
                                <button onclick='editTax({{ json_encode($tax) }})' class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="confirmDeleteTax({{ $tax->id }})" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">No tax rate rules configured.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $taxRates->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add Tax Rate -->
<div id="addTaxModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-md overflow-hidden border border-slate-700">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold font-heading text-white">Add Tax & Duty Rule</h3>
            <button onclick="closeModal('addTaxModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('tax-rates.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Rule Name</label>
                <input type="text" name="name" placeholder="e.g. Standard Export Duty" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-teal-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Tax Rate (%)</label>
                    <input type="number" step="0.01" name="rate" value="5.00" min="0" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Tax Type</label>
                    <select name="type" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-teal-500">
                        <option value="Export Duty">Export Duty</option>
                        <option value="Tax Exempt">Tax Exempt</option>
                        <option value="Customs Surcharge">Customs Surcharge</option>
                        <option value="Local VAT">Local VAT</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-teal-500"></textarea>
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('addTaxModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-sm font-semibold">Save Tax Rule</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Tax Rate -->
<div id="editTaxModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-md overflow-hidden border border-slate-700">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold font-heading text-white">Edit Tax & Duty Rule</h3>
            <button onclick="closeModal('editTaxModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="editTaxForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Rule Name</label>
                <input type="text" id="edit_tax_name" name="name" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-teal-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Tax Rate (%)</label>
                    <input type="number" step="0.01" id="edit_tax_rate" name="rate" min="0" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Tax Type</label>
                    <select id="edit_tax_type" name="type" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-teal-500">
                        <option value="Export Duty">Export Duty</option>
                        <option value="Tax Exempt">Tax Exempt</option>
                        <option value="Customs Surcharge">Customs Surcharge</option>
                        <option value="Local VAT">Local VAT</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Description</label>
                <textarea id="edit_tax_description" name="description" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-teal-500"></textarea>
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('editTaxModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-sm font-semibold">Update Rule</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteTaxForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function editTax(tax) {
        document.getElementById('editTaxForm').action = "/tax-rates/" + tax.id;
        document.getElementById('edit_tax_name').value = tax.name;
        document.getElementById('edit_tax_rate').value = tax.rate;
        document.getElementById('edit_tax_type').value = tax.type;
        document.getElementById('edit_tax_description').value = tax.description || '';
        openModal('editTaxModal');
    }

    function confirmDeleteTax(id) {
        if (confirm("Are you sure you want to delete this tax rate?")) {
            const form = document.getElementById('deleteTaxForm');
            form.action = "/tax-rates/" + id;
            form.submit();
        }
    }
</script>
@endsection
