<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer - Nexus Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/styles.css">
</head>
<body>

    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-building-columns"></i>
            <h2>Nexus Bank</h2>
        </div>
        <ul class="nav-links">
            <li onclick="window.location.href='/dashboard'"><i class="fa-solid fa-chart-pie"></i> Back to Dashboard</li>
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
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="/dashboard" style="color: var(--text-muted); text-decoration: none; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            <div class="header-actions">
                <form action="/logout" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-weight: 500;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Edit Form Section -->
        <section class="content-section active" style="padding: 30px; overflow-y: auto;">
            <div class="section-header" style="margin-bottom: 30px;">
                <h1 style="color: var(--text-main); font-size: 1.8rem; margin-bottom: 6px;">Edit Customer Profile</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Modify details for customer ID: <strong>{{ $customer->customer_id ?? $customer->CUSTOMER_ID }}</strong></p>
            </div>

            @if(session('success'))
                <div style="background-color: rgba(16, 185, 129, 0.1); border-left: 4px solid var(--success); color: var(--success); padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 0.95rem; font-weight: 500;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background-color: rgba(239, 68, 68, 0.1); border-left: 4px solid var(--danger); color: var(--danger); padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 0.95rem;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="background-color: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-md); max-width: 650px; padding: 32px;">
                <form action="{{ route('customer.update', $customer->id ?? $customer->ID) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name ?? $customer->FIRST_NAME) }}" required
                                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; outline: none; font-size: 0.95rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name ?? $customer->LAST_NAME) }}" required
                                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; outline: none; font-size: 0.95rem;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email ?? $customer->EMAIL) }}" required
                               style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; outline: none; font-size: 0.95rem;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Mobile Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? $customer->PHONE) }}"
                                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; outline: none; font-size: 0.95rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">National ID (NID)</label>
                            <input type="text" name="nid" value="{{ old('nid', $customer->nid ?? $customer->NID) }}"
                                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; outline: none; font-size: 0.95rem;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Home Address</label>
                        <input type="text" name="address" value="{{ old('address', $customer->address ?? $customer->ADDRESS) }}"
                               style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; outline: none; font-size: 0.95rem;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Date of Birth</label>
                            <input type="date" name="dob" value="{{ old('dob', isset($customer->dob) ? \Carbon\Carbon::parse($customer->dob)->format('Y-m-d') : (isset($customer->DOB) ? \Carbon\Carbon::parse($customer->DOB)->format('Y-m-d') : '')) }}"
                                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; outline: none; font-size: 0.95rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Account Status</label>
                            <select name="status" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; outline: none; font-size: 0.95rem; background: white;">
                                <option value="ACTIVE" {{ old('status', $customer->status ?? $customer->STATUS) === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                <option value="SUSPENDED" {{ old('status', $customer->status ?? $customer->STATUS) === 'SUSPENDED' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; justify-content: flex-end;">
                        <a href="/dashboard" style="padding: 10px 20px; border: 1px solid var(--border); border-radius: 8px; background: none; cursor: pointer; text-decoration: none; color: var(--text-muted); font-size: 0.95rem; font-weight: 600; text-align: center; line-height: 1.5;">Cancel</a>
                        <button type="submit" style="padding: 10px 24px; border: none; border-radius: 8px; background: var(--primary); color: white; cursor: pointer; font-size: 0.95rem; font-weight: 600;">Save Changes</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

</body>
</html>
