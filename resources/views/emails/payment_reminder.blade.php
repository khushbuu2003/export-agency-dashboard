<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 720px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 30px; border: 1px solid #e1e8ed; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 25px; }
        .header h2 { color: #1e1b4b; margin: 0; font-size: 22px; }
        .header p { color: #6366f1; margin: 5px 0 0 0; font-weight: bold; }
        .section-title { font-size: 16px; font-weight: bold; color: #0f172a; margin-top: 25px; margin-bottom: 12px; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        th { background-color: #f8fafc; color: #475569; text-align: left; padding: 10px; border: 1px solid #e2e8f0; font-weight: bold; }
        td { padding: 10px; border: 1px solid #e2e8f0; color: #1e293b; }
        .badge-danger { background-color: #fee2e2; color: #dc2626; padding: 3px 8px; border-radius: 12px; font-weight: bold; font-size: 11px; }
        .badge-amber { background-color: #fef3c7; color: #d97706; padding: 3px 8px; border-radius: 12px; font-weight: bold; font-size: 11px; }
        .badge-success { background-color: #dcfce7; color: #16a34a; padding: 3px 8px; border-radius: 12px; font-weight: bold; font-size: 11px; }
        .footer { font-size: 11px; color: #94a3b8; text-align: center; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>EXIM GLOBAL AGENCY PORTAL</h2>
            <p>Admin Executive Payment Reminder & Financial Audit Report</p>
        </div>

        <p>Dear Administrator,</p>
        <p>Below is your configured executive summary report detailing <strong>Investor Payments to Receive</strong>, <strong>Supplier Cotton Payout Reminders</strong>, and current <strong>Stock Inventory Levels</strong>.</p>

        <!-- SECTION 1: INVESTOR PAYMENTS TO COLLECT -->
        <div class="section-title">1. 💰 Investor Payments to Receive (Capital Reminders)</div>
        <table>
            <thead>
                <tr>
                    <th>Investor & Firm</th>
                    <th>Share %</th>
                    <th>Agreed Capital</th>
                    <th>Amount Received</th>
                    <th>Pending to Collect</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($investors as $inv)
                    <tr>
                        <td>
                            <strong>{{ $inv->name }}</strong><br>
                            <small>{{ $inv->company ?? 'Individual' }} ({{ $inv->mobile ?? $inv->phone }})</small>
                        </td>
                        <td>{{ number_format($inv->share_percentage, 2) }}%</td>
                        <td>₹ {{ number_format($inv->investment_amount, 2) }}</td>
                        <td>₹ {{ number_format($inv->amount_received, 2) }}</td>
                        <td><strong style="color: #d97706;">₹ {{ number_format($inv->pendingToReceive(), 2) }}</strong></td>
                        <td>
                            @if($inv->pendingToReceive() > 0)
                                <span class="badge-amber">⏳ Pending Collection</span>
                            @else
                                <span class="badge-success">Fully Received</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- SECTION 2: SUPPLIER PAYOUTS -->
        <div class="section-title">2. 🏭 Supplier Cotton Purchases & Payout Due Reminders</div>
        <table>
            <thead>
                <tr>
                    <th>Supplier & Company</th>
                    <th>Cotton Type</th>
                    <th>Terms</th>
                    <th>Due Date</th>
                    <th>Balance Due</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $sup)
                    <tr>
                        <td>
                            <strong>{{ $sup->company_name }}</strong><br>
                            <small>{{ $sup->supplier_name }} ({{ $sup->mobile }})</small>
                        </td>
                        <td>{{ $sup->cotton_type ?? 'N/A' }}</td>
                        <td>{{ $sup->payment_terms }}</td>
                        <td>{{ date('d M Y', strtotime($sup->dueDate)) }}</td>
                        <td><strong>₹ {{ number_format($sup->balanceDue(), 2) }}</strong></td>
                        <td>
                            @if($sup->isOverdue())
                                <span class="badge-danger">⚠️ OVERDUE</span>
                            @else
                                <span class="badge-success">OK / Active</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- SECTION 3: STOCK AUDIT -->
        <div class="section-title">3. 📦 Stock & Inventory Audit Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Stock Code</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Total Qty</th>
                    <th>Available Qty</th>
                    <th>Unit Cost</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                    <tr>
                        <td><strong>{{ $stock->code }}</strong></td>
                        <td>{{ $stock->name }}</td>
                        <td>{{ $stock->category }}</td>
                        <td>{{ number_format($stock->total_qty) }} {{ $stock->unit }}</td>
                        <td><strong>{{ number_format($stock->availableQty()) }}</strong></td>
                        <td>₹ {{ number_format($stock->unit_cost, 2) }}</td>
                        <td>{{ $stock->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Sent automatically to your configured Admin Email • Local Time: {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
