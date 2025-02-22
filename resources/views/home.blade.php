<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
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
        <button class="cash-btn" id="cash-btn">Cash</button>
    </div>

    <script src="{{ asset('js/home.js') }}"></script>

</body>
</html>
