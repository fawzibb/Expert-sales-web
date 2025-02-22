<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/items.css') }}">
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

    <script src="{{ asset('js/items.js') }}"></script>

</body>
</html>
