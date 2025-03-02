var currentGroupId = null; // Globális változó a csoport ID tárolására

function openForm(groupId) {
    currentGroupId = groupId; // Beállítjuk a jelenlegi csoport ID-t
    document.getElementById("group_id").value = groupId;
    document.getElementById("myForm").style.display = "block";
    loadMessages(groupId);
}

function closeForm() {
    document.getElementById("myForm").style.display = "none";
}

function sendMessage() {
    var messageInput = document.querySelector(".footer input[type='text']");
    var messageText = messageInput.value;
    var groupId = document.getElementById("group_id").value;
    var userName = document.getElementById("username").value; // Felhasználó neve

    if (messageText.trim() !== "") {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "save_message.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                loadMessages(groupId); // Frissítsük az üzeneteket elküldés után
                messageInput.value = ""; // Üzenet mező törlése
            }
        };
        xhr.send("group_id=" + groupId + "&message=" + encodeURIComponent(messageText));
    }
}

function loadMessages(groupId) {
    if (!groupId) return; // Ha nincs aktív csoport, ne próbáljuk meg betölteni az üzeneteket

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "load_messages.php?group_id=" + groupId, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("chat-messages").innerHTML = xhr.responseText;
            var chatArea = document.querySelector(".main-chat");
            chatArea.scrollTop = chatArea.scrollHeight; // Automatikus görgetés az új üzenetre
        }
    };
    xhr.send();
}

// Üzenet küldése gombbal
document.querySelector(".footer button").addEventListener("click", function(event) {
    event.preventDefault();
    sendMessage();
});

// Automatikus üzenetfrissítés
setInterval(function() {
    if (currentGroupId) {
        loadMessages(currentGroupId);
    }
}, 3000);
