<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit & Withdraw - Nexus Bank</title>
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
            <li><a href="/user-deposit" class="active"><i class="fa-solid fa-money-bill-transfer"></i> Deposit / Withdraw</a></li>
            <li><a href="/user-transfer"><i class="fa-solid fa-arrow-right-arrow-left"></i> Fund Transfer</a></li>
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
                <h1 style="color: var(--text-main);">Deposit & Withdraw</h1>
                <p style="color: var(--text-muted);">Manage cash flows securely into and out of your accounts.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; max-width: 900px; margin: 0 auto;">
                
                <!-- Deposit Form -->
                <div class="section-panel" style="border-top: 4px solid var(--success);">
                    <div class="panel-header" style="justify-content: center; margin-bottom: 24px;">
                        <h3><i class="fa-solid fa-arrow-down" style="color: var(--success); margin-right: 8px;"></i> Deposit Funds</h3>
                    </div>
                    
                    <form>
                        <div class="form-group">
                            <label>Deposit To</label>
                            <select class="form-control">
                                <option>Savings Account (ACC-10492)</option>
                                <option>Current Account (ACC-10493)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Amount ($)</label>
                            <input type="number" class="form-control" placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label>Deposit Method</label>
                            <select class="form-control">
                                <option>Credit/Debit Card</option>
                                <option>Bank Wire</option>
                                <option>Mobile Wallet</option>
                            </select>
                        </div>
                        
                        <button type="button" class="btn-accent" style="margin-top: 16px; background-color: var(--success); border-color: var(--success);">
                            <i class="fa-solid fa-plus-circle" style="margin-right: 8px;"></i> Deposit
                        </button>
                    </form>
                </div>

                <!-- Withdraw Form -->
                <div class="section-panel" style="border-top: 4px solid var(--danger);">
                    <div class="panel-header" style="justify-content: center; margin-bottom: 24px;">
                        <h3><i class="fa-solid fa-arrow-up" style="color: var(--danger); margin-right: 8px;"></i> Withdraw Funds</h3>
                    </div>
                    
                    <form>
                        <div class="form-group">
                            <label>Withdraw From</label>
                            <select class="form-control">
                                <option>Savings Account (ACC-10492) - Avail: $24,500</option>
                                <option>Current Account (ACC-10493) - Avail: $5,200</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Amount ($)</label>
                            <input type="number" class="form-control" placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label>Withdraw Method</label>
                            <select class="form-control">
                                <option>Bank Wire (To Linked Bank)</option>
                                <option>Mobile Wallet</option>
                            </select>
                        </div>
                        
                        <button type="button" class="btn-accent" style="margin-top: 16px; background-color: var(--danger); border-color: var(--danger);">
                            <i class="fa-solid fa-minus-circle" style="margin-right: 8px;"></i> Withdraw
                        </button>
                    </form>
                </div>

            </div>

        </main>
    </div>

    <script src="user-script.js"></script>
</body>
</html>
