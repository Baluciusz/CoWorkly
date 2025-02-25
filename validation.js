document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("form");
    const username_input = document.getElementById("username-input");
    const email_input = document.getElementById("email-input");
    const password_input = document.getElementById("password-input");
    const repeat_password_input = document.getElementById("repeat-password-input");
    const error_message = document.getElementById("error-message");
    const terms_checkbox = document.getElementById("terms");
    const signup_button = form.querySelector("button");

    if (!form) {
        console.error("Form element not found! Check your HTML ID.");
        return;
    }

    form.addEventListener("submit", (e) => {
        let errors = [];

        // Validate the form fields
        errors = getSignupFormErrors(
            username_input.value.trim(),
            email_input.value.trim(),
            password_input.value.trim(),
            repeat_password_input ? repeat_password_input.value.trim() : ""
        );

        // Always show errors, even if the checkbox is not checked
        if (errors.length > 0) {
            e.preventDefault();  // Prevent form submission
            error_message.innerText = errors.join(". ");
        }

        // Check if terms checkbox is ticked, if not, show a specific error
        if (!terms_checkbox.checked) {
            if (!error_message.innerText) {
                error_message.innerText = "Please accept the terms and privacy policy.";
            } else {
                error_message.innerText += " Please accept the terms and privacy policy.";
            }
        }
    });

    function getSignupFormErrors(username, email, password, repeatPassword) {
        let errors = [];

        if (!username) {
            errors.push("Username is required");
            highlightError(username_input);
        } else if (username.length < 5) {
            errors.push("Username must be at least 5 characters long");
            highlightError(username_input);
        } else if (isUsernameTaken(username)) {
            errors.push("Username is already taken");
            highlightError(username_input);
        }

        if (!email) {
            errors.push("Email is required");
            highlightError(email_input);
        } else if (!isValidEmail(email)) {
            errors.push("Invalid email format");
            highlightError(email_input);
        }

        if (!password) {
            errors.push("Password is required");
            highlightError(password_input);
        } else if (password.length < 8) {
            errors.push("Password must be at least 8 characters long");
            highlightError(password_input);
        }

        if (password !== repeatPassword) {
            errors.push("Passwords do not match");
            highlightError(password_input);
            highlightError(repeat_password_input);
        }

        return errors;
    }

    function isValidEmail(email) {
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        return emailPattern.test(email);
    }

    function isUsernameTaken(username) {
        // Here, simulate a check for an existing username (e.g., database query)
        const existingUsernames = ["existinguser1", "user123", "admin"]; // Example existing usernames
        return existingUsernames.includes(username);
    }

    function highlightError(input) {
        if (input && input.parentElement) {
            input.parentElement.classList.add("incorrect");
        }
    }

    function clearError(input) {
        if (input && input.parentElement) {
            input.parentElement.classList.remove("incorrect");
        }
    }

    const allInputs = [username_input, email_input, password_input, repeat_password_input].filter(input => input != null);

    allInputs.forEach(input => {
        input.addEventListener("input", () => {
            clearError(input);
            error_message.innerText = "";
        });
    });

    // Modal handling for terms
    const termsLink = document.getElementById("terms-link");
    const termsModal = document.getElementById("terms-modal");
    const closeModal = document.querySelector(".close");

    if (termsLink && termsModal) {
        termsLink.addEventListener("click", function () {
            termsModal.style.display = "block";
        });
    }

    if (closeModal && termsModal) {
        closeModal.addEventListener("click", function () {
            termsModal.style.display = "none";
        });

        window.onclick = function (event) {
            if (event.target === termsModal) {
                termsModal.style.display = "none";
            }
        };
    }
});
