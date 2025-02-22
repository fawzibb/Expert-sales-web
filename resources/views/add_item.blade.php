<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            text-align: center;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Add New Item</h2>
        <form id="add-item-form">
            <input type="text" id="name" placeholder="Item Name" required>
            <input type="number" id="price" placeholder="Price" required>
            <textarea id="description" placeholder="Description"></textarea>
            <button type="submit">Add Item</button>
        </form>
    </div>

    <script>
        document.getElementById("add-item-form").addEventListener("submit", function(event) {
            event.preventDefault();

            let token = localStorage.getItem("auth_token");
            if (!token) {
                alert("You need to log in first.");
                window.location.href = "/login";
                return;
            }

            let name = document.getElementById("name").value;
            let price = document.getElementById("price").value;
            let description = document.getElementById("description").value;

            fetch("http://127.0.0.1:8000/api/items", {
                method: "POST",
                headers: {
                    "Authorization": "Bearer " + token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    name: name,
                    price: price,
                    description: description
                })
            })
            .then(response => response.json())
            .then(data => {
                alert("Item added successfully!");
                window.location.href = "/home"; // إعادة التوجيه إلى صفحة العناصر
            })
            .catch(error => {
                console.error("Error adding item:", error);
                alert("Failed to add item.");
            });
        });
    </script>

</body>
</html>
