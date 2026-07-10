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
        .admin-header .search-box { background: #f4f7fe; border-radius: 20px; padding: 8px 16px; display: flex; align-items: center; gap: 10px; width: 300px; }
        .admin-header .search-box input { border: none; background: transparent; outline: none; width: 100%; font-size: 0.9rem; }
        .admin-header-right { display: flex; align-items: center; gap: 20px; }
        .admin-profile { display: flex; align-items: center; gap: 10px; }
        .admin-profile img { width: 40px; height: 40px; border-radius: 50%; }
        .admin-content { flex: 1; padding: 32px; overflow-y: auto; }
        
        /* SPA Sections */
        .content-section { display: none; animation: fadeIn 0.3s ease-in-out; }
        .content-section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Dashboard Cards & Charts */
        .grid-6 { display: grid; grid-template-columns: repeat(6, 1fr); gap: 20px; margin-bottom: 30px; }
        .admin-stat-card { background: #ffffff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 15px; }
        .admin-stat-card .icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
        .admin-stat-card .icon.blue { background: #e8f0fe; color: #4318ff; }
        .admin-stat-card .icon.green { background: #e6faf0; color: #059669; }
        .admin-stat-card .icon.purple { background: #f3e8ff; color: #8b5cf6; }
        .admin-stat-card .icon.orange { background: #fef3c7; color: #d97706; }
        .admin-stat-card .icon.red { background: #fee2e2; color: #ef4444; }
        .admin-stat-card .info p { font-size: 0.85rem; color: #a3aed1; margin-bottom: 5px; margin-top:0;}
        .admin-stat-card .info h3 { font-size: 1.3rem; color: #2b3674; margin:0;}
        .charts-row-1 { display: grid; grid-template-columns: 1fr 1fr 1.5fr; gap: 20px; margin-bottom: 30px; }
        .charts-row-2 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .chart-card { background: #ffffff; border-radius: 16px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .chart-card h3 { font-size: 1.1rem; color: #2b3674; margin-top:0; margin-bottom: 20px; }
        .chart-container { position: relative; height: 250px; width: 100%; }

        /* Tables & Lists */
        .table-container { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 30px; }
        .table-header { padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .table-header h3 { margin: 0; color: #2b3674; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 16px 24px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        .data-table th { font-size: 0.85rem; color: #a3aed1; font-weight: 600; text-transform: uppercase; }
        .data-table td { font-size: 0.95rem; color: #2b3674; font-weight: 500; }
        .data-table tbody tr:hover { background: #f8fafc; }
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #e0f2fe; color: #075985; }
        
        .btn-primary { background: #4318ff; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        
        /* Reports Grid */
        .reports-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .report-card { background: white; padding: 24px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .report-card:hover { transform: translateY(-3px); }
        .report-card .icon-box { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .report-card .text-box h4 { margin: 0 0 5px 0; color: #2b3674; font-size: 1.1rem; }
        .report-card .text-box p { margin: 0; color: #a3aed1; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <nav class="dark-sidebar">
            <div class="logo">
                <i class="fa-solid fa-building-columns"></i>
                <h2>Nexus Bank</h2>
            </div>
            <ul class="nav-links">
                <li class="active" data-target="dashboard-section"><i class="fa-solid fa-chart-pie"></i> Dashboard</li>
                
                <div class="nav-section">Management</div>
                <li data-target="customers-section"><i class="fa-solid fa-users"></i> Customers</li>
                <li data-target="employees-section"><i class="fa-solid fa-user-tie"></i> Employees</li>
                <li data-target="accounts-section"><i class="fa-solid fa-wallet"></i> Accounts</li>
                <li data-target="loans-section"><i class="fa-solid fa-hand-holding-dollar"></i> Loans</li>
                <li data-target="transactions-section"><i class="fa-solid fa-right-left"></i> Transactions</li>
                <li data-target="transfers-section"><i class="fa-solid fa-money-bill-transfer"></i> Transfers</li>
                <li data-target="branches-section"><i class="fa-solid fa-code-branch"></i> Branches</li>
                
                <div class="nav-section">Reports & Logs</div>
                <li data-target="reports-section"><i class="fa-solid fa-file-invoice"></i> Reports</li>
                <li data-target="audit-section"><i class="fa-solid fa-clipboard-list"></i> Audit Logs</li>
                <li data-target="notifications-section"><i class="fa-regular fa-bell"></i> Notifications</li>
                
                <div class="nav-section">System</div>
                <li data-target="settings-section"><i class="fa-solid fa-gear"></i> Settings</li>
                <li data-target="profile-section"><i class="fa-solid fa-user"></i> Profile</li>
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
                    <h2 id="header-title" style="color: #2b3674; margin: 0 0 5px 0;">Admin Dashboard</h2>
                    <p id="header-subtitle" style="color: #a3aed1; font-size: 0.9rem; margin: 0;">Welcome back, Admin! Here's an overview of your bank.</p>
                </div>
                <div class="admin-header-right">
                    <div class="search-box">
                        <i class="fa-solid fa-search" style="color: #a3aed1;"></i>
                        <input type="text" placeholder="Search anything...">
                    </div>
                    <i class="fa-regular fa-bell" style="font-size: 1.2rem; color: #a3aed1; cursor: pointer;"></i>
                    <div class="admin-profile">
                        <div style="text-align: right;">
                            <h4 style="color: #2b3674; font-size: 0.9rem; margin: 0;">{{ auth()->user()->first_name ?? 'Admin' }}</h4>
                            <p style="color: #a3aed1; font-size: 0.75rem; margin: 0;">Super Admin</p>
                        </div>
                        <img src="https://i.pravatar.cc/150?img=11" alt="Profile">
                    </div>
                </div>
            </header>

            <div class="admin-content">
                
                <!-- 01. Dashboard Section -->
                <section id="dashboard-section" class="content-section active">
                    <div class="grid-6">
                        <div class="admin-stat-card"><div class="icon blue"><i class="fa-solid fa-users"></i></div><div class="info"><p>Total Customers</p><h3>{{ number_format($stats['total_customers'] ?? 12850) }}</h3></div></div>
                        <div class="admin-stat-card"><div class="icon green"><i class="fa-solid fa-user-tie"></i></div><div class="info"><p>Total Employees</p><h3>{{ number_format($stats['total_employees'] ?? 286) }}</h3></div></div>
                        <div class="admin-stat-card"><div class="icon purple"><i class="fa-solid fa-wallet"></i></div><div class="info"><p>Total Accounts</p><h3>{{ number_format($stats['total_accounts'] ?? 18540) }}</h3></div></div>
                        <div class="admin-stat-card"><div class="icon green"><i class="fa-solid fa-sack-dollar"></i></div><div class="info"><p>Total Balance</p><h3>${{ number_format($stats['total_balance'] ?? 245680000, 2) }}</h3></div></div>
                        <div class="admin-stat-card"><div class="icon red"><i class="fa-solid fa-hand-holding-dollar"></i></div><div class="info"><p>Total Loans</p><h3>{{ number_format($stats['total_loans'] ?? 5430) }}</h3></div></div>
                        <div class="admin-stat-card"><div class="icon orange"><i class="fa-solid fa-building"></i></div><div class="info"><p>Total Branches</p><h3>{{ $stats['total_branches'] ?? 24 }}</h3></div></div>
                    </div>
                    <div class="charts-row-1">
                        <div class="chart-card"><h3>Account Types Overview</h3><div class="chart-container"><canvas id="accountTypesChart"></canvas></div></div>
                        <div class="chart-card"><h3>Loan Overview</h3><div class="chart-container"><canvas id="loanOverviewChart"></canvas></div></div>
                        <div class="chart-card">
                            <h3>Recent Transactions</h3>
                            <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #e8f0fe; color: #4318ff; display: flex; align-items: center; justify-content: center; font-weight: bold;">R</div>
                                        <div><p style="margin:0; font-weight: 600; color: #2b3674; font-size: 0.9rem;">Rahim Ahmed</p><p style="margin:0; font-size: 0.8rem; color: #a3aed1;">Transfer to ACC-52584</p></div>
                                    </div>
                                    <div style="text-align: right;"><p style="margin:0; font-weight: 600; color: #2b3674; font-size: 0.9rem;">$500.00</p><p style="margin:0; font-size: 0.8rem; color: #a3aed1;">02 Jul 2026</p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="charts-row-2">
                        <div class="chart-card"><h3>Branch Distribution</h3><div class="chart-container"><canvas id="branchDistributionChart"></canvas></div></div>
                        <div class="chart-card"><h3>Account Status</h3><div class="chart-container"><canvas id="accountStatusChart"></canvas></div></div>
                        <div class="chart-card"><h3>Loan Status</h3><div class="chart-container"><canvas id="loanStatusChart"></canvas></div></div>
                    </div>
                </section>

                <!-- 02. Customers Section -->
                <section id="customers-section" class="content-section">
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Customers</h3>
                            <button class="btn-primary"><i class="fa-solid fa-plus"></i> Add Customer</button>
                        </div>
                        <table class="data-table">
                            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                                <tr><td>CUS001</td><td>Rahim Ahmed</td><td>rahim@email.com</td><td>01712345678</td><td><span class="badge badge-success">Active</span></td><td><i class="fa-solid fa-pen" style="color: #4318ff; cursor:pointer;"></i></td></tr>
                                <tr><td>CUS002</td><td>Sumaiya Akter</td><td>sumaiya@email.com</td><td>01812345678</td><td><span class="badge badge-success">Active</span></td><td><i class="fa-solid fa-pen" style="color: #4318ff; cursor:pointer;"></i></td></tr>
                                <tr><td>CUS003</td><td>Nusrat Jahan</td><td>nusrat@email.com</td><td>01912345678</td><td><span class="badge badge-warning">Inactive</span></td><td><i class="fa-solid fa-pen" style="color: #4318ff; cursor:pointer;"></i></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- 03. Employees Section -->
                <section id="employees-section" class="content-section">
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Employees</h3>
                            <button class="btn-primary"><i class="fa-solid fa-plus"></i> Add Employee</button>
                        </div>
                        <table class="data-table">
                            <thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Manager</th><th>Join Date</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr><td>EMP001</td><td>Ashikur Rahman</td><td>Manager</td><td>Admin</td><td>01 Jan 2024</td><td><span class="badge badge-success">Active</span></td></tr>
                                <tr><td>EMP002</td><td>Farhana Islam</td><td>Officer</td><td>EMP001</td><td>15 Feb 2024</td><td><span class="badge badge-success">Active</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- 04. Accounts Section -->
                <section id="accounts-section" class="content-section">
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Accounts</h3>
                        </div>
                        <table class="data-table">
                            <thead><tr><th>Account No.</th><th>Customer Name</th><th>Type</th><th>Balance</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr><td>ACC-12314</td><td>Rahim Ahmed</td><td>Savings</td><td>$25,600.00</td><td><span class="badge badge-success">Active</span></td></tr>
                                <tr><td>ACC-55284</td><td>Sumaiya Akter</td><td>Current</td><td>$15,750.00</td><td><span class="badge badge-success">Active</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- 05. Loans Section -->
                <section id="loans-section" class="content-section">
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Loans</h3>
                        </div>
                        <table class="data-table">
                            <thead><tr><th>Loan ID</th><th>Customer Name</th><th>Type</th><th>Amount</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr><td>L001</td><td>Rahim Ahmed</td><td>Personal Loan</td><td>$50,000.00</td><td><span class="badge badge-success">Active</span></td></tr>
                                <tr><td>L002</td><td>Omar Faruk</td><td>Business Loan</td><td>$15,000.00</td><td><span class="badge badge-info">Completed</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- 06. Transactions Section -->
                <section id="transactions-section" class="content-section">
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Transactions</h3>
                        </div>
                        <table class="data-table">
                            <thead><tr><th>ID</th><th>Date</th><th>Account No.</th><th>Type</th><th>Amount</th></tr></thead>
                            <tbody>
                                <tr><td>TXN1001</td><td>02 Jul 2026</td><td>ACC-12314</td><td>Deposit</td><td><span style="color: #059669; font-weight:bold;">+$850.00</span></td></tr>
                                <tr><td>TXN1002</td><td>02 Jul 2026</td><td>ACC-55284</td><td>Withdrawal</td><td><span style="color: #ef4444; font-weight:bold;">-$1,500.00</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- 07. Transfers Section -->
                <section id="transfers-section" class="content-section">
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Transfers</h3>
                        </div>
                        <table class="data-table">
                            <thead><tr><th>ID</th><th>Date</th><th>From Account</th><th>To Account</th><th>Amount</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr><td>TRF1001</td><td>02 Jul 2026</td><td>ACC-12314</td><td>ACC-52584</td><td>$500.00</td><td><span class="badge badge-success">Completed</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- 08. Branches Section -->
                <section id="branches-section" class="content-section">
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Branches</h3>
                            <button class="btn-primary"><i class="fa-solid fa-plus"></i> Add Branch</button>
                        </div>
                        <table class="data-table">
                            <thead><tr><th>ID</th><th>Branch Name</th><th>Location</th><th>Manager</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr><td>BR001</td><td>Dhaka Main Branch</td><td>Dhaka</td><td>Ashikur Rahman</td><td><span class="badge badge-success">Active</span></td></tr>
                                <tr><td>BR002</td><td>Chittagong Branch</td><td>Chittagong</td><td>Farhana Islam</td><td><span class="badge badge-success">Active</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- 09. Reports Section -->
                <section id="reports-section" class="content-section">
                    <h3 style="color: #2b3674; margin-bottom: 20px;">System Reports</h3>
                    <div class="reports-grid">
                        <div class="report-card">
                            <div style="display:flex; gap:15px; align-items:center;">
                                <div class="icon-box" style="background:#e8f0fe; color:#4318ff;"><i class="fa-solid fa-user-chart"></i></div>
                                <div class="text-box"><h4>Customer Report</h4><p>View customer details and statistics</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="color: #a3aed1;"></i>
                        </div>
                        <div class="report-card">
                            <div style="display:flex; gap:15px; align-items:center;">
                                <div class="icon-box" style="background:#e6faf0; color:#059669;"><i class="fa-solid fa-wallet"></i></div>
                                <div class="text-box"><h4>Account Report</h4><p>View account summary and details</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="color: #a3aed1;"></i>
                        </div>
                        <div class="report-card">
                            <div style="display:flex; gap:15px; align-items:center;">
                                <div class="icon-box" style="background:#fef3c7; color:#d97706;"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                                <div class="text-box"><h4>Loan Report</h4><p>View loan statistics and details</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="color: #a3aed1;"></i>
                        </div>
                        <div class="report-card">
                            <div style="display:flex; gap:15px; align-items:center;">
                                <div class="icon-box" style="background:#fee2e2; color:#ef4444;"><i class="fa-solid fa-money-bill-transfer"></i></div>
                                <div class="text-box"><h4>Transaction Report</h4><p>View transaction summary and details</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="color: #a3aed1;"></i>
                        </div>
                        <div class="report-card">
                            <div style="display:flex; gap:15px; align-items:center;">
                                <div class="icon-box" style="background:#f3e8ff; color:#8b5cf6;"><i class="fa-solid fa-code-branch"></i></div>
                                <div class="text-box"><h4>Branch Report</h4><p>View branch performance report</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="color: #a3aed1;"></i>
                        </div>
                        <div class="report-card">
                            <div style="display:flex; gap:15px; align-items:center;">
                                <div class="icon-box" style="background:#e0f2fe; color:#075985;"><i class="fa-solid fa-chart-line"></i></div>
                                <div class="text-box"><h4>Financial Report</h4><p>View financial overview report</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="color: #a3aed1;"></i>
                        </div>
                    </div>
                </section>
                
                <!-- 10. Audit Logs Section -->
                <section id="audit-section" class="content-section">
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Audit Logs</h3>
                        </div>
                        <table class="data-table">
                            <thead><tr><th>Log ID</th><th>Table Name</th><th>Action</th><th>Performed By</th><th>Date</th></tr></thead>
                            <tbody>
                                <tr><td>LOG1001</td><td>ACCOUNTS</td><td><span class="badge badge-success">INSERT</span></td><td>Ashikur Rahman</td><td>02 Jul 2026 10:14 AM</td></tr>
                                <tr><td>LOG1002</td><td>LOANS</td><td><span class="badge badge-warning">UPDATE</span></td><td>Farhana Islam</td><td>02 Jul 2026 03:45 PM</td></tr>
                                <tr><td>LOG1003</td><td>USERS</td><td><span class="badge badge-danger">DELETE</span></td><td>Super Admin</td><td>01 Jul 2026 09:20 AM</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- 11. Notifications Section -->
                <section id="notifications-section" class="content-section">
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Notifications</h3>
                            <a href="#" style="color:#4318ff; text-decoration:none; font-size:0.9rem; font-weight:600;">Mark all as read</a>
                        </div>
                        <div style="padding: 20px 24px;">
                            <div style="display:flex; align-items:center; gap:15px; padding:15px 0; border-bottom:1px solid #f1f5f9;">
                                <div style="width:40px; height:40px; border-radius:50%; background:#e0f2fe; color:#075985; display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-user-plus"></i></div>
                                <div style="flex:1;"><p style="margin:0; color:#2b3674; font-weight:500;">New customer Rahim Ahmed has been registered.</p></div>
                                <div style="color:#a3aed1; font-size:0.85rem;">2 mins ago</div>
                            </div>
                            <div style="display:flex; align-items:center; gap:15px; padding:15px 0; border-bottom:1px solid #f1f5f9;">
                                <div style="width:40px; height:40px; border-radius:50%; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-file-signature"></i></div>
                                <div style="flex:1;"><p style="margin:0; color:#2b3674; font-weight:500;">Loan approved for customer Sumaiya Akter.</p></div>
                                <div style="color:#a3aed1; font-size:0.85rem;">15 mins ago</div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- 12. Settings Section -->
                <section id="settings-section" class="content-section">
                    <div class="table-container" style="padding: 30px;">
                        <h3 style="color: #2b3674; margin-bottom: 20px;">General Settings</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                            <div>
                                <label style="display:block; color:#6b7a99; font-size:0.9rem; margin-bottom:8px;">Bank Name</label>
                                <input type="text" value="Nexus Bank Limited" style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px; outline:none; font-family:'Inter'; color:#2b3674;">
                            </div>
                            <div>
                                <label style="display:block; color:#6b7a99; font-size:0.9rem; margin-bottom:8px;">Currency</label>
                                <select style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px; outline:none; font-family:'Inter'; color:#2b3674;">
                                    <option>USD ($)</option>
                                    <option>BDT (৳)</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block; color:#6b7a99; font-size:0.9rem; margin-bottom:8px;">Time Zone</label>
                                <select style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px; outline:none; font-family:'Inter'; color:#2b3674;">
                                    <option>UTC +06:00 Dhaka</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn-primary" style="margin-top: 30px;">Save Changes</button>
                    </div>
                </section>
                
                <!-- 13. Profile Section -->
                <section id="profile-section" class="content-section">
                    <div class="table-container" style="padding: 30px; display:flex; gap:40px;">
                        <div style="text-align:center;">
                            <img src="https://i.pravatar.cc/150?img=11" alt="Profile" style="width:120px; height:120px; border-radius:50%; margin-bottom:15px;">
                            <h3 style="color:#2b3674; margin:0;">Super Admin</h3>
                            <p style="color:#a3aed1; font-size:0.9rem; margin-top:5px;">System Administrator</p>
                            <button class="btn-primary" style="margin: 15px auto;">Edit Profile</button>
                        </div>
                        <div style="flex:1;">
                            <h3 style="color: #2b3674; margin-bottom: 20px;">Profile Information</h3>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div><p style="color:#6b7a99; font-size:0.85rem; margin:0;">Full Name</p><p style="color:#2b3674; font-weight:500; margin-top:5px;">Super Admin</p></div>
                                <div><p style="color:#6b7a99; font-size:0.85rem; margin:0;">Email</p><p style="color:#2b3674; font-weight:500; margin-top:5px;">admin@nexus.com</p></div>
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
        // JS Navigation logic
        const navLinks = document.querySelectorAll('.nav-links li[data-target]');
        const sections = document.querySelectorAll('.content-section');
        const headerTitle = document.getElementById('header-title');
        const headerSubtitle = document.getElementById('header-subtitle');

        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Remove active class from all links
                navLinks.forEach(l => l.classList.remove('active'));
                // Add active class to clicked link
                this.classList.add('active');
                
                // Hide all sections
                sections.forEach(s => s.classList.remove('active'));
                
                // Show target section
                const targetId = this.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active');
                
                // Update header titles based on clicked section
                const sectionName = this.innerText.trim();
                if(sectionName === 'Dashboard') {
                    headerTitle.innerText = 'Admin Dashboard';
                    headerSubtitle.innerText = 'Welcome back, Admin! Here\'s an overview of your bank.';
                } else {
                    headerTitle.innerText = sectionName;
                    headerSubtitle.innerText = 'Manage ' + sectionName.toLowerCase() + ' details and records.';
                }
            });
        });

        // Chart configurations
        const accountTypesCtx = document.getElementById('accountTypesChart').getContext('2d');
        new Chart(accountTypesCtx, {
            type: 'doughnut',
            data: { labels: ['Savings', 'Current', 'Fixed Deposit', 'Others'], datasets: [{ data: [49.9, 33, 15.4, 1.7], backgroundColor: ['#4318ff', '#059669', '#f59e0b', '#8b5cf6'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'right' } } }
        });

        const loanOverviewCtx = document.getElementById('loanOverviewChart').getContext('2d');
        new Chart(loanOverviewCtx, {
            type: 'doughnut',
            data: { labels: ['Personal Loan', 'Home Loan', 'Business Loan', 'Other Loans'], datasets: [{ data: [45.1, 32.3, 17.5, 4.6], backgroundColor: ['#ef4444', '#3b82f6', '#f59e0b', '#8b5cf6'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'right' } } }
        });

        const branchDistCtx = document.getElementById('branchDistributionChart').getContext('2d');
        new Chart(branchDistCtx, {
            type: 'bar',
            data: { labels: ['Dhaka', 'Chittagong', 'Sylhet', 'Khulna', 'Rajshahi'], datasets: [{ label: 'Branches', data: [5, 4, 3, 3, 3], backgroundColor: '#4318ff', borderRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        const accountStatusCtx = document.getElementById('accountStatusChart').getContext('2d');
        new Chart(accountStatusCtx, {
            type: 'bar',
            data: { labels: ['Active', 'Inactive', 'Closed', 'Frozen'], datasets: [{ label: 'Accounts', data: [15230, 1850, 980, 480], backgroundColor: ['#4318ff', '#ef4444', '#64748b', '#3b82f6'], borderRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        const loanStatusCtx = document.getElementById('loanStatusChart').getContext('2d');
        new Chart(loanStatusCtx, {
            type: 'bar',
            data: { labels: ['Active', 'Completed', 'Closed', 'Defaulted'], datasets: [{ label: 'Loans', data: [5230, 1850, 350, 130], backgroundColor: ['#10b981', '#8b5cf6', '#64748b', '#ef4444'], borderRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    </script>
</body>
</html>
