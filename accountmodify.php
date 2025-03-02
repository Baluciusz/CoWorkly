<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['gender']) && isset($_POST['birthdate'])) {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $gender = $_POST['gender'];
        $birthdate = $_POST['birthdate'];

        // Profilkép feltöltése
        $profile_pic = "";
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $profile_pic = $target_file;
            } else {
                $error_message = "Hiba történt a profilkép feltöltésekor.";
            }
        }

        // Adatok frissítése az adatbázisban
        if (!empty($profile_pic)) {
            $sql = "UPDATE users SET email=?, username=?, password=?, gender=?, birthdate=?, profile_pic=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $email, $username, $password, $gender, $birthdate, $profile_pic, $user_id);
        } else {
            $sql = "UPDATE users SET email=?, username=?, password=?, gender=?, birthdate=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $email, $username, $password, $gender, $birthdate, $user_id);
        }

        if ($stmt->execute()) {
            $success_message = "Adatok sikeresen módosítva!";
            $_SESSION['username'] = $username; // Munkamenet frissítése
        } else {
            $error_message = "Hiba történt: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = "Minden mezőt ki kell tölteni.";
    }
}

$sql = "SELECT email, username, gender, birthdate, profile_pic FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $username, $gender, $birthdate, $profile_pic);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhasználói adatok módosítása</title>
    <link rel="stylesheet" href="css/accountmodify.css">
</head>
<body>
    <h1>Felhasználói adatok módosítása</h1>
    <form action="accountmodify.php" method="post" enctype="multipart/form-data">
        <label for="email">Email cím:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        <br><br>
        <label for="username">Felhasználónév:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        <br><br>
        <label for="password">Jelszó:</label>
        <input type="password" id="password" name="password" required>
        <br><br>
        <label for="gender">Nem:</label>
        <select id="gender" name="gender">
            <option value="male" <?php if ($gender == 'male') echo 'selected'; ?>>Férfi</option>
            <option value="female" <?php if ($gender == 'female') echo 'selected'; ?>>Nő</option>
            <option value="other" <?php if ($gender == 'other') echo 'selected'; ?>>Egyéb</option>
        </select>
        <br><br>
        <label for="birthdate">Születési idő:</label>
        <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($birthdate); ?>" required>
        <br><br>
        <label for="profile_pic">Profilkép:</label>
        <input type="file" id="profile_pic" name="profile_pic">
        <br><br>
        <button type="submit">Módosítás</button>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>