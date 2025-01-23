<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <h1>Signup</h1>
        <form method="post" action="">
            <div>
                <label for="firstname-input">Username:</label>
                <input type="text" name="firstname" id="firstname-input" placeholder="Username" required>
            </div>
            <div>
                <label for="email-input">Email:</label>
                <input type="email" name="email" id="email-input" placeholder="Email" required>
            </div>
            <div>
                <label for="password-input">Password:</label>
                <input type="password" name="password" id="password-input" placeholder="Password" required>
            </div>
            <div>
                <label for="repeat-password-input">Repeat Password:</label>
                <input type="password" name="repeat-password" id="repeat-password-input" placeholder="Repeat Password" required>
            </div>
            <button type="submit">Signup</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $firstname = htmlspecialchars($_POST["firstname"]);
            $email = htmlspecialchars($_POST["email"]);
            $password = htmlspecialchars($_POST["password"]);
            $repeatPassword = htmlspecialchars($_POST["repeat-password"]);

            if ($password !== $repeatPassword) {
                echo "<p style='color: red;'>A jelszavak nem egyeznek!</p>";
            } else {
                $output = "Username: $firstname\nEmail: $email\nPassword: $password";
                echo "<textarea rows='5' cols='40' readonly>$output</textarea>";
            }
        }
        ?>
    </div>
</body>
</html>
