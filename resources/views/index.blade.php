<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Bank - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="landing.css">
</head>
<body>

    <!-- 1. Top Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <i class="fa-solid fa-building-columns"></i>
            <div><span class="logo-blue">Nexus</span><span class="logo-black">Bank</span></div>
        </div>
        <ul class="nav-links">
            <li><a href="#" class="active">Home</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="nav-actions">
            <div class="login-dropdown" id="loginDropdown">
                <button class="login-btn" id="loginToggleBtn">
                    <i class="fa-solid fa-right-to-bracket"></i> Login <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                </button>
                <div class="login-dropdown-menu" id="loginDropdownMenu">
                    <a href="/login?role=employee" class="dropdown-item">
                        <div class="dropdown-item-icon employee-icon"><i class="fa-solid fa-user-tie"></i></div>
                        <div class="dropdown-item-text">
                            <span class="dropdown-item-title">Employee</span>
                            <span class="dropdown-item-desc">Staff portal</span>
                        </div>
                    </a>
                    <a href="/login?role=admin" class="dropdown-item">
                        <div class="dropdown-item-icon admin-icon" style="background: #fef3c7; color: #d97706;"><i class="fa-solid fa-user-shield"></i></div>
                        <div class="dropdown-item-text">
                            <span class="dropdown-item-title">Admin</span>
                            <span class="dropdown-item-desc">System administration</span>
                        </div>
                    </a>
                    <a href="/login?role=customer" class="dropdown-item">
                        <div class="dropdown-item-icon customer-icon"><i class="fa-solid fa-user"></i></div>
                        <div class="dropdown-item-text">
                            <span class="dropdown-item-title">Customer</span>
                            <span class="dropdown-item-desc">Personal banking</span>
                        </div>
                    </a>
                </div>
            </div>
            <a href="/signup" class="signup-btn">Register</a>
        </div>
    </nav>

    <!-- 2. Hero Section -->
    <main class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Secure. Fast. Reliable<br><span class="text-blue">Banking at Your Fingertips</span></h1>
            <p class="hero-desc">
                Experience the next generation of banking with our modern, secure, and intuitive financial platform.
            </p>
            <div class="hero-buttons">
                <a href="/signup" class="cta-btn primary">Open Account <i class="fa-solid fa-arrow-right"></i></a>
                <a href="/login?role=customer" class="cta-btn secondary">Login to Dashboard <i class="fa-solid fa-lock"></i></a>
            </div>
        </div>
        <div class="hero-image">
            <img src="bank_illustration.png" alt="Bank Illustration">
        </div>
    </main>

    <!-- 3. Feature Cards Section -->
    <section id="services" class="section-padding bg-light">
        <div class="container">
            <div class="section-title">
                <h2>Our Comprehensive Services</h2>
                <p>Everything you need to manage your finances in one place</p>
            </div>
            <div class="features-grid">
                <!-- Customer Management -->
                <div class="feature-card-full">
                    <div class="icon-wrapper"><i class="fa-solid fa-users"></i></div>
                    <h3>Customer Management</h3>
                    <p>Manage customer profiles, KYC details and personal information.</p>
                    <a href="#" class="view-more">View More <i class="fa-solid fa-angle-right"></i></a>
                </div>
                <!-- Account Management -->
                <div class="feature-card-full">
                    <div class="icon-wrapper"><i class="fa-solid fa-wallet"></i></div>
                    <h3>Account Management</h3>
                    <p>Create, update, and manage different types of bank accounts.</p>
                    <a href="#" class="view-more">View More <i class="fa-solid fa-angle-right"></i></a>
                </div>
                <!-- Deposit & Withdrawal -->
                <div class="feature-card-full">
                    <div class="icon-wrapper"><i class="fa-solid fa-money-bill-transfer"></i></div>
                    <h3>Deposit & Withdrawal</h3>
                    <p>Easy cash in and cash out transactions with instant processing.</p>
                    <a href="#" class="view-more">View More <i class="fa-solid fa-angle-right"></i></a>
                </div>
                <!-- Fund Transfer -->
                <div class="feature-card-full">
                    <div class="icon-wrapper"><i class="fa-solid fa-money-check-dollar"></i></div>
                    <h3>Fund Transfer</h3>
                    <p>Send money between accounts securely and internationally.</p>
                    <a href="#" class="view-more">View More <i class="fa-solid fa-angle-right"></i></a>
                </div>
                <!-- Balance Inquiry -->
                <div class="feature-card-full">
                    <div class="icon-wrapper"><i class="fa-solid fa-scale-balanced"></i></div>
                    <h3>Balance Inquiry</h3>
                    <p>Check your real-time account balance anytime, anywhere.</p>
                    <a href="#" class="view-more">View More <i class="fa-solid fa-angle-right"></i></a>
                </div>
                <!-- Transaction History -->
                <div class="feature-card-full">
                    <div class="icon-wrapper"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <h3>Transaction History</h3>
                    <p>View detailed transaction logs and download statements.</p>
                    <a href="#" class="view-more">View More <i class="fa-solid fa-angle-right"></i></a>
                </div>
                <!-- Loan Management -->
                <div class="feature-card-full">
                    <div class="icon-wrapper"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <h3>Loan Management</h3>
                    <p>Apply, approve, and track personal and business loans.</p>
                    <a href="#" class="view-more">View More <i class="fa-solid fa-angle-right"></i></a>
                </div>
                <!-- Employee Management -->
                <div class="feature-card-full">
                    <div class="icon-wrapper"><i class="fa-solid fa-user-tie"></i></div>
                    <h3>Employee Management</h3>
                    <p>Manage banking staff, roles, and administrative permissions.</p>
                    <a href="#" class="view-more">View More <i class="fa-solid fa-angle-right"></i></a>
                </div>
                <!-- Branch Management -->
                <div class="feature-card-full">
                    <div class="icon-wrapper"><i class="fa-solid fa-code-branch"></i></div>
                    <h3>Branch Management</h3>
                    <p>Add and control bank branches across different locations.</p>
                    <a href="#" class="view-more">View More <i class="fa-solid fa-angle-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Quick Stats Section -->
    <section class="stats-section">
        <div class="container stats-grid">
            <div class="stat-item">
                <div class="stat-number" data-target="50000">0</div>
                <div class="stat-label">Total Customers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="120000">0</div>
                <div class="stat-label">Active Accounts</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="850">0</div>
                <div class="stat-label">Total Deposits (M)</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="125">0</div>
                <div class="stat-label">Branches</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="3200">0</div>
                <div class="stat-label">Employees</div>
            </div>
        </div>
    </section>

    <!-- 5. Role-Based Access Section -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="section-title">
                <h2>Role-Based Access</h2>
                <p>Dedicated portals for different users</p>
            </div>
            <div class="roles-grid">
                <div class="role-card">
                    <div class="role-icon"><i class="fa-solid fa-user"></i></div>
                    <h3>Customer Portal</h3>
                    <p>Access your accounts, transfer funds, and apply for loans.</p>
                    <a href="/login?role=customer" class="cta-btn secondary btn-sm">Login as Customer</a>
                </div>
                <div class="role-card">
                    <div class="role-icon"><i class="fa-solid fa-user-tie"></i></div>
                    <h3>Employee Panel</h3>
                    <p>Process transactions, approve loans, and assist customers.</p>
                    <a href="/login?role=employee" class="cta-btn secondary btn-sm">Login as Employee</a>
                </div>
                <div class="role-card admin-card">
                    <div class="role-icon"><i class="fa-solid fa-user-shield"></i></div>
                    <h3>Admin Dashboard</h3>
                    <p>System configuration, branch management, and analytics.</p>
                    <a href="/login?role=admin" class="cta-btn primary btn-sm">Login as Admin</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Why Choose Us Section -->
    <section id="about" class="section-padding bg-light">
        <div class="container">
            <div class="why-choose-us-wrapper">
                <div class="why-content">
                    <h2>Why Choose Us?</h2>
                    <p>We provide world-class financial solutions tailored to your needs, backed by cutting-edge security and innovative technology.</p>
                    <ul class="benefit-list">
                        <li><i class="fa-solid fa-check-circle"></i> <strong>Secure Banking System:</strong> Bank with peace of mind.</li>
                        <li><i class="fa-solid fa-bolt"></i> <strong>Fast Transactions:</strong> Instant processing for transfers.</li>
                        <li><i class="fa-solid fa-chart-pie"></i> <strong>Real-time Data:</strong> Always know your current balance.</li>
                        <li><i class="fa-solid fa-mobile-screen"></i> <strong>Responsive Design:</strong> Works perfectly on all devices.</li>
                        <li><i class="fa-solid fa-file-invoice-dollar"></i> <strong>Complete History:</strong> Never lose track of a payment.</li>
                    </ul>
                </div>
                <div class="why-image">
                    <div class="shield-graphic">
                        <i class="fa-solid fa-shield-halved"></i>
                        <div class="pulse-ring"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Footer Section -->
    <footer id="contact" class="footer">
        <div class="container footer-grid">
            <div class="footer-about">
                <div class="logo footer-logo">
                    <i class="fa-solid fa-building-columns"></i>
                    <div><span class="logo-white">Nexus</span><span class="logo-light">Bank</span></div>
                </div>
                <p>Empowering your financial journey with secure, fast, and reliable banking solutions.</p>
                <div class="social-links">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h3>Legal</h3>
                <ul>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Security</a></li>
                    <li><a href="#">Cookie Policy</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <ul>
                    <li><i class="fa-solid fa-location-dot"></i> 123 Financial District, NY 10004</li>
                    <li><i class="fa-solid fa-phone"></i> +1 (555) 123-4567</li>
                    <li><i class="fa-solid fa-envelope"></i> support@nexusbank.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Nexus Bank Ltd. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>