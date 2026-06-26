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

    <!-- Sidebar Navigation -->
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
            <li data-target="employees-section"><i class="fa-solid fa-user-tie"></i> Employees</li>
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

    <!-- Main Content Area -->
    <main class="main-content">
        
        <!-- Header -->
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
                        <tr>
                            <td>C-1001</td>
                            <td>Sarah Jenkins</td>
                            <td>(555) 123-4567</td>
                            <td>123 Main St, Springfield</td>
                            <td>
                                <button class="action-btn edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="action-btn delete"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>C-1002</td>
                            <td>Michael Chen</td>
                            <td>(555) 987-6543</td>
                            <td>456 Oak Ave, Metropolis</td>
                            <td>
                                <button class="action-btn edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="action-btn delete"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Accounts Section -->
        <section id="accounts-section" class="content-section">
            <div class="section-header flex-between">
                <div>
                    <h1>Accounts</h1>
                    <p>Manage checking and saving accounts.</p>
                </div>
                <!-- Removed Open Account Button -->
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

        <!-- Loans Section -->
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

        <!-- Employees Section -->
        <section id="employees-section" class="content-section">
            <div class="section-header flex-between">
                <div>
                    <h1>Employees</h1>
                    <p>Manage bank staff and assignments.</p>
                </div>
                <button class="btn primary-btn" onclick="openModal('employeeModal')"><i class="fa-solid fa-plus"></i> Add Employee</button>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Emp ID</th>
                            <th>Name</th>
                            <th>Mobile No</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Emp-001</td>
                            <td>David Miller</td>
                            <td>(555) 111-2222</td>
                            <td>789 Pine Rd, Gotham</td>
                            <td>
                                <button class="action-btn edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="action-btn delete"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- My Profile Section -->
        <section id="profile-section" class="content-section">
            <div class="section-header">
                <h1>My Profile</h1>
                <p>Manage your personal details and security settings.</p>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <!-- Profile Edit Form -->
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

                <!-- Security Form -->
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

    </main>

    <!-- Modals (Add Forms) -->
    
    <!-- Customer Modal -->
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

    <!-- Account Modal -->
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

    <!-- Loan Modal -->
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

    <!-- Employee Modal -->
    <div id="employeeModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Add Employee</h2>
                <button class="close-btn" onclick="closeModal('employeeModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form class="modal-form">
                <div class="form-group">
                    <label>Employee Name</label>
                    <input type="text" placeholder="Full Name" required>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" placeholder="e.g. (555) 000-0000" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea rows="3" placeholder="Residential address" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn outline" onclick="closeModal('employeeModal')">Cancel</button>
                    <button type="submit" class="btn primary-btn">Save Employee</button>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
