document.addEventListener('DOMContentLoaded', () => {
    // Load dynamic user data from sessionStorage
    const currentUser = JSON.parse(sessionStorage.getItem('nexus_currentUser'));
    if (currentUser) {
        // Update topbar name
        const topbarName = document.querySelector('.user-info-top h4');
        if (topbarName) topbarName.textContent = currentUser.fullName;

        // Update profile box details
        const profileBox = document.querySelector('.profile-box');
        if (profileBox) {
            const h3 = profileBox.querySelector('h3');
            if (h3) {
                h3.innerHTML = currentUser.fullName + ' <i class="fa-solid fa-circle-check" style="color: var(--success); font-size: 1.1rem;" title="KYC Verified"></i>';
            }
            const p = profileBox.querySelector('p');
            if (p) p.textContent = currentUser.email;

            // Details inside list
            const detailItems = profileBox.querySelectorAll('.detail-item');
            detailItems.forEach(item => {
                const label = item.querySelector('span:first-child');
                const val = item.querySelector('span:last-child');
                if (label && val) {
                    const txt = label.textContent.trim().toLowerCase();
                    if (txt === 'phone') {
                        val.textContent = currentUser.phone || '+1 (555) 000-0000';
                    } else if (txt === 'joined') {
                        val.textContent = currentUser.createdDate || 'Oct 2023';
                    } else if (txt === 'address') {
                        val.innerHTML = (currentUser.address || 'Address').replace(/\n/g, '<br>');
                    }
                }
            });
        }

        // Update edit profile inputs if we are on edit profile page
        const formGroups = document.querySelectorAll('.form-group');
        formGroups.forEach(group => {
            const label = group.querySelector('label');
            const input = group.querySelector('input, textarea');
            if (label && input) {
                const txt = label.textContent.trim().toLowerCase();
                if (txt === 'full name') {
                    input.value = currentUser.fullName;
                } else if (txt === 'phone number') {
                    input.value = currentUser.phone || '';
                } else if (txt === 'email address') {
                    input.value = currentUser.email;
                } else if (txt === 'residential address') {
                    input.value = currentUser.address || '';
                }
            }
        });

        // Update Account Number card if it exists
        const summaryCards = document.querySelectorAll('.summary-cards .card');
        summaryCards.forEach(card => {
            const title = card.querySelector('.card-title');
            const value = card.querySelector('.card-value');
            if (title && value) {
                if (title.textContent.trim().toLowerCase() === 'account number') {
                    value.textContent = currentUser.id;
                }
            }
        });
    }

    // Mobile Sidebar Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const closeSidebar = document.getElementById('close-sidebar');
    const sidebar = document.getElementById('sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.add('active');
        });
    }

    if (closeSidebar && sidebar) {
        closeSidebar.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });
    }

    // Logout Click Handler
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            sessionStorage.removeItem('nexus_currentUser');
        });
    }

    // Dark Mode Toggle
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const themeIcon = darkModeToggle?.querySelector('i');
    
    // Check saved preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        if (themeIcon) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }
    }

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            let targetTheme = 'light';
            
            if (currentTheme === 'dark') {
                document.documentElement.removeAttribute('data-theme');
                if (themeIcon) {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
            } else {
                targetTheme = 'dark';
                document.documentElement.setAttribute('data-theme', 'dark');
                if (themeIcon) {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                }
            }
            
            localStorage.setItem('theme', targetTheme);
            
            // Re-render chart to match theme colors if it exists
            if (window.balanceChart) {
                updateChartTheme(targetTheme);
            }
        });
    }

    // Sidebar Links Active State
    const navLinks = document.querySelectorAll('.nav-menu li a:not(.logout-btn)');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            navLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // If on mobile, close the sidebar after clicking a link
            if (window.innerWidth <= 768 && sidebar) {
                sidebar.classList.remove('active');
            }
        });
    });

    // Chart.js Analytics
    initChart();
});

function initChart() {
    const ctx = document.getElementById('balanceChart');
    if (!ctx) return;

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#94a3b8' : '#718096';
    const gridColor = isDark ? '#334155' : '#e2e8f0';

    window.balanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Account Balance',
                data: [12500, 14200, 13800, 15500, 15000, 17240],
                borderColor: '#0A2E5C', // Primary Deep Blue
                backgroundColor: 'rgba(10, 46, 92, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: textColor }
                }
            }
        }
    });
}

function updateChartTheme(theme) {
    if (!window.balanceChart) return;
    
    const isDark = theme === 'dark';
    const textColor = isDark ? '#94a3b8' : '#718096';
    const gridColor = isDark ? '#334155' : '#e2e8f0';
    const primaryColor = isDark ? '#4e8deb' : '#0A2E5C';
    const bgColor = isDark ? 'rgba(78, 141, 235, 0.1)' : 'rgba(10, 46, 92, 0.1)';

    window.balanceChart.options.scales.y.grid.color = gridColor;
    window.balanceChart.options.scales.y.ticks.color = textColor;
    window.balanceChart.options.scales.x.ticks.color = textColor;
    window.balanceChart.data.datasets[0].borderColor = primaryColor;
    window.balanceChart.data.datasets[0].backgroundColor = bgColor;
    
    window.balanceChart.update();
}
