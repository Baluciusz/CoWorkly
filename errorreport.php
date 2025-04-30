<?php
require 'src/JWT.php';
require 'src/Key.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

session_start();
if (!isset($_SESSION['jwt'])) {
    header("Location: login.php");
    exit();
}

$jwt = $_SESSION['jwt'];
$secret_key = "your_secret_key";

try {
    $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
    $user_id = $decoded->data->id;
    $username = $decoded->data->username;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(array(
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ));
    exit();
}

// Hibák megjelenítése
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Adatbázis kapcsolat létrehozása
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coworkly";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kapcsolat ellenőrzése
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error_type = $_POST['error_type'];
    $comment = $_POST['comment'];
    $sql = "INSERT INTO error_reports (error_type, comment, user_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Hiba történt az SQL lekérdezés előkészítésekor: " . $conn->error);
    }
    $stmt->bind_param("ssi", $error_type, $comment, $user_id);
    if ($stmt->execute()) {
        $success_message = "Hiba sikeresen bejelentve!";
    } else {
        $error_message = "Hiba történt: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hiba bejelentés</title>
    <link rel="stylesheet" href="css/erroreport.css">
    <style>
        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
        }
        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="errorreport.php" method="post">
            <h1>Hiba bejelentés</h1>
            <label for="error_type">Hiba típusa:</label>
            <select id="error_type" name="error_type">
                <option value="login_problem">Bejelentkezési probléma</option>
                <option value="chat_issue">Csevegési probléma</option>
                <option value="file_upload_error">Fájl feltöltési hiba</option>
                <option value="notification_issue">Értesítési probléma</option>
                <option value="other">Egyéb</option>
            </select>   
            <label for="comment">Megjegyzés:</label>
            <textarea id="comment" name="comment" maxlength="300" placeholder="Írd ide a megjegyzésed..." required></textarea>         
            <button type="submit">Beküldés</button>
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>