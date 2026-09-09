@extends('layouts.app')

@section('title', 'Export Orders & Transactions')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-xs font-semibold uppercase tracking-wider">International Sales Register</span>
            <h1 class="text-2xl font-bold font-heading text-white mt-1">Export Transactions</h1>
            <p class="text-slate-400 text-xs mt-0.5">Record export shipments, customer invoices, auto-computed taxes, duties, and payment statuses.</p>
        </div>

        <button onclick="openModal('addTxModal')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-semibold text-sm shadow-lg shadow-amber-600/20 flex items-center gap-2 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i> New Export Order
        </button>
    </div>

    <!-- Search Toolbar -->
    <div class="glass-card rounded-2xl p-4 flex items-center justify-between">
        <form method="GET" action="{{ route('export-transactions.index') }}" class="flex items-center gap-3 w-full sm:w-96">
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transaction code (INV-EXP-2026-001)..." class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:border-amber-500">
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
                        <th class="py-4 px-5">Invoice Code</th>
                        <th class="py-4 px-5">Customer Importer</th>
                        <th class="py-4 px-5">Stock Item</th>
                        <th class="py-4 px-5 text-right">Qty & Price</th>
                        <th class="py-4 px-5 text-right">Subtotal</th>
                        <th class="py-4 px-5 text-right">Tax & Shipping</th>
                        <th class="py-4 px-5 text-right">Total Export Value</th>
                        <th class="py-4 px-5 text-center">Payment</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 font-mono font-bold text-xs">
                                    {{ $tx->transaction_code }}
                                </span>
                                <div class="text-[11px] text-slate-500 mt-1"><i class="fa-solid fa-calendar mr-1"></i> {{ date('d M Y', strtotime($tx->export_date)) }}</div>
                            </td>
                            <td class="py-4 px-5 font-bold text-white">
                                {{ $tx->customer->company_name ?? 'N/A' }}
                                <div class="text-xs font-normal text-slate-400">{{ $tx->customer->country ?? '' }}</div>
                            </td>
                            <td class="py-4 px-5 font-medium text-slate-300">
                                <span class="text-xs font-mono text-slate-400 mr-1">[{{ $tx->stock->code ?? 'N/A' }}]</span>
                                {{ $tx->stock->name ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-5 text-right text-xs text-slate-300">
                                <div class="font-bold text-white">{{ number_format($tx->quantity) }} units</div>
                                <div class="text-slate-400">@ ${{ number_format($tx->unit_price, 2) }}</div>
                            </td>
                            <td class="py-4 px-5 text-right font-semibold text-slate-200">${{ number_format($tx->subtotal, 2) }}</td>
                            <td class="py-4 px-5 text-right text-xs text-slate-300">
                                <div class="text-teal-400">Tax: ${{ number_format($tx->tax_amount, 2) }}</div>
                                <div class="text-slate-400">Ship: ${{ number_format($tx->shipping_cost, 2) }}</div>
                            </td>
                            <td class="py-4 px-5 text-right font-bold text-emerald-400">${{ number_format($tx->total_value, 2) }}</td>
                            <td class="py-4 px-5 text-center">
                                @if($tx->payment_status === 'Paid')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-semibold">Paid</span>
                                @elseif($tx->payment_status === 'Pending')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-semibold">Pending</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 text-xs font-semibold">{{ $tx->payment_status }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right space-x-2">
                                <button onclick='editTx({{ json_encode($tx) }})' class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="confirmDeleteTx({{ $tx->id }})" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-500">No export transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add Export Transaction -->
<div id="addTxModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-xl overflow-hidden border border-slate-700">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold font-heading text-white">Create Export Order / Invoice</h3>
            <button onclick="closeModal('addTxModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('export-transactions.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Invoice Code</label>
                    <input type="text" name="transaction_code" placeholder="INV-EXP-2026-006" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Export Date</label>
                    <input type="date" name="export_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Customer / Importer</label>
                    <select name="customer_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->company_name }} ({{ $c->country }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Stock Item</label>
                    <select name="stock_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                        @foreach($stocks as $s)
                            <option value="{{ $s->id }}">{{ $s->code }} - {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="1" value="1000" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Unit Price ($)</label>
                    <input type="number" step="0.01" name="unit_price" value="24.50" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Shipping Cost ($)</label>
                    <input type="number" step="0.01" name="shipping_cost" value="1200.00" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Tax Rate / Duty Rule</label>
                    <select name="tax_rate_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                        <option value="">No Tax (0%)</option>
                        @foreach($taxRates as $tr)
                            <option value="{{ $tr->id }}">{{ $tr->name }} ({{ $tr->rate }}%)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Payment Status</label>
                    <select name="payment_status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Partial">Partial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Destination Port</label>
                    <input type="text" name="destination_port" placeholder="e.g. Port of Rotterdam" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('addTxModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold">Generate Export Invoice</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Export Transaction -->
<div id="editTxModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-xl overflow-hidden border border-slate-700">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold font-heading text-white">Edit Export Order / Invoice</h3>
            <button onclick="closeModal('editTxModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="editTxForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Invoice Code</label>
                    <input type="text" id="edit_tx_code" name="transaction_code" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Export Date</label>
                    <input type="date" id="edit_tx_date" name="export_date" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Customer / Importer</label>
                    <select id="edit_tx_customer_id" name="customer_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Stock Item</label>
                    <select id="edit_tx_stock_id" name="stock_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                        @foreach($stocks as $s)
                            <option value="{{ $s->id }}">{{ $s->code }} - {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Quantity</label>
                    <input type="number" id="edit_tx_qty" name="quantity" min="1" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Unit Price ($)</label>
                    <input type="number" step="0.01" id="edit_tx_price" name="unit_price" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Shipping Cost ($)</label>
                    <input type="number" step="0.01" id="edit_tx_shipping" name="shipping_cost" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Tax Rate / Duty Rule</label>
                    <select id="edit_tx_tax_id" name="tax_rate_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                        <option value="">No Tax (0%)</option>
                        @foreach($taxRates as $tr)
                            <option value="{{ $tr->id }}">{{ $tr->name }} ({{ $tr->rate }}%)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Payment Status</label>
                    <select id="edit_tx_payment" name="payment_status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Partial">Partial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Destination Port</label>
                    <input type="text" id="edit_tx_port" name="destination_port" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('editTxModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold">Update Invoice</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteTxForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function editTx(tx) {
        document.getElementById('editTxForm').action = "/export-transactions/" + tx.id;
        document.getElementById('edit_tx_code').value = tx.transaction_code;
        document.getElementById('edit_tx_date').value = tx.export_date;
        document.getElementById('edit_tx_customer_id').value = tx.customer_id;
        document.getElementById('edit_tx_stock_id').value = tx.stock_id;
        document.getElementById('edit_tx_qty').value = tx.quantity;
        document.getElementById('edit_tx_price').value = tx.unit_price;
        document.getElementById('edit_tx_shipping').value = tx.shipping_cost;
        document.getElementById('edit_tx_tax_id').value = tx.tax_rate_id || '';
        document.getElementById('edit_tx_payment').value = tx.payment_status;
        document.getElementById('edit_tx_port').value = tx.destination_port;
        openModal('editTxModal');
    }

    function confirmDeleteTx(id) {
        if (confirm("Are you sure you want to delete this export transaction?")) {
            const form = document.getElementById('deleteTxForm');
            form.action = "/export-transactions/" + id;
            form.submit();
        }
    }
</script>
@endsection
