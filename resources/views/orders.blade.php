<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale System - Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
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
        <h2>Orders</h2>
        <table id="orders-table">
            <thead>
                <tr>
                    <th>Order IDs</th>
                    <th>Items</th>
                    <th>Total Price</th>
                    <th>Date</th>
                    
                </tr>
            </thead>
            <tbody>
                <!-- Orders will be dynamically added here -->
            </tbody>
        </table>
        <button class="back-btn" onclick="window.location.href='/home'">Back to home</button>
    </div>

    <script src="{{ asset('js/orders.js') }}"></script>
</body>
</html>
