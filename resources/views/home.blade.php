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
            width: 100%;
            background-color: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .user-info {
            display: flex;
            align-items: center;
        }
        .header .user-name {
            font-size: 18px;
            margin-left: 20px;
        }
        .header .add-item, .header .inventory, .header .orders, .header .edit-items, .header .items {
            margin-left: 15px;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .header .add-item:hover, .header .inventory:hover, .header .orders:hover, .header .edit-items:hover, .header .items:hover {
            background-color: #45a049;
        }
        .header .logout {
            font-size: 18px;
            cursor: pointer;
            margin-right: 20px;
        }
        .active-days {
            font-size: 16px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            margin-top: 70px;
            padding: 20px;
            width: 80%;
            margin-right: 20px;
            justify-content: flex-start;
        }
        .item {
            width: 150px;
            padding: 20px;
            margin: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }
        .item img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .item .delete-item {
            color: red;
            cursor: pointer;
            position: absolute;
            top: 5px;
            right: 5px;
            display: none;
        }
        .item.edit-mode .delete-item {
            display: block;
        }
        .cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 15%;
            text-align: center;
            max-height: 80vh;
            overflow-y: auto;
            scroll-behavior: smooth;
        }
        .cart ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .cart ul li {
            margin-bottom: 10px;
        }
        .cart .total {
            font-size: 18px;
            margin-top: 20px;
            font-weight: bold;
        }
        .cart .clear-btn {
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .cart .clear-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="user-info">
            <div class="user-name" id="user-name"></div>
            <button class="add-item" onclick="window.location.href='/add_item'">+ Add Item</button>
            <button class="inventory" onclick="window.location.href='/inventory'">Inventory</button>
            <button class="orders" onclick="window.location.href='/orders'">Orders</button>
            <button class="edit-items" onclick="toggleEditMode()">Edit Items</button>
            <button class="items" onclick="window.location.href='/items'">Items</button>
        </div>
        <div class="active-days" id="active-days"></div>
        <div class="logout" id="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
        </div>
    </div>

    <div class="container" id="items-container">
        <!-- Items will be dynamically added here -->
    </div>

    <div class="cart" id="cart">
        <h3>Your Cart</h3>
        <ul id="cart-items">
            <!-- Cart items will be listed here -->
        </ul>
        <div class="total" id="total-price">Total: $0.00</div>
        <button class="clear-btn" id="clear-cart-btn">Clear Cart</button>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let token = localStorage.getItem("auth_token");

            if (!token) {
                window.location.href = "/login";
                return;
            }

            fetch("http://127.0.0.1:8000/api/user", {
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + token,
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("user-name").innerText = data.name;
                let activeToDate = new Date(data.active_to);
                let currentDate = new Date();
                let timeDifference = activeToDate - currentDate;
                let daysRemaining = Math.ceil(timeDifference / (1000 * 3600 * 24));
                document.getElementById("active-days").innerText = daysRemaining > 0 ? daysRemaining + " days remaining" : "Subscription expired";

                fetch("http://127.0.0.1:8000/api/items", {
                    method: "GET",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(items => {
                    let container = document.getElementById("items-container");
                    container.innerHTML = "";
                    items.forEach(item => {
                        let itemDiv = document.createElement("div");
                        itemDiv.classList.add("item");
                        itemDiv.innerHTML =
                            `<img src="https://via.placeholder.com/150" alt="${item.name}">
                            <h3>${item.name}</h3>
                            <p>$${item.price}</p>
                            <button onclick="addToCart('${item.name}', ${item.price})">Add to Cart</button>
                            <span class="delete-item" onclick="deleteItem(${item.id})">X</span>`;
                        container.appendChild(itemDiv);
                    });
                })
                .catch(error => console.error("Error fetching items:", error));
            })
            .catch(error => {
                console.error("Error fetching user data:", error);
                alert("Session expired. Please log in again.");
                localStorage.removeItem("auth_token");
                window.location.href = "/login";
            });
        });

        document.getElementById("logout-btn").addEventListener("click", function () {
            localStorage.removeItem("auth_token");
            window.location.href = "/login";
        });

        let cartItems = [];
        let totalPrice = 0;

        function addToCart(name, price) {
            cartItems.push({ name, price });
            totalPrice += price;
            updateCart();
        }

        function updateCart() {
            let cartList = document.getElementById("cart-items");
            let totalElement = document.getElementById("total-price");
            cartList.innerHTML = "";
            cartItems.forEach(item => {
                let li = document.createElement("li");
                li.innerText = `${item.name} - $${item.price.toFixed(2)}`;
                cartList.appendChild(li);
            });
            totalElement.innerText = `Total: $${totalPrice.toFixed(2)}`;

            // Auto scroll to bottom
            document.getElementById('cart').scrollTop = document.getElementById('cart').scrollHeight;
        }

        document.getElementById("clear-cart-btn").addEventListener("click", function () {
            cartItems = [];
            totalPrice = 0;
            updateCart();
        });

        function toggleEditMode() {
            let items = document.querySelectorAll(".item");
            items.forEach(item => {
                item.classList.toggle("edit-mode");
            });
        }

        function deleteItem(itemId) {
            let token = localStorage.getItem("auth_token");

            fetch(`http://127.0.0.1:8000/api/items/${itemId}`, {
                method: "DELETE",
                headers: {
                    "Authorization": "Bearer " + token,
                    "Content-Type": "application/json"
                }
            })
            .then(response => {
                if (response.ok) {
                    alert("Item deleted successfully!");
                    location.reload();
                } else {
                    alert("Error deleting item.");
                }
            })
            .catch(error => console.error("Error deleting item:", error));
        }
    </script>
</body>
</html>
