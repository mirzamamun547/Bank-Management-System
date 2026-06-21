<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Bank - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0A2E5C;
            --secondary: #1E90FF;
            --accent: #28a745;
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
            padding: 20px;
        }

        .login-card {
            background: white;
            width: 100%;
            max-width: 400px;
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
        }

        .role-label.employee { background: #e8f0fe; color: var(--primary); }
        .role-label.customer { background: #e6faf0; color: #059669; }

        .center { text-align: center; }

        .form-group { margin-bottom: 18px; }

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

        .input-box input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 0.95rem;
            color: var(--text-dark);
            outline: none;
            transition: border-color 0.2s;
        }

        .input-box input:focus {
            border-color: var(--secondary);
        }

        .input-box input::placeholder { color: #a0aec0; }

        .input-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 0.95rem;
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

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            font-size: 0.85rem;
        }

        .options label {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-dark);
            cursor: pointer;
        }

        .options a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .login-btn {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            color: white;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .login-btn:hover { opacity: 0.9; }
        .login-btn.employee { background: var(--primary); }
        .login-btn.customer { background: #059669; }

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
        .back a:hover { color: var(--secondary); }

        .error-message {
            background: #fcebeb;
            color: #ea5455;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            display: none;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo"><i class="fa-solid fa-building-columns"></i></div>
        <div class="logo-text"><span class="blue">Nexus</span><span class="dark">Bank</span></div>
        <div class="center">
            <div class="role-label" id="roleLabel">Employee Login</div>
        </div>

        @if($errors->any())
            <div class="error-message" style="display: flex;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form id="loginForm" method="POST" action="/login">
            @csrf
            <input type="hidden" name="expected_role" id="expectedRole">
            <div class="form-group">
                <label id="idLabel">Employee ID or Email</label>
                <div class="input-box">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="loginId" name="loginId" placeholder="Enter your ID or email" value="{{ old('loginId') }}" autocomplete="username" required>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="loginPassword" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                    <button type="button" class="toggle-pw" id="togglePw"><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>

            <div class="options">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="login-btn" id="submitBtn">Sign In</button>
        </form>

        <div class="switch" id="switchRole"></div>
        <div class="back"><a href="/"><i class="fa-solid fa-arrow-left"></i> Back to Home</a></div>
    </div>

    <script>
        const role = new URLSearchParams(window.location.search).get('role') || 'employee';
        const isEmp = role === 'employee';

        document.getElementById('roleLabel').textContent = isEmp ? 'Employee Login' : 'Customer Login';
        document.getElementById('roleLabel').classList.add(isEmp ? 'employee' : 'customer');
        document.getElementById('idLabel').textContent = isEmp ? 'Employee ID or Email' : 'Customer ID or Email';
        document.getElementById('loginId').placeholder = isEmp ? 'Enter your employee ID' : 'CUST-XXXXXX or email';
        document.getElementById('loginId').name = 'loginId';
        document.getElementById('loginPassword').name = 'password';
        document.getElementById('expectedRole').value = isEmp ? 'employee' : 'customer';
        document.getElementById('submitBtn').classList.add(isEmp ? 'employee' : 'customer');
        
        document.getElementById('switchRole').innerHTML = isEmp
            ? 'Are you a customer? <a href="login?role=customer">Login here</a>'
            : 'Are you an employee? <a href="login?role=employee">Login here</a><br><div style="margin-top: 10px;">Don\'t have an account? <a href="signup">Sign up here</a></div>';
        
        document.title = 'Nexus Bank - ' + (isEmp ? 'Employee Login' : 'Customer Login');

        // Password toggle
        document.getElementById('togglePw').addEventListener('click', () => {
            const pw = document.getElementById('loginPassword');
            pw.type = pw.type === 'password' ? 'text' : 'password';
            document.getElementById('togglePw').querySelector('i').classList.toggle('fa-eye');
            document.getElementById('togglePw').querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>
