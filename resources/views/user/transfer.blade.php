<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fund Transfer - Nexus Bank</title>
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
            <li><a href="/user-transfer" class="active"><i class="fa-solid fa-arrow-right-arrow-left"></i> Fund Transfer</a></li>
            <li><a href="/user-transactions"><i class="fa-solid fa-clock-rotate-left"></i> Transaction History</a></li>
            <li><a href="/user-dashboard"><i class="fa-solid fa-chart-line"></i> Balance Inquiry</a></li>
            <li><a href="/user-loan"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Services</a></li>
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
                    <input type="text" placeholder="Search transactions, accounts...">
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
                <h1 style="color: var(--text-main);">Fund Transfer</h1>
                <p style="color: var(--text-muted);">Securely transfer funds between your accounts or to other users.</p>
            </div>

            <div style="max-width: 600px; margin: 0 auto 32px auto;">
                <div class="section-panel" style="margin-bottom: 0;">
                    <div class="panel-header" style="justify-content: center; margin-bottom: 24px;">
                        <h3><i class="fa-solid fa-arrow-right-arrow-left" style="color: var(--accent); margin-right: 8px;"></i> Make a Transfer</h3>
                    </div>
                    
                    @if(session('success'))
                        <div style="padding: 12px; background: #e6f7eb; color: #2e7d32; border-radius: 6px; margin-bottom: 24px;">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div style="padding: 12px; background: #fdeded; color: #c62828; border-radius: 6px; margin-bottom: 24px;">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div style="padding: 12px; background: #fdeded; color: #c62828; border-radius: 6px; margin-bottom: 24px;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    
                    <form action="{{ route('user.transfer.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>From Account</label>
                            <select name="from_account" class="form-control" required>
                                @foreach($accounts as $account)
                                <option value="{{ $account->account_number }}">{{ $account->account_type }} ({{ $account->account_number }}) - Balance: ${{ number_format($account->balance, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>To Account Number</label>
                            <input type="text" name="to_account" class="form-control" placeholder="Enter recipient account number" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Amount ($)</label>
                            <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <input type="text" name="description" class="form-control" placeholder="What is this transfer for?">
                        </div>
                        
                        <button type="submit" class="btn-accent" style="margin-top: 16px;">
                            <i class="fa-solid fa-paper-plane" style="margin-right: 8px;"></i> Transfer Now
                        </button>
                    </form>
                </div>
            </div>

            <!-- Transfer History -->
            <div class="section-panel">
                <div class="panel-header">
                    <h3>Transfer History</h3>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $accountIds = $accounts->pluck('id')->toArray();
                            $transfers = \Illuminate\Support\Facades\DB::table('transactions')
                                ->whereIn('account_id', $accountIds)
                                ->whereIn('transaction_type', ['TRANSFER_OUT', 'TRANSFER_IN'])
                                ->orderBy('created_at', 'desc')
                                ->get();
                        @endphp
                        @forelse($transfers as $tx)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($tx->created_at)->format('d-m-Y') }}</td>
                            <td>{{ $tx->transaction_type == 'TRANSFER_OUT' ? 'Your Account' : $tx->reference }}</td>
                            <td>{{ $tx->transaction_type == 'TRANSFER_OUT' ? $tx->reference : 'Your Account' }}</td>
                            <td class="amount {{ $tx->transaction_type == 'TRANSFER_OUT' ? 'debit' : 'credit' }}">
                                {{ $tx->transaction_type == 'TRANSFER_OUT' ? '-' : '+' }} ${{ number_format($tx->amount, 2) }}
                            </td>
                            <td><span class="badge success">Completed</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">No transfers found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </div>

    <script src="user-script.js"></script>
</body>
</html>
