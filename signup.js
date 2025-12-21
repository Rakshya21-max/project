let form = document.getElementById('signupForm');

let namePattern = /^[A-Za-z\s'-]+$/;
let passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
let phonePattern = /^\d{10}$/;

function validateSignup(event) {
    event.preventDefault();

    let error = 0;

    const firstName = document.getElementById('first');
    const lastName = document.getElementById('last');
    const phoneNumber = document.getElementById('phone');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');  // FIXED

    // Clear previous errors
    ['err_first','err_last','err_phone','err_email','err_password','err_confirm_password']
        .forEach(id => document.getElementById(id).innerText = '');

    let firstInvalidField = null;

    function setError(field, errId, msg) {
        document.getElementById(errId).innerText = msg;
        error++;
        if (!firstInvalidField) firstInvalidField = field;
    }

    let firstVal = firstName.value.trim();
    let lastVal = lastName.value.trim();
    let phoneVal = phoneNumber.value.trim();
    let emailVal = email.value.trim();
    let passVal = password.value;
    let confVal = confirmPassword.value;

    if (!firstVal) setError(firstName, 'err_first', 'First name is required.');
    else if (!namePattern.test(firstVal)) setError(firstName, 'err_first', 'Invalid first name.');

    if (!lastVal) setError(lastName, 'err_last', 'Last name is required.');
    else if (!namePattern.test(lastVal)) setError(lastName, 'err_last', 'Invalid last name.');

    if (!phoneVal) setError(phoneNumber, 'err_phone', 'Phone number is required.');
    else if (!phonePattern.test(phoneVal)) setError(phoneNumber, 'err_phone', 'Phone must be 10 digits.');

    if (!emailVal) setError(email, 'err_email', 'Email is required.');
    else if (!emailPattern.test(emailVal)) setError(email, 'err_email', 'Invalid email address.');

    if (!passVal) setError(password, 'err_password', 'Password is required.');
    else if (!passwordPattern.test(passVal)) setError(password, 'err_password', 'Password must be 8+ chars, include capital, small, number, symbol.');

    if (!confVal) {
        setError(confirmPassword, 'err_confirm_password', 'Please confirm your password.');
    } else if (confVal !== passVal) {
        setError(confirmPassword, 'err_confirm_password', 'Passwords do not match.');
    }

     if (error > 0) {
         if (firstInvalidField) firstInvalidField.focus();
         return false;
     }
     else{
   form.submit(); 
}
}


form.addEventListener('submit', validateSignup);
