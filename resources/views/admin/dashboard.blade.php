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
        </ul>
        <div class="user-profile">
            <img src="https://i.pravatar.cc/150?img=11" alt="Admin Profile">
            <div>
                <h4>{{ auth()->user()->first_name ?? 'Admin' }} {{ auth()->user()->last_name ?? 'User' }}</h4>
                <p>Role: {{ ucfirst(strtolower(auth()->user()->role ?? 'Admin')) }}</p>
            </div>
        </div>
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
                <p>Welcome back! Here's what's happening today.</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-info">
                        <h3>Total Customers</h3>
                        <h2>1,245</h2>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-vault"></i></div>
                    <div class="stat-info">
                        <h3>Total Deposits</h3>
                        <h2>$4.2M</h2>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <div class="stat-info">
                        <h3>Active Loans</h3>
                        <h2>$1.8M</h2>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fa-solid fa-id-badge"></i></div>
                    <div class="stat-info">
                        <h3>Employees</h3>
                        <h2>42</h2>
                    </div>
                </div>
            </div>

            <div class="recent-activity">
                <h2>Recent Transactions</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Account No</th>
                            <th>Customer Name</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ACC-9821</td>
                            <td>Sarah Jenkins</td>
                            <td><span class="badge info">Deposit</span></td>
                            <td class="amount positive">+$1,500.00</td>
                            <td><span class="badge success">Completed</span></td>
                        </tr>
                        <tr>
                            <td>ACC-4512</td>
                            <td>Michael Chen</td>
                            <td><span class="badge warning">Withdrawal</span></td>
                            <td class="amount negative">-$400.00</td>
                            <td><span class="badge success">Completed</span></td>
                        </tr>
                        <tr>
                            <td>ACC-7734</td>
                            <td>Emma Stone</td>
                            <td><span class="badge purple">Loan Payment</span></td>
                            <td class="amount negative">-$850.00</td>
                            <td><span class="badge pending">Processing</span></td>
                        </tr>
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
                <button class="btn primary-btn" onclick="openModal('customerModal')"><i class="fa-solid fa-plus"></i> Add Customer</button>
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

        <a href="{{ route('customer.edit', $customer->id) }}"
           class="action-btn edit">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('customer.delete', $customer->id) }}"
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
                                    <form action="{{ route('admin.approveAccount', $account->id) }}" method="POST" style="display:inline;">
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
                    <p>Manage customer loans and track payments.</p>
                </div>
                <button class="btn primary-btn" onclick="openModal('loanModal')"><i class="fa-solid fa-plus"></i> Issue Loan</button>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Loan No</th>
                            <th>Cust ID</th>
                            <th>Principal Amount</th>
                            <th>Last Payment No</th>
                            <th>Payment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>L-5001</td>
                            <td>C-1003</td>
                            <td class="amount">$50,000.00</td>
                            <td>P-901</td>
                            <td>2023-10-15</td>
                            <td>
                                <button class="btn small-btn outline">Record Payment</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                <form id="depositSearchForm" method="POST" action="{{ route('admin.deposit.search') }}">
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

                <form method="POST" action="{{ route('admin.deposit.otp') }}" style="margin-top: 20px;">
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
                <form method="POST" action="{{ route('admin.deposit.verify') }}">
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
                <form method="POST" action="{{ route('admin.withdraw.search') }}">
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

                <form method="POST" action="{{ route('admin.withdraw.otp') }}" style="margin-top: 20px;">
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
                <form method="POST" action="{{ route('admin.withdraw.verify') }}">
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
                <form method="POST" action="{{ route('admin.transfer.search') }}">
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

                <form method="POST" action="{{ route('admin.transfer.otp') }}" style="margin-top: 20px;">
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
                <form method="POST" action="{{ route('admin.transfer.verify') }}">
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
