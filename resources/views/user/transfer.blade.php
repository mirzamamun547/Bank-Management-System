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
            <li><a href="/user-deposit"><i class="fa-solid fa-money-bill-transfer"></i> Deposit / Withdraw</a></li>
            <li><a href="/user-transfer" class="active"><i class="fa-solid fa-arrow-right-arrow-left"></i> Fund Transfer</a></li>
            <li><a href="/user-transactions"><i class="fa-solid fa-clock-rotate-left"></i> Transaction History</a></li>
            <li><a href="/user-dashboard"><i class="fa-solid fa-chart-line"></i> Balance Inquiry</a></li>
            <li><a href="/user-loan"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Services</a></li>
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
                <button class="icon-action" title="Notifications">
                    <i class="fa-regular fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                <div class="user-dropdown">
                    <img src="https://i.pravatar.cc/150?img=12" alt="User Profile" class="user-avatar">
                    <div class="user-info-top">
                        <h4>Mirza Mamun</h4>
                        <p>Standard Account</p>
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
                    
                    <form>
                        <div class="form-group">
                            <label>From Account</label>
                            <select class="form-control">
                                <option>Savings Account (ACC-10492) - Balance: $24,500.00</option>
                                <option>Current Account (ACC-10493) - Balance: $5,200.00</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>To Account Number</label>
                            <input type="text" class="form-control" placeholder="Enter recipient account number">
                        </div>
                        
                        <div class="form-group">
                            <label>Amount ($)</label>
                            <input type="number" class="form-control" placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <input type="text" class="form-control" placeholder="What is this transfer for?">
                        </div>
                        
                        <button type="button" class="btn-accent" style="margin-top: 16px;">
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
                        <tr>
                            <td>08-06-2026</td>
                            <td>ACC-10492</td>
                            <td>ACC-55219 (External)</td>
                            <td class="amount debit">- $1,200.00</td>
                            <td><span class="badge success">Completed</span></td>
                        </tr>
                        <tr>
                            <td>05-06-2026</td>
                            <td>ACC-10492</td>
                            <td>ACC-10493 (Self)</td>
                            <td class="amount debit">- $500.00</td>
                            <td><span class="badge success">Completed</span></td>
                        </tr>
                        <tr>
                            <td>28-05-2026</td>
                            <td>ACC-10493</td>
                            <td>ACC-88321 (External)</td>
                            <td class="amount debit">- $250.00</td>
                            <td><span class="badge pending">Processing</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

    <script src="user-script.js"></script>
</body>
</html>
