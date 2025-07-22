let readMessageId = null; // Store the message ID when opened

function viewMessage(id) {
    fetch(`fetch_message.php?view_id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                let message = data.data;
                document.getElementById("messageContent").innerHTML = `
                    <p><strong>Name:</strong> ${message.name}</p>
                    <p><strong>Email:</strong> ${message.email}</p>
                    <p><strong>Phone:</strong> ${message.phone}</p>
                    <p><strong>Subject:</strong> ${message.subject}</p>
                    <p><strong>Message:</strong> ${message.message.replace(/\n/g, "<br>")}</p>
                `;

                document.getElementById("messageSidebar").classList.add("show");

                // Store the message ID but DO NOT update status yet
                readMessageId = id;
            } else {
                alert("Error fetching message!");
            }
        })
        .catch(error => console.error("Error fetching message:", error));
}

function closeSidebar() {
    document.getElementById("messageSidebar").classList.remove("show");

    // Ensure the status updates only after the sidebar is closed
    if (readMessageId) {
        fetch(`fetch_message.php?mark_read=${readMessageId}`)
            .then(response => response.json())
            .then(result => {
                if (result.status === "success") {
                    let statusCell = document.querySelector(`tr[data-id='${readMessageId}'] .status span`);
                    if (statusCell) {
                        statusCell.classList.remove("unread");
                        statusCell.classList.add("read");
                        statusCell.textContent = "Read";
                    }
                } else {
                    console.error("Failed to update status.");
                }
            })
            .catch(error => console.error("Error updating status:", error))
            .finally(() => {
                // Reset readMessageId to avoid duplicate updates
                readMessageId = null;
            });
    }
}

function confirmDelete(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to undo this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`fetch_message.php?delete_id=${id}`, { method: 'POST' })
                .then(response => response.json())
                .then(result => {
                    if (result.status === "success") {
                        let row = document.querySelector(`tr[data-id='${id}']`);
                        if (row) {
                            row.remove();

                            // Check if all messages are deleted
                            let remainingRows = document.querySelectorAll(".message-table tbody tr").length;
                            if (remainingRows === 0) {
                                document.querySelector(".message-table tbody").innerHTML = `
                                    <tr>
                                        <td colspan="5" style="text-align:center; padding:15px; font-weight:bold; background-color: #f9f9f9; color: red;">
                                            No messages found.
                                        </td>
                                    </tr>
                                `;
                            }
                        }
                        Swal.fire("Deleted!", "The message has been deleted.", "success");
                    } else {
                        Swal.fire("Error!", "Failed to delete message!", "error");
                    }
                })
                .catch(error => {
                    console.error("Error deleting message:", error);
                    Swal.fire("Error!", "Something went wrong!", "error");
                });
        }
    });
}
