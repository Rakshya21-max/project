document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".report-form");

    if (!form) return; // Safety check

    form.addEventListener("submit", function (event) {
        const picture = form.picture.files[0];
        const location = form.location.value.trim();
        const description = form.description.value.trim();
        const email = form.email.value.trim();

        let errors = [];

        if (!picture) errors.push("Please upload a picture.");
        if (!location) errors.push("Location is required.");
        if (!description) errors.push("Description is required.");
        if (!email || !/\S+@\S+\.\S+/.test(email))
            errors.push("Valid email is required.");

        if (errors.length > 0) {
            event.preventDefault();
            alert(errors.join("\n"));
        }
    });
});