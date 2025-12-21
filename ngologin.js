// Back arrow navigation
document.querySelector('.back-arrow').addEventListener('click', function() {
    window.history.back();
});

// Tab handling (basic toggle, assumes no sign-up form yet)
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        // if (this.textContent === 'Sign Up') {
        //     alert('Sign Up feature coming soon!');
        // }
    });
});

// Form validation
document.querySelector('.form').addEventListener('submit', function(event) {
    const email = this.querySelector('input[type="email"]').value;
    const password = this.querySelector('input[type="password"]').value;
    if (!staffId || !password) {
        event.preventDefault();
        alert('Please fill in all fields.');
    }
    // Backend PHP will handle authentication
});
