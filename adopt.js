
    let adopt=document.querySelector('.login2');
    if (adopt) {
        adopt.addEventListener('click', function(event){
            event.preventDefault();
            let error = 0;

            let namePattern = /^[A-Z][a-z]+$/;
            let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            let phonePattern = /^\d{10}$/;

            let email = document.getElementById('email');
            if (email.value === ""){
                document.getElementById('err_email').innerText = "Email is required.";
                error++;
            }
            let password = document.getElementById('password'); 
            if (password.value === "" || !passwordPattern.test(password.value)){
                document.getElementById('err_password').innerText = 
                "Password must be at least 10 characters long." ;
                error++;
            }
            else {
                document.getElementById('err_password').innerText = "";
            }

            if (error >0) {
                alert("Please fill the required information!");
                event.preventDefault();
}
        });
    }