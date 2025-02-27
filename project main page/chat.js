function openForm() {
  document.getElementById("myForm").style.display = "block";
}

function closeForm() {
  document.getElementById("myForm").style.display = "none";
}

function sendMessage() {
  var messageInput = document.querySelector(".footer input[type='text']");
  var messageText = messageInput.value;

  if (messageText.trim() !== "") {
      var messageContainer = document.createElement("div");
      messageContainer.classList.add("chat-message");

      var userInfo = document.createElement("div");
      userInfo.classList.add("user-info");

      var profilePic = document.createElement("img");
      profilePic.src = "user-profile.jpg"; // Profilkép URL
      profilePic.classList.add("profile-pic");

      var userName = document.createElement("div");
      userName.classList.add("user-name");
      userName.textContent = "User Name"; // Felhasználó neve

      var messageContent = document.createElement("div");
      messageContent.classList.add("message-text");
      messageContent.textContent = messageText;

      userInfo.appendChild(profilePic);
      userInfo.appendChild(userName);
      messageContainer.appendChild(userInfo);
      messageContainer.appendChild(messageContent);

      document.querySelector(".main-chat").appendChild(messageContainer);
      messageInput.value = ""; // Üzenet mező törlése

      // Az üzenet magasságának automatikus növelése
      messageContainer.style.height = 'auto'; // Magasság beállítása, ha a tartalom változik

      // Görgetés az új üzenetre
      var chatArea = document.querySelector(".main-chat");
      chatArea.scrollTop = chatArea.scrollHeight;
  }
}


// Üzenet küldése
document.querySelector(".footer button").addEventListener("click", function(event) {
  event.preventDefault();
  sendMessage();
});
