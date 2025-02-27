<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale System - Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}"> <!-- استخدم نفس ملف CSS -->
</head>
<body>
    <div class="header">
        <div class="user-info">
            <div class="user-name" id="user-name"></div>
        </div>
        <div class="active-days" id="active-days"></div>
        <div class="logout" id="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
        </div>
    </div>

    <div class="container">
        <h2>Inventory</h2>
        <table id="inventory-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Stock Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Items will be dynamically added here -->
            </tbody>
        </table>
        <button class="back-btn" onclick="window.location.href='/home'">Back to home</button>
    </div>

    <script src="{{ asset('js/orders.js') }}"></script>
    <script>
        // Fetch all items from the API
        fetch('/api/items', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),  // Add token if authentication is required
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(items => {
                console.log(items);
                const inventoryTable = document.querySelector("#inventory-table tbody");
                items.forEach(item => {
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.name}</td>
                        <td>
                            <input type="number" value="${item.stock}" id="stock-${item.id}" class="stock-input" min="0">
                        </td>
                        <td>
                            <button onclick="updateStock(${item.id})" class="update-btn">Update</button>
                        </td>
                    `;
                    inventoryTable.appendChild(row);
                });
            })
            .catch(error => console.error('Error fetching items:', error));

        // Function to update stock for a specific item
        function updateStock(itemId) {
            const stockInput = document.getElementById(`stock-${itemId}`);
            const newStock = stockInput.value;

            fetch(`/api/items/${itemId}`, {  // This will use the PUT method for the '/items/{item}' route
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('auth_token')  // Add token if authentication is required
                },
                body: JSON.stringify({ stock: newStock })  // Send updated stock in the request body
            })
            .then(response => response.json())
            .then(updatedItem => {
                alert('Stock updated successfully');
                console.log(updatedItem);
            })
            .catch(error => {
                alert('Error updating stock');
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
