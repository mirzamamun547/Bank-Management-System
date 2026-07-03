<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Nexus Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/user-styles.css">
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
            <li><a href="/user-loan"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Services</a></li>
            <li><a href="/user-notifications" class="active"><i class="fa-solid fa-bell"></i> Notifications</a></li>
        </ul>
        <a href="/" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>
        <form id="logout-form" action="/logout" method="POST" style="display: none;">
            @csrf
        </form>
    </nav>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menu-toggle"><i class="fa-solid fa-bars"></i></button>
                <div class="search-box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="Search...">
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
            <div class="section-panel" style="background-color: var(--panel-bg, #fff); border-radius: 12px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <div class="panel-header" style="border-bottom: 1px solid var(--border); padding-bottom: 15px; margin-bottom: 20px;">
                    <h3 style="font-size: 1.5rem; display: flex; align-items: center; gap: 10px; margin: 0; color: var(--text-dark);">
                        <i class="fa-solid fa-bell" style="color: var(--secondary);"></i> Notifications
                    </h3>
                    <p style="color: var(--text-light); font-size: 0.875rem; margin: 5px 0 0 0;">View your latest alerts, approval messages, and account credentials.</p>
                </div>

                @if($notifications->isEmpty())
                    <div style="text-align: center; padding: 60px 20px; color: var(--text-light);">
                        <i class="fa-solid fa-bell-slash" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p style="margin: 0; font-size: 1rem;">You have no notifications at this time.</p>
                    </div>
                @else
                    <div class="notification-list" style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach($notifications as $notification)
                            <div class="notification-item" 
                                 style="padding: 16px; border: 1px solid var(--border); border-radius: 8px; display: flex; gap: 16px; align-items: flex-start; transition: all 0.2s; {{ !$notification->is_read ? 'border-left: 4px solid var(--secondary); background-color: rgba(30, 144, 255, 0.04);' : '' }}">
                                <div style="color: var(--secondary); font-size: 1.25rem; margin-top: 2px;">
                                    <i class="fa-solid fa-envelope-open-text"></i>
                                </div>
                                <div style="flex-grow: 1;">
                                    <p style="margin: 0 0 8px; font-size: 0.95rem; line-height: 1.5; color: var(--text-dark); font-weight: {{ !$notification->is_read ? '600' : '400' }};">
                                        {{ $notification->message }}
                                    </p>
                                    <span style="font-size: 0.8rem; color: var(--text-light); display: flex; align-items: center; gap: 5px;">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- JavaScript to support responsiveness/menu toggle -->
    <script src="/user-script.js"></script>
</body>
</html>
