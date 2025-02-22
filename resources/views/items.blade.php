<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Items</title>
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
        .header .home-btn {
            margin-left: 15px;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .header .home-btn:hover {
            background-color: #45a049;
        }
        .active-days {
            font-size: 16px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        .container {
            max-width: 800px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .edit-input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .save-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            color: white;
        }
        .save-btn {
            background-color: #008CBA;
        }
        .save-btn:hover {
            background-color: #0073a4;
        }
        .delete-btn {
            background-color: #f44336;
        }
        .delete-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="user-info">
            <div class="user-name" id="user-name"></div>
            <button class="home-btn" onclick="window.location.href='/'">🏠 Home</button>
        </div>
        <div class="active-days" id="active-days"></div>
    </div>

    <div class="container">
        <h2>My Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="items-table">
                <!-- سيتم تحميل العناصر هنا -->
            </tbody>
        </table>
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
                let daysRemaining = Math.ceil((activeToDate - currentDate) / (1000 * 3600 * 24));
                document.getElementById("active-days").innerText = daysRemaining > 0 ? daysRemaining + " days remaining" : "Subscription expired";
            })
            .catch(error => console.error("Error fetching user:", error));

            fetch("http://127.0.0.1:8000/api/items", {
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + token,
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(items => {
                let tableBody = document.getElementById("items-table");
                tableBody.innerHTML = "";

                items.forEach(item => {
                    let row = document.createElement("tr");
                    row.innerHTML = `
                        <td><input type="text" class="edit-input" id="name-${item.id}" value="${item.name}"></td>
                        <td><input type="number" class="edit-input" id="price-${item.id}" value="${item.price}"></td>
                        <td><input type="text" class="edit-input" id="description-${item.id}" value="${item.description || ''}"></td>
                        <td>
                            <button class="save-btn" onclick="updateItem(${item.id})">Save</button>
                            <button class="delete-btn" onclick="deleteItem(${item.id})">Delete</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => console.error("Error fetching items:", error));
        });

        function updateItem(itemId) {
            let token = localStorage.getItem("auth_token");
            let name = document.getElementById(`name-${itemId}`).value;
            let price = document.getElementById(`price-${itemId}`).value;
            let description = document.getElementById(`description-${itemId}`).value;

            fetch(`http://127.0.0.1:8000/api/items/${itemId}`, {
                method: "PUT",
                headers: {
                    "Authorization": "Bearer " + token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ name, price, description })
            })
            .then(response => response.json())
            .then(data => {
                alert("Item updated successfully!");
            })
            .catch(error => console.error("Error updating item:", error));
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
