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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];
    $target_dir = __DIR__ . "/uploads/";
    $target_file = $target_dir . basename($image);
    $users = isset($_POST['users']) ? $_POST['users'] : []; // Kiválasztott felhasználók

    // Ellenőrizd, hogy létezik-e a célkönyvtár, ha nem, hozd létre
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Adatbázis kapcsolat
    $conn = new mysqli('localhost', 'root', '', 'coworkly');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Ha nincs kép feltöltve, használjuk a default.jpg képet a groupicons mappából
    if (empty($image)) {
        $image = 'default.jpg';
        $target_file = __DIR__ . "/groupicons/" . $image;
    }

    // Kártya hozzáadása
    $sql = "INSERT INTO cards (title, description, image, created_by) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Hiba történt az SQL lekérdezés előkészítésekor: " . $conn->error);
    }
    $stmt->bind_param("ssss", $title, $description, $image, $username);
    if ($stmt->execute()) {
        $card_id = $stmt->insert_id; // Az új kártya ID-ja

        // Felhasználó automatikus hozzáadása a csoporthoz
        $group_member_sql = "INSERT INTO group_members (group_id, user_id) VALUES (?, ?)";
        $group_stmt = $conn->prepare($group_member_sql);
        if ($group_stmt === false) {
            die("Hiba történt az SQL lekérdezés előkészítésekor: " . $conn->error);
        }
        $group_stmt->bind_param("ii", $card_id, $user_id);
        $group_stmt->execute();

        // Kiválasztott felhasználók hozzáadása a csoporthoz
        foreach ($users as $user_id) {
            $group_stmt->bind_param("ii", $card_id, $user_id);
            $group_stmt->execute();
        }
        $group_stmt->close();

        if (!empty($_FILES['image']['tmp_name'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                echo "<div style='text-align: center; margin-top: 20px;'>
                        <h2>Kártya sikeresen hozzáadva!</h2>
                        <p>Átirányítás a főoldalra...</p>
                      </div>";
                header("refresh:3;url=prototype.php");
            } else {
                echo "Hiba történt a kép feltöltésekor.";
            }
        } else {
            echo "<div style='text-align: center; margin-top: 20px;'>
                    <h2>Kártya sikeresen hozzáadva!</h2>
                    <p>Átirányítás a főoldalra...</p>
                  </div>";
            header("refresh:3;url=prototype.php");
        }
    } else {
        echo "Hiba: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>