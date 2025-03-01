<?php
session_start();

// Adatbázis kapcsolat létrehozása
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "coworkly";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Kapcsolat ellenőrzése
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT id, username, password FROM admins WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Admin bejelentkezés sikeres
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_username'] = $username;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error_message = "Hibás jelszó.";
            }
        } else {
            $error_message = "Nincs ilyen admin.";
        }

        $stmt->close();
    } else {
        $error_message = "Kérjük, töltse ki az összes mezőt.";
    }
}

$conn->close();
?>
