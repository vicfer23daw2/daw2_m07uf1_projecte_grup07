<?php
session_start();

// Verifica si el formulario de inicio de sesión ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Comprueba las credenciales leyendo el archivo de usuarios
    $usersFile = './dades/users';

    // Lee el archivo de usuarios y verifica las credenciales
    $usersData = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($usersData as $userData) {
        list($storedUsername, $storedPassword, $storedRole, $storedEmail, $storedId, $storedNom, $storedCognom, $storedTelefon, $storedCp, $storedVisa, $nomGestor) = explode(':', $userData);
        if ($username === $storedUsername && (password_verify($password, $storedPassword))) {
            // Usuario autenticado
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $storedRole;

            // Redirige a la interfaz correspondiente
            header("Location: interficie.php");
            exit();
        }
    }

    // Si las credenciales son incorrectas
    $error_message = "Credenciales incorrectas. Intenta de nuevo.";
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sessió</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <header>
        <h1>Iniciar sessió</h1>
    </header>

    <section>
        <?php
        // Muestra un mensaje de error si las credenciales son incorrectas
        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        ?>

        <!-- Formulario de inicio de sesión -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Nom d'usuari:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Contrasenya:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Iniciar sessió</button>
        </form>
    </section>

    <footer>
        <p><a href="index.php">Torna a la pàgina de presentació</a></p>
    </footer>

</body>
</html>
