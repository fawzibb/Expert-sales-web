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



    fetch("api/items", {
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
        window.location.href = "/home";
    })
    .catch(error => {
        
        console.error("Error adding item:", error);
        alert("Failed to add item.");
    });
});
