<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>expert sales</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    <div class="header">
        <div class="user-info">
            <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
            <div class="user-name" id="user-name"></div>

        </div>
        <div class="active-days" id="active-days"></div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div style="height: 60px"></div>

        <button class="add-item" onclick="window.location.href='/add_item'">+ Add Item</button>
        <button class="inventory" onclick="window.location.href='/inventory'">Inventory</button>
        <button class="orders" onclick="window.location.href='/orders'">Orders</button>
        <button class="edit-items" onclick="toggleEditMode()">delete item</button>
        <button class="items" onclick="window.location.href='/items'">Items</button>
        <button class="logout" id="logout-btn">Logout</button>
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
        <button class="cash-btn" id="cash-btn">Cash</button>
    </div>

    <script src="{{ asset('js/home.js') }}"></script>
    <script>
        // Toggle sidebar menu on button click
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
</body>
</html>
