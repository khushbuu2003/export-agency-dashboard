@extends('layouts.app')

@section('title', 'Investors Management & Collectables')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold uppercase tracking-wider border border-purple-200">Capital Partners & Collectables</span>
            <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Investors & Payments to Receive</h1>
            <p class="text-slate-500 text-xs mt-0.5">Track capital investments, share % splits, amounts received, and pending payments to collect from investors in ₹ Rupees.</p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <form action="{{ route('suppliers.send-test-email') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm shadow-sm flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-paper-plane text-amber-400"></i> Send Reminder Email
                </button>
            </form>

            <button onclick="openModal('addInvestorModal')" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm shadow-md shadow-purple-600/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-plus"></i> Add Investor
            </button>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="light-card rounded-2xl p-4 flex items-center justify-between">
        <form method="GET" action="{{ route('investors.index') }}" class="flex items-center gap-3 w-full sm:w-80">
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search investor name, company..." class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-sm text-slate-900 focus:outline-none focus:border-purple-500">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold">Search</button>
        </form>
    </div>

    <!-- Data Table -->
    <div class="light-card rounded-2xl overflow-hidden border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-4 px-5">Investor Name</th>
                        <th class="py-4 px-5">Company / Firm</th>
                        <th class="py-4 px-5 text-right">Share %</th>
                        <th class="py-4 px-5 text-right">Agreed Capital</th>
                        <th class="py-4 px-5 text-right">Amount Received</th>
                        <th class="py-4 px-5 text-right">Pending to Receive</th>
                        <th class="py-4 px-5 text-center">Status</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($investors as $inv)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-5 font-bold text-slate-900">
                                {{ $inv->name }}
                                <div class="text-xs font-normal text-slate-500"><i class="fa-solid fa-phone text-slate-400 mr-1"></i>{{ $inv->phone }}</div>
                            </td>
                            <td class="py-4 px-5 text-purple-700 font-semibold">{{ $inv->company ?? 'Individual Investor' }}</td>
                            <td class="py-4 px-5 text-right font-bold text-slate-800">{{ number_format($inv->share_percentage, 2) }}%</td>
                            <td class="py-4 px-5 text-right font-bold text-slate-900">₹ {{ number_format($inv->investment_amount, 2) }}</td>
                            <td class="py-4 px-5 text-right font-bold text-emerald-600">₹ {{ number_format($inv->amount_received, 2) }}</td>
                            <td class="py-4 px-5 text-right font-bold {{ $inv->pendingToReceive() > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                ₹ {{ number_format($inv->pendingToReceive(), 2) }}
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if($inv->pendingToReceive() > 0)
                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold">
                                        ⏳ Pending Collection
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                                        Fully Settled
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right space-x-2">
                                <button onclick='editInvestor({{ json_encode($inv) }})' class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="confirmDeleteInvestor({{ $inv->id }})" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">No investor profiles recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $investors->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add Investor -->
<div id="addInvestorModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="light-card rounded-2xl w-full max-w-lg overflow-hidden border border-slate-200 shadow-xl">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-lg font-bold font-heading text-slate-900">Add Investor & Capital Entry</h3>
            <button onclick="closeModal('addInvestorModal')" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('investors.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Investor Name</label>
                <input type="text" name="name" placeholder="e.g. Robert Vance" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Company / Firm Name</label>
                <input type="text" name="company" placeholder="e.g. Global Ventures Group" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Phone / Mobile</label>
                    <input type="text" name="phone" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Agreed Capital (₹)</label>
                    <input type="number" step="0.01" name="investment_amount" value="500000" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Amount Received (₹)</label>
                    <input type="number" step="0.01" name="amount_received" value="350000" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Share %</label>
                    <input type="number" step="0.01" name="share_percentage" value="10.00" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="pt-4 flex justify-end gap-3 border-t border-slate-200">
                <button type="button" onclick="closeModal('addInvestorModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-sm font-semibold">Save Profile</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Investor -->
<div id="editInvestorModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="light-card rounded-2xl w-full max-w-lg overflow-hidden border border-slate-200 shadow-xl">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-lg font-bold font-heading text-slate-900">Edit Investor Profile</h3>
            <button onclick="closeModal('editInvestorModal')" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="editInvestorForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Investor Name</label>
                <input type="text" id="edit_inv_name" name="name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Company / Firm Name</label>
                <input type="text" id="edit_inv_company" name="company" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                    <input type="email" id="edit_inv_email" name="email" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Phone</label>
                    <input type="text" id="edit_inv_phone" name="phone" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Agreed Capital (₹)</label>
                    <input type="number" step="0.01" id="edit_inv_amount" name="investment_amount" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Amount Received (₹)</label>
                    <input type="number" step="0.01" id="edit_inv_received" name="amount_received" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Share %</label>
                    <input type="number" step="0.01" id="edit_inv_share" name="share_percentage" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select id="edit_inv_status" name="status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-purple-500">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="pt-4 flex justify-end gap-3 border-t border-slate-200">
                <button type="button" onclick="closeModal('editInvestorModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-sm font-semibold">Update Profile</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteInvestorForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function editInvestor(inv) {
        document.getElementById('editInvestorForm').action = "/investors/" + inv.id;
        document.getElementById('edit_inv_name').value = inv.name;
        document.getElementById('edit_inv_company').value = inv.company || '';
        document.getElementById('edit_inv_email').value = inv.email;
        document.getElementById('edit_inv_phone').value = inv.phone;
        document.getElementById('edit_inv_amount').value = inv.investment_amount;
        document.getElementById('edit_inv_received').value = inv.amount_received || 0;
        document.getElementById('edit_inv_share').value = inv.share_percentage;
        document.getElementById('edit_inv_status').value = inv.status;
        openModal('editInvestorModal');
    }

    function confirmDeleteInvestor(id) {
        if (confirm("Are you sure you want to remove this investor profile?")) {
            const form = document.getElementById('deleteInvestorForm');
            form.action = "/investors/" + id;
            form.submit();
        }
    }
</script>
@endsection
