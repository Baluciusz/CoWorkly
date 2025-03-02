<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);
    $users = isset($_POST['users']) ? $_POST['users'] : []; // Kiválasztott felhasználók

    // Adatbázis kapcsolat
    $conn = new mysqli('localhost', 'root', '', 'coworkly');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Kártya hozzáadása
    $sql = "INSERT INTO cards (title, description, image, created_by) VALUES ('$title', '$description', '$image', '$username')";
    if ($conn->query($sql) === TRUE) {
        $card_id = $conn->insert_id; // Az új kártya ID-ja

        // Felhasználó ID lekérdezése
        $user_id_query = "SELECT id FROM users WHERE username='$username'";
        $user_id_result = $conn->query($user_id_query);
        if ($user_id_result->num_rows > 0) {
            $user_id_row = $user_id_result->fetch_assoc();
            $user_id = $user_id_row['id'];

            // Felhasználó automatikus hozzáadása a csoporthoz
            $group_member_sql = "INSERT INTO group_members (group_id, user_id) VALUES ('$card_id', '$user_id')";
            $conn->query($group_member_sql);
        }

        // Kiválasztott felhasználók hozzáadása a csoporthoz
        foreach ($users as $user_id) {
            $group_member_sql = "INSERT INTO group_members (group_id, user_id) VALUES ('$card_id', '$user_id')";
            $conn->query($group_member_sql);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            echo "<div style='text-align: center; margin-top: 20px;'>
                    <h2>Kártya sikeresen hozzáadva!</h2>
                    <p>Átirányítás a főoldalra...</p>
                  </div>";
            header("refresh:3;url=prototype.php");
        } else {
            echo "Hiba történt a kép feltöltésekor.";
        }
    } else {
        echo "Hiba: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>