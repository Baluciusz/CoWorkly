<?php
require 'src/JWT.php';
require 'src/Key.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

session_start();
if (!isset($_SESSION['jwt'])) {
    header("Location: admin_login.php");
    exit();
}

$jwt = $_SESSION['jwt'];
$secret_key = "your_secret_key";

try {
    $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
    $admin_id = $decoded->data->id;
    $admin_username = $decoded->data->username;
} catch (Exception $e) {
    header("Location: admin_login.php");
    exit();
}

$files = [
    'accountmodify.php',
    'add_card.php',
    'admin_dashboard.php',
    'admin_login.php',
    'admin_reg.php',
    'connect.php',
    'errorreport.php',
    'load_messages.php',
    'login.php',
    'logout.php',
    'manage_groups.php',
    'prototype.php',
    'reset-password.php',
    'save_message.php',
    'signup.php',
    'update_user.php',
    'admin_menu.php'
];

function getStatusMessage($status_code) {
    $messages = [
        '200' => 'OK',
        '201' => 'Létrehozva',
        '202' => 'Elfogadva',
        '204' => 'Nincs tartalom',
        '301' => 'Áthelyezve véglegesen',
        '302' => 'Találva',
        '304' => 'Nem módosított',
        '400' => 'Hibás kérés',
        '401' => 'Nem engedélyezett',
        '403' => 'Tiltott',
        '404' => 'Nem található',
        '500' => 'Belső szerver hiba',
        '502' => 'Hibás átjáró',
        '503' => 'Szolgáltatás nem elérhető',
        '504' => 'Átjáró időtúllépés',
        // Itt lehet több hibakódot hozzáadni.
    ];
    return isset($messages[$status_code]) ? $messages[$status_code] : 'Ismeretlen státuszkód';
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Check</title>
    <link rel="stylesheet" href="css/status_check.css">
</head>
<body>
    <div class="wrapper">
        <h1>Status Check</h1>
        <?php
        foreach ($files as $file) {
            $url = "http://localhost/coworkly/" . $file;
            $response = get_headers($url, 1);
            $status_code = substr($response[0], 9, 3); 
            if ($status_code != '200') {
                $status_message = getStatusMessage($status_code);
                echo "<p>$file  Visszaadott státuszkód: $status_code - $status_message</p>";
            } else {
                echo "<p>$file 200-as státuszkódot adott vissza.</p>";
            }
        }
        ?>
        <button class="back-button" onclick="window.location.href='manage_groups.php'">Csoportok</button>
        <button class="back-button" onclick="window.location.href='admin_dashboard.php'">Dashboard</button>
        <button class="back-button" onclick="window.location.href='admin_menu.php'">Vissza a Menübe</button> 
        <button class="logout-button" onclick="window.location.href='admin_login.php'">Kijelentkezés</button>
    </div>
</body>
</html>