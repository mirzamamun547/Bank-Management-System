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
                @php
                    $activeLoans = $loans->where('status', 'Active');
                    $totalLoanAmount = $activeLoans->sum('amount');
                    $remainingAmount = $activeLoans->sum('remaining_amount');
                    $monthlyInstallment = $activeLoans->sum('monthly_installment');
                    $nextDueDate = $activeLoans->min('next_due_date');
                @endphp
                <div class="card">
                    <div class="card-icon icon-blue"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <p class="card-title">Total Loan Amount</p>
                    <h2 class="card-value">${{ number_format($totalLoanAmount, 2) }}</h2>
                </div>
                <div class="card">
                    <div class="card-icon icon-orange"><i class="fa-solid fa-sack-dollar"></i></div>
                    <p class="card-title">Remaining Amount</p>
                    <h2 class="card-value">${{ number_format($remainingAmount, 2) }}</h2>
                </div>
                <div class="card">
                    <div class="card-icon icon-purple"><i class="fa-solid fa-calendar-check"></i></div>
                    <p class="card-title">Monthly Installment</p>
                    <h2 class="card-value">${{ number_format($monthlyInstallment, 2) }}</h2>
                </div>
                <div class="card">
                    <div class="card-icon icon-green"><i class="fa-solid fa-stopwatch"></i></div>
                    <p class="card-title">Next Due Date</p>
                    <h2 class="card-value" style="font-size: 1.25rem;">
                        {{ $nextDueDate ? \Carbon\Carbon::parse($nextDueDate)->format('d M Y') : 'N/A' }}
                    </h2>
                </div>
            </div>

            @if(session('success'))
                <div class="alert success" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background-color: rgba(46, 204, 113, 0.1); border: 1px solid var(--success); color: var(--success);">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert error" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background-color: rgba(231, 76, 60, 0.1); border: 1px solid var(--danger); color: var(--danger);">
                    {{ session('error') }}
                </div>
            @endif

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                
                <!-- Left: Application Form -->
                <div>
                    <div class="section-panel" style="margin-bottom: 0;">
                        <div class="panel-header">
                            <h3><i class="fa-solid fa-file-signature" style="color: var(--accent); margin-right: 8px;"></i> Apply for a Loan</h3>
                        </div>
                        <div style="background: rgba(67, 24, 255, 0.05); border: 1px solid rgba(67, 24, 255, 0.2); padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-main);">Current Loan Interest Rate:</span>
                            <span style="font-size: 1.1rem; font-weight: 700; color: var(--accent);">{{ $settings['LOAN_INTEREST'] ?? '8.5' }}%</span>
                        </div>
                        <input type="hidden" id="loanInterestRate" value="{{ $settings['LOAN_INTEREST'] ?? '8.5' }}">
                        <form action="{{ route('user.loan.apply') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Loan Type</label>
                                <select name="loan_type" class="form-control" required>
                                    <option value="Personal Loan">Personal Loan</option>
                                    <option value="Home Loan">Home Loan</option>
                                    <option value="Car Loan">Car Loan</option>
                                    <option value="Education Loan">Education Loan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Amount Needed ($)</label>
                                <input type="number" id="loanAmount" name="amount" class="form-control" placeholder="0.00" min="500" required>
                            </div>
                            <div class="form-group">
                                <label>Duration (Months)</label>
                                <select id="loanDuration" name="duration_months" class="form-control" required>
                                    <option value="12">12 Months</option>
                                    <option value="24">24 Months</option>
                                    <option value="36">36 Months</option>
                                    <option value="48">48 Months</option>
                                    <option value="60">60 Months</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Purpose of Loan</label>
                                <textarea name="purpose" class="form-control" rows="3" placeholder="Briefly explain the reason for the loan"></textarea>
                            </div>

                            <!-- Live EMI calculator preview -->
                            <div id="emiPreviewBox" style="display: none; background: #f8fafc; border: 1px dashed var(--border); padding: 12px 16px; border-radius: 8px; margin-top: 16px;">
                                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--text-muted); margin-bottom: 4px;">
                                    <span>Estimated Monthly Installment (EMI):</span>
                                    <strong style="color: var(--accent); font-size: 1.05rem;" id="emiPreviewValue">$0.00</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-muted);">
                                    <span>Total Repayment (Principal + Interest):</span>
                                    <span id="totalRepaymentValue">$0.00</span>
                                </div>
                            </div>

                            <button type="submit" class="btn-accent" style="margin-top: 16px;">
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
                                @forelse($loans as $loan)
                                <tr>
                                    <td>LN-{{ $loan->id }}</td>
                                    <td class="amount">${{ number_format($loan->amount, 2) }}</td>
                                    <td>
                                        @if($loan->status == 'Active')
                                            <span class="badge success">Active</span>
                                        @elseif($loan->status == 'Pending')
                                            <span class="badge pending">Pending</span>
                                        @elseif($loan->status == 'Closed')
                                            <span class="badge" style="background-color: var(--text-muted); color: white;">Closed</span>
                                        @else
                                            <span class="badge pending">{{ $loan->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($loan->status == 'Active')
                                            <form action="{{ route('user.loan.pay') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                                                <select name="account_id" style="padding: 4px; border-radius: 4px; font-size: 0.8rem; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-main);" required>
                                                    <option value="" disabled selected>Select Account</option>
                                                    @foreach($accounts as $acc)
                                                        <option value="{{ $acc->id }}">{{ $acc->account_number }} (${{ $acc->balance }})</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem; margin-top: 4px;">Pay EMI (${{ number_format($loan->monthly_installment, 2) }})</button>
                                            </form>
                                        @elseif($loan->status == 'Pending')
                                            <button class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;" disabled>Reviewing</button>
                                        @else
                                            <button class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">Statement</button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align: center;">No loans found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script src="user-script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loanAmountInput = document.getElementById('loanAmount');
            const loanDurationSelect = document.getElementById('loanDuration');
            const loanInterestRateInput = document.getElementById('loanInterestRate');
            const emiPreviewBox = document.getElementById('emiPreviewBox');
            const emiPreviewValue = document.getElementById('emiPreviewValue');
            const totalRepaymentValue = document.getElementById('totalRepaymentValue');

            function calculateEmi() {
                const amount = parseFloat(loanAmountInput.value);
                const months = parseInt(loanDurationSelect.value);
                const interestRate = parseFloat(loanInterestRateInput.value);

                if (isNaN(amount) || amount <= 0 || isNaN(months) || months <= 0) {
                    emiPreviewBox.style.display = 'none';
                    return;
                }

                // Formula: EMI = (Amount * (1 + InterestRate/100)) / Duration
                const totalRepayment = amount * (1 + (interestRate / 100));
                const emi = totalRepayment / months;

                emiPreviewValue.textContent = '$' + emi.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                totalRepaymentValue.textContent = '$' + totalRepayment.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                emiPreviewBox.style.display = 'block';
            }

            loanAmountInput.addEventListener('input', calculateEmi);
            loanDurationSelect.addEventListener('change', calculateEmi);
        });
    </script>
</body>
</html>
