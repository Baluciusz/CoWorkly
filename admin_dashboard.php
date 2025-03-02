<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

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

// Hibajelentés lezárása
if (isset($_POST['close_report'])) {
    $report_id = $_POST['report_id'];
    $sql = "UPDATE error_reports SET status = 'closed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $stmt->close();
}

// Kijelentkezés
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

// Hibajelentések lekérdezése
$sql_reports = "SELECT * FROM error_reports";
$result_reports = $conn->query($sql_reports);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <style>
       
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Hibajelentések</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hiba Típusa</th>
                    <th>Megjegyzés</th>
                    <th>Dátum</th>
                    <th>Státusz</th>
                    <th>Művelet</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_reports->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['error_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['comment']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <?php if ($row['status'] == 'open'): ?>
                                <form method="POST" action="admin_dashboard.php">
                                    <input type="hidden" name="report_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="close_report">Lezárás</button>
                                </form>
                            <?php else: ?>
                                Lezárva
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button class="back-button" onclick="window.location.href='manage_groups.php'">Csoportok</button>                             
        <form method="POST" action="admin_login.php">
            <button type="submit" name="logout" class="logout-button">Kijelentkezés</button>
        </form>
    
    </div>
</body>
</html>

<?php
$conn->close();
?>