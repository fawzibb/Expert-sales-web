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

        // Fetching orders data
        fetch("api/orders", {
            method: "GET",
            headers: {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/json"
            }
        })
        .then(response => response.json())
        .then(orders => {
            let tableBody = document.querySelector("#orders-table tbody");
            tableBody.innerHTML = "";

            orders.forEach(order => {
                // Create an object to store items and their quantities
                let itemsMap = {};

                order.items.forEach(item => {
                    if (itemsMap[item.name]) {
                        itemsMap[item.name] += item.quantity; // Add to existing quantity
                    } else {
                        itemsMap[item.name] = item.quantity; // Initialize with quantity
                    }
                });

                // Convert the items map into a string
                let itemsNames = Object.keys(itemsMap).map(itemName => `${itemName} x${itemsMap[itemName]}`).join(", ");

                // Calculate the total price
                let totalPrice = order.items.reduce((total, item) => total + parseFloat(item.price) * item.quantity, 0).toFixed(2);
                if (parseFloat(totalPrice) === 0) {
                    return; // Skip this iteration and don't append the row
                }
                let row = document.createElement("tr");
                row.innerHTML = `
                    <td>${order.id}</td>
                    <td>${itemsNames}</td>
                    <td>$${totalPrice}</td>
                    <td>${new Date(order.created_at).toLocaleString()}</td>

                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error fetching orders:", error));
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
