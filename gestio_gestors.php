<?php
session_start();

// Verifica si el usuario está autenticado y es un administrador
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    // Si no está autenticado o no es un administrador, redirige a la página de inicio
    header("Location: error_acces.php");
    exit();
}

// Ruta al archivo de usuarios
$usersFile = './dades/users';

// Lee el archivo de usuarios
$usersData = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Filtra los usuarios por el rol de "gestor"
$gestorsData = array_filter($usersData, function($userData) {
    return explode(':', $userData)[2] === "gestor";
});

// Ordena los gestores por nombre
usort($gestorsData, function($a, $b) {
    $nameA = explode(':', $a)[0];
    $nameB = explode(':', $b)[0];
    return strcasecmp($nameA, $nameB);
});

// Variable para almacenar el nombre del gestor seleccionado
$selectedGestor = null;

// Verifica si el formulario de gestión de gestores ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["select_gestor"])) {
        // Seleccionar un gestor
        $selectedGestor = $_POST["selected_gestor"];
        // Obtén los datos del gestor seleccionado
        list($editedUsername, $editedPassword, $editedRole, $editedEmail, $editedId, $editedNom, $editedCognom, $editedTelefon) = explode(':', getUserData($selectedGestor, $usersData));
    } elseif (isset($_POST["add_gestor"])) {
        // Agregar nuevo gestor
        $newUsername = $_POST["new_gestor_username"];
        if (!usernameExists($newUsername, $usersData)) {
            $newGestorData = $newUsername . ":" . password_hash($_POST["new_gestor_password"], PASSWORD_DEFAULT) . ":gestor:" . $_POST["new_gestor_email"]. ":" . $_POST["new_gestor_id"] . ":" . $_POST["new_gestor_nom"] . ":" . $_POST["new_gestor_cognom"] . ":" . $_POST["new_gestor_telefon"];
            $usersData[] = $newGestorData;
            file_put_contents($usersFile, implode("\n", $usersData));
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        } else {
            // Puedes manejar el caso de nombre de usuario duplicado aquí
            echo "<p style='color: red;'>Error: El nombre de usuario ya existe.</p>";
        }
    } elseif (isset($_POST["edit_gestor"])) {
        // Modificar gestor existente
        $editedUsername = $_POST["edited_gestor_username"];
        $editedPassword = password_hash($_POST["edited_gestor_password"], PASSWORD_DEFAULT);
        $editedEmail = $_POST["edited_gestor_email"];
        $editedId = $_POST["edited_gestor_id"];
        $editedNom = $_POST["edited_gestor_nom"];
        $editedCognom = $_POST["edited_gestor_cognom"];
        $editedTelefon = $_POST["edited_gestor_telefon"];

        // Verifica si el nuevo nombre de usuario ya existe
        if (!usernameExists($editedUsername, $usersData, $editedUsername) || $editedUsername === $selectedGestor) {
            foreach ($usersData as $key => $userData) {
                $storedUsername = explode(':', $userData)[0];
                if ($storedUsername === $editedUsername) {
                    $usersData[$key] = "$editedUsername:$editedPassword:gestor:$editedEmail:$editedId:$editedNom:$editedCognom:$editedTelefon";
                    break;
                }
            }
            file_put_contents($usersFile, implode("\n", $usersData));
            header("Location: " . $_SERVER["PHP_SELF"]);
        } else {
            // Puedes manejar el caso de nombre de usuario duplicado aquí
            echo "<p style='color: red;'>Error: El nombre de usuario ya existe.</p>";
        }
    } elseif (isset($_POST["delete_gestor"])) {
        // Eliminar gestor
        $deletedUsername = $_POST["deleted_gestor_username"];
        $gestorsData = array_filter($gestorsData, function ($gestorData) use ($deletedUsername) {
            return explode(':', $gestorData)[0] !== $deletedUsername;
        });
        $usersData = array_filter($usersData, function ($userData) use ($deletedUsername) {
            return explode(':', $userData)[0] !== $deletedUsername || explode(':', $userData)[2] !== "gestor";
        });
        file_put_contents($usersFile, implode("\n", $usersData));
    }
}

// Función para verificar si un nombre de usuario ya existe
function usernameExists($username, $usersData, $excludeUsername = null)
{
    foreach ($usersData as $userData) {
        $storedUsername = explode(':', $userData)[0];
        if ($storedUsername === $username && $storedUsername !== $excludeUsername) {
            return true; // El nombre de usuario ya existe
        }
    }
    return false; // El nombre de usuario no existe
}

// Función para obtener los datos de un usuario por nombre de usuario
function getUserData($username, $usersData)
{
    foreach ($usersData as $userData) {
        $storedUsername = explode(':', $userData)[0];
        if ($storedUsername === $username) {
            return $userData;
        }
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestors</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <header>
        <h1>Gestió d'usuaris Gestors</h1>
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
        <!-- Lista de Gestors en formato de tabla -->
        <h2>Llista de Gestors</h2>
        <table border='1'>
            <tr>
                <th>ID</th>
                <th>Nom d'Usuari</th>
                <th>Correu Electrònic</th>
                <th>Nom</th>
                <th>Cognom</th>
                <th>Telèfon</th>
                <th>Accions</th>
            </tr>
            <?php foreach ($gestorsData as $gestorData): ?>
                <?php list($username, $password, $role, $email, $id, $nom, $cognom, $telefon) = explode(':', $gestorData); ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $username; ?></td>
                    <td><?php echo $email; ?></td>
                    <td><?php echo $nom; ?></td>
                    <td><?php echo $cognom; ?></td>
                    <td><?php echo $telefon; ?></td>
                    <td>
                        <!-- Botón para eliminar gestor -->
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="deleted_gestor_username" value="<?php echo $username; ?>">
                            <button type="submit" name="delete_gestor">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table><br>
        <a href="fitxerHTML2PDF.php?action=tabla_gestors"><button type="button">Descarrega en format PDF</button></a>
    </section>

    <section>
        <h2>Creació nou usuari</h2>
        <!-- Formulario para agregar un nuevo gestor -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="new_gestor_username">Nom d'Usuari:</label>
            <input type="text" id="new_gestor_username" name="new_gestor_username" required><br>

            <label for="new_gestor_password">Contrasenya:</label>
            <input type="password" id="new_gestor_password" name="new_gestor_password" required><br>

            <label for="new_gestor_email">Correu Electrònic:</label>
            <input type="email" id="new_gestor_email" name="new_gestor_email" required><br>

            <label for="new_gestor_id">ID:</label>
            <input type="text" id="new_gestor_id" name="new_gestor_id" required><br>

            <label for="new_gestor_nom">Nom:</label>
            <input type="text" id="new_gestor_nom" name="new_gestor_nom" required><br>

            <label for="new_gestor_cognom">Cognom:</label>
            <input type="text" id="new_gestor_cognom" name="new_gestor_cognom" required><br>
            
            <label for="new_gestor_telefon">Telèfon:</label>
            <input type="text" id="new_gestor_telefon" name="new_gestor_telefon" required><br>


            <button type="submit" name="add_gestor">Afegir Gestor</button>
        </form>
    </section>

    <section>
        <!-- Lista desplegable de gestores -->
        <h2>Modifica un Gestor</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="selected_gestor">Gestor:</label>
            <select id="selected_gestor" name="selected_gestor" onchange="this.form.submit()">
                <option value="" selected>Selecciona un gestor</option>
                <?php foreach ($gestorsData as $gestorData): ?>
                    <?php $username = explode(':', $gestorData)[0]; ?>
                    <option value="<?php echo $username; ?>" <?php echo ($username === $selectedGestor) ? 'selected' : ''; ?>><?php echo $username; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="select_gestor" value="1">
        </form>
    </section>

    <?php if ($selectedGestor !== null): ?>
        <!-- Formulario para editar el gestor seleccionado -->
        <section>
            <h2>Editar Gestor: <?php echo $selectedGestor; ?></h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="edited_gestor_username">Nou nom d'Usuari:</label>
                <input type="text" name="edited_gestor_username" value="<?php echo $selectedGestor; ?>"><br>
                
                <label for="edited_gestor_password">Nova Contrasenya:</label>
                <input type="password" id="edited_gestor_password" name="edited_gestor_password" value="<?php echo $editedPassword; ?>" required><br>
                
                <label for="edited_gestor_email">Nou Correu Electrònic:</label>
                <input type="email" id="edited_gestor_email" name="edited_gestor_email" value="<?php echo $editedEmail; ?>" required><br>
                
                <label for="edited_gestor_id">Nova ID:</label>
                <input type="text" id="edited_gestor_id" name="edited_gestor_id" value="<?php echo $editedId; ?>" required><br>

                <label for="edited_gestor_nom">Nou Nom:</label>
                <input type="text" id="edited_gestor_nom" name="edited_gestor_nom" value="<?php echo $editedNom; ?>" required><br>

                <label for="edited_gestor_cognom">Nou Cognom:</label>
                <input type="text" id="edited_gestor_cognom" name="edited_gestor_cognom" value="<?php echo $editedCognom; ?>" required><br>

                <label for="edited_gestor_telefon">Nou Telèfon:</label>
                <input type="text" id="edited_gestor_telefon" name="edited_gestor_telefon" value="<?php echo $editedTelefon; ?>" required><br>
                
                
                <button type="submit" name="edit_gestor">Modificar</button>
            </form>
        </section>
    <?php endif; ?>

</body>
</html>
