<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Bank Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    
    <nav class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-building-columns"></i>
            <h2>Nexus Bank</h2>
        </div>
        <ul class="nav-links">
            <li class="active" data-target="dashboard-section"><i class="fa-solid fa-chart-pie"></i> Dashboard</li>
            <li data-target="customers-section"><i class="fa-solid fa-users"></i> Customers</li>
            <li data-target="accounts-section"><i class="fa-solid fa-wallet"></i> Accounts</li>
            <li data-target="loans-section"><i class="fa-solid fa-hand-holding-dollar"></i> Loans & Payments</li>
            <li data-target="deposit-section"><i class="fa-solid fa-money-bill-wave"></i> Deposit Money</li>
            <li data-target="withdraw-section"><i class="fa-solid fa-hand-holding-dollar"></i> Withdraw Money</li>
            <li data-target="transfer-section"><i class="fa-solid fa-right-left"></i> Money Transfer</li>
            <li data-target="profile-section"><i class="fa-solid fa-user"></i> My Profile</li>
                <li data-target="notifications-section"><i class="fa-regular fa-bell"></i> Notifications</li>
        </ul>
        <div class="user-profile">
            <img src="https://i.pravatar.cc/150?img=11" alt="Admin Profile">
            <div>
                <h4>{{ auth()->user()->first_name ?? 'Admin' }} {{ auth()->user()->last_name ?? 'User' }}</h4>
                <p>Role: {{ ucfirst(strtolower(auth()->user()->role ?? 'Admin')) }}</p>
            </div>
        </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="margin-top: 20px;">
                    @csrf
                    <button type="submit" class="btn outline"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                </form>
    </nav>

   
    <main class="main-content">
        
       
        <header>
            <div class="search-bar">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Search across bank records...">
            </div>
            <div class="header-actions">
                <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                <button class="icon-btn"><i class="fa-solid fa-gear"></i></button>
            </div>
        </header>

        <!-- Dashboard Section -->
        <section id="dashboard-section" class="content-section active">
            <div class="section-header">
                <h1>Overview</h1>
                <p>Welcome back, <strong>{{ auth()->user()->first_name }}</strong>!
                    @if($branchName) &nbsp;Branch: <strong>{{ $branchName }}</strong> @endif
                    &nbsp;— Here's what's happening today.
                </p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-info">
                        <h3>Branch Customers</h3>
                        <h2>{{ number_format($stats->total_customers) }}</h2>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-vault"></i></div>
                    <div class="stat-info">
                        <h3>Branch Balance</h3>
                        <h2>${{ number_format($stats->branch_balance, 0) }}</h2>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <div class="stat-info">
                        <h3>Active Loans Total</h3>
                        <h2>${{ number_format($stats->active_loans_total, 0) }}</h2>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fa-solid fa-wallet"></i></div>
                    <div class="stat-info">
                        <h3>Active Accounts</h3>
                        <h2>{{ number_format($stats->total_accounts) }}</h2>
                    </div>
                </div>
            </div>

            <!-- Quick-action alert badges -->
            @if($stats->pending_accounts > 0 || $stats->pending_loans > 0)
            <div style="display:flex; gap:14px; margin-bottom:24px; flex-wrap:wrap;">
                @if($stats->pending_accounts > 0)
                <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; padding:12px 18px; display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="document.querySelector('[data-target=accounts-section]').click()">
                    <i class="fa-solid fa-clock" style="color:#d97706;"></i>
                    <span style="font-size:0.9rem; font-weight:600; color:#92400e;">{{ $stats->pending_accounts }} Pending Account{{ $stats->pending_accounts > 1 ? 's' : '' }} awaiting approval</span>
                </div>
                @endif
                @if($stats->pending_loans > 0)
                <div style="background:#fef3c7; border:1px solid #fde68a; border-radius:10px; padding:12px 18px; display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="document.querySelector('[data-target=loans-section]').click()">
                    <i class="fa-solid fa-hand-holding-dollar" style="color:#b45309;"></i>
                    <span style="font-size:0.9rem; font-weight:600; color:#92400e;">{{ $stats->pending_loans }} Pending Loan{{ $stats->pending_loans > 1 ? 's' : '' }} to review</span>
                </div>
                @endif
            </div>
            @endif

            <div class="recent-activity">
                <h2>Recent Branch Transactions</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Account No</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $txn)
                            @php
                                $type = strtoupper($txn->transaction_type);
                                $isCredit = in_array($type, ['DEPOSIT','TRANSFER_IN']);
                                $badgeClass = match($type) {
                                    'DEPOSIT'      => 'info',
                                    'WITHDRAWAL'   => 'warning',
                                    'TRANSFER_OUT' => 'warning',
                                    'TRANSFER_IN'  => 'info',
                                    'LOAN_EMI'     => 'purple',
                                    default        => 'pending',
                                };
                                $label = match($type) {
                                    'DEPOSIT'      => 'Deposit',
                                    'WITHDRAWAL'   => 'Withdrawal',
                                    'TRANSFER_OUT' => 'Transfer Out',
                                    'TRANSFER_IN'  => 'Transfer In',
                                    'LOAN_EMI'     => 'Loan EMI',
                                    default        => ucfirst(strtolower($type)),
                                };
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($txn->created_at)->format('d M, H:i') }}</td>
                                <td>{{ $txn->account_number }}</td>
                                <td>{{ $txn->first_name }} {{ $txn->last_name }}</td>
                                <td><span class="badge {{ $badgeClass }}">{{ $label }}</span></td>
                                <td class="amount {{ $isCredit ? 'positive' : 'negative' }}">
                                    {{ $isCredit ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:#aaa; padding:30px;">No recent transactions for this branch.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>


        <!-- Customers Section -->
        <section id="customers-section" class="content-section">
            <div class="section-header flex-between">
                <div>
                    <h1>Customers</h1>
                    <p>Manage bank customers and their personal details.</p>
                </div>

            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cust ID</th>
                            <th>Name (First, Last)</th>
                            <th>Mobile No</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

@forelse($customers as $customer)

<tr>
    <td>{{ $customer->customer_id }}</td>

    <td>
        {{ $customer->first_name }}
        {{ $customer->last_name }}
    </td>

    <td>{{ $customer->phone }}</td>

    <td>{{ $customer->address }}</td>

    <td>

        <a href="{{ route('employee.customer.edit', $customer->id) }}"
           class="action-btn edit">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('employee.customer.delete', $customer->id) }}"
              method="POST"
              style="display:inline;">

            @csrf
            @method('DELETE')

            <button class="action-btn delete"
                    onclick="return confirm('Delete this customer?')">
                <i class="fa-solid fa-trash"></i>
            </button>

        </form>

    </td>

</tr>

@empty

<tr>
    <td colspan="5" style="text-align:center;">
        No customers found.
    </td>
</tr>

@endforelse

                    </tbody>
                </table>
            </div>
        </section>

     
        <section id="accounts-section" class="content-section">
            <div class="section-header flex-between">
                <div>
                    <h1>Accounts</h1>
                    <p>Manage checking and saving accounts.</p>
                </div>
           
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="margin-bottom: 15px; font-size: 1.2rem; color: var(--primary);">Pending Approvals</h2>
                @if(session('success'))
                    <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Account No</th>
                                <th>Customer Name</th>
                                <th>NID</th>
                                <th>Account Type</th>
                                <th>Initial Balance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingAccounts ?? [] as $account)
                            <tr>
                                <td>{{ $account->account_number }}</td>
                                <td>{{ optional($account->user)->FULL_NAME ?? optional($account->user)->first_name ?? 'N/A' }}</td>
                                <td>{{ optional($account->user)->NID ?? 'N/A' }}</td>
                                <td><span class="badge pending">{{ $account->account_type }}</span></td>
                                <td class="amount">${{ number_format($account->balance, 2) }}</td>
                                <td>
                                    <form action="{{ route('employee.approveAccount', $account->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn primary-btn small-btn" style="padding: 6px 12px; border-radius: 6px;"><i class="fa-solid fa-check"></i> Approve</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" style="text-align: center; padding: 20px;">No pending accounts</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 style="margin-bottom: 15px; font-size: 1.2rem; color: var(--primary);">Active Accounts</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Account No</th>
                                <th>Customer Name</th>
                                <th>Account Type</th>
                                <th>Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeAccounts ?? [] as $account)
                            <tr>
                                <td>{{ $account->account_number }}</td>
                                <td>{{ optional($account->user)->FULL_NAME ?? optional($account->user)->first_name ?? 'N/A' }}</td>
                                <td><span class="badge info">{{ $account->account_type }}</span></td>
                                <td class="amount">${{ number_format($account->balance, 2) }}</td>
                                <td><span class="badge success">{{ $account->status }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="text-align: center; padding: 20px;">No active accounts found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

      
        <section id="loans-section" class="content-section">
            <div class="section-header flex-between">
                <div>
                    <h1>Loans & Payments</h1>
                    <p>Review, approve, or reject customer loan applications.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert-box success-alert">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert-box error-alert">
                    <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
                </div>
            @endif

            <div style="margin-bottom: 30px;">
                <h2 style="margin-bottom: 15px; font-size: 1.2rem; color: var(--primary);">
                    <i class="fa-solid fa-clock" style="margin-right: 6px;"></i>Pending Loan Applications ({{ $pendingLoans->count() }})
                </h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Customer</th>
                                <th>Loan Type</th>
                                <th>Amount</th>
                                <th>Duration</th>
                                <th>Purpose</th>
                                <th>Eligibility</th>
                                <th>Applied On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingLoans as $loan)
                            <tr>
                                <td>LN-{{ $loan->loan_id }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $loan->full_name }}</strong>
                                        <br><small style="color: var(--text-muted);">{{ $loan->email }}</small>
                                    </div>
                                </td>
                                <td><span class="badge info">{{ $loan->loan_type }}</span></td>
                                <td class="amount">${{ number_format($loan->amount, 2) }}</td>
                                <td>{{ $loan->duration_months }} months</td>
                                <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $loan->purpose }}">{{ $loan->purpose ?? 'N/A' }}</td>
                                <td>
                                    @if(str_starts_with($loan->eligibility ?? '', 'ELIGIBLE'))
                                        <span class="badge success"><i class="fa-solid fa-circle-check"></i> Eligible</span>
                                    @else
                                        <span class="badge warning" title="{{ $loan->eligibility }}"><i class="fa-solid fa-triangle-exclamation"></i> Not Eligible</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($loan->created_at)->format('d M Y') }}</td>
                                <td>
                                    <div style="display: flex; gap: 6px;">
                                        <form action="{{ route('employee.approveLoan', $loan->loan_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="action" value="APPROVE">
                                            <button type="submit" class="btn primary-btn small-btn" style="padding: 6px 12px; border-radius: 6px; font-size: 0.8rem;" onclick="return confirm('Approve this loan for ${{ number_format($loan->amount, 2) }}?')">
                                                <i class="fa-solid fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('employee.approveLoan', $loan->loan_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="action" value="REJECT">
                                            <button type="submit" class="btn outline small-btn" style="padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; color: var(--danger); border-color: var(--danger);" onclick="return confirm('Reject this loan application?')">
                                                <i class="fa-solid fa-xmark"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" style="text-align: center; padding: 20px;">No pending loan applications</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 style="margin-bottom: 15px; font-size: 1.2rem; color: var(--primary);">
                    <i class="fa-solid fa-file-invoice-dollar" style="margin-right: 6px;"></i>All Loan Transactions
                </h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Transaction ID</th>
                                <th>Account No</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loanTransactions as $txn)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($txn->created_at)->format('d M Y, H:i') }}</td>
                                <td>TXN-{{ $txn->id }}</td>
                                <td>{{ $txn->account_number }}</td>
                                <td>{{ $txn->first_name }} {{ $txn->last_name }}</td>
                                <td>
                                    @if($txn->transaction_type == 'LOAN_PAYMENT')
                                        <span class="badge success">EMI Payment</span>
                                    @else
                                        <span class="badge info">Disbursement</span>
                                    @endif
                                </td>
                                <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $txn->description }}">
                                    {{ $txn->description }}
                                </td>
                                <td class="amount {{ $txn->transaction_type == 'LOAN_DISBURSEMENT' ? 'positive' : 'negative' }}">
                                    {{ $txn->transaction_type == 'LOAN_DISBURSEMENT' ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" style="text-align: center; padding: 20px;">No loan transactions found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

       
      
        <section id="deposit-section" class="content-section">
            <div class="section-header">
                <h1><i class="fa-solid fa-money-bill-wave" style="color: var(--success); margin-right: 10px;"></i>Deposit Money</h1>
                <p>Process a deposit for a verified customer.</p>
            </div>

            {{-- Flash messages --}}
            @if(session('deposit_success'))
                <div class="alert-box success-alert">
                    <i class="fa-solid fa-circle-check"></i> {{ session('deposit_success') }}
                </div>
            @endif
            @if(session('deposit_error'))
                <div class="alert-box error-alert">
                    <i class="fa-solid fa-circle-xmark"></i> {{ session('deposit_error') }}
                </div>
            @endif

            {{-- STEP 1: Search Customer --}}
            <div id="deposit-step1" class="workflow-card">
                <div class="step-badge">Step 1</div>
                <h3>Search Customer</h3>
                <p class="step-desc">Enter the customer's Account Number and NID to verify identity.</p>
                <form id="depositSearchForm" method="POST" action="{{ route('employee.deposit.search') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" placeholder="e.g. ACC-100001" required>
                        </div>
                        <div class="form-group">
                            <label>NID Number</label>
                            <input type="text" name="nid" placeholder="e.g. 1234567890" required>
                        </div>
                    </div>
                    <button type="submit" class="btn primary-btn"><i class="fa-solid fa-magnifying-glass"></i> Verify Customer</button>
                </form>
            </div>

            {{-- STEP 2 & 3: Customer Verified + Enter Amount (shown via session) --}}
            @if(session('deposit_customer'))
            @php $dc = session('deposit_customer'); @endphp
            <div class="workflow-card verified-card">
                <div class="step-badge success-badge">✓ Verified</div>
                <h3>Customer Verified</h3>
                <div class="customer-info-grid">
                    <div class="info-item"><span class="info-label">Customer Name</span><span class="info-value">{{ $dc['full_name'] }}</span></div>
                    <div class="info-item"><span class="info-label">Account No</span><span class="info-value">{{ $dc['account_number'] }}</span></div>
                    <div class="info-item"><span class="info-label">Current Balance</span><span class="info-value amount positive">${{ number_format($dc['balance'], 2) }}</span></div>
                </div>

                <form method="POST" action="{{ route('employee.deposit.otp') }}" style="margin-top: 20px;">
                    @csrf
                    <input type="hidden" name="account_number" value="{{ $dc['account_number'] }}">
                    <div class="form-group" style="max-width: 300px;">
                        <label>Deposit Amount ($)</label>
                        <input type="number" name="amount" placeholder="0.00" min="0.01" step="0.01" required style="font-size: 1.2rem; font-weight: 600;">
                    </div>
                    <button type="submit" class="btn primary-btn"><i class="fa-solid fa-paper-plane"></i> Generate OTP & Deposit</button>
                </form>
            </div>
            @endif

            {{-- STEP 4 & 5: OTP Verification (shown via session) --}}
            @if(session('deposit_otp_id'))
            <div class="workflow-card otp-card">
                <div class="step-badge">Step 4</div>
                <h3><i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-right: 8px;"></i>OTP Verification</h3>
                <p class="step-desc">An OTP has been sent to the customer's Notifications page. Ask the customer for the code.</p>
                <form method="POST" action="{{ route('employee.deposit.verify') }}">
                    @csrf
                    <input type="hidden" name="otp_id" value="{{ session('deposit_otp_id') }}">
                    <div class="form-group" style="max-width: 250px;">
                        <label>Enter 6-digit OTP</label>
                        <input type="text" name="otp" maxlength="6" placeholder="______" required
                               style="font-size: 1.8rem; text-align: center; letter-spacing: 12px; font-weight: 700;">
                    </div>
                    <button type="submit" class="btn primary-btn"><i class="fa-solid fa-check-double"></i> Verify OTP & Complete Deposit</button>
                </form>
            </div>
            @endif
        </section>

        
        
        <section id="withdraw-section" class="content-section">
            <div class="section-header">
                <h1><i class="fa-solid fa-hand-holding-dollar" style="color: var(--warning); margin-right: 10px;"></i>Withdraw Money</h1>
                <p>Process a withdrawal for a verified customer.</p>
            </div>

            @if(session('withdraw_success'))
                <div class="alert-box success-alert">
                    <i class="fa-solid fa-circle-check"></i> {{ session('withdraw_success') }}
                </div>
            @endif
            @if(session('withdraw_error'))
                <div class="alert-box error-alert">
                    <i class="fa-solid fa-circle-xmark"></i> {{ session('withdraw_error') }}
                </div>
            @endif

            {{-- STEP 1: Search Customer --}}
            <div id="withdraw-step1" class="workflow-card">
                <div class="step-badge">Step 1</div>
                <h3>Search Customer</h3>
                <p class="step-desc">Enter the customer's Account Number and NID to verify identity.</p>
                <form method="POST" action="{{ route('employee.withdraw.search') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" placeholder="e.g. ACC-100001" required>
                        </div>
                        <div class="form-group">
                            <label>NID Number</label>
                            <input type="text" name="nid" placeholder="e.g. 1234567890" required>
                        </div>
                    </div>
                    <button type="submit" class="btn primary-btn"><i class="fa-solid fa-magnifying-glass"></i> Verify Customer</button>
                </form>
            </div>

            @if(session('withdraw_customer'))
            @php $wc = session('withdraw_customer'); @endphp
            <div class="workflow-card verified-card">
                <div class="step-badge success-badge">✓ Verified</div>
                <h3>Customer Verified</h3>
                <div class="customer-info-grid">
                    <div class="info-item"><span class="info-label">Customer Name</span><span class="info-value">{{ $wc['full_name'] }}</span></div>
                    <div class="info-item"><span class="info-label">Account No</span><span class="info-value">{{ $wc['account_number'] }}</span></div>
                    <div class="info-item"><span class="info-label">Current Balance</span><span class="info-value amount positive">${{ number_format($wc['balance'], 2) }}</span></div>
                </div>

                <form method="POST" action="{{ route('employee.withdraw.otp') }}" style="margin-top: 20px;">
                    @csrf
                    <input type="hidden" name="account_number" value="{{ $wc['account_number'] }}">
                    <div class="form-group" style="max-width: 300px;">
                        <label>Withdrawal Amount ($)</label>
                        <input type="number" name="amount" placeholder="0.00" min="0.01" step="0.01" max="{{ $wc['balance'] }}" required style="font-size: 1.2rem; font-weight: 600;">
                    </div>
                    <button type="submit" class="btn primary-btn" style="background-color: var(--warning);"><i class="fa-solid fa-paper-plane"></i> Generate OTP & Withdraw</button>
                </form>
            </div>
            @endif

            @if(session('withdraw_otp_id'))
            <div class="workflow-card otp-card">
                <div class="step-badge">Step 4</div>
                <h3><i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-right: 8px;"></i>OTP Verification</h3>
                <p class="step-desc">An OTP has been sent to the customer's Notifications page. Ask the customer for the code.</p>
                <form method="POST" action="{{ route('employee.withdraw.verify') }}">
                    @csrf
                    <input type="hidden" name="otp_id" value="{{ session('withdraw_otp_id') }}">
                    <div class="form-group" style="max-width: 250px;">
                        <label>Enter 6-digit OTP</label>
                        <input type="text" name="otp" maxlength="6" placeholder="______" required
                               style="font-size: 1.8rem; text-align: center; letter-spacing: 12px; font-weight: 700;">
                    </div>
                    <button type="submit" class="btn primary-btn" style="background-color: var(--warning);"><i class="fa-solid fa-check-double"></i> Verify OTP & Complete Withdrawal</button>
                </form>
            </div>
            @endif
        </section>

       
    
      
        <section id="transfer-section" class="content-section">
            <div class="section-header">
                <h1><i class="fa-solid fa-right-left" style="color: var(--purple); margin-right: 10px;"></i>Money Transfer</h1>
                <p>Transfer funds between two verified accounts.</p>
            </div>

            @if(session('transfer_success'))
                <div class="alert-box success-alert">
                    <i class="fa-solid fa-circle-check"></i> {{ session('transfer_success') }}
                </div>
            @endif
            @if(session('transfer_error'))
                <div class="alert-box error-alert">
                    <i class="fa-solid fa-circle-xmark"></i> {{ session('transfer_error') }}
                </div>
            @endif

            {{-- STEP 1: Search Source Account --}}
            <div class="workflow-card">
                <div class="step-badge">Step 1</div>
                <h3>Verify Source Account</h3>
                <p class="step-desc">Enter the sender's Account Number, NID, and the destination account.</p>
                <form method="POST" action="{{ route('employee.transfer.search') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>From Account Number</label>
                            <input type="text" name="from_account" placeholder="e.g. ACC-100001" required>
                        </div>
                        <div class="form-group">
                            <label>NID Number (Sender)</label>
                            <input type="text" name="nid" placeholder="e.g. 1234567890" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>To Account Number (Destination)</label>
                        <input type="text" name="to_account" placeholder="e.g. ACC-100045" required>
                    </div>
                    <button type="submit" class="btn primary-btn" style="background-color: var(--purple);"><i class="fa-solid fa-magnifying-glass"></i> Verify Accounts</button>
                </form>
            </div>

            @if(session('transfer_accounts'))
            @php $ta = session('transfer_accounts'); @endphp
            <div class="workflow-card verified-card">
                <div class="step-badge success-badge">✓ Verified</div>
                <h3>Accounts Verified</h3>
                <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 20px; align-items: center;">
                    <div class="customer-info-grid" style="border: 1px solid var(--border); padding: 16px; border-radius: 10px;">
                        <h4 style="color: var(--danger); margin-bottom: 8px;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sender</h4>
                        <div class="info-item"><span class="info-label">Name</span><span class="info-value">{{ $ta['from_name'] }}</span></div>
                        <div class="info-item"><span class="info-label">Account</span><span class="info-value">{{ $ta['from_account'] }}</span></div>
                        <div class="info-item"><span class="info-label">Balance</span><span class="info-value amount positive">${{ number_format($ta['from_balance'], 2) }}</span></div>
                    </div>
                    <div style="font-size: 2rem; color: var(--purple);"><i class="fa-solid fa-arrow-right"></i></div>
                    <div class="customer-info-grid" style="border: 1px solid var(--border); padding: 16px; border-radius: 10px;">
                        <h4 style="color: var(--success); margin-bottom: 8px;"><i class="fa-solid fa-arrow-right-to-bracket"></i> Receiver</h4>
                        <div class="info-item"><span class="info-label">Name</span><span class="info-value">{{ $ta['to_name'] }}</span></div>
                        <div class="info-item"><span class="info-label">Account</span><span class="info-value">{{ $ta['to_account'] }}</span></div>
                        <div class="info-item"><span class="info-label">Balance</span><span class="info-value amount positive">${{ number_format($ta['to_balance'], 2) }}</span></div>
                    </div>
                </div>

                <form method="POST" action="{{ route('employee.transfer.otp') }}" style="margin-top: 20px;">
                    @csrf
                    <input type="hidden" name="from_account" value="{{ $ta['from_account'] }}">
                    <input type="hidden" name="to_account" value="{{ $ta['to_account'] }}">
                    <div class="form-group" style="max-width: 300px;">
                        <label>Transfer Amount ($)</label>
                        <input type="number" name="amount" placeholder="0.00" min="0.01" step="0.01" max="{{ $ta['from_balance'] }}" required style="font-size: 1.2rem; font-weight: 600;">
                    </div>
                    <button type="submit" class="btn primary-btn" style="background-color: var(--purple);"><i class="fa-solid fa-paper-plane"></i> Generate OTP & Transfer</button>
                </form>
            </div>
            @endif

            @if(session('transfer_otp_id'))
            <div class="workflow-card otp-card">
                <div class="step-badge">Step 4</div>
                <h3><i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-right: 8px;"></i>OTP Verification</h3>
                <p class="step-desc">An OTP has been sent to the sender's Notifications page. Ask the customer for the code.</p>
                <form method="POST" action="{{ route('employee.transfer.verify') }}">
                    @csrf
                    <input type="hidden" name="otp_id" value="{{ session('transfer_otp_id') }}">
                    <div class="form-group" style="max-width: 250px;">
                        <label>Enter 6-digit OTP</label>
                        <input type="text" name="otp" maxlength="6" placeholder="______" required
                               style="font-size: 1.8rem; text-align: center; letter-spacing: 12px; font-weight: 700;">
                    </div>
                    <button type="submit" class="btn primary-btn" style="background-color: var(--purple);"><i class="fa-solid fa-check-double"></i> Verify OTP & Complete Transfer</button>
                </form>
            </div>
            @endif
        </section>

      
        <section id="profile-section" class="content-section">
            <div class="section-header">
                <h1>My Profile</h1>
                <p>Manage your personal details and security settings.</p>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        
                <div style="background-color: white; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 16px;"><i class="fa-solid fa-user-pen" style="color: var(--primary); margin-right: 8px;"></i> Edit Profile Details</h3>
                    @if(session('profile_success'))
                        <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
                            {{ session('profile_success') }}
                        </div>
                    @endif
                    <form method="POST" action="/user-profile/update">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" value="{{ auth()->user()->email }}" readonly style="background-color: #f1f5f9; cursor: not-allowed;">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                        </div>
                        <div class="form-group">
                            <label>Residential Address</label>
                            <textarea name="address" rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                        </div>
                        <button type="submit" class="btn primary-btn"><i class="fa-solid fa-save" style="margin-right: 8px;"></i> Save Changes</button>
                    </form>
                </div>

             
                <div style="background-color: white; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 16px;"><i class="fa-solid fa-shield" style="color: var(--danger); margin-right: 8px;"></i> Security Settings</h3>
                    @if(session('password_success'))
                        <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
                            {{ session('password_success') }}
                        </div>
                    @endif
                    @if($errors->has('password_error'))
                        <div style="background-color: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
                            {{ $errors->first('password_error') }}
                        </div>
                    @endif
                    <form method="POST" action="/user-profile/password">
                        @csrf
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" placeholder="Enter current password" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" placeholder="Enter new password" required>
                                @error('new_password')
                                    <small style="color: #dc2626; margin-top: 4px; display: block;">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="new_password_confirmation" placeholder="Confirm new password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn outline"><i class="fa-solid fa-key" style="margin-right: 8px;"></i> Update Password</button>
                    </form>
                </div>
            </div>
        </section>
        
        <!-- Notifications Section -->
        <section id="notifications-section" class="content-section">
            <div class="section-header">
                <h1>Notifications</h1>
                <p>Your recent notifications.</p>
            </div>
            <div class="table-container">
                @forelse($notifications as $note)
                <div class="notification-item" style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <p style="margin: 0;">{{ $note->message }}</p>
                    <small style="color: var(--text-muted);">{{ \Carbon\Carbon::parse($note->created_at)->diffForHumans() }}</small>
                </div>
                @empty
                <p>No notifications.</p>
                @endforelse
            </div>
        </section>

        @if(session('profile_success') || session('password_success') || $errors->has('password_error') || $errors->has('new_password'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
                document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
                document.querySelector('[data-target="profile-section"]').classList.add('active');
                document.getElementById('profile-section').classList.add('active');
            });
        </script>
        @endif

        {{-- Auto-navigate to deposit/withdraw/transfer section after form submission --}}
        @if(session('deposit_customer') || session('deposit_otp_id') || session('deposit_success') || session('deposit_error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
                document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
                document.querySelector('[data-target="deposit-section"]').classList.add('active');
                document.getElementById('deposit-section').classList.add('active');
            });
        </script>
        @endif

        @if(session('withdraw_customer') || session('withdraw_otp_id') || session('withdraw_success') || session('withdraw_error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
                document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
                document.querySelector('[data-target="withdraw-section"]').classList.add('active');
                document.getElementById('withdraw-section').classList.add('active');
            });
        </script>
        @endif

        @if(session('transfer_accounts') || session('transfer_otp_id') || session('transfer_success') || session('transfer_error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
                document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
                document.querySelector('[data-target="transfer-section"]').classList.add('active');
                document.getElementById('transfer-section').classList.add('active');
            });
        </script>
        @endif

        @if(session('success') || session('error'))
        @php
            $msg = session('success') ?? session('error') ?? '';
            $isLoanMsg = str_contains(strtolower($msg), 'loan');
            $isAccountMsg = str_contains(strtolower($msg), 'account');
        @endphp
        @if($isLoanMsg)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
                document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
                document.querySelector('[data-target="loans-section"]').classList.add('active');
                document.getElementById('loans-section').classList.add('active');
            });
        </script>
        @elseif($isAccountMsg)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
                document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
                document.querySelector('[data-target="accounts-section"]').classList.add('active');
                document.getElementById('accounts-section').classList.add('active');
            });
        </script>
        @endif
        @endif

    </main>

    
    <div id="customerModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Add New Customer</h2>
                <button class="close-btn" onclick="closeModal('customerModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form class="modal-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" placeholder="e.g. John" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" placeholder="e.g. Doe" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" placeholder="e.g. (555) 000-0000" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea rows="3" placeholder="Full residential address" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn outline" onclick="closeModal('customerModal')">Cancel</button>
                    <button type="submit" class="btn primary-btn">Save Customer</button>
                </div>
            </form>
        </div>
    </div>

   
    <div id="accountModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Open New Account</h2>
                <button class="close-btn" onclick="closeModal('accountModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form class="modal-form">
                <div class="form-group">
                    <label>Customer ID</label>
                    <input type="text" placeholder="e.g. C-1001" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Account Type</label>
                        <select required>
                            <option value="">Select type...</option>
                            <option value="saving">Saving</option>
                            <option value="current">Current</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Initial Balance ($)</label>
                        <input type="number" placeholder="0.00" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Managing Employee ID</label>
                    <input type="text" placeholder="e.g. Emp-001" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn outline" onclick="closeModal('accountModal')">Cancel</button>
                    <button type="submit" class="btn primary-btn">Open Account</button>
                </div>
            </form>
        </div>
    </div>


    <div id="loanModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Issue New Loan</h2>
                <button class="close-btn" onclick="closeModal('loanModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form class="modal-form">
                <div class="form-group">
                    <label>Customer ID</label>
                    <input type="text" placeholder="e.g. C-1001" required>
                </div>
                <div class="form-group">
                    <label>Loan Amount ($)</label>
                    <input type="number" placeholder="0.00" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn outline" onclick="closeModal('loanModal')">Cancel</button>
                    <button type="submit" class="btn primary-btn">Issue Loan</button>
                </div>
            </form>
        </div>
    </div>



    <script src="script.js"></script>
</body>
</html>
