document.getElementById("login-form").addEventListener("submit", function(event) {
    event.preventDefault();

    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    fetch("http://127.0.0.1:8000/api/login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ email: email, password: password })
    })
    .then(response => response.json())
    .then(data => {

        if (data.token) {


            if (data.active && data.active_to) {
                let activeToDate = new Date(data.active_to);
                let today = new Date();

                // If active_to date has passed, deactivate the user
                if (activeToDate < today) {
                    fetch("http://127.0.0.1:8000/api/deactivate", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": "Bearer " + data.token
                        },
                        body: JSON.stringify({ email: email })
                    })
                    .then(() => {
                        alert("Your account has been deactivated due to an expired subscription. Please contact support.");
                        localStorage.removeItem("auth_token");
                        window.location.href = "/login"; // Redirect to login
                    });
                } else {
                    localStorage.setItem("auth_token", data.token);
                    window.location.href = "/home";
                }
            } else {
                alert("Your account is inactive. Please contact support.");
            }
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert("An error occurred: " + error.message);
    });
});
