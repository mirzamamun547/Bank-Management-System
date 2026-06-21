<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Nexus Bank</title>
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
            <li><a href="/user-profile" class="active"><i class="fa-solid fa-user"></i> My Profile</a></li>
            <li><a href="/user-accounts"><i class="fa-solid fa-wallet"></i> My Accounts</a></li>
            <li><a href="/user-deposit"><i class="fa-solid fa-money-bill-transfer"></i> Deposit / Withdraw</a></li>
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
        <main class="dashboard-content" style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
            
            <!-- Left Column: Profile Card -->
            <div class="left-col">
                <div class="section-panel profile-box" style="margin-bottom: 0;">
                    <img src="https://i.pravatar.cc/150?img=12" alt="Mirza Mamun" class="user-avatar" style="width: 120px; height: 120px; border-width: 4px;">
                    <h3>Mirza Mamun <i class="fa-solid fa-circle-check" style="color: var(--success); font-size: 1.2rem;" title="KYC Verified"></i></h3>
                    <p style="margin-bottom: 24px;">mirza.mamun@example.com</p>
                    
                    <div class="status-badges" style="flex-direction: column; align-items: center; gap: 12px; margin-bottom: 24px;">
                        <span class="status-badge" style="width: 100%; justify-content: center;"><i class="fa-solid fa-shield-halved"></i> Account Status: Active</span>
                        <span class="status-badge" style="background-color: var(--primary-light); color: var(--primary); width: 100%; justify-content: center;"><i class="fa-solid fa-lock"></i> 2FA Enabled</span>
                    </div>

                    <div class="profile-details">
                        <div class="detail-item">
                            <span>Phone</span>
                            <span>+880 1711-223344</span>
                        </div>
                        <div class="detail-item">
                            <span>Joined</span>
                            <span>Oct 2023</span>
                        </div>
                        <div class="detail-item" style="border-bottom: none; display: flex; flex-direction: column; gap: 8px;">
                            <span>Address</span>
                            <span style="font-weight: 400; color: var(--text-muted); line-height: 1.5;">123 Tech Avenue, Block B<br>Dhaka, 1212</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Edit & Security Forms -->
            <div class="right-col">
                <div class="section-panel" style="margin-bottom: 24px;">
                    <div class="panel-header">
                        <h3><i class="fa-solid fa-user-pen" style="color: var(--accent); margin-right: 8px;"></i> Edit Profile Details</h3>
                    </div>
                    <form>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" class="form-control" value="Mirza Mamun">
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" class="form-control" value="+880 1711-223344">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" value="mirza.mamun@example.com" readonly style="background-color: var(--background); cursor: not-allowed;">
                        </div>
                        <div class="form-group">
                            <label>Residential Address</label>
                            <textarea class="form-control" rows="3">123 Tech Avenue, Block B&#13;&#10;Dhaka, 1212</textarea>
                        </div>
                        <button type="button" class="btn-accent" style="width: auto;"><i class="fa-solid fa-save"></i> Save Changes</button>
                    </form>
                </div>

                <div class="section-panel">
                    <div class="panel-header">
                        <h3><i class="fa-solid fa-shield" style="color: var(--danger); margin-right: 8px;"></i> Security Settings</h3>
                    </div>
                    <form>
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="form-control" placeholder="Enter current password">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" class="form-control" placeholder="Enter new password">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" class="form-control" placeholder="Confirm new password">
                            </div>
                        </div>
                        <button type="button" class="btn-secondary" style="margin-top: 8px;"><i class="fa-solid fa-key"></i> Update Password</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="user-script.js"></script>
</body>
</html>
