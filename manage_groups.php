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

// Csoport törlése
if (isset($_POST['delete_card'])) {
    $card_id = $_POST['card_id'];

    // Group members törlése
    $sql_delete_group_members = "DELETE FROM group_members WHERE group_id = ?";
    $stmt_delete_group_members = $conn->prepare($sql_delete_group_members);
    $stmt_delete_group_members->bind_param("i", $card_id);
    $stmt_delete_group_members->execute();
    $stmt_delete_group_members->close();

    // Card törlése
    $sql_delete_card = "DELETE FROM cards WHERE id = ?";
    $stmt_delete_card = $conn->prepare($sql_delete_card);
    $stmt_delete_card->bind_param("i", $card_id);
    $stmt_delete_card->execute();
    $stmt_delete_card->close();

    header("Location: manage_groups.php");
    exit();
}

// Új csoport létrehozása
if (isset($_POST['create_card'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $created_by = $_POST['created_by'];
    $sql = "INSERT INTO cards (title, description, created_by) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $title, $description, $created_by);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_groups.php");
    exit();
}

// Csoport (card) címének és leírásának módosítása
if (isset($_POST['update_card'])) {
    $card_id = $_POST['card_id'];
    $new_title = $_POST['new_title'];
    $new_description = $_POST['new_description'];
    $sql = "UPDATE cards SET title = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $new_title, $new_description, $card_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_groups.php");
    exit();
}

// Felhasználó hozzáadása csoporthoz
if (isset($_POST['add_user_to_group'])) {
    $group_id = $_POST['group_id'];
    $user_id = $_POST['user_id'];
    $sql = "INSERT INTO group_members (group_id, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $group_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_groups.php");
    exit();
}

// Felhasználó törlése csoportból
if (isset($_POST['delete_user_from_group'])) {
    $group_member_id = $_POST['group_member_id'];
    $sql = "DELETE FROM group_members WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_member_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_groups.php");
    exit();
}

// Csoportok (cards) lekérdezése
$sql_cards = "SELECT * FROM cards";
$result_cards = $conn->query($sql_cards);

// Csoportok (group_members) lekérdezése
$sql_groups = "SELECT gm.id as group_member_id, gm.group_id, gm.user_id, u.username FROM group_members gm JOIN users u ON gm.user_id = u.id";
$result_groups = $conn->query($sql_groups);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Csoportok Kezelése</title>
    <link rel="stylesheet" href="css/manage_groups.css">
</head>
<body>
    <div class="wrapper">
        <h1>Csoportok</h1>
        <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cím</th>
                    <th>Leírás</th>
                    <th>Kép</th>
                    <th>Létrehozta</th>
                    <th>Létrehozás dátuma</th>
                    <th>Művelet</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_cards->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['image']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_by']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <form method="POST" action="manage_groups.php">
                                <input type="hidden" name="card_id" value="<?php echo $row['id']; ?>">
                                <input type="text" name="new_title" placeholder="Új cím">
                                <input type="text" name="new_description" placeholder="Új leírás">
                                <button type="submit" name="update_card">Módosítás</button>
                            </form>
                            <form method="POST" action="manage_groups.php">
                                <input type="hidden" name="card_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_card">Törlés</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Új csoport létrehozása</h2>
        <form method="POST" action="manage_groups.php">
            <input type="text" name="title" placeholder="Cím" required>
            <input type="text" name="description" placeholder="Leírás" required>
            <input type="text" name="created_by" placeholder="Létrehozta" required>
            <button type="submit" name="create_card">Létrehozás</button>
        </form>

        <h1>Csoportok (group_members)</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Csoport ID</th>
                    <th>Felhasználó ID</th>
                    <th>Felhasználónév</th>
                    <th>Művelet</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_groups->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['group_member_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['group_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>
                            <form method="POST" action="manage_groups.php">
                                <input type="hidden" name="group_member_id" value="<?php echo $row['group_member_id']; ?>">
                                <button type="submit" name="delete_user_from_group">Törlés</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Felhasználó hozzáadása csoporthoz</h2>
        <form method="POST" action="manage_groups.php">
            <input type="text" name="group_id" placeholder="Csoport ID" required>
            <input type="text" name="user_id" placeholder="Felhasználó ID" required>
            <button type="submit" name="add_user_to_group">Hozzáadás</button>
        </form>
        <button class="back-button" onclick="window.location.href='admin_dashboard.php'">Vissza a Dashboardra</button>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>