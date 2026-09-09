@extends('layouts.app')

@section('title', 'Supplier & Cotton Management')

@section('content')
<div class="space-y-6">
    
    <!-- Module Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold uppercase tracking-wider">Sourcing & Purchasing</span>
                <span class="text-slate-400 text-xs">• Supplier Master</span>
            </div>
            <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Supplier Management</h1>
            <p class="text-slate-500 text-xs mt-0.5">Manage raw cotton suppliers, GST/PAN, cotton types, payment terms (7, 15, 30, 45 days), purchases & paid balances in ₹ Rupees.</p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <!-- Test Email Button -->
            <form action="{{ route('suppliers.send-test-email') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm shadow-sm flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-paper-plane text-amber-400"></i> Send Test Payment Email
                </button>
            </form>

            <button onclick="openModal('addSupplierModal')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-brand-600 hover:from-indigo-500 hover:to-brand-500 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-plus"></i> Add New Supplier
            </button>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="light-card rounded-2xl p-4 flex items-center justify-between">
        <form method="GET" action="{{ route('suppliers.index') }}" class="flex items-center gap-3 w-full sm:w-96">
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search supplier, company, cotton type, GST..." class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold">Search</button>
        </form>
    </div>

    <!-- Data Table -->
    <div class="light-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100/80 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-4 px-5">Supplier & Company</th>
                        <th class="py-4 px-5">Contact & Mobile</th>
                        <th class="py-4 px-5">Cotton Type</th>
                        <th class="py-4 px-5">Payment Term & Due Date</th>
                        <th class="py-4 px-5 text-right">Total Purchased</th>
                        <th class="py-4 px-5 text-right">Total Paid</th>
                        <th class="py-4 px-5 text-right">Balance Due</th>
                        <th class="py-4 px-5 text-center">Status</th>
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
            {{ $suppliers->links() }}
        </div>
    </div>
</div>

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
