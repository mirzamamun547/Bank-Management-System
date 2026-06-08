document.addEventListener('DOMContentLoaded', () => {
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
