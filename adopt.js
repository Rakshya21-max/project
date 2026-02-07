<script>
document.querySelector('.adopt-form').addEventListener('submit', function (event) {

    // 1️⃣ Validate radio button questions
    const radioGroups = ['q1', 'q2', 'q3', 'q4'];

    for (let group of radioGroups) {
        const selected = document.querySelector(`input[name="${group}"]:checked`);
        if (!selected) {
            event.preventDefault();
            alert('Please answer all Yes/No questions.');
            return;
        }
    }

    // 2️⃣ Validate all text inputs
    const textInputs = document.querySelectorAll('input[type="text"]');

    for (let input of textInputs) {
        if (input.value.trim() === '') {
            event.preventDefault();
            alert('Please fill in all text fields.');
            input.focus();
            return;
        }
    }

});
</script>
