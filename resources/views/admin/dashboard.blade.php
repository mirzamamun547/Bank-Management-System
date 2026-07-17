<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Bank - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Global & Layout */
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #f4f7fe; }
        .admin-layout { display: flex; height: 100vh; overflow: hidden; width: 100%; }
        
        /* Sidebar */
        .dark-sidebar { width: 260px; background: #0b1437; color: #ffffff; display: flex; flex-direction: column; padding: 24px 0; z-index: 10; }
        .dark-sidebar .logo { padding: 0 24px 32px; display: flex; align-items: center; gap: 12px; font-size: 1.2rem; }
        .dark-sidebar .nav-links { list-style: none; flex-grow: 1; overflow-y: auto; padding: 0; margin: 0; }
        .dark-sidebar .nav-links li { padding: 14px 24px; display: flex; align-items: center; gap: 12px; color: #a3aed1; cursor: pointer; transition: all 0.2s ease; font-size: 0.95rem; border-left: 3px solid transparent; }
        .dark-sidebar .nav-links li:hover { background: rgba(255,255,255,0.05); color: #ffffff; }
        .dark-sidebar .nav-links li.active { background: #4318ff; color: #ffffff; border-left: 3px solid #ffffff; }
        .dark-sidebar .nav-section { padding: 10px 24px 5px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #6b7a99; margin-top: 15px; }
        
        /* Main Area */
        .admin-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .admin-header { background: #ffffff; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .admin-header-right { display: flex; align-items: center; gap: 20px; }
        .admin-profile { display: flex; align-items: center; gap: 10px; }
        .admin-profile img { width: 40px; height: 40px; border-radius: 50%; }
        .admin-content { flex: 1; padding: 32px; overflow-y: auto; }
        
        /* SPA Sections */
        .content-section { display: none; animation: fadeIn 0.3s ease-in-out; }
        .content-section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Dashboard Cards & Charts */
        .grid-6 { display: grid; grid-template-columns: repeat(6, 1fr); gap: 15px; margin-bottom: 30px; }
        .admin-stat-card { background: #ffffff; border-radius: 16px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 12px; }
        .admin-stat-card .icon { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .admin-stat-card .icon.blue { background: #e8f0fe; color: #4318ff; }
        .admin-stat-card .icon.green { background: #e6faf0; color: #059669; }
        .admin-stat-card .icon.purple { background: #f3e8ff; color: #8b5cf6; }
        .admin-stat-card .icon.orange { background: #fef3c7; color: #d97706; }
        .admin-stat-card .icon.red { background: #fee2e2; color: #ef4444; }
        .admin-stat-card .info p { font-size: 0.8rem; color: #a3aed1; margin-bottom: 3px; margin-top:0;}
        .admin-stat-card .info h3 { font-size: 1.15rem; color: #2b3674; margin:0;}
        
        .charts-row-1 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .chart-card { background: #ffffff; border-radius: 16px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .chart-card h3 { font-size: 1.1rem; color: #2b3674; margin-top:0; margin-bottom: 20px; }
        .chart-container { position: relative; height: 280px; width: 100%; }

        /* Tables & Lists */
        .table-container { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 30px; }
        .table-header { padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .table-header h3 { margin: 0; color: #2b3674; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 16px 24px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        .data-table th { font-size: 0.85rem; color: #a3aed1; font-weight: 600; text-transform: uppercase; }
        .data-table td { font-size: 0.95rem; color: #2b3674; font-weight: 500; }
        .data-table tbody tr:hover { background: #f8fafc; }
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #e0f2fe; color: #075985; }
        
        .btn-primary { background: #4318ff; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px; font-family: inherit; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.95rem; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        /* Reports Grid */
        .reports-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .report-card { background: white; padding: 24px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .report-card:hover { transform: translateY(-3px); }
        .report-card .icon-box { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .report-card .text-box h4 { margin: 0 0 5px 0; color: #2b3674; font-size: 1.1rem; }
        .report-card .text-box p { margin: 0; color: #a3aed1; font-size: 0.85rem; }

        /* ── Pagination (Laravel default Tailwind output) ─────────────────── */
        nav[role="navigation"] { display: flex; flex-direction: column; gap: 10px; align-items: flex-start; }
        nav[role="navigation"] > div:first-child { font-size: 0.85rem; color: #a3aed1; }
        nav[role="navigation"] > div:last-child > span,
        nav[role="navigation"] > div:last-child > a { display: inline-flex; }
        .pagination-links { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
        /* Tailwind's span wrapper */
        nav[role="navigation"] span[aria-disabled="true"] span,
        nav[role="navigation"] span[aria-current="page"] span,
        nav[role="navigation"] a {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 36px; height: 36px; padding: 0 10px;
            border-radius: 8px; font-size: 0.85rem; font-weight: 500;
            font-family: 'Inter', sans-serif; text-decoration: none;
            transition: all 0.18s ease;
        }
        /* Normal page links */
        nav[role="navigation"] a {
            background: #f4f7fe; color: #2b3674; border: 1px solid #e2e8f0;
        }
        nav[role="navigation"] a:hover {
            background: #4318ff; color: #ffffff; border-color: #4318ff;
        }
        /* Active (current) page */
        nav[role="navigation"] span[aria-current="page"] span {
            background: #4318ff; color: #ffffff; border: 1px solid #4318ff;
        }
        /* Disabled prev/next */
        nav[role="navigation"] span[aria-disabled="true"] span {
            background: #f4f7fe; color: #c0cae8; border: 1px solid #e2e8f0;
            cursor: not-allowed;
        }
        /* Dots (...) */
        nav[role="navigation"] span[aria-disabled="true"]:not([aria-label]) span {
            background: transparent; border-color: transparent; color: #a3aed1;
        }
    </style>
</head>
<body>
    @php
        $currentSection = $section ?? 'dashboard';
    @endphp
    <div class="admin-layout">
        <!-- Sidebar -->
        <nav class="dark-sidebar">
            <div class="logo">
                <i class="fa-solid fa-building-columns"></i>
                <h2>Nexus Bank</h2>
            </div>
            <ul class="nav-links">
                <li class="{{ $currentSection === 'dashboard' ? 'active' : '' }}" data-target="dashboard-section"><i class="fa-solid fa-chart-pie"></i> Dashboard</li>
                
                <div class="nav-section">Management</div>
                <li class="{{ $currentSection === 'customers' ? 'active' : '' }}" data-target="customers-section"><i class="fa-solid fa-users"></i> Customers</li>
                <li class="{{ $currentSection === 'employees' ? 'active' : '' }}" data-target="employees-section"><i class="fa-solid fa-user-tie"></i> Employees</li>
                <li class="{{ $currentSection === 'branches' ? 'active' : '' }}" data-target="branches-section"><i class="fa-solid fa-code-branch"></i> Branches</li>
                <li class="{{ $currentSection === 'accounts' ? 'active' : '' }}" data-target="accounts-section"><i class="fa-solid fa-wallet"></i> Accounts</li>
                <li class="{{ $currentSection === 'loans' ? 'active' : '' }}" data-target="loans-section"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Approval</li>
                
                <div class="nav-section">Reports & Logs</div>
                <li class="{{ $currentSection === 'reports' ? 'active' : '' }}" data-target="reports-section"><i class="fa-solid fa-file-invoice"></i> Reports</li>
                <li class="{{ $currentSection === 'audit-logs' ? 'active' : '' }}" data-target="audit-section"><i class="fa-solid fa-clipboard-list"></i> Audit Logs</li>
                
                <div class="nav-section">System</div>
                <li class="{{ $currentSection === 'settings' ? 'active' : '' }}" data-target="settings-section"><i class="fa-solid fa-gear"></i> Settings</li>
                <li class="{{ $currentSection === 'profile' ? 'active' : '' }}" data-target="profile-section"><i class="fa-solid fa-user"></i> Profile</li>
            </ul>
            <div style="margin-top: auto; padding: 24px;">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 100%; padding: 12px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 0.95rem; transition: background 0.2s;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2 id="header-title" style="color: #2b3674; margin: 0 0 5px 0;">
                        {{ ucfirst($currentSection) }}
                    </h2>
                    <p id="header-subtitle" style="color: #a3aed1; font-size: 0.9rem; margin: 0;">Welcome back, Admin! Here's your systems panel.</p>
                </div>
                <div class="admin-header-right">
                    <div class="admin-profile">
                        <div style="text-align: right;">
                            <h4 style="color: #2b3674; font-size: 0.9rem; margin: 0;">{{ auth()->user()->first_name ?? 'Super' }} {{ auth()->user()->last_name ?? 'Admin' }}</h4>
                            <p style="color: #a3aed1; font-size: 0.75rem; margin: 0;">Super Admin</p>
                        </div>
                        <img src="https://i.pravatar.cc/150?img=11" alt="Profile">
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <!-- Session Alerts -->
                @if(session('success'))
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
                @endif

                <!-- 01. Dashboard Section -->
                <section id="dashboard-section" class="content-section {{ $currentSection === 'dashboard' ? 'active' : '' }}">
                    @if(isset($stats))
                        <div class="grid-6">
                            <div class="admin-stat-card"><div class="icon blue"><i class="fa-solid fa-users"></i></div><div class="info"><p>Customers</p><h3>{{ number_format($stats->total_customers) }}</h3></div></div>
                            <div class="admin-stat-card"><div class="icon green"><i class="fa-solid fa-user-tie"></i></div><div class="info"><p>Employees</p><h3>{{ number_format($stats->total_employees) }}</h3></div></div>
                            <div class="admin-stat-card"><div class="icon orange"><i class="fa-solid fa-building"></i></div><div class="info"><p>Branches</p><h3>{{ number_format($stats->total_branches) }}</h3></div></div>
                            <div class="admin-stat-card"><div class="icon purple"><i class="fa-solid fa-wallet"></i></div><div class="info"><p>Accounts</p><h3>{{ number_format($stats->total_accounts) }}</h3></div></div>
                            <div class="admin-stat-card"><div class="icon red"><i class="fa-solid fa-hand-holding-dollar"></i></div><div class="info"><p>Loans</p><h3>{{ number_format($stats->total_loans) }}</h3></div></div>
                            <div class="admin-stat-card"><div class="icon green"><i class="fa-solid fa-sack-dollar"></i></div><div class="info"><p>Total Deposits</p><h3>${{ number_format($stats->total_deposits, 2) }}</h3></div></div>
                        </div>
                        
                        <div class="charts-row-1">
                            <div class="chart-card">
                                <h3>Monthly Transactions Summary (Oracle View)</h3>
                                <div class="chart-container">
                                    <canvas id="monthlyTransChart"></canvas>
                                </div>
                            </div>
                            <div class="chart-card">
                                <h3>Total Deposits vs Loans Ratio</h3>
                                <div class="chart-container">
                                    <canvas id="depositsLoansRatioChart"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </section>

                <!-- 02. Customers Section -->
                <section id="customers-section" class="content-section {{ $currentSection === 'customers' ? 'active' : '' }}">
                    @if(isset($customers))
                        <div class="table-container">
                            <div class="table-header">
                                <h3>Customer Management</h3>
                                <form action="{{ route('admin.customers') }}" method="GET" style="display: flex; gap: 10px;">
                                    <input type="text" name="search" placeholder="Search customers..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; outline: none;">
                                    <button type="submit" class="btn-primary">Search</button>
                                </form>
                            </div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>NID</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->customer_id }}</td>
                                            <td>{{ $customer->full_name }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->phone }}</td>
                                            <td>{{ $customer->nid }}</td>
                                            <td>
                                                <span class="badge {{ $customer->status === 'ACTIVE' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $customer->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.customers.suspend', $customer->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="status" value="{{ $customer->status === 'ACTIVE' ? 'SUSPENDED' : 'ACTIVE' }}">
                                                    <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem; background: {{ $customer->status === 'ACTIVE' ? '#ef4444' : '#059669' }}">
                                                        {{ $customer->status === 'ACTIVE' ? 'Suspend' : 'Activate' }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7">No customers found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div style="padding: 15px 24px;">
                                {{ $customers->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </section>

                <!-- 03. Employees Section -->
                <section id="employees-section" class="content-section {{ $currentSection === 'employees' ? 'active' : '' }}">
                    @if(isset($employees))
                        <div class="table-container" style="padding: 24px; margin-bottom: 30px;">
                            <h3>Add New Employee (Oracle Stored Procedure)</h3>
                            <form action="{{ route('admin.employees.store') }}" method="POST" style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px;">
                                @csrf
                                <input type="text" name="first_name" placeholder="First Name" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <input type="text" name="last_name" placeholder="Last Name" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <input type="email" name="email" placeholder="Email" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <input type="text" name="phone" placeholder="Phone" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <input type="text" name="address" placeholder="Address" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <input type="text" name="nid" placeholder="NID" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <input type="date" name="dob" placeholder="DOB" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <input type="password" name="password" placeholder="Password" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <select name="branch_id" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    <option value="">Select Assign Branch</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn-primary" style="grid-column: span 3; justify-content: center; font-size: 1rem;"><i class="fa-solid fa-plus"></i> Create Employee via PL/SQL</button>
                            </form>
                        </div>

                        <div class="table-container">
                            <div class="table-header">
                                <h3>Employee Database</h3>
                            </div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $emp)
                                        <tr>
                                            <td>{{ $emp->customer_id }}</td>
                                            <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                            <td>{{ $emp->email }}</td>
                                            <td>{{ $emp->phone }}</td>
                                            <td>
                                                <span class="badge {{ $emp->status === 'ACTIVE' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $emp->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.employees.toggle', $emp->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem; background: {{ $emp->status === 'ACTIVE' ? '#ef4444' : '#059669' }}">
                                                        {{ $emp->status === 'ACTIVE' ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6">No employees found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <!-- 04. Branches Section -->
                <section id="branches-section" class="content-section {{ $currentSection === 'branches' ? 'active' : '' }}">
                    @if(isset($branches) && $currentSection === 'branches')
                        <div class="table-container" style="padding: 24px; margin-bottom: 30px;">
                            <h3>Add New Branch (Oracle PL/SQL procedure)</h3>
                            <form action="{{ route('admin.branches.store') }}" method="POST" style="display:flex; gap: 15px; margin-top: 15px;">
                                @csrf
                                <input type="text" name="branch_name" placeholder="Branch Name" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; flex: 1;">
                                <input type="text" name="location" placeholder="Location" required style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; flex: 1;">
                                <select name="manager_employee_id" style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; flex: 1;">
                                    <option value="">Assign Manager Employee ID</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->customer_id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->customer_id }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn-primary"><i class="fa-solid fa-plus"></i> Create Branch</button>
                            </form>
                        </div>

                        <div class="table-container">
                            <div class="table-header">
                                <h3>Branches List</h3>
                            </div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Branch ID</th>
                                        <th>Branch Name</th>
                                        <th>Location</th>
                                        <th>Manager</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($branches as $branch)
                                        <tr>
                                            <td>{{ $branch->branch_id }}</td>
                                            <td>{{ $branch->branch_name }}</td>
                                            <td>{{ $branch->location }}</td>
                                            <td>{{ $branch->first_name ? $branch->first_name . ' ' . $branch->last_name : 'Unassigned' }}</td>
                                            <td>
                                                <span class="badge {{ $branch->status === 'ACTIVE' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $branch->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.branches.update', $branch->branch_id) }}" method="POST" style="display:inline-flex; gap: 5px;">
                                                    @csrf
                                                    <input type="hidden" name="branch_name" value="{{ $branch->branch_name }}">
                                                    <input type="hidden" name="location" value="{{ $branch->location }}">
                                                    <select name="manager_employee_id" style="padding: 6px; font-size:0.8rem; border-radius:4px; border: 1px solid #e2e8f0;">
                                                        <option value="">Select Manager</option>
                                                        @foreach($employees as $emp)
                                                            <option value="{{ $emp->customer_id }}" {{ $branch->manager_employee_id === $emp->customer_id ? 'selected' : '' }}>
                                                                {{ $emp->first_name }} ({{ $emp->customer_id }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select name="status" style="padding: 6px; font-size:0.8rem; border-radius:4px; border: 1px solid #e2e8f0;">
                                                        <option value="ACTIVE" {{ $branch->status === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                                        <option value="INACTIVE" {{ $branch->status === 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                    <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">Update</button>
                                                </form>
                                                
                                                <form action="{{ route('admin.branches.delete', $branch->branch_id) }}" method="POST" style="display:inline; margin-left: 5px;">
                                                    @csrf
                                                    <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem; background: #ef4444;" onclick="return confirm('Are you sure you want to delete this branch?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6">No branches found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <!-- 05. Accounts Section -->
                <section id="accounts-section" class="content-section {{ $currentSection === 'accounts' ? 'active' : '' }}">
                    @if(isset($accounts))
                        <div class="table-container">
                            <div class="table-header">
                                <h3>Account Monitoring</h3>
                                <form action="{{ route('admin.accounts') }}" method="GET" style="display:flex; gap: 10px;">
                                    <input type="text" name="search" placeholder="Search account or client..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; outline: none;">
                                    <select name="branch_id" style="padding: 8px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; outline: none;">
                                        <option value="">All Branches</option>
                                        @foreach($branches as $b)
                                            <option value="{{ $b->branch_id }}" {{ request('branch_id') == $b->branch_id ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                                        @endforeach
                                    </select>
                                    <select name="status" style="padding: 8px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; outline: none;">
                                        <option value="">All Statuses</option>
                                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                    <button type="submit" class="btn-primary">Filter</button>
                                </form>
                            </div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Account No.</th>
                                        <th>Customer</th>
                                        <th>Branch</th>
                                        <th>Type</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($accounts as $acc)
                                        <tr>
                                            <td>{{ $acc->account_number }}</td>
                                            <td>{{ $acc->user->first_name ?? '' }} {{ $acc->user->last_name ?? '' }} ({{ $acc->user->customer_id ?? 'Unknown' }})</td>
                                            <td>{{ $acc->branch_name }}</td>
                                            <td>{{ $acc->account_type }}</td>
                                            <td>${{ number_format($acc->balance, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $acc->status === 'Active' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $acc->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6">No accounts found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div style="padding: 15px 24px;">
                                {{ $accounts->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </section>

                <!-- 06. Loans Section -->
                <section id="loans-section" class="content-section {{ $currentSection === 'loans' ? 'active' : '' }}">
                    @if(isset($loans))
                        <div class="table-container">
                            <div class="table-header">
                                <h3>Loan Final Approval Panel</h3>
                            </div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Loan ID</th>
                                        <th>Client</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loans as $loan)
                                        <tr>
                                            <td>LN-{{ $loan->id }}</td>
                                            <td>{{ $loan->first_name }} {{ $loan->last_name }} ({{ $loan->customer_id }})</td>
                                            <td>{{ $loan->loan_type }}</td>
                                            <td>${{ number_format($loan->amount, 2) }}</td>
                                            <td>{{ $loan->duration_months }} Months</td>
                                            <td>
                                                <span class="badge @if($loan->status === 'Pending') badge-warning @elseif($loan->status === 'Approved') badge-success @else badge-danger @endif">
                                                    {{ $loan->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($loan->status === 'Pending')
                                                    <div style="display:flex; gap:10px;">
                                                        <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="badge badge-success" style="border:none; cursor:pointer; font-family: inherit;">Approve via PL/SQL</button>
                                                        </form>
                                                        <form action="{{ route('admin.loans.reject', $loan->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="badge badge-danger" style="border:none; cursor:pointer; font-family: inherit;">Reject</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7">No loan requests found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <!-- 07. Reports Section -->
                <section id="reports-section" class="content-section {{ $currentSection === 'reports' ? 'active' : '' }}">
                    @if(isset($customerReport))
                        <div class="table-container" style="margin-bottom: 30px;">
                            <div class="table-header"><h3>Customer Database Report (Read-Only)</h3></div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customerReport as $cust)
                                        <tr>
                                            <td>{{ $cust->customer_id }}</td>
                                            <td>{{ $cust->full_name }}</td>
                                            <td>{{ $cust->email }}</td>
                                            <td>{{ $cust->phone }}</td>
                                            <td>{{ $cust->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-container" style="margin-bottom: 30px;">
                            <div class="table-header"><h3>Employee Roster Report (Read-Only)</h3></div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employeeReport as $emp)
                                        <tr>
                                            <td>{{ $emp->customer_id }}</td>
                                            <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                            <td>{{ $emp->email }}</td>
                                            <td>{{ $emp->branch_name ?? 'Central Branch' }}</td>
                                            <td>{{ $emp->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-container" style="margin-bottom: 30px;">
                            <div class="table-header"><h3>Branch Summary Report (Oracle View)</h3></div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Branch ID</th>
                                        <th>Branch Name</th>
                                        <th>Total Accounts</th>
                                        <th>Total Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($branchReport as $br)
                                        <tr>
                                            <td>{{ $br->branch_id }}</td>
                                            <td>{{ $br->branch_name }}</td>
                                            <td>{{ $br->total_accounts }}</td>
                                            <td>${{ number_format($br->total_balance, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <!-- 08. Audit Logs Section -->
                <section id="audit-section" class="content-section {{ $currentSection === 'audit-logs' ? 'active' : '' }}">
                    @if(isset($logs))
                        <div class="table-container">
                            <div class="table-header">
                                <h3><i class="fa-solid fa-clipboard-list" style="color:#4318ff; margin-right:8px;"></i>Audit Logs</h3>
                                <span style="font-size:0.85rem; color:#a3aed1; background:#f4f7fe; padding:4px 14px; border-radius:20px;">
                                    {{ $logs->total() }} total records
                                </span>
                            </div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Log ID</th>
                                        <th>Table</th>
                                        <th>Action</th>
                                        <th>Performed By</th>
                                        <th>Details</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        @php
                                            $action = strtoupper($log->action ?? '');
                                            $badgeClass = match(true) {
                                                in_array($action, ['INSERT','DEPOSIT','TRANSFER_IN','APPROVE']) => 'badge-success',
                                                in_array($action, ['UPDATE','TRANSFER','WITHDRAWAL','TRANSFER_OUT']) => 'badge-warning',
                                                in_array($action, ['DELETE','REJECT','SUSPEND']) => 'badge-danger',
                                                default => 'badge-info',
                                            };
                                        @endphp
                                        <tr>
                                            <td style="color:#a3aed1; font-size:0.82rem;">#{{ $log->id }}</td>
                                            <td>
                                                <span style="background:#f4f7fe; padding:3px 10px; border-radius:6px; font-size:0.8rem; color:#4318ff; font-weight:600;">
                                                    {{ strtoupper($log->table_name ?? '—') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $badgeClass }}">{{ $log->action }}</span>
                                            </td>
                                            <td style="font-weight:500;">{{ $log->performed_by ?? '—' }}</td>
                                            <td style="max-width:260px;">
                                                <span title="{{ $log->details }}" style="display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:260px; font-size:0.88rem;">
                                                    {{ $log->details ?? '—' }}
                                                </span>
                                            </td>
                                            <td style="white-space:nowrap; font-size:0.88rem; color:#a3aed1;">
                                                {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" style="text-align:center; color:#a3aed1; padding:40px;">
                                                <i class="fa-solid fa-clipboard" style="font-size:2rem; display:block; margin-bottom:10px; opacity:0.3;"></i>
                                                No audit logs found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Pagination footer -->
                            <div style="padding: 16px 24px; border-top: 1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                                <span style="font-size:0.85rem; color:#a3aed1;">
                                    Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} records
                                </span>
                                <div>
                                    {{ $logs->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </section>


                <!-- 09. Settings Section -->
                <section id="settings-section" class="content-section {{ $currentSection === 'settings' ? 'active' : '' }}">
                    @if(isset($settings))
                        <div class="table-container" style="padding: 30px;">
                            <h3 style="color: #2b3674; margin-bottom: 20px;">System Parameters</h3>
                            <form action="{{ route('admin.settings.update') }}" method="POST" style="display:grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                                @csrf
                                <div>
                                    <label style="display:block; color:#6b7a99; font-size:0.9rem; margin-bottom:8px;">Interest Rate (%)</label>
                                    <input type="number" step="0.01" name="interest_rate" value="{{ $settings['INTEREST_RATE'] ?? '5.0' }}" required style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px; outline:none; font-family:'Inter'; color:#2b3674;">
                                </div>
                                <div>
                                    <label style="display:block; color:#6b7a99; font-size:0.9rem; margin-bottom:8px;">Loan Interest Rate (%)</label>
                                    <input type="number" step="0.01" name="loan_interest" value="{{ $settings['LOAN_INTEREST'] ?? '8.5' }}" required style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px; outline:none; font-family:'Inter'; color:#2b3674;">
                                </div>
                                <div>
                                    <label style="display:block; color:#6b7a99; font-size:0.9rem; margin-bottom:8px;">OTP Expiry Time (Minutes)</label>
                                    <input type="number" name="otp_expiry" value="{{ $settings['OTP_EXPIRY'] ?? '10' }}" required style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px; outline:none; font-family:'Inter'; color:#2b3674;">
                                </div>
                                <div>
                                    <label style="display:block; color:#6b7a99; font-size:0.9rem; margin-bottom:8px;">Minimum Required Balance ($)</label>
                                    <input type="number" name="minimum_balance" value="{{ $settings['MINIMUM_BALANCE'] ?? '500' }}" required style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px; outline:none; font-family:'Inter'; color:#2b3674;">
                                </div>
                                <button type="submit" class="btn-primary" style="grid-column: span 2; margin-top:20px; justify-content: center; font-size: 1rem;"><i class="fa-solid fa-save"></i> Save Settings</button>
                            </form>
                        </div>
                    @endif
                </section>

                <!-- 10. Profile Section -->
                <section id="profile-section" class="content-section {{ $currentSection === 'profile' ? 'active' : '' }}">
                    <div class="table-container" style="padding: 30px; display:flex; gap:40px;">
                        <div style="text-align:center;">
                            <img src="https://i.pravatar.cc/150?img=11" alt="Profile" style="width:120px; height:120px; border-radius:50%; margin-bottom:15px;">
                            <h3 style="color:#2b3674; margin:0;">Super Admin</h3>
                            <p style="color:#a3aed1; font-size:0.9rem; margin-top:5px;">System Administrator</p>
                        </div>
                        <div style="flex:1;">
                            <h3 style="color: #2b3674; margin-bottom: 20px;">Profile Information</h3>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div><p style="color:#6b7a99; font-size:0.85rem; margin:0;">Full Name</p><p style="color:#2b3674; font-weight:500; margin-top:5px;">Super Admin</p></div>
                                <div><p style="color:#6b7a99; font-size:0.85rem; margin:0;">Email</p><p style="color:#2b3674; font-weight:500; margin-top:5px;">{{ auth()->user()->email ?? 'admin@nexus.com' }}</p></div>
                                <div><p style="color:#6b7a99; font-size:0.85rem; margin:0;">Phone</p><p style="color:#2b3674; font-weight:500; margin-top:5px;">+880 1712 345678</p></div>
                                <div><p style="color:#6b7a99; font-size:0.85rem; margin:0;">Role</p><p style="color:#2b3674; font-weight:500; margin-top:5px;">Admin</p></div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script>
        // JS Navigation logic (Redirects to correct data loading routes on menu clicks)
        const navLinks = document.querySelectorAll('.nav-links li[data-target]');
        
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                let url = '{{ route("admin.dashboard") }}';
                
                if (target === 'customers-section') url = '{{ route("admin.customers") }}';
                else if (target === 'employees-section') url = '{{ route("admin.employees") }}';
                else if (target === 'branches-section') url = '{{ route("admin.branches") }}';
                else if (target === 'accounts-section') url = '{{ route("admin.accounts") }}';
                else if (target === 'loans-section') url = '{{ route("admin.loans") }}';
                else if (target === 'reports-section') url = '{{ route("admin.reports") }}';
                else if (target === 'audit-section') url = '{{ route("admin.audit") }}';
                else if (target === 'settings-section') url = '{{ route("admin.settings") }}';
                
                window.location.href = url;
            });
        });

        // Charts configuration binding dynamic data
        @if($currentSection === 'dashboard' && isset($stats))
            // Monthly transactions line chart
            const transCtx = document.getElementById('monthlyTransChart').getContext('2d');
            const months = [
                @foreach($monthlyTransactions as $item)
                    '{{ $item->month }}',
                @endforeach
            ];
            const amounts = [
                @foreach($monthlyTransactions as $item)
                    {{ $item->total_amount }},
                @endforeach
            ];
            
            new Chart(transCtx, {
                type: 'line',
                data: {
                    labels: months.length > 0 ? months : ['Jan', 'Feb', 'Mar'],
                    datasets: [{
                        label: 'Total Transaction Amount ($)',
                        data: amounts.length > 0 ? amounts : [1200, 1900, 3000],
                        borderColor: '#4318ff',
                        backgroundColor: 'rgba(67, 24, 255, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Ratio of deposits vs loans chart
            const ratioCtx = document.getElementById('depositsLoansRatioChart').getContext('2d');
            new Chart(ratioCtx, {
                type: 'pie',
                data: {
                    labels: ['Deposits ($)', 'Loans ($)'],
                    datasets: [{
                        data: [
                            {{ $stats->total_deposits }},
                            {{ $stats->total_loans * 15000 }} // Estimated average loan size for visualization
                        ],
                        backgroundColor: ['#059669', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        @endif
    </script>
</body>
</html>
