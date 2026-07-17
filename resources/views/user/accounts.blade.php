<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Accounts - Nexus Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="user-styles.css">
</head>
<body>
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
            <li><a href="/user-accounts" class="active"><i class="fa-solid fa-wallet"></i> My Accounts</a></li>
            <li><a href="/user-transfer"><i class="fa-solid fa-arrow-right-arrow-left"></i> Fund Transfer</a></li>
            <li><a href="/user-transactions"><i class="fa-solid fa-clock-rotate-left"></i> Transaction History</a></li>
            <li><a href="/user-dashboard"><i class="fa-solid fa-chart-line"></i> Balance Inquiry</a></li>
            <li><a href="/user-loan"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Services</a></li>
            <li><a href="/user-notifications"><i class="fa-solid fa-bell"></i> Notifications</a></li>
        </ul>
        <a href="/" class="logout-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
    </nav>

   
    <div class="main-wrapper">
      
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menu-toggle"><i class="fa-solid fa-bars"></i></button>
                <div class="search-box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="Search accounts...">
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

       
        <main class="dashboard-content" style="display: block;">
            @if(session('success'))
                <div style="background-color: var(--success); color: white; padding: 12px; border-radius: 8px; margin-bottom: 24px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h1 style="color: var(--text-main);">My Accounts</h1>
                    <p style="color: var(--text-muted);">Manage your active accounts and open new ones.</p>
                </div>
                <button class="btn-accent" style="width: auto;" onclick="document.getElementById('createAccountModal').style.display='flex'">
                    <i class="fa-solid fa-plus"></i> Create Account
                </button>
            </div>

          
            <form action="{{ route('user.accounts') }}" method="GET" style="margin-bottom: 24px; display: flex; gap: 16px; align-items: center;">
                <label style="font-weight: 500; color: var(--text-main);"><i class="fa-solid fa-filter"></i> Filter By:</label>
                <select name="type" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                    <option value="All Accounts" {{ request('type') == 'All Accounts' ? 'selected' : '' }}>All Accounts</option>
                    <option value="Savings Account" {{ request('type') == 'Savings Account' ? 'selected' : '' }}>Savings Account</option>
                    <option value="Current Account" {{ request('type') == 'Current Account' ? 'selected' : '' }}>Current Account</option>
                    <option value="Fixed Deposit" {{ request('type') == 'Fixed Deposit' ? 'selected' : '' }}>Fixed Deposit</option>
                </select>
            </form>

           
            <div class="summary-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                @forelse($accounts as $account)
                <div class="card" style="position: relative; overflow: hidden; {{ $account->status == 'Blocked' ? 'opacity: 0.8;' : '' }}">
                    @php
                        $colorVar = '--primary';
                        $iconClass = 'fa-piggy-bank';
                        $iconBg = '';
                        $iconColorStyle = 'color: var(--primary);';
                        if($account->account_type == 'Current Account') {
                            $colorVar = '--accent';
                            $iconClass = 'fa-wallet';
                            $iconBg = 'background-color: var(--primary-light);';
                            $iconColorStyle = 'color: var(--accent);';
                        } elseif($account->account_type == 'Fixed Deposit') {
                            $colorVar = '--danger';
                            $iconClass = 'fa-lock';
                            $iconBg = 'background-color: var(--danger-light);';
                            $iconColorStyle = 'color: var(--danger);';
                        }
                    @endphp
                    <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background-color: var({{ $colorVar }});"></div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <div class="card-icon" style="{{ $iconBg }} {{ $iconColorStyle }} margin-bottom: 0;"><i class="fa-solid {{ $iconClass }}"></i></div>
                        <span class="badge {{ $account->status == 'Active' ? 'success' : '' }}" {{ $account->status != 'Active' ? 'style="background-color: var(--danger-light); color: var(--danger);"' : '' }}>{{ $account->status }}</span>
                    </div>
                    <p class="card-title">{{ $account->account_type }}</p>
                    <h2 class="card-value" style="font-size: 1.5rem; margin-bottom: 4px;">{{ $account->account_number }}</h2>
                    <h2 class="card-value" style="color: var({{ $colorVar }});">${{ number_format($account->balance, 2) }}</h2>
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--border); color: var(--text-muted); font-size: 0.85rem; display: flex; justify-content: space-between;">
                        <span><i class="fa-solid fa-code-branch"></i> {{ $account->branch }}</span>
                        <a href="/user-transactions" style="color: var(--accent); text-decoration: none; font-weight: 600;">View History</a>
                    </div>
                </div>
                @empty
                <div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: var(--text-muted); background: white; border-radius: 12px; border: 1px solid var(--border);">
                    <i class="fa-solid fa-folder-open" style="font-size: 3rem; margin-bottom: 16px; color: var(--border);"></i>
                    <p>No accounts found.</p>
                </div>
                @endforelse
            </div>
        </main>
    </div>


    <div id="createAccountModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; overflow-y: auto; padding: 20px;">
        <div style="background: #f8f9fa; padding: 24px; border-radius: 12px; width: 100%; max-width: 900px; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            
            <form action="{{ url('/user-accounts') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 24px;">
                @csrf
                
             
                <div style="width: 200px; flex-shrink: 0; display: flex; flex-direction: column; gap: 24px; align-items: center; padding-top: 20px;">
                  
                    <div style="width: 150px; height: 150px; background: #e9ecef; border: 2px solid #dee2e6; display: flex; align-items: center; justify-content: center; border-radius: 8px; position: relative; overflow: hidden;">
                        <i class="fa-solid fa-user" style="font-size: 5rem; color: #ced4da;"></i>
                        <input type="file" name="profile_photo" accept="image/*" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                    </div>
                    <button type="button" style="background: none; border: none; color: #0056b3; font-weight: bold; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-upload" style="font-size: 1.5rem;"></i> Upload
                    </button>

                    <hr style="width: 100%; border: 0; border-top: 1px solid #dee2e6; margin: 10px 0;">

                  
                    <div style="width: 150px; height: 80px; background: #e9ecef; border: 2px solid #dee2e6; display: flex; align-items: center; justify-content: center; border-radius: 8px; position: relative; overflow: hidden;">
                        <i class="fa-solid fa-signature" style="font-size: 2.5rem; color: #ced4da;"></i>
                        <input type="file" name="signature" accept="image/*" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                    </div>
                    <button type="button" style="background: none; border: none; color: #0056b3; font-weight: bold; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-upload" style="font-size: 1.5rem;"></i> Upload
                    </button>
                </div>

                
                <div style="flex-grow: 1; background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    
                    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label style="font-weight: bold;">Account Type</label>
                        <div style="display: flex; gap: 24px;">
                            <label style="display: flex; align-items: center; gap: 8px;"><input type="radio" name="account_type" value="Saving" required checked onclick="updateInterestDisclosure()"> Saving</label>
                            <label style="display: flex; align-items: center; gap: 8px;"><input type="radio" name="account_type" value="Current" required onclick="updateInterestDisclosure()"> Current</label>
                            <label style="display: flex; align-items: center; gap: 8px;"><input type="radio" name="account_type" value="FD" required onclick="updateInterestDisclosure()"> FD</label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label></label>
                        <div id="interestDisclosure" style="font-size: 0.85rem; color: var(--accent); font-weight: 500; background: rgba(67, 24, 255, 0.05); padding: 8px 12px; border-radius: 6px; display: inline-block;">
                            Savings Account: Pays {{ $settings['INTEREST_RATE'] ?? '5.0' }}% dynamic interest rate annually.
                        </div>
                        <input type="hidden" id="rawInterestRate" value="{{ $settings['INTEREST_RATE'] ?? '5.0' }}">
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label style="font-weight: bold;">Select Branch</label>
                        <select name="branch_id" required style="background: #f1f3f5; border: none; padding: 8px 12px; border-radius: 4px; width: 100%; border: 1px solid #ccc;">
                            @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}">{{ $b->branch_name }} ({{ $b->location }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr 140px 1fr; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label style="font-weight: bold;">Customer ID</label>
                        <input type="text" value="HED{{ str_pad(auth()->id() ?? random_int(100, 999), 11, '0', STR_PAD_LEFT) }}" readonly style="background: #e9ecef; border: none; padding: 8px 12px; border-radius: 4px; width: 100%;">
                        
                        <label style="font-weight: bold; text-align: right;">Opening Balance Rs.</label>
                        <input type="number" name="opening_balance" min="0" required style="background: #f1f3f5; border: none; padding: 8px 12px; border-radius: 4px; width: 100%;">
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label style="font-weight: bold;">Name</label>
                        <input type="text" name="name" value="{{ auth()->user()->FULL_NAME ?? auth()->user()->FIRST_NAME . ' ' . auth()->user()->LAST_NAME }}" style="background: #f1f3f5; border: none; padding: 8px 12px; border-radius: 4px; width: 100%;">
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label style="font-weight: bold;">Father's Name</label>
                        <input type="text" name="father_name" value="{{ auth()->user()->FATHER_NAME ?? '' }}" style="background: #f1f3f5; border: none; padding: 8px 12px; border-radius: 4px; width: 100%;">
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label style="font-weight: bold;">Mother's Name</label>
                        <input type="text" name="mother_name" value="{{ auth()->user()->MOTHER_NAME ?? '' }}" style="background: #f1f3f5; border: none; padding: 8px 12px; border-radius: 4px; width: 100%;">
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr 140px; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label style="font-weight: bold;">Date Of Birth</label>
                        <input type="date" name="dob" value="{{ auth()->user()->DOB ? \Carbon\Carbon::parse(auth()->user()->DOB)->format('Y-m-d') : '' }}" style="background: #f1f3f5; border: none; padding: 8px 12px; border-radius: 4px; width: 200px;">
                        <div style="text-align: right; color: #0056b3;"><i class="fa-regular fa-user-circle" style="font-size: 2rem;"></i></div>
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label style="font-weight: bold;">Gender</label>
                        <div style="display: flex; gap: 24px;">
                            <label style="display: flex; align-items: center; gap: 8px;"><input type="radio" name="gender" value="Male" {{ (auth()->user()->GENDER ?? '') == 'Male' ? 'checked' : '' }}> Male</label>
                            <label style="display: flex; align-items: center; gap: 8px;"><input type="radio" name="gender" value="Female" {{ (auth()->user()->GENDER ?? '') == 'Female' ? 'checked' : '' }}> Female</label>
                            <label style="display: flex; align-items: center; gap: 8px;"><input type="radio" name="gender" value="Transgender" {{ (auth()->user()->GENDER ?? '') == 'Transgender' ? 'checked' : '' }}> Transgender</label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr 140px; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label style="font-weight: bold;">Mobile</label>
                        <input type="text" name="mobile" value="{{ auth()->user()->PHONE ?? '' }}" style="background: #f1f3f5; border: none; padding: 8px 12px; border-radius: 4px; width: 100%;">
                        <div style="text-align: right; color: #0056b3;"><i class="fa-solid fa-signature" style="font-size: 1.5rem;"></i></div>
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 16px; align-items: center; margin-bottom: 12px;">
                        <label style="font-weight: bold;">E-mail</label>
                        <input type="email" name="email" value="{{ auth()->user()->EMAIL ?? '' }}" style="background: #f1f3f5; border: none; padding: 8px 12px; border-radius: 4px; width: 100%;">
                    </div>

                    <div style="display: grid; grid-template-columns: 140px 1fr; gap: 16px; align-items: center; margin-bottom: 24px;">
                        <label style="font-weight: bold;">Address</label>
                        <input type="text" name="address" value="{{ auth()->user()->ADDRESS ?? '' }}" style="background: #f1f3f5; border: none; padding: 8px 12px; border-radius: 4px; width: 100%;">
                    </div>

                    
                    <div style="display: flex; justify-content: flex-end; gap: 16px;">
                        <button type="button" onclick="document.getElementById('createAccountModal').style.display='none'" style="background: #003366; color: white; border: none; padding: 10px 32px; border-radius: 24px; font-weight: bold; cursor: pointer; font-size: 1rem;">Cancel</button>
                        <button type="submit" style="background: #003366; color: white; border: none; padding: 10px 32px; border-radius: 24px; font-weight: bold; cursor: pointer; font-size: 1rem;">OPEN</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script src="user-script.js"></script>
    <script>
        function updateInterestDisclosure() {
            const types = document.getElementsByName('account_type');
            let selectedType = 'Saving';
            for (let t of types) {
                if (t.checked) {
                    selectedType = t.value;
                    break;
                }
            }
            
            const rawRate = parseFloat(document.getElementById('rawInterestRate').value);
            const label = document.getElementById('interestDisclosure');

            if (selectedType === 'Saving') {
                label.style.display = 'inline-block';
                label.textContent = `Savings Account: Pays ${rawRate}% dynamic interest rate annually.`;
            } else if (selectedType === 'FD') {
                label.style.display = 'inline-block';
                label.textContent = `Fixed Deposit: Pays premium ${rawRate + 2}% dynamic interest rate.`;
            } else {
                label.style.display = 'none';
            }
        }
    </script>
</body>
</html>
