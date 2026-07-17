<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Nexus Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="user-styles.css">
    <style>
        .summary-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px; }
        .summary-card { background: var(--card-bg, #fff); border-radius: 16px; padding: 22px 24px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .summary-card .sc-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
        .summary-card .sc-icon.green { background: #e6faf0; color: #059669; }
        .summary-card .sc-icon.red   { background: #fee2e2; color: #ef4444; }
        .summary-card .sc-icon.blue  { background: #e8f0fe; color: #4318ff; }
        .summary-card .sc-info p { margin: 0 0 4px; font-size: 0.8rem; color: var(--text-muted, #888); font-weight: 500; text-transform: uppercase; letter-spacing: 0.04em; }
        .summary-card .sc-info h3 { margin: 0; font-size: 1.35rem; font-weight: 700; color: var(--text-main, #1a1a2e); }

        .filter-panel { background: var(--card-bg, #fff); border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .filter-form { display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap; }
        .filter-group { display: flex; flex-direction: column; gap: 6px; min-width: 160px; }
        .filter-group label { font-size: 0.8rem; font-weight: 600; color: var(--text-muted, #888); text-transform: uppercase; letter-spacing: 0.04em; }
        .filter-group input, .filter-group select {
            padding: 10px 14px; border: 1.5px solid var(--border, #e2e8f0); border-radius: 10px;
            font-size: 0.9rem; font-family: 'Inter', sans-serif; color: var(--text-main, #1a1a2e);
            background: var(--input-bg, #f8fafc); outline: none; transition: border 0.2s;
        }
        .filter-group input:focus, .filter-group select:focus { border-color: var(--accent, #4318ff); }
        .filter-btn { padding: 10px 22px; background: var(--accent, #4318ff); color: #fff; border: none; border-radius: 10px; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: opacity 0.2s; align-self: flex-end; }
        .filter-btn:hover { opacity: 0.88; }
        .filter-reset { padding: 10px 18px; background: transparent; color: var(--text-muted, #888); border: 1.5px solid var(--border, #e2e8f0); border-radius: 10px; font-size: 0.9rem; font-family: 'Inter', sans-serif; cursor: pointer; align-self: flex-end; transition: all 0.2s; }
        .filter-reset:hover { border-color: #ef4444; color: #ef4444; }

        .txn-table-wrapper { background: var(--card-bg, #fff); border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden; }
        .txn-table-header { padding: 20px 24px; border-bottom: 1.5px solid var(--border, #f1f5f9); display: flex; justify-content: space-between; align-items: center; }
        .txn-table-header h3 { margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--text-main, #1a1a2e); }
        .txn-count { font-size: 0.85rem; color: var(--text-muted, #888); background: var(--border, #f1f5f9); padding: 4px 12px; border-radius: 20px; }

        .txn-table { width: 100%; border-collapse: collapse; }
        .txn-table th { padding: 14px 20px; text-align: left; font-size: 0.78rem; font-weight: 600; color: var(--text-muted, #888); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1.5px solid var(--border, #f1f5f9); }
        .txn-table td { padding: 16px 20px; font-size: 0.9rem; color: var(--text-main, #1a1a2e); border-bottom: 1px solid var(--border, #f8fafc); vertical-align: middle; }
        .txn-table tbody tr:hover { background: var(--hover-bg, #f8fafc); }
        .txn-table tbody tr:last-child td { border-bottom: none; }

        .txn-type-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .txn-type-badge.deposit    { background: #e6faf0; color: #059669; }
        .txn-type-badge.withdrawal { background: #fee2e2; color: #ef4444; }
        .txn-type-badge.transfer-out { background: #fff7ed; color: #d97706; }
        .txn-type-badge.transfer-in  { background: #e0f2fe; color: #0284c7; }
        .txn-type-badge.loan-emi   { background: #f3e8ff; color: #8b5cf6; }
        .txn-type-badge.other      { background: #f1f5f9; color: #64748b; }

        .txn-amount.credit { color: #059669; font-weight: 700; }
        .txn-amount.debit  { color: #ef4444; font-weight: 700; }

        .txn-desc { font-size: 0.82rem; color: var(--text-muted, #888); margin-top: 2px; }
        .txn-date  { font-size: 0.82rem; color: var(--text-muted, #888); }
        .txn-acc   { font-size: 0.82rem; color: var(--text-muted, #888); }

        .empty-state { text-align: center; padding: 60px 24px; }
        .empty-state i { font-size: 3rem; color: var(--border, #e2e8f0); margin-bottom: 16px; display: block; }
        .empty-state p { color: var(--text-muted, #888); font-size: 1rem; margin: 0; }

        .pagination-wrapper { padding: 18px 24px; border-top: 1.5px solid var(--border, #f1f5f9); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .pagination-wrapper .pagination-info { font-size: 0.85rem; color: var(--text-muted, #888); }
        .pagination-wrapper nav { display: flex; gap: 6px; }
        .pagination-wrapper nav a, .pagination-wrapper nav span { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: var(--text-main, #1a1a2e); background: var(--hover-bg, #f8fafc); text-decoration: none; transition: all 0.2s; border: 1.5px solid transparent; }
        .pagination-wrapper nav a:hover { background: var(--accent, #4318ff); color: #fff; }
        .pagination-wrapper nav span[aria-current="page"] { background: var(--accent, #4318ff); color: #fff; border-color: var(--accent, #4318ff); }
    </style>
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
            <li><a href="/user-transfer"><i class="fa-solid fa-arrow-right-arrow-left"></i> Fund Transfer</a></li>
            <li><a href="/user-transactions" class="active"><i class="fa-solid fa-clock-rotate-left"></i> Transaction History</a></li>
            <li><a href="/user-loan"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Services</a></li>
            <li><a href="/user-notifications"><i class="fa-solid fa-bell"></i> Notifications</a></li>
        </ul>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</button>
        </form>
    </nav>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menu-toggle"><i class="fa-solid fa-bars"></i></button>
                <div class="topbar-title">
                    <span style="font-weight:700; font-size:1.1rem; color:var(--text-main,#1a1a2e);">Transaction History</span>
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

            <!-- Session Alerts -->
            @if(session('success'))
                <div style="background:#e6faf0; color:#059669; border:1px solid #a7f3d0; padding:14px 20px; border-radius:12px; margin-bottom:20px; font-size:0.95rem; display:flex; align-items:center; gap:10px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="background:#fee2e2; color:#ef4444; border:1px solid #fecaca; padding:14px 20px; border-radius:12px; margin-bottom:20px; font-size:0.95rem; display:flex; align-items:center; gap:10px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="sc-icon green"><i class="fa-solid fa-arrow-down"></i></div>
                    <div class="sc-info">
                        <p>Total Credited</p>
                        <h3>+${{ number_format($totalCredit, 2) }}</h3>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="sc-icon red"><i class="fa-solid fa-arrow-up"></i></div>
                    <div class="sc-info">
                        <p>Total Debited</p>
                        <h3>-${{ number_format($totalDebit, 2) }}</h3>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="sc-icon blue"><i class="fa-solid fa-list-ul"></i></div>
                    <div class="sc-info">
                        <p>Total Transactions</p>
                        <h3>{{ $transactions->total() }}</h3>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-panel">
                <form method="GET" action="{{ route('user.transactions') }}" class="filter-form">
                    <div class="filter-group">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}">
                    </div>
                    <div class="filter-group">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}">
                    </div>
                    <div class="filter-group">
                        <label>Type</label>
                        <select name="type">
                            <option value="">All Types</option>
                            <option value="DEPOSIT"      {{ request('type') === 'DEPOSIT'      ? 'selected' : '' }}>Deposit</option>
                            <option value="WITHDRAWAL"   {{ request('type') === 'WITHDRAWAL'   ? 'selected' : '' }}>Withdrawal</option>
                            <option value="TRANSFER_OUT" {{ request('type') === 'TRANSFER_OUT' ? 'selected' : '' }}>Transfer Out</option>
                            <option value="TRANSFER_IN"  {{ request('type') === 'TRANSFER_IN'  ? 'selected' : '' }}>Transfer In</option>
                            <option value="LOAN_EMI"     {{ request('type') === 'LOAN_EMI'     ? 'selected' : '' }}>Loan EMI</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Account</label>
                        <select name="account">
                            <option value="">All Accounts</option>
                            @foreach($userAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ request('account') == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->account_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="filter-btn"><i class="fa-solid fa-filter"></i> Apply</button>
                    <a href="{{ route('user.transactions') }}" class="filter-reset">Reset</a>
                </form>
            </div>

            <!-- Transaction Table -->
            <div class="txn-table-wrapper">
                <div class="txn-table-header">
                    <h3><i class="fa-solid fa-clock-rotate-left" style="color:var(--accent,#4318ff); margin-right:8px;"></i>All Transactions</h3>
                    <span class="txn-count">{{ $transactions->total() }} records</span>
                </div>

                @if($transactions->isEmpty())
                    <div class="empty-state">
                        <i class="fa-solid fa-receipt"></i>
                        <p>No transactions found for the selected filters.</p>
                    </div>
                @else
                    <table class="txn-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Account</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $txn)
                                @php
                                    $type = strtoupper($txn->transaction_type);
                                    $isCredit = in_array($type, ['DEPOSIT', 'TRANSFER_IN']);
                                    $badgeClass = match($type) {
                                        'DEPOSIT'      => 'deposit',
                                        'WITHDRAWAL'   => 'withdrawal',
                                        'TRANSFER_OUT' => 'transfer-out',
                                        'TRANSFER_IN'  => 'transfer-in',
                                        'LOAN_EMI'     => 'loan-emi',
                                        default        => 'other',
                                    };
                                    $badgeIcon = match($type) {
                                        'DEPOSIT'      => 'fa-arrow-down-to-line',
                                        'WITHDRAWAL'   => 'fa-arrow-up-from-line',
                                        'TRANSFER_OUT' => 'fa-arrow-right',
                                        'TRANSFER_IN'  => 'fa-arrow-left',
                                        'LOAN_EMI'     => 'fa-hand-holding-dollar',
                                        default        => 'fa-circle',
                                    };
                                    $badgeLabel = match($type) {
                                        'DEPOSIT'      => 'Deposit',
                                        'WITHDRAWAL'   => 'Withdrawal',
                                        'TRANSFER_OUT' => 'Transfer Out',
                                        'TRANSFER_IN'  => 'Transfer In',
                                        'LOAN_EMI'     => 'Loan EMI',
                                        default        => ucfirst(strtolower($type)),
                                    };
                                    $parsedDate = \Carbon\Carbon::parse($txn->created_at);
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight:500;">{{ $parsedDate->format('d M Y') }}</div>
                                        <div class="txn-date">{{ $parsedDate->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        <span class="txn-type-badge {{ $badgeClass }}">
                                            <i class="fa-solid {{ $badgeIcon }}"></i>
                                            {{ $badgeLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight:500;">{{ $txn->account_number ?? 'N/A' }}</div>
                                        <div class="txn-acc">{{ $txn->branch ?? '' }}</div>
                                    </td>
                                    <td>
                                        <div class="txn-desc">{{ $txn->description ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <span class="txn-amount {{ $isCredit ? 'credit' : 'debit' }}">
                                            {{ $isCredit ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                        <span class="pagination-info">
                            Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
                        </span>
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>

    <script src="user-script.js"></script>
</body>
</html>
