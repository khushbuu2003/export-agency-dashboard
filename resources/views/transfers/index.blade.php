@extends('layouts.app')

@section('title', 'Stock Transfers Log')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 text-xs font-semibold uppercase tracking-wider">Logistics & Consignments</span>
            <h1 class="text-2xl font-bold font-heading text-white mt-1">Stock Transfers</h1>
            <p class="text-slate-400 text-xs mt-0.5">Track warehouse movements, sender parties, receiver terminals, and transfer statuses.</p>
        </div>

        <button onclick="openModal('addTransferModal')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-semibold text-sm shadow-lg shadow-cyan-600/20 flex items-center gap-2 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i> New Transfer Order
        </button>
    </div>

    <!-- Search Toolbar -->
    <div class="glass-card rounded-2xl p-4 flex items-center justify-between">
        <form method="GET" action="{{ route('transfers.index') }}" class="flex items-center gap-3 w-full sm:w-96">
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code (TRF-9001), sender party..." class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
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
                        <th class="py-4 px-5">Transfer Code</th>
                        <th class="py-4 px-5">Stock Item</th>
                        <th class="py-4 px-5 text-right">Transfer Qty</th>
                        <th class="py-4 px-5">Sender Party Name</th>
                        <th class="py-4 px-5">Receiver Party Name</th>
                        <th class="py-4 px-5">Date</th>
                        <th class="py-4 px-5 text-center">Status</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($transfers as $trf)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono font-bold text-xs">
                                    {{ $trf->transfer_code }}
                                </span>
                            </td>
                            <td class="py-4 px-5 font-bold text-white">
                                <span class="text-xs font-mono text-slate-400 mr-1">[{{ $trf->stock->code ?? 'N/A' }}]</span>
                                {{ $trf->stock->name ?? 'Deleted Item' }}
                            </td>
                            <td class="py-4 px-5 text-right font-bold text-slate-200">{{ number_format($trf->qty) }}</td>
                            <td class="py-4 px-5 font-semibold text-cyan-300">{{ $trf->sender_party }}</td>
                            <td class="py-4 px-5 text-slate-300">{{ $trf->receiver_party }}</td>
                            <td class="py-4 px-5 text-slate-400 text-xs">{{ date('d M Y', strtotime($trf->transfer_date)) }}</td>
                            <td class="py-4 px-5 text-center">
                                @if($trf->status === 'Pending')
                                    <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-xs font-semibold">Pending</span>
                                @elseif($trf->status === 'In Transit')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-semibold">In Transit</span>
                                @elseif($trf->status === 'Completed')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-semibold">Completed</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-800 text-slate-400 text-xs font-semibold">{{ $trf->status }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right space-x-2">
                                <button onclick='editTransfer({{ json_encode($trf) }})' class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="confirmDeleteTransfer({{ $trf->id }})" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500">No stock transfers recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $transfers->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add Transfer Order -->
<div id="addTransferModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg overflow-hidden border border-slate-700">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold font-heading text-white">Create Stock Transfer Order</h3>
            <button onclick="closeModal('addTransferModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('transfers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Transfer Code</label>
                    <input type="text" name="transfer_code" placeholder="TRF-9005" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Select Stock Item</label>
                    <select name="stock_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
                        @foreach($stocks as $st)
                            <option value="{{ $st->id }}">{{ $st->code }} - {{ $st->name }} (Available: {{ $st->availableQty() }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Transfer Quantity</label>
                    <input type="number" name="qty" min="1" value="500" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Transfer Date</label>
                    <input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Sender Party Name (Consignor)</label>
                <input type="text" name="sender_party" placeholder="e.g. Apex Logistics Hub (Warehouse A)" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Receiver Party Name (Consignee / Port Terminal)</label>
                <input type="text" name="receiver_party" placeholder="e.g. Port Terminal Bay 4 (Mombasa)" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Status</label>
                <select name="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
                    <option value="Pending">Pending</option>
                    <option value="In Transit">In Transit</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Notes / Instructions</label>
                <textarea name="notes" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('addTransferModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-semibold">Save Order</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Transfer Order -->
<div id="editTransferModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg overflow-hidden border border-slate-700">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold font-heading text-white">Edit Stock Transfer Order</h3>
            <button onclick="closeModal('editTransferModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="editTransferForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Transfer Code</label>
                    <input type="text" id="edit_trf_code" name="transfer_code" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Select Stock Item</label>
                    <select id="edit_trf_stock_id" name="stock_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
                        @foreach($stocks as $st)
                            <option value="{{ $st->id }}">{{ $st->code }} - {{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Transfer Quantity</label>
                    <input type="number" id="edit_trf_qty" name="qty" min="1" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Transfer Date</label>
                    <input type="date" id="edit_trf_date" name="transfer_date" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Sender Party Name</label>
                <input type="text" id="edit_trf_sender" name="sender_party" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Receiver Party Name</label>
                <input type="text" id="edit_trf_receiver" name="receiver_party" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Status</label>
                <select id="edit_trf_status" name="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500">
                    <option value="Pending">Pending</option>
                    <option value="In Transit">In Transit</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Notes</label>
                <textarea id="edit_trf_notes" name="notes" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-cyan-500"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('editTransferModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-semibold">Update Order</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteTransferForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function editTransfer(trf) {
        document.getElementById('editTransferForm').action = "/transfers/" + trf.id;
        document.getElementById('edit_trf_code').value = trf.transfer_code;
        document.getElementById('edit_trf_stock_id').value = trf.stock_id;
        document.getElementById('edit_trf_qty').value = trf.qty;
        document.getElementById('edit_trf_sender').value = trf.sender_party;
        document.getElementById('edit_trf_receiver').value = trf.receiver_party;
        document.getElementById('edit_trf_date').value = trf.transfer_date;
        document.getElementById('edit_trf_status').value = trf.status;
        document.getElementById('edit_trf_notes').value = trf.notes || '';
        openModal('editTransferModal');
    }

    function confirmDeleteTransfer(id) {
        if (confirm("Are you sure you want to delete this transfer order?")) {
            const form = document.getElementById('deleteTransferForm');
            form.action = "/transfers/" + id;
            form.submit();
        }
    }
</script>
@endsection
