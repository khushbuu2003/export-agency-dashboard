@extends('layouts.app')

@section('title', 'Customers / Importers')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-xs font-semibold uppercase tracking-wider">Global Importers Directory</span>
            <h1 class="text-2xl font-bold font-heading text-white mt-1">Customers & Importers</h1>
            <p class="text-slate-400 text-xs mt-0.5">Manage international buyer company profiles, tax/IEC IDs, and contact info.</p>
        </div>

        <button onclick="openModal('addCustomerModal')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-sm shadow-lg shadow-blue-600/20 flex items-center gap-2 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i> Add Customer
        </button>
    </div>

    <!-- Search Toolbar -->
    <div class="glass-card rounded-2xl p-4 flex items-center justify-between">
        <form method="GET" action="{{ route('customers.index') }}" class="flex items-center gap-3 w-full sm:w-96">
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company, country, contact..." class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-200 text-sm font-semibold">Search</button>
        </form>
    </div>

    <!-- Data Table -->
    <div class="glass-card rounded-2xl overflow-hidden border border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-800/80 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/80">
                    <tr>
                        <th class="py-4 px-5">Company / Buyer Name</th>
                        <th class="py-4 px-5">Contact Person</th>
                        <th class="py-4 px-5">Country</th>
                        <th class="py-4 px-5">Tax ID / IEC Code</th>
                        <th class="py-4 px-5">Contact Info</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($customers as $cust)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-5 font-bold text-white">{{ $cust->company_name }}</td>
                            <td class="py-4 px-5 font-medium text-slate-300">{{ $cust->name }}</td>
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-semibold">
                                    <i class="fa-solid fa-earth-americas mr-1"></i> {{ $cust->country }}
                                </span>
                            </td>
                            <td class="py-4 px-5 font-mono text-xs text-slate-400">{{ $cust->tax_id ?? 'N/A' }}</td>
                            <td class="py-4 px-5 text-xs text-slate-300">
                                <div><i class="fa-solid fa-envelope text-slate-500 mr-1"></i> {{ $cust->email }}</div>
                                <div><i class="fa-solid fa-phone text-slate-500 mr-1"></i> {{ $cust->phone }}</div>
                            </td>
                            <td class="py-4 px-5 text-right space-x-2">
                                <button onclick='editCustomer({{ json_encode($cust) }})' class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="confirmDeleteCustomer({{ $cust->id }})" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">No customer profiles recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $customers->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add Customer -->
<div id="addCustomerModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg overflow-hidden border border-slate-700">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold font-heading text-white">Add Importer / Customer</h3>
            <button onclick="closeModal('addCustomerModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('customers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Company Name</label>
                    <input type="text" name="company_name" placeholder="e.g. Sterling Fabrics LLC" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Contact Person Name</label>
                    <input type="text" name="name" placeholder="e.g. John Sterling" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Country</label>
                    <input type="text" name="country" placeholder="e.g. United States" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Tax ID / IEC Code</label>
                    <input type="text" name="tax_id" placeholder="e.g. US-EIN-987654321" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Phone Number</label>
                    <input type="text" name="phone" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Physical Address</label>
                <textarea name="address" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('addCustomerModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold">Save Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Customer -->
<div id="editCustomerModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg overflow-hidden border border-slate-700">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold font-heading text-white">Edit Customer Profile</h3>
            <button onclick="closeModal('editCustomerModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="editCustomerForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Company Name</label>
                    <input type="text" id="edit_cust_company" name="company_name" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Contact Person Name</label>
                    <input type="text" id="edit_cust_name" name="name" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Country</label>
                    <input type="text" id="edit_cust_country" name="country" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Tax ID / IEC Code</label>
                    <input type="text" id="edit_cust_tax_id" name="tax_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Email Address</label>
                    <input type="email" id="edit_cust_email" name="email" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Phone Number</label>
                    <input type="text" id="edit_cust_phone" name="phone" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Physical Address</label>
                <textarea id="edit_cust_address" name="address" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-blue-500"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('editCustomerModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold">Update Customer</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteCustomerForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function editCustomer(cust) {
        document.getElementById('editCustomerForm').action = "/customers/" + cust.id;
        document.getElementById('edit_cust_company').value = cust.company_name;
        document.getElementById('edit_cust_name').value = cust.name;
        document.getElementById('edit_cust_country').value = cust.country;
        document.getElementById('edit_cust_tax_id').value = cust.tax_id || '';
        document.getElementById('edit_cust_email').value = cust.email;
        document.getElementById('edit_cust_phone').value = cust.phone;
        document.getElementById('edit_cust_address').value = cust.address || '';
        openModal('editCustomerModal');
    }

    function confirmDeleteCustomer(id) {
        if (confirm("Are you sure you want to delete this customer profile?")) {
            const form = document.getElementById('deleteCustomerForm');
            form.action = "/customers/" + id;
            form.submit();
        }
    }
</script>
@endsection
