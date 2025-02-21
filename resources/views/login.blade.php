<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .head-container {
            background: rgb(27, 12, 112);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form id="login-form">
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register">Register here</a></p>
    </div>

    <script>
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
                    // Check if the user is active and their active_to date
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
                    alert("Your account is inactive. Please contact support.");
                }
            })
            .catch(error => {
                alert("An error occurred: " + error.message);
            });
        });
    </script>
</body>
</html>
