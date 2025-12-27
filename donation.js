// donation.js - Validation and Payment Handling for Donation Form

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.donation-form');
    const paymentImages = document.querySelectorAll('.payments img');

    // Payment method click handlers
    paymentImages.forEach(img => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            const alt = this.alt.toLowerCase();
            let url = '';

            switch(alt) {
                case 'esewa':
                    // Try app first, fallback to web
                    url = 'esewa://'; // App scheme
                    if (!openAppOrWeb(url, 'https://esewa.com.np')) {
                        window.open('https://esewa.com.np', '_blank');
                    }
                    break;
                case 'khalti':
                    url = 'khalti://'; // App scheme
                    if (!openAppOrWeb(url, 'https://khalti.com')) {
                        window.open('https://khalti.com', '_blank');
                    }
                    break;
                case 'paypal':
                    window.open('https://www.paypal.com', '_blank');
                    break;
                case 'ime':
                    url = 'imepay://'; // App scheme
                    if (!openAppOrWeb(url, 'https://imepay.com.np')) {
                        window.open('https://imepay.com.np', '_blank');
                    }
                    break;
                default:
                    alert('Payment method not available');
            }
        });
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent form submission

        const firstName = form.querySelector('input[name="first"]').value.trim();
        const lastName = form.querySelector('input[name="last"]').value.trim();
        const email = form.querySelector('input[name="email"]').value.trim();
        const amount = form.querySelector('input[name="amount"]').value;

        let isValid = true;
        let errors = [];

        // Validate first name
        if (!firstName) {
            errors.push('First name is required');
            isValid = false;
        }

        // Validate last name
        if (!lastName) {
            errors.push('Last name is required');
            isValid = false;
        }

        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            errors.push('Email is required');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            errors.push('Please enter a valid email address');
            isValid = false;
        }

        // Validate amount
        if (!amount || amount <= 0) {
            errors.push('Please enter a valid donation amount');
            isValid = false;
        }

        if (!isValid) {
            alert('Please fix the following errors:\n' + errors.join('\n'));
            return;
        }

        // If valid, show success message (in real app, submit to server)
        alert('Thank you for your donation of NPR ' + amount + '! Your payment will be processed through the selected method.');
        
        // Reset form
        form.reset();
    });

    // Helper function to try opening app, fallback to web
    function openAppOrWeb(appUrl, webUrl) {
        // For mobile, try app scheme
        if (/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            window.location.href = appUrl;
            // If app doesn't open, fallback after timeout
            setTimeout(function() {
                window.open(webUrl, '_blank');
            }, 2000);
            return true;
        }
        return false;
    }
});