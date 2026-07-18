<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Nexus Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="user-styles.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="logo">
                <i class="fa-solid fa-building-columns"></i>
                <span>Nexus Bank</span>
            </a>
            <button class="close-sidebar" id="close-sidebar"><i class="fa-solid fa-times"></i></button>
        </div>
        <ul class="nav-menu">
            <li><a href="/user-dashboard" class="active"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="/user-profile"><i class="fa-solid fa-user"></i> My Profile</a></li>
            <li><a href="/user-accounts"><i class="fa-solid fa-wallet"></i> My Accounts</a></li>
            <li><a href="/user-transfer"><i class="fa-solid fa-arrow-right-arrow-left"></i> Fund Transfer</a></li>
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
        <main class="dashboard-content">
            @if(session('ticket_success'))
                <div style="grid-column: span 2; background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; border: 1px solid #bbf7d0; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i>{{ session('ticket_success') }}
                </div>
            @endif
            @if(session('ticket_error'))
                <div style="grid-column: span 2; background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; border: 1px solid #fecaca; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-xmark" style="margin-right: 8px;"></i>{{ session('ticket_error') }}
                </div>
            @endif

            <!-- Left Column -->
            <div class="left-col">
                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="card">
                        <div class="card-icon icon-blue"><i class="fa-solid fa-wallet"></i></div>
                        <p class="card-title">Total Balance</p>
                        <h2 class="card-value">${{ number_format($totalBalance, 2) }}</h2>
                    </div>
                    @if($accounts->count() > 0)
                    <div class="card">
                        <div class="card-icon icon-green"><i class="fa-solid fa-building-columns"></i></div>
                        <p class="card-title">Primary Account</p>
                        <h2 class="card-value" style="font-size: 1.25rem;">{{ $accounts->first()->account_number }}</h2>
                    </div>
                    <div class="card">
                        <div class="card-icon icon-purple"><i class="fa-solid fa-piggy-bank"></i></div>
                        <p class="card-title">Account Type</p>
                        <h2 class="card-value" style="font-size: 1.25rem;">{{ $accounts->first()->account_type }}</h2>
                    </div>
                    @else
                    <div class="card" style="grid-column: span 2;">
                        <p class="card-title">No Active Accounts</p>
                        <h2 class="card-value" style="font-size: 1.25rem;">Please apply for an account.</h2>
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-icon icon-orange"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <p class="card-title">Last Transaction</p>
                        <h2 class="card-value" style="font-size: 1.25rem;">
                            {{ $recentTransactions->first() ? \Carbon\Carbon::parse($recentTransactions->first()->created_at)->format('d M Y') : 'No Activity' }}
                        </h2>
                    </div>
                </div>

                <div class="quick-actions-section">
                    <h2>Quick Actions</h2>
                    <div class="quick-actions">
                        <a href="/user-transfer" class="action-btn">
                            <i class="fa-solid fa-exchange-alt"></i>
                            <span>Transfer Funds</span>
                        </a>
                        <a href="/user-transactions" class="action-btn">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span>View Statement</span>
                        </a>
                    </div>
                </div>

                <!-- Balance Analytics -->
                <div class="section-panel">
                    <div class="panel-header">
                        <h3>Balance Analytics</h3>
                        <a href="#" class="view-all"><i class="fa-solid fa-download"></i> Download PDF</a>
                    </div>
                    <div class="chart-container">
                        <canvas id="balanceChart"></canvas>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="section-panel">
                    <div class="panel-header">
                        <h3>Recent Transactions</h3>
                        <a href="#" class="view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($tx->created_at)->format('d-m-Y') }}</td>
                                <td>
                                    @if($tx->transaction_type == 'DEPOSIT')
                                        <span class="badge success">Deposit</span>
                                    @elseif($tx->transaction_type == 'WITHDRAW')
                                        <span class="badge pending">Withdrawal</span>
                                    @elseif($tx->transaction_type == 'TRANSFER_OUT')
                                        <span class="badge pending">Transfer Out</span>
                                    @elseif($tx->transaction_type == 'TRANSFER_IN')
                                        <span class="badge success">Transfer In</span>
                                    @else
                                        <span class="badge pending">{{ $tx->transaction_type }}</span>
                                    @endif
                                </td>
                                <td class="amount {{ in_array($tx->transaction_type, ['WITHDRAW', 'TRANSFER_OUT']) ? 'debit' : 'credit' }}">
                                    {{ in_array($tx->transaction_type, ['WITHDRAW', 'TRANSFER_OUT']) ? '-' : '+' }} ${{ number_format($tx->amount, 2) }}
                                </td>
                                <td><span class="badge success">Success</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align: center;">No recent transactions.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Loan Section -->
                <div class="loan-info">
                    <div class="loan-details">
                        <h4>Personal Loan <span class="badge pending">Active</span></h4>
                        <p>Remaining Amount: <strong>$12,500.00</strong></p>
                        <p>Next Payment: 15 June 2026 ($450.00)</p>
                    </div>
                    <button class="btn-primary">Apply Loan / Pay</button>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-col">
                <!-- Profile Box -->
                <div class="section-panel profile-box">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->first_name }}" class="user-avatar">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&background=random" alt="{{ $user->first_name }}" class="user-avatar">
                    @endif
                    <h3>{{ $user->full_name }} <i class="fa-solid fa-circle-check" style="color: var(--success); font-size: 1rem;" title="Verified"></i></h3>
                    <p>{{ $user->email }}</p>
                    
                    <div class="status-badges">
                        <span class="status-badge"><i class="fa-solid fa-shield-halved"></i> Active</span>
                        <span class="status-badge"><i class="fa-solid fa-lock"></i> Secured</span>
                    </div>

                    <div class="profile-details">
                        <div class="detail-item">
                            <span>Phone</span>
                            <span>{{ $user->phone ?? 'Not provided' }}</span>
                        </div>
                        <div class="detail-item">
                            <span>Joined</span>
                            <span>{{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notifications Panel -->
                <div class="section-panel">
                    <div class="panel-header">
                        <h3>Notifications</h3>
                        <a href="#" class="view-all">Mark read</a>
                    </div>
                    <div class="notification-list">
                        @forelse($notifications as $notif)
                        <div class="notification-item {{ $notif->is_read ? '' : 'info' }}">
                            <div class="notif-icon {{ $notif->is_read ? 'success' : 'info' }}"><i class="fa-solid fa-bell"></i></div>
                            <div class="notif-content">
                                <p>{{ $notif->message }}</p>
                                <span class="notif-time">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</span>
                            </div>
                        </div>
                        @empty
                        <div style="padding: 1rem; text-align: center; color: var(--text-muted);">No new notifications</div>
                        @endforelse
                    </div>
                </div>

                <!-- Support & Feedback Panel -->
                <div class="section-panel" style="margin-top: 20px;">
                    <div class="panel-header">
                        <h3><i class="fa-solid fa-circle-question" style="color: #4318ff; margin-right: 8px;"></i>Support & Feedback</h3>
                    </div>
                    <form action="{{ route('user.support.submit') }}" method="POST" style="display: flex; flex-direction: column; gap: 12px; padding: 15px 0 0 0;">
                        @csrf
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 500; color: #6b7a99; display: block; margin-bottom: 6px;">Request Type</label>
                            <select name="ticket_type" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 0.9rem; outline: none; background: #fff; color: #2b3674;">
                                <option value="COMPLAINT">Complaint</option>
                                <option value="FEEDBACK">Feedback / Advantage</option>
                                <option value="REQUEST">Need of Extra Report / Service</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 500; color: #6b7a99; display: block; margin-bottom: 6px;">Priority</label>
                            <select name="priority" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 0.9rem; outline: none; background: #fff; color: #2b3674;">
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 500; color: #6b7a99; display: block; margin-bottom: 6px;">Subject</label>
                            <input type="text" name="subject" required placeholder="Brief title of the issue" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 0.9rem; outline: none; box-sizing: border-box; color: #2b3674;">
                        </div>
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 500; color: #6b7a99; display: block; margin-bottom: 6px;">Details / Message</label>
                            <textarea name="message" required rows="4" placeholder="Explain your feedback or issue in detail..." style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 0.9rem; outline: none; resize: vertical; box-sizing: border-box; color: #2b3674;"></textarea>
                        </div>
                        <button type="submit" class="btn-primary" style="margin-top: 10px; font-size: 0.95rem; justify-content: center; width: 100%; padding: 12px; border-radius: 8px;">
                            <i class="fa-solid fa-paper-plane"></i> Submit Support Request
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="user-script.js"></script>
</body>
</html>
