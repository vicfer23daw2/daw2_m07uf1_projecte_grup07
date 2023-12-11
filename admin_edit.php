<?php
session_start();

// Verifica si el usuario está autenticado y es un administrador
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    // Si no está autenticado o no es un administrador, redirige a la página de error
    header("Location: error_acces.php");
    exit();
}

$defaultAdminUsername = $_SESSION["username"];
$userRole = $_SESSION["role"];

// Verifica si el formulario de edición ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoge los datos del formulario
    $newUsername = $_POST["new_username"];
    $newPassword = $_POST["new_password"];
    $newEmail = $_POST["new_email"];

    $ctsnya_hash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Actualiza los datos del usuario administrador por defecto
    $usersFile = './dades/users';
    $updatedUserData = "$newUsername:$ctsnya_hash:$userRole:$newEmail";
    
    // Lee el archivo de usuarios
    $usersData = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Busca y actualiza el usuario administrador por defecto
    foreach ($usersData as $key => $userData) {
        list($storedUsername, $storedPassword, $storedRole, $storedEmail) = explode(':', $userData);
        if ($storedUsername === $defaultAdminUsername && $storedRole === "admin") {
            // Actualiza los datos
            $usersData[$key] = $updatedUserData;
            $_SESSION["username"] = $newUsername;
            $_SESSION["email"] = $newEmail;
            break;
        }
    }

    // Escribe los datos actualizados en el archivo
    file_put_contents($usersFile, implode("\n", $usersData));

    echo "<p style='color: green;'>Dades modificades amb èxit.</p>";
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuari Admin</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <header>
        <h1>Editar Usuari Administrador</h1>
        <nav>
            <a href="interficie.php">Torna a la pàgina anterior</a>
        </nav>
        <div class="userlogeado">
            <?php
                echo "Nom usuari: ".$_SESSION['username']."<br>";
                echo "Tipus d'usuari: " . $_SESSION['role'] ;
                echo '<a href="logout.php">Tanca la sessió</a>';   
            ?>
        </div>
    </header>

    <section>
        <!-- Formulario de edición -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="new_username">Nou Nom d'Usuari:</label>
            <input type="text" id="new_username" name="new_username" required>

            <label for="new_password">Nova Contrasenya:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="new_email">Nou Correu Electrònic:</label>
            <input type="email" id="new_email" name="new_email" required>

            <button type="submit">Guardar Canvis</button>
        </form>
    </section>

</body>
</html>
