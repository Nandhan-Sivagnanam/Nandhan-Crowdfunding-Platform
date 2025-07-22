
document.addEventListener("DOMContentLoaded", function () {
    const messageBox = document.getElementById("messageBox");
    
    if (messageBox) {
        // Ensure message box is fully visible initially
        messageBox.style.opacity = "1";
        messageBox.style.visibility = "visible";

        // Keep message box visible for **30 seconds** before fading out
        setTimeout(() => {
            messageBox.style.transition = "opacity 1s ease-in-out, visibility 1s ease-in-out";
            messageBox.style.opacity = "0";
            messageBox.style.visibility = "hidden";

            // Remove message box after transition ends (to avoid it being removed instantly)
            setTimeout(() => {
                if (messageBox.parentNode) {
                    messageBox.parentNode.removeChild(messageBox);
                }
            }, 1000); // Matches the CSS transition duration (1s)
        }, 5000); 
    }
});
