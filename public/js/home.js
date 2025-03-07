document.addEventListener("DOMContentLoaded", function () {
    let token = localStorage.getItem("auth_token");

    if (!token) {
        window.location.href = "/login";
        return;
    }

    fetch("api/user", {
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

        fetch("api/items", {
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
                    <button onclick="addToCart('${item.name}', ${item.price}, ${item.id})">Add to Cart</button>
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

function addToCart(name, price, id) {
    let itemFound = false;

    for (let item of cartItems) {
        if (item.id === id) {
            item.quantity += 1;
            totalPrice += price;
            itemFound = true;
            break;
        }
    }

    if (!itemFound) {
        cartItems.push({ name, price, id, quantity: 1 });
        totalPrice += price;
    }

    updateCart();
}

function updateCart() {
    let cartList = document.getElementById("cart-items");
    let totalElement = document.getElementById("total-price");
    cartList.innerHTML = "";

    cartItems.forEach(item => {
        let li = document.createElement("li");
        li.innerText = `${item.name} - $${item.price.toFixed(2)} x ${item.quantity}`;
        cartList.appendChild(li);
    });

    totalElement.innerText = `Total: $${totalPrice.toFixed(2)}`;
    document.getElementById('cart').scrollTop = document.getElementById('cart').scrollHeight;
}

document.getElementById("clear-cart-btn").addEventListener("click", function () {
    cartItems = [];
    totalPrice = 0;
    updateCart();
});

document.getElementById("cash-btn").addEventListener("click", function () {
    if (cartItems.length === 0) {
        alert("Your cart is empty.");
        return;
    }

    let token = localStorage.getItem("auth_token");

    let itemQuantities = cartItems.map(item => ({
        id: item.id,
        quantity: item.quantity
    }));

    let orderData = {
        name: "My Order",
        description: "Order with multiple items",
        items: itemQuantities,
        created_at: Date.now()
    };

    fetch("api/orders", {
        method: "POST",
        headers: {
            "Authorization": "Bearer " + token,
            "Content-Type": "application/json"
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(result => {
        if (result.status === 201) {
            alert("Order placed successfully!");
            cartItems = [];
            totalPrice = 0;
            updateCart();
        } else {
            alert(result.body.message || "Error placing order.");
        }
    })
    .catch(error => {
        console.error("Error placing order:", error);
        alert("An error occurred while placing the order.");
    });
});

function toggleEditMode() {
    let items = document.querySelectorAll(".item");
    document.getElementById('sidebar').classList.toggle('show');
    items.forEach(item => {
        item.classList.toggle("edit-mode");
    });
}

function deleteItem(itemId) {
    let token = localStorage.getItem("auth_token");

    fetch(`api/items/${itemId}`, {
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

document.getElementById('menu-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
});
