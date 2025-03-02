<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];

// Adatbázis kapcsolat
$conn = new mysqli('localhost', 'root', '', 'coworkly');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kártyák lekérése az adatbázisból
$sql = "SELECT * FROM cards";
$result = $conn->query($sql);


// Felhasználó ID lekérdezése
$user_id_query = "SELECT id FROM users WHERE username='$username'";
$user_id_result = $conn->query($user_id_query);
if ($user_id_result->num_rows > 0) {
    $user_id_row = $user_id_result->fetch_assoc();
    $user_id = $user_id_row['id'];
}

// Kártyák lekérdezése, amelyekhez a felhasználó hozzá van adva
$sql = "SELECT cards.* FROM cards 
        JOIN group_members ON cards.id = group_members.group_id 
        WHERE group_members.user_id = '$user_id'";
$result = $conn->query($sql);


$users_sql = "SELECT id, username FROM users WHERE username != '$username'";
$users_result = $conn->query($users_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoWorkly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/prototype.css">
</head>
<body>
    <nav class="navbar">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="accountmodify.php"><svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-person-square" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                    <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1v-1c0-1-1-4-6-4s-6 3-6 4v1a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                  </svg> <?php echo htmlspecialchars($username); ?></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="errorreport.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bug-fill" viewBox="0 0 16 16">
                    <path d="M4.978.855a.5.5 0 1 0-.956.29l.41 1.352A5 5 0 0 0 3 6h10a5 5 0 0 0-1.432-3.503l.41-1.352a.5.5 0 1 0-.956-.29l-.291.956A5 5 0 0 0 8 1a5 5 0 0 0-2.731.811l-.29-.956z"/>
                    <path d="M13 6v1H8.5v8.975A5 5 0 0 0 13 11h.5a.5.5 0 0 1 .5.5v.5a.5.5 0 1 0 1 0v-.5a1.5 1.5 0 0 0-1.5-1.5H13V9h1.5a.5.5 0 0 0 0-1H13V7h.5A1.5 1.5 0 0 0 15 5.5V5a.5.5 0 0 0-1 0v.5a.5.5 0 0 1-.5.5zm-5.5 9.975V7H3V6h-.5a.5.5 0 0 1-.5-.5V5a.5.5 0 0 0-1 0v.5A1.5 1.5 0 0 0 2.5 7H3v1H1.5a.5.5 0 0 0 0 1H3v1h-.5A1.5 1.5 0 0 0 1 11.5v.5a.5.5 0 1 0 1 0v-.5a.5.5 0 0 1 .5-.5H3a5 5 0 0 0 4.5 4.975"/>
                  </svg> Hiba jelentés</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open" viewBox="0 0 16 16">
                    <path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/>
                    <path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117M11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5M4 1.934V15h6V1.077z"/>
                  </svg> Kijelentkezés</a>
            </li>
        </ul>
    </nav>

    <div class="content">
        <h1>Groups</h1>
    <div class="card-deck">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card" onclick="openForm(<?php echo $row['id']; ?>)">
    <img class="card-img-top" src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Card image cap">
    <div class="card-body">
        <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
        <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($row['description']); ?></small></p>
    </div>
</div>
<?php endwhile; ?>

        <!-- Új kártya hozzáadása gomb -->
        <div class="add">
            <button type="button" class="btn" data-toggle="button" aria-pressed="false" autocomplete="off" data-bs-toggle="modal" data-bs-target="#addCardModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="white" class="bi bi-plus-square" viewBox="0 0 16 16">
                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="content"> 
    <div class="chat-popup" id="myForm">
        <form action="save_message.php" method="POST" class="form-container">
            <h1>Chat</h1>
            <div class="main-chat" id="chat-messages">
                <!-- Üzenetek betöltése AJAX-szal -->
            </div>
    
            <div class="footer">
                <input type="hidden" name="group_id" id="group_id">
                <input type="text" name="message" placeholder="Üzenet küldése" required />
                <button type="submit">Elküldés</button>
            </div>
              
            <button type="button" class="btn cancel" onclick="closeForm()">X</button>
        </form>
    </div>
</div>
                  
              
        </div>
        <div class="content"> 
        <div class="chat-popup" id="myForm">
            <form action="/action_page.php" class="form-container">
                <h1>Chat</h1>
                <div class="main-chat">
                    <div class="chat-message">
                        <div class="user-info">
                            <img class="profile-pic" src="groupicons/legenybucsu.jpg" alt="Profilkép">
                            <div class="user-name">Ferenc</div>
                        </div>
                        <div class="message-text">Ez egy üzenet szövege.</div>
                    </div>
                    <!-- További üzenetek -->
                </div>
        
                <div class="footer">
                    <input type="text" placeholder="Üzenet küldése" />
                    <button>Elküldés</button>
                </div>
                  
                <button type="button" class="btn cancel" onclick="closeForm()">X</button>
            </form>
        </div>
    </div>
    </div>
</div>




<!-- Modal a kártya hozzáadásához -->
<div class="modal fade" id="addCardModal" tabindex="-1" aria-labelledby="addCardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCardModalLabel">Új kártya hozzáadása</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="add_card.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Cím:</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Leírás:</label>
                        <input type="text" class="form-control" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Kép:</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="users">Felhasználók hozzáadása:</label>
                        <select multiple class="form-control" name="users[]">
                            <?php while ($user_row = $users_result->fetch_assoc()): ?>
                                <option value="<?php echo $user_row['id']; ?>"><?php echo htmlspecialchars($user_row['username']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" id="mentes" class="btn btn-primary">Mentés</button>
                </form>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="username" value="<?php echo htmlspecialchars($username); ?>">
<?php $conn->close(); ?>

<script src="js/chat.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>