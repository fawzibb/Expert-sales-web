<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        /* Add CSS for the spinner */
        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
            border-width: 0.25em;
        }

        .btn-register {
            position: relative;
        }

        .spinner-container {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4" style="width: 400px;">
            <h2 class="text-center mb-4">Register</h2>

            <form method="POST" action="{{ route('user.store') }}" id="registrationForm">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    @error('password')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-register" style="background: #28a745;">
                    Register
                    <div class="spinner-container" id="spinner">
                        <div class="spinner-border text-light" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </button>
            </form>

            <p class="text-center mt-3">
                Already have an account? <a href="{{ route('login') }}">Login</a>
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('registrationForm').onsubmit = function(e) {
            e.preventDefault(); // Prevent default form submission

            // Show the spinner and disable the button
            var spinner = document.getElementById('spinner');
            var button = document.querySelector('.btn-register');
            spinner.style.display = 'block'; // Show spinner
            button.disabled = true; // Disable button to prevent multiple submissions

            // Submit the form via AJAX
            fetch(this.action, {
                method: this.method,
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = "{{ route('login') }}"; // Redirect to login page on success
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.errors) {
                    alert("Registration failed. Please check your inputs.");
                }
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                spinner.style.display = 'none'; // Hide spinner after the request completes
                button.disabled = false; // Re-enable the button
            });
        };
    </script>
</body>
</html>
