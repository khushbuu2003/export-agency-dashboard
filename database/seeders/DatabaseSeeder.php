<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Investor;
use App\Models\Transfer;
use App\Models\Pricing;
use App\Models\TaxRate;
use App\Models\Customer;
use App\Models\ExportTransaction;
use App\Models\Supplier;
use App\Models\SystemSetting;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Seed System Settings (Default Admin Email)
        SystemSetting::set('admin_email', 'admin@exportagency.com');

        // 1. Seed Stocks
        $stock1 = Stock::create([
            'code' => 'EXP-1001',
            'name' => 'Cotton Fabric',
            'category' => 'Textile',
            'total_qty' => 12500,
            'reserved_qty' => 1800,
            'unit' => 'Meters',
            'unit_cost' => 1150.00, // ₹ Rupees
            'status' => 'In Stock',
        ]);

        $stock2 = Stock::create([
            'code' => 'EXP-1002',
            'name' => 'Raw Leather Hide',
            'category' => 'Leather Goods',
            'total_qty' => 4200,
            'reserved_qty' => 600,
            'unit' => 'Sheets',
            'unit_cost' => 3600.00, // ₹ Rupees
            'status' => 'In Stock',
        ]);

        // 2. Seed Suppliers with Payment Terms (7 days, 15 days, 30 days, 45 days)
        Supplier::create([
            'supplier_name' => 'Rajesh Sharma',
            'company_name' => 'Gujarat Long Staple Cotton Mills',
            'contact_person' => 'Rajesh Sharma',
            'mobile' => '+91 98250 11223',
            'address' => 'Plot 45, GIDC Industrial Estate, Rajkot, Gujarat - 360002',
            'gst_pan' => '24AAACG1234F1Z5 / AAACG1234F',
            'cotton_type' => 'Shankar-6 (Premium Raw Cotton)',
            'payment_terms' => '7 days',
            'payment_due_days' => 7,
            'purchase_date' => Carbon::now()->subDays(9)->format('Y-m-d'), // 9 days ago -> Overdue for 7 day term!
            'bank_details' => 'HDFC Bank, A/C: 50200012345678, IFSC: HDFC0000123',
            'total_purchased' => 145000.00,
            'total_paid' => 120000.00,
        ]);

        Supplier::create([
            'supplier_name' => 'Amit Patel',
            'company_name' => 'Surat Supreme Textile Fibers',
            'contact_person' => 'Amit Patel',
            'mobile' => '+91 94261 88990',
            'address' => 'Ring Road Textile Market, Surat, Gujarat - 395002',
            'gst_pan' => '24AABCS9876K1Z2 / AABCS9876K',
            'cotton_type' => 'MCU-5 Combed Cotton Yarn',
            'payment_terms' => '15 days',
            'payment_due_days' => 15,
            'purchase_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'bank_details' => 'ICICI Bank, A/C: 000405012999, IFSC: ICIC0000004',
            'total_purchased' => 98000.00,
            'total_paid' => 98000.00,
        ]);

        // 3. Seed Investors with Amount Received and Pending to Receive
        Investor::create([
            'name' => 'Robert Vance',
            'company' => 'Global Ventures Group',
            'email' => 'rvance@globalventures.com',
            'phone' => '+91 98200 44556',
            'investment_amount' => 2500000.00, // ₹ 25 Lakhs Agreed
            'amount_received' => 1800000.00,   // ₹ 18 Lakhs Received -> ₹ 7 Lakhs Pending to Receive!
            'share_percentage' => 18.50,
            'status' => 'Active',
        ]);

        Investor::create([
            'name' => 'Elena Rostova',
            'company' => 'Apex Capital Partners',
            'email' => 'elena@apextrade.io',
            'phone' => '+91 98110 33445',
            'investment_amount' => 1500000.00, // ₹ 15 Lakhs Agreed
            'amount_received' => 1500000.00,   // ₹ 15 Lakhs Received -> Fully Settled
            'share_percentage' => 12.00,
            'status' => 'Active',
        ]);

        Investor::create([
            'name' => 'Tariq Al-Mansoor',
            'company' => 'Emirates Trade Holdings',
            'email' => 'tariq@emiratescap.ae',
            'phone' => '+91 97110 99887',
            'investment_amount' => 3500000.00, // ₹ 35 Lakhs Agreed
            'amount_received' => 2000000.00,   // ₹ 20 Lakhs Received -> ₹ 15 Lakhs Pending to Receive!
            'share_percentage' => 25.00,
            'status' => 'Active',
        ]);

        // 4. Seed Transfers
        Transfer::create([
            'transfer_code' => 'TRF-9001',
            'stock_id' => $stock1->id,
            'qty' => 3000,
            'sender_party' => 'Apex Logistics Hub (Warehouse A)',
            'receiver_party' => 'Port Terminal Bay 4 (Mombasa)',
            'transfer_date' => '2026-08-25',
            'status' => 'Pending',
            'notes' => 'Awaiting customs seal verification before loading onto vessel.',
        ]);

        // 5. Seed Customers
        $cust1 = Customer::create([
            'name' => 'John Sterling',
            'company_name' => 'Sterling Fabrics LLC',
            'email' => 'jsterling@sterlingfabrics.com',
            'phone' => '+1 (212) 555-0199',
            'country' => 'United States',
            'tax_id' => 'US-EIN-987654321',
            'address' => '450 Fashion Ave, New York, NY 10018',
        ]);

        // 6. Seed Export Transactions
        ExportTransaction::create([
            'transaction_code' => 'INV-EXP-2026-001',
            'customer_id' => $cust1->id,
            'stock_id' => $stock1->id,
            'quantity' => 2000,
            'unit_price' => 1950.00,
            'subtotal' => 3900000.00,
            'tax_amount' => 195000.00,
            'shipping_cost' => 120000.00,
            'total_value' => 4215000.00,
            'payment_status' => 'Paid',
            'export_date' => '2026-05-15',
            'destination_port' => 'Port of New York / New Jersey',
        ]);
    }
}
