<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Services - Nexus Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="user-styles.css">
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/" class="logo">
                <i class="fa-solid fa-building-columns"></i>
                <span>Nexus Bank</span>
            </a>
            <button class="close-sidebar" id="close-sidebar"><i class="fa-solid fa-times"></i></button>
        </div>
        <ul class="nav-menu">
            <li><a href="/user-dashboard"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="/user-profile"><i class="fa-solid fa-user"></i> My Profile</a></li>
            <li><a href="/user-accounts"><i class="fa-solid fa-wallet"></i> My Accounts</a></li>
            <li><a href="/user-deposit"><i class="fa-solid fa-money-bill-transfer"></i> Deposit / Withdraw</a></li>
            <li><a href="/user-transfer"><i class="fa-solid fa-arrow-right-arrow-left"></i> Fund Transfer</a></li>
            <li><a href="/user-transactions"><i class="fa-solid fa-clock-rotate-left"></i> Transaction History</a></li>
            <li><a href="/user-dashboard"><i class="fa-solid fa-chart-line"></i> Balance Inquiry</a></li>
            <li><a href="/user-loan" class="active"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Services</a></li>
            <li><a href="/user-notifications"><i class="fa-solid fa-bell"></i> Notifications</a></li>
        </ul>
        <a href="/" class="logout-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
    </nav>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menu-toggle"><i class="fa-solid fa-bars"></i></button>
                <div class="search-box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="Search loans...">
                </div>
            </div>
            <div class="topbar-right">
                <button class="icon-action" id="dark-mode-toggle" title="Toggle Dark Mode">
                    <i class="fa-solid fa-moon"></i>
                </button>
                @php
                    $unreadNotificationsCount = \Illuminate\Support\Facades\DB::table('notifications')
                        ->where('user_id', auth()->id())
                        ->where('is_read', 0)
                        ->count();
                @endphp
                <button class="icon-action" title="Notifications" onclick="window.location.href='/user-notifications'">
                    <i class="fa-regular fa-bell"></i>
                    @if($unreadNotificationsCount > 0)
                        <span class="notification-badge">{{ $unreadNotificationsCount }}</span>
                    @endif
                </button>
                <div class="user-dropdown">
                    <img src="https://i.pravatar.cc/150?img=12" alt="User Profile" class="user-avatar">
                    <div class="user-info-top">
                        <h4>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h4>
                        <p>{{ ucfirst(strtolower(auth()->user()->role)) }} Account</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="dashboard-content" style="display: block;">
            
            <div class="section-header" style="margin-bottom: 24px;">
                <h1 style="color: var(--text-main);">Loan Services</h1>
                <p style="color: var(--text-muted);">Manage your active loans or apply for a new one.</p>
            </div>

            <!-- Loan Dashboard Cards -->
            <div class="summary-cards" style="margin-bottom: 32px;">
                <div class="card">
                    <div class="card-icon icon-blue"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <p class="card-title">Total Loan Amount</p>
                    <h2 class="card-value">$50,000.00</h2>
                </div>
                <div class="card">
                    <div class="card-icon icon-orange"><i class="fa-solid fa-sack-dollar"></i></div>
                    <p class="card-title">Remaining Amount</p>
                    <h2 class="card-value">$12,500.00</h2>
                </div>
                <div class="card">
                    <div class="card-icon icon-purple"><i class="fa-solid fa-calendar-check"></i></div>
                    <p class="card-title">Monthly Installment</p>
                    <h2 class="card-value">$450.00</h2>
                </div>
                <div class="card">
                    <div class="card-icon icon-green"><i class="fa-solid fa-stopwatch"></i></div>
                    <p class="card-title">Next Due Date</p>
                    <h2 class="card-value" style="font-size: 1.25rem;">15 June 2026</h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                
                <!-- Left: Application Form -->
                <div>
                    <div class="section-panel" style="margin-bottom: 0;">
                        <div class="panel-header">
                            <h3><i class="fa-solid fa-file-signature" style="color: var(--accent); margin-right: 8px;"></i> Apply for a Loan</h3>
                        </div>
                        <form>
                            <div class="form-group">
                                <label>Loan Type</label>
                                <select class="form-control">
                                    <option>Personal Loan</option>
                                    <option>Home Loan</option>
                                    <option>Car Loan</option>
                                    <option>Education Loan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Amount Needed ($)</label>
                                <input type="number" class="form-control" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label>Duration (Months)</label>
                                <select class="form-control">
                                    <option>12 Months</option>
                                    <option>24 Months</option>
                                    <option>36 Months</option>
                                    <option>48 Months</option>
                                    <option>60 Months</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Purpose of Loan</label>
                                <textarea class="form-control" rows="3" placeholder="Briefly explain the reason for the loan"></textarea>
                            </div>
                            <button type="button" class="btn-accent" style="margin-top: 16px;">
                                Apply for Loan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right: Loan Status Table -->
                <div>
                    <div class="section-panel" style="height: 100%;">
                        <div class="panel-header">
                            <h3>Active & Pending Loans</h3>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Loan ID</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>LN-88210</td>
                                    <td class="amount">$50,000.00</td>
                                    <td><span class="badge success">Active</span></td>
                                    <td><button class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">Pay EMI</button></td>
                                </tr>
                                <tr>
                                    <td>LN-99341</td>
                                    <td class="amount">$15,000.00</td>
                                    <td><span class="badge pending">Pending</span></td>
                                    <td><button class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;" disabled>Reviewing</button></td>
                                </tr>
                                <tr>
                                    <td>LN-44120</td>
                                    <td class="amount">$5,000.00</td>
                                    <td><span class="badge" style="background-color: var(--text-muted); color: white;">Closed</span></td>
                                    <td><button class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">Statement</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script src="user-script.js"></script>
</body>
</html>
