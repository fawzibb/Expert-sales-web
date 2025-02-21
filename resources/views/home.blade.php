<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 99%;
            background-color: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
            z-index: 1000;
        }
        .header .logout {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 18px;
            cursor: pointer;
        }
        .active-days {
            font-size: 16px;
            margin-top: 5px;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            margin-top: 70px; /* Adjust to prevent content from being hidden behind the fixed header */
            padding: 20px;
        }
        .item {
            width: 150px;
            padding: 20px;
            margin: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .item img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .item h3 {
            margin: 10px 0;
            font-size: 18px;
        }
        .item p {
            font-size: 14px;
            color: #555;
        }
        .item button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }
        .item button:hover {
            background-color: #45a049;
        }
        .cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 200px;
            text-align: center;
        }
        .cart h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }
        .cart p {
            font-size: 14px;
            color: #555;
        }
        .cart button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }
        .cart button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logout" id="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <div class="active-days" id="active-days"></div>
    </div>

    <div class="container">
        <div class="item">
            <img src="https://via.placeholder.com/150" alt="Item">
            <h3>Item 1</h3>
            <p>$10.00</p>
            <button>Add to Cart</button>
        </div>
        <div class="item">
            <img src="https://via.placeholder.com/150" alt="Item">
            <h3>Item 2</h3>
            <p>$15.00</p>
            <button>Add to Cart</button>
        </div>
        <div class="item">
            <img src="https://via.placeholder.com/150" alt="Item">
            <h3>Item 3</h3>
            <p>$12.50</p>
            <button>Add to Cart</button>
        </div>
        <!-- Add more items as needed -->
    </div>

    <div class="cart">
        <h3>Cart</h3>
        <p>Items: 0</p>
        <p>Total: $0.00</p>
        <button>Checkout</button>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let token = localStorage.getItem("auth_token");

            if (!token) {
                window.location.href = "/login";
            } else {
                fetch("http://127.0.0.1:8000/api/user", {
                    method: "GET",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update active days
                    let activeToDate = new Date(data.active_to);
                    let currentDate = new Date();
                    let timeDifference = activeToDate - currentDate;
                    let daysRemaining = Math.ceil(timeDifference / (1000 * 3600 * 24));

                    if (daysRemaining > 0) {
                        document.getElementById("active-days").innerText = daysRemaining + " days remaining";
                    } else {
                        document.getElementById("active-days").innerText = "Subscription expired";
                    }
                })
                .catch(error => {
                    console.error("Error fetching user data:", error);
                    alert("Session expired. Please log in again.");
                    localStorage.removeItem("auth_token");
                    window.location.href = "/login";
                });
            }
        });

        document.getElementById("logout-btn").addEventListener("click", function () {
            localStorage.removeItem("auth_token");
            window.location.href = "/login";
        });
    </script>

</body>
</html>
