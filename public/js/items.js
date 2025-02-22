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
