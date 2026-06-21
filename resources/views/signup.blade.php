<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Bank - Customer Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0A2E5C;
            --secondary: #1E90FF;
            --accent: #059669;
            --accent-light: #e6faf0;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --bg: #f0f4f8;
            --border: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            padding: 30px 20px;
        }

        .signup-card {
            background: white;
            width: 100%;
            max-width: 650px;
            padding: 40px 32px;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }

        .logo {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo i {
            font-size: 2.2rem;
            color: var(--secondary);
        }

        .logo-text {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .logo-text .blue { color: var(--primary); }
        .logo-text .dark { color: var(--text-dark); }

        .role-label {
            text-align: center;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 4px 14px;
            border-radius: 20px;
            display: inline-block;
            margin: 12px auto 28px;
            width: fit-content;
            background: var(--accent-light);
            color: var(--accent);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 6px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .input-box {
            position: relative;
        }

        .input-box input, .input-box textarea {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 0.95rem;
            color: var(--text-dark);
            outline: none;
            transition: border-color 0.2s;
        }

        .input-box textarea {
            resize: none;
            height: 80px;
        }

        .input-box input:focus, .input-box textarea:focus {
            border-color: var(--secondary);
        }

        .input-box input::placeholder, .input-box textarea::placeholder { color: #a0aec0; }

        .input-box i {
            position: absolute;
            left: 14px;
            top: 15px;
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .input-box input[type="date"] {
            padding-left: 42px;
        }

        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 0.95rem;
        }

        .signup-btn {
            grid-column: span 2;
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            color: white;
            background: var(--accent);
            cursor: pointer;
            transition: opacity 0.2s;
            margin-top: 10px;
        }

        .signup-btn:hover { opacity: 0.9; }

        .switch {
            text-align: center;
            margin-top: 22px;
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .switch a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .back {
            text-align: center;
            margin-top: 16px;
        }

        .back a {
            font-size: 0.85rem;
            color: var(--text-light);
            text-decoration: none;
        }

        .back a:hover { color: var(--secondary); }

        .error-message {
            grid-column: span 2;
            background: #fcebeb;
            color: #ea5455;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            display: none;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .success-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.7);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(4px);
        }

        .success-card {
            background: white;
            border-radius: 16px;
            max-width: 450px;
            width: 100%;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: popUp 0.3s ease-out;
        }

        @keyframes popUp {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .success-card i {
            font-size: 4rem;
            color: var(--accent);
            margin-bottom: 20px;
        }

        .success-card h2 {
            font-size: 1.5rem;
            color: var(--text-dark);
            margin-bottom: 10px;
            font-weight: 800;
        }

        .success-card p {
            color: var(--text-light);
            font-size: 0.95rem;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .cust-id-badge {
            background: #f0fdf4;
            border: 1px dashed var(--accent);
            color: var(--accent);
            font-family: monospace;
            font-size: 1.6rem;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 10px;
            margin: 15px auto 25px;
            width: fit-content;
            letter-spacing: 1px;
        }

        .done-btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            font-weight: 700;
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .done-btn:hover { opacity: 0.9; }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width, .signup-btn, .error-message {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>

    <div class="signup-card">
        <div class="logo"><i class="fa-solid fa-building-columns"></i></div>
        <div class="logo-text"><span class="blue">Nexus</span><span class="dark">Bank</span></div>
        <div class="center" style="text-align: center;">
            <div class="role-label">Customer Registration</div>
        </div>

        @if($errors->any())
            <div class="error-message" style="display: flex;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form id="signupForm" method="POST" action="/signup">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name</label>
                    <div class="input-box">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="firstName" name="firstName" placeholder="John" value="{{ old('firstName') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <div class="input-box">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="lastName" name="lastName" placeholder="Doe" value="{{ old('lastName') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-box">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="john.doe@example.com" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="input-box">
                        <i class="fa-solid fa-phone"></i>
                        <input type="tel" id="phone" name="phone" placeholder="+1 (555) 000-0000" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Residential Address</label>
                    <div class="input-box">
                        <i class="fa-solid fa-map-pin"></i>
                        <textarea id="address" name="address" placeholder="123 Financial Way, Suite 100" required>{{ old('address') }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <div class="input-box">
                        <i class="fa-solid fa-calendar"></i>
                        <input type="date" id="dob" name="dob" value="{{ old('dob') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>National ID (NID)</label>
                    <div class="input-box">
                        <i class="fa-solid fa-id-card"></i>
                        <input type="text" id="nid" name="nid" placeholder="NID-12345678" value="{{ old('nid') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="signup-btn">Create Account</button>
            </div>
        </form>

        <div class="switch">
            Already have an account? <a href="/login?role=customer">Login here</a>
        </div>
        <div class="back"><a href="/"><i class="fa-solid fa-arrow-left"></i> Back to Home</a></div>
    </div>

    <!-- Success Popup Overlay -->
    <div class="success-overlay" id="successOverlay">
        <div class="success-card">
            <i class="fa-solid fa-circle-check"></i>
            <h2>Registration Successful!</h2>
            <p>Welcome to Nexus Bank. Your new Customer ID has been generated successfully. Please make sure to save it as you will need it to log in.</p>
            <div class="cust-id-badge" id="generatedCustId">CUST-000000</div>
            <a href="/login?role=customer" class="done-btn">Proceed to Login</a>
        </div>
    </div>

    <script>
        // Set maximum date of birth to today
        document.getElementById('dob').max = new Date().toISOString().split("T")[0];

        @if(session('success'))
            // Display Success Popup
            document.getElementById('generatedCustId').textContent = '{{ session('success') }}';
            document.getElementById('successOverlay').style.display = 'flex';
        @endif
    </script>
</body>
</html>
