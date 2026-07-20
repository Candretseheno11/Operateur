document.addEventListener("DOMContentLoaded", function () {

    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");
    const message = document.getElementById("message");
    const submitBtn = document.getElementById("submitBtn");

    if (!password || !confirmPassword || !message || !submitBtn) return;

    function checkPassword() {

        if (confirmPassword.value === "") {
            message.innerHTML = "";
            submitBtn.disabled = true;
            return;
        }

        if (password.value === confirmPassword.value) {
            message.innerHTML = "✔ Mots de passe identiques";
            message.classList.remove("text-danger");
            message.classList.add("text-success");
            submitBtn.disabled = false;
        } else {
            message.innerHTML = "✖ Les mots de passe ne correspondent pas";
            message.classList.remove("text-success");
            message.classList.add("text-danger");
            submitBtn.disabled = true;
        }
    }

    password.addEventListener("keyup", checkPassword);
    confirmPassword.addEventListener("keyup", checkPassword);
});