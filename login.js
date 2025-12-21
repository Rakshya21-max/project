let loginBtn = document.querySelector('.login2');
loginBtn.addEventListener('click', function(event) {

    let error = 0;

 
       
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    let email = document.getElementById('email');
    let password = document.getElementById('password');

    // Email validation
    if (email.value === "") {
        document.getElementById('err_email').innerText = "Email is required.";
        error++;
    } else if (!emailPattern.test(email.value)) {
        document.getElementById('err_email').innerText = "Email format is invalid.";
        error++;
    } else {
        document.getElementById('err_email').innerText = "";
    }

    // Password validation
    if (password.value === "") {
        document.getElementById('err_password').innerText = "Password is required.";
        error++;
    } else if (!passwordPattern.test(password.value)) {
        document.getElementById('err_password').innerText = 
        "Password must contain 8+ characters with uppercase, lowercase, number, special symbol.";
        error++;
    } else {
        document.getElementById('err_password').innerText = "";
    }

    if (error > 0) {
        event.preventDefault();
        alert("Please fill the required information correctly!");
        return;
    }

    // If no errors, submit the form
    // document.querySelector('form').submit();
});