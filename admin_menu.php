<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Menü</title>
    <link rel="stylesheet" href="css/admin_menu.css">
</head>
<body>
    <div class="wrapper">
        <h1>Admin Menü</h1>
        <button onclick="window.location.href='admin_dashboard.php'">Hibajelentések</button>
        <button onclick="window.location.href='manage_groups.php'">Csoportok</button>
        <button onclick="window.location.href='status_check.php'">Oldalak státuszai</button>
        <form method="POST" href=admin_login.php action="admin_logout.php">
            <button type="submit" name="logout" class="logout-button">Kijelentkezés</button>
        </form>
    </div>
   
</body>
</html>