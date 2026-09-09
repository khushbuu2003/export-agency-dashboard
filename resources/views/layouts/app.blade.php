<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Export Management System') - Export Hub</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#1e1b4b',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .light-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .sidebar-link {
            transition: all 0.2s ease-in-out;
        }
        .sidebar-link.active {
            background: #eef2ff;
            border-left: 4px solid #4f46e5;
            color: #4f46e5;
            font-weight: 600;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col font-sans">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Light Sidebar Navigation -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0 z-20">
            <!-- Brand Header -->
            <div class="h-20 flex items-center px-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center shadow-md shadow-brand-500/20">
                        <i class="fa-solid fa-ship text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="font-heading font-bold text-lg text-slate-900 leading-tight">EXIM GLOBAL</h1>
                        <span class="text-[11px] font-bold text-brand-600 uppercase tracking-widest">Agency Portal</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 py-4 px-3 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-brand-600 hover:bg-slate-100 font-medium text-sm {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-brand-600"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('suppliers.index') }}" class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-slate-100 font-medium text-sm {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-field w-5 text-center text-indigo-600"></i>
                    <span>Suppliers & Cotton</span>
                </a>

                <a href="{{ route('stocks.index') }}" class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-emerald-600 hover:bg-slate-100 font-medium text-sm {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-boxes-stacked w-5 text-center text-emerald-600"></i>
                    <span>Stock / Inventory</span>
                </a>

                <a href="{{ route('export-transactions.index') }}" class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-amber-600 hover:bg-slate-100 font-medium text-sm {{ request()->routeIs('export-transactions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-amber-600"></i>
                    <span>Export Orders</span>
                </a>

                <a href="{{ route('transfers.index') }}" class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-cyan-600 hover:bg-slate-100 font-medium text-sm {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-fast w-5 text-center text-cyan-600"></i>
                    <span>Stock Transfers</span>
                </a>

                <a href="{{ route('investors.index') }}" class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-purple-600 hover:bg-slate-100 font-medium text-sm {{ request()->routeIs('investors.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-handshake w-5 text-center text-purple-600"></i>
                    <span>Investors</span>
                </a>

                <a href="{{ route('customers.index') }}" class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-blue-600 hover:bg-slate-100 font-medium text-sm {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-earth-americas w-5 text-center text-blue-600"></i>
                    <span>Customers / Importers</span>
                </a>

                <a href="{{ route('pricings.index') }}" class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-rose-600 hover:bg-slate-100 font-medium text-sm {{ request()->routeIs('pricings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags w-5 text-center text-rose-600"></i>
                    <span>Pricing Rules</span>
                </a>

                <a href="{{ route('tax-rates.index') }}" class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-teal-600 hover:bg-slate-100 font-medium text-sm {{ request()->routeIs('tax-rates.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-percent w-5 text-center text-teal-600"></i>
                    <span>Tax Rates & Duties</span>
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-200 bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold">
                        EX
                    </div>
                    <div class="text-xs">
                        <p class="font-semibold text-slate-900">Export Agency Admin</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            
            <!-- Top Navbar -->
            <header class="h-20 bg-white/80 border-b border-slate-200 flex items-center justify-between px-8 z-10 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <h2 class="font-heading text-xl font-bold text-slate-900">@yield('title', 'Dashboard')</h2>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Admin Email Configuration Button -->
                    @php
                        $currentAdminEmail = \App\Models\SystemSetting::get('admin_email', 'admin@exportagency.com');
                    @endphp
                    <button onclick="openModal('adminEmailModal')" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 text-xs font-bold transition-all">
                        <i class="fa-solid fa-envelope-gear text-indigo-600 text-sm"></i>
                        <span>Admin Email: <strong class="text-indigo-600 font-mono">{{ $currentAdminEmail }}</strong></span>
                    </button>

                    <!-- Status Indicator & Live Preview -->
                    <a href="{{ route('emails.preview') }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-700 text-xs font-bold transition-all">
                        <i class="fa-solid fa-eye text-purple-600 text-sm"></i>
                        <span>Preview Email in Browser</span>
                    </a>

                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Auto Email Reminders Active</span>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Body -->
            <main class="flex-1 overflow-y-auto p-8">
                
                <!-- Notification Alerts -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 shadow-sm">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                        <span class="font-semibold text-sm">{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                        <div class="flex items-center gap-3 mb-2 font-semibold">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                            <span>Please fix the following validation errors:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Modal: Admin Email Configuration -->
    <div id="adminEmailModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="light-card rounded-2xl w-full max-w-md overflow-hidden shadow-xl border border-slate-200">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <h3 class="text-lg font-bold font-heading text-slate-900">Configure Admin Notification Email</h3>
                <button onclick="closeModal('adminEmailModal')" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('settings.update-email') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Admin Email Address for Reminders</label>
                    <input type="email" name="admin_email" value="{{ $currentAdminEmail }}" required placeholder="e.g. admin@exportagency.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-600 font-mono">
                    <p class="text-[11px] text-slate-500 mt-1.5">All automated overdue supplier payment alerts, investor collectable reminders, and stock audits will be sent to this email address.</p>
                </div>
                <div class="pt-4 flex justify-end gap-3 border-t border-slate-200">
                    <button type="button" onclick="closeModal('adminEmailModal')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold">Save Admin Email</button>
                </div>
            </form>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
