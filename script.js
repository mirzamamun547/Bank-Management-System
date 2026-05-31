document.addEventListener('DOMContentLoaded', () => {
    // Navigation Logic
    const navLinks = document.querySelectorAll('.nav-links li');
    const contentSections = document.querySelectorAll('.content-section');

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            // Remove active class from all links
            navLinks.forEach(item => item.classList.remove('active'));
            // Add active class to clicked link
            link.classList.add('active');

            // Hide all sections
            contentSections.forEach(section => section.classList.remove('active'));
            
            // Show target section
            const targetId = link.getAttribute('data-target');
            document.getElementById(targetId).classList.add('active');
        });
    });

    // Handle form submissions (Prevent default reload for demo purposes)
    const forms = document.querySelectorAll('.modal-form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Record saved successfully! (Demo mode)');
            
            // Close the closest modal
            const modalId = form.closest('.modal-overlay').id;
            closeModal(modalId);
            form.reset();
        });
    });
});

// Modal Logic
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});
