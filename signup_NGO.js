
        document.addEventListener("DOMContentLoaded", function() {
            let form = document.getElementById('signupForm');

            const namePattern = /^[A-Za-z\s'-]+$/;
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phonePattern = /^\d{10}$/;
            const staffIdPattern = /^[A-Za-z0-9-]{4,}$/;

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                let error = 0;
                const first = document.getElementById('first');
                const last = document.getElementById('last');
                const email = document.getElementById('email');
                const phone = document.getElementById('phone');
                const staff_id = document.getElementById('staff_id');
                const password = document.getElementById('password');
                const confirm_password = document.getElementById('confirm_password');

                // Clear previous errors
                ['err_first','err_last','err_email','err_phone','err_staff_id','err_password','err_confirm_password']
                    .forEach(id => document.getElementById(id).innerText = '');

                function setError(field, errId, msg) {
                    document.getElementById(errId).innerText = msg;
                    error++;
                    field.focus();
                }

                if (!first.value.trim()) setError(first, 'err_first', 'First name is required.');
                else if (!namePattern.test(first.value.trim())) setError(first, 'err_first', 'Invalid first name.');

                if (!last.value.trim()) setError(last, 'err_last', 'Last name is required.');
                else if (!namePattern.test(last.value.trim())) setError(last, 'err_last', 'Invalid last name.');

                if (!email.value.trim()) setError(email, 'err_email', 'Email is required.');
                else if (!emailPattern.test(email.value.trim())) setError(email, 'err_email', 'Invalid email.');

                if (!phone.value.trim()) setError(phone, 'err_phone', 'Phone is required.');
                else if (!phonePattern.test(phone.value.trim())) setError(phone, 'err_phone', 'Phone must be 10 digits.');

                if (!staff_id.value.trim()) setError(staff_id, 'err_staff_id', 'Staff ID is required.');
                else if (!staffIdPattern.test(staff_id.value.trim())) setError(staff_id, 'err_staff_id', 'Invalid Staff ID.');

                if (!password.value) setError(password, 'err_password', 'Password is required.');
                else if (!passwordPattern.test(password.value)) setError(password, 'err_password', 'Password must be 8+ chars with uppercase, lowercase, number, symbol.');

                if (!confirm_password.value) setError(confirm_password, 'err_confirm_password', 'Confirm your password.');
                else if (confirm_password.value !== password.value) setError(confirm_password, 'err_confirm_password', 'Passwords do not match.');

                if (error === 0) form.submit();
            });
        });
