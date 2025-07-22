document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const tableBody = document.getElementById("userTableBody");

    //  Get column count from the table header
    const columnCount = document.querySelector("#usersSection table thead tr").children.length;

    //  Create the "No results found" row (hidden by default)
    let noResultsRow = document.createElement("tr");
    noResultsRow.classList.add("no-results");
    noResultsRow.innerHTML = `<td colspan="${columnCount}" style="text-align: center; color: red; font-weight: bold;">No results found</td>`;
    noResultsRow.style.display = "none"; // Initially hidden
    tableBody.appendChild(noResultsRow); // Add to the table body

    searchInput.addEventListener("input", function () {
        const filter = searchInput.value.toLowerCase().trim();
        let visibleRows = 0;

        document.querySelectorAll("#userTableBody tr:not(.no-results)").forEach(row => {
            const name = row.querySelector(".user-name")?.textContent.toLowerCase() || "";
            const email = row.querySelector(".user-email")?.textContent.toLowerCase() || "";
            const role = row.querySelector(".user-role")?.textContent.toLowerCase() || "";

            if (name.includes(filter) || email.includes(filter) || role.includes(filter)) {
                row.style.display = "";
                visibleRows++;
            } else {
                row.style.display = "none";
            }
        });

        //  Show "No results found" row when no matches exist
        noResultsRow.style.display = visibleRows === 0 ? "" : "none";
    });

    let userTable = document.querySelector("#usersSection table tbody");

    //  Handle table clicks (Edit, Save, Cancel, Delete)
    userTable.addEventListener("click", function (event) {
        let target = event.target;
        let userId = target.getAttribute("data-id");
        let row = target.closest("tr");

        if (target.classList.contains("edit-btn")) {
            enableEditing(row);
        } else if (target.classList.contains("save-btn")) {
            saveUser(userId, row);
        } else if (target.classList.contains("cancel-btn")) {
            cancelEditing(row);
        } else if (target.classList.contains("delete-btn")) {
            deleteUser(userId, row);
        }
    });
});

// Enable Editing Mode
function enableEditing(row) {
    let nameCell = row.querySelector(".user-name");
    let emailCell = row.querySelector(".user-email");
    let roleCell = row.querySelector(".user-role");

    let originalName = nameCell.textContent.trim();
    let originalEmail = emailCell.textContent.trim();
    let originalRole = roleCell.textContent.trim();

    row.setAttribute("data-original-name", originalName);
    row.setAttribute("data-original-email", originalEmail);
    row.setAttribute("data-original-role", originalRole);

    nameCell.innerHTML = `<input type="text" value="${originalName}" class="edit-input">`;
    emailCell.innerHTML = `<input type="email" value="${originalEmail}" class="edit-input">`;
    roleCell.innerHTML = `<select class="edit-input">
                            <option value="admin" ${originalRole === "admin" ? "selected" : ""}>Admin</option>
                            <option value="user" ${originalRole === "user" ? "selected" : ""}>User</option>
                          </select>`;

    row.querySelector(".edit-btn").classList.add("hidden");
    row.querySelector(".save-btn").classList.remove("hidden");
    row.querySelector(".cancel-btn").classList.remove("hidden");
    row.querySelector(".delete-btn").classList.add("hidden");
}

// Cancel Editing Mode
function cancelEditing(row) {
    let originalName = row.getAttribute("data-original-name");
    let originalEmail = row.getAttribute("data-original-email");
    let originalRole = row.getAttribute("data-original-role");

    row.querySelector(".user-name").textContent = originalName;
    row.querySelector(".user-email").textContent = originalEmail;
    row.querySelector(".user-role").textContent = originalRole;

    row.querySelector(".edit-btn").classList.remove("hidden");
    row.querySelector(".save-btn").classList.add("hidden");
    row.querySelector(".cancel-btn").classList.add("hidden");
    row.querySelector(".delete-btn").classList.remove("hidden");
}

//  Save Edited User to Database
function saveUser(userId, row) {
    let nameInput = row.querySelector(".user-name input").value.trim();
    let emailInput = row.querySelector(".user-email input").value.trim();
    let roleInput = row.querySelector(".user-role select").value.trim();

    if (!nameInput || !emailInput || !roleInput) {
        alert("All fields are required!");
        return;
    }

    fetch("admin_users_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "edit", id: userId, name: nameInput, email: emailInput, role: roleInput })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("User updated successfully!");

            row.querySelector(".user-name").textContent = nameInput;
            row.querySelector(".user-email").textContent = emailInput;
            row.querySelector(".user-role").textContent = roleInput;

            row.querySelector(".edit-btn").classList.remove("hidden");
            row.querySelector(".save-btn").classList.add("hidden");
            row.querySelector(".cancel-btn").classList.add("hidden");
            row.querySelector(".delete-btn").classList.remove("hidden");
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => console.error("Error updating user:", error));
}

//  Delete User from Database
function deleteUser(userId, row) {
    if (!confirm("Are you sure you want to delete this user?")) return;

    fetch("admin_users_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "delete", id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("User deleted successfully!");
            row.remove();

            //  Check if there are any remaining visible rows
            updateNoResultsMessage();
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => console.error("Error deleting user:", error));
}

//  Function to check and update "No results found" message dynamically
function updateNoResultsMessage() {
    let visibleRows = document.querySelectorAll("#userTableBody tr:not(.no-results)").length;
    let noResultsRow = document.querySelector(".no-results");

    noResultsRow.style.display = visibleRows === 0 ? "table-row" : "none";
}
