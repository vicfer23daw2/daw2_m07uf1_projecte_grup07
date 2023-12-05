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

// Filtra los usuarios por el rol de "client"
$clientsData = array_filter($usersData, function($userData) {
    return explode(':', $userData)[2] === "client";
});

// Filtra los usuarios por el rol de "gestor"
$gestorsData = array_filter($usersData, function($userData) {
    return explode(':', $userData)[2] === "gestor";
});

// Ordena los clientes por nombre
usort($clientsData, function($a, $b) {
    $nameA = explode(':', $a)[0];
    $nameB = explode(':', $b)[0];
    return strcasecmp($nameA, $nameB);
});

// Variable para almacenar el nombre del cliente seleccionado
$selectedClient = null;

// Verifica si el formulario de gestión de clientes ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["select_client"])) {
        // Seleccionar un client
        $selectedClient = $_POST["selected_client"];

        // Obtén los datos del client seleccionado
        list($editedUsername, $editedPassword, $editedRole, $editedEmail, $editedId, $editedNom, $editedCognom, $editedTelefon, $editedCp, $editedVisa, $editedGestorAssignat) = explode(':', getUserData($selectedClient, $usersData));
    } elseif (isset($_POST["add_client"])) {
        // Agregar nuevo client
        $newUsername = $_POST["new_client_username"];
        if (!usernameExists($newUsername, $usersData)) {
            $newClientData = $newUsername . ":" . $_POST["new_client_password"] . ":client:" . $_POST["new_client_email"]. ":" . $_POST["new_client_id"] . ":" . $_POST["new_client_nom"] . ":" . $_POST["new_client_cognom"] . ":" . $_POST["new_client_telefon"]. ":" . $_POST["new_client_cp"]. ":" . $_POST["new_client_visa"]. ":" . $_POST["new_client_gestorAssignat"];
            $usersData[] = $newClientData;
            file_put_contents($usersFile, implode("\n", $usersData));
            $carpetaComandes = "./comandes/" . $newUsername;
            $carpetaCistelles = "./cistelles/" . $newUsername;
            if (!file_exists($carpetaComandes)||!file_exists($carpetaCistelles)) {
                mkdir($carpetaComandes);
                mkdir($carpetaCistelles);
            }
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        } else {
            // Puedes manejar el caso de nombre de usuario duplicado aquí
            echo "<p style='color: red;'>Error: El nombre de usuario ya existe.</p>";
        }
    } elseif (isset($_POST["edit_client"])) {
        // Modificar client existente
        $editedUsername = $_POST["edited_client_username"];
        $editedPassword = $_POST["edited_client_password"];
        $editedEmail = $_POST["edited_client_email"];
        $editedId = $_POST["edited_client_id"];
        $editedNom = $_POST["edited_client_nom"];
        $editedCognom = $_POST["edited_client_cognom"];
        $editedTelefon = $_POST["edited_client_telefon"];
        $editedCp = $_POST["edited_client_cp"];
        $editedVisa = $_POST["edited_client_visa"];
        $editedGestorAssignat = $_POST["edited_client_gestorAssignat"];

        // Verifica si el nuevo nombre de usuario ya existe
        if (!usernameExists($editedUsername, $usersData, $editedUsername) || $editedUsername === $selectedClient) {
            foreach ($usersData as $key => $userData) {
                $storedUsername = explode(':', $userData)[0];
                if ($storedUsername === $editedUsername) {
                    $usersData[$key] = "$editedUsername:$editedPassword:client:$editedEmail:$editedId:$editedNom:$editedCognom:$editedTelefon:$editedCp:$editedVisa:$editedGestorAssignat";
                    break;
                }
            }
            file_put_contents($usersFile, implode("\n", $usersData));

            header("Location: " . $_SERVER["PHP_SELF"]);
        } else {
            // Puedes manejar el caso de nombre de usuario duplicado aquí
            echo "<p style='color: red;'>Error: El nombre de usuario ya existe.</p>";
        }
    } elseif (isset($_POST["delete_client"])) {
        // Eliminar client
        $deletedUsername = $_POST["deleted_client_username"];
        $clientsData = array_filter($clientsData, function ($clientData) use ($deletedUsername) {
            return explode(':', $clientData)[0] !== $deletedUsername;
        });
        $usersData = array_filter($usersData, function ($userData) use ($deletedUsername) {
            return explode(':', $userData)[0] !== $deletedUsername || explode(':', $userData)[2] !== "client";
        });
        file_put_contents($usersFile, implode("\n", $usersData));

        $carpetaComandes = "./comandes/" . $deletedUsername;
        $carpetaCistelles = "./cistelles/" . $deletedUsername;
            if (file_exists($carpetaComandes) || file_exists($carpetaCistelles)) {
                rmdir($carpetaComandes);
                rmdir($carpetaCistelles);
            }
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
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
        <h1>Gestió d'usuaris Clients</h1>
    </header>

    <section>
        <!-- Lista de Clients en formato de tabla -->
        <h2>Llista de Clients</h2>
        <table border='1'>
            <tr>
                <th>ID</th>
                <th>Nom d'Usuari</th>
                <th>Password</th>
                <th>Correu Electrònic</th>
                <th>Nom</th>
                <th>Cognom</th>
                <th>Telèfon</th>
                <th>Codi Postal</th>
                <th>VISA</th>
                <th>Gestor Assignat</th>
                <th>Accions</th>
            </tr>
            <?php foreach ($clientsData as $clientData): ?>
                <?php list($username, $password, $role, $email, $id, $nom, $cognom, $telefon, $cp, $visa, $gestorAssignat) = explode(':', $clientData); ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $username; ?></td>
                    <td><?php echo $password; ?></td>
                    <td><?php echo $email; ?></td>
                    <td><?php echo $nom; ?></td>
                    <td><?php echo $cognom; ?></td>
                    <td><?php echo $telefon; ?></td>
                    <td><?php echo $cp; ?></td>
                    <td><?php echo $visa; ?></td>
                    <td><?php echo $gestorAssignat; ?></td>
                    <td>
                        <!-- Botón para eliminar client -->
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="deleted_client_username" value="<?php echo $username; ?>">
                            <button type="submit" name="delete_client">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table><br>
        <a href="fitxerHTML2PDF.php?action=tabla_clients"><button type="button">Descarrega en format PDF</button></a>
    </section>

    <section>
        <h2>Creació nou usuari</h2>
        <!-- Formulario para agregar un nuevo client -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="new_client_username">Nom d'Usuari:</label>
            <input type="text" id="new_client_username" name="new_client_username" required><br>

            <label for="new_client_password">Contrasenya:</label>
            <input type="password" id="new_client_password" name="new_client_password" required><br>

            <label for="new_client_email">Correu Electrònic:</label>
            <input type="email" id="new_client_email" name="new_client_email" required><br>

            <label for="new_client_id">ID:</label>
            <input type="text" id="new_client_id" name="new_client_id" required><br>

            <label for="new_client_nom">Nom:</label>
            <input type="text" id="new_client_nom" name="new_client_nom" required><br>

            <label for="new_client_cognom">Cognom:</label>
            <input type="text" id="new_client_cognom" name="new_client_cognom" required><br>
            
            <label for="new_client_telefon">Telèfon:</label>
            <input type="text" id="new_client_telefon" name="new_client_telefon" required><br>

            <label for="new_client_cp">Codi Postal:</label>
            <input type="text" id="new_client_cp" name="new_client_cp" required><br>

            <label for="new_client_visa">VISA:</label>
            <input type="text" id="new_client_visa" name="new_client_visa" required><br>

            <label for="new_client_gestorAssignat">Gestor Assignat:</label>
            <select id="new_client_gestorAssignat" name="new_client_gestorAssignat" required><br>
                <option value="" selected>Selecciona un gestor</option>
                <?php foreach ($gestorsData as $gestorData): ?>
                    <?php $username = explode(':', $gestorData)[0]; ?>
                    <option value="<?php echo $username; ?>" <?php echo ($username === $selectedGestor) ? 'selected' : ''; ?>><?php echo $username; ?></option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" name="add_client">Afegir Client</button>
        </form>
    </section>

    <section>
        <!-- Lista desplegable de clients -->
        <h2>Modifica un Client</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="selected_client">Client:</label>
            <select id="selected_client" name="selected_client" onchange="this.form.submit()">
                <option value="" selected>Selecciona un client</option>
                <?php foreach ($clientsData as $clientData): ?>
                    <?php $username = explode(':', $clientData)[0]; ?>
                    <option value="<?php echo $username; ?>" <?php echo ($username === $selectedClient) ? 'selected' : ''; ?>><?php echo $username; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="select_client" value="1">
        </form>
    </section>

    <?php if ($selectedClient !== null): ?>
        <!-- Formulario para editar el client seleccionado -->
        <section>
            <h2>Editar Client: <?php echo $selectedClient; ?></h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="edited_client_username">Nou nom d'Usuari:</label>
                <input type="text" name="edited_client_username" value="<?php echo $selectedClient; ?>"><br>

                <label for="edited_client_password">Nova Contrasenya:</label>
                <input type="password" id="edited_client_password" name="edited_client_password" value="<?php echo $editedPassword; ?>" required><br>

                <label for="edited_client_email">Nou Correu Electrònic:</label>
                <input type="email" id="edited_client_email" name="edited_client_email" value="<?php echo $editedEmail; ?>" required><br>

                <label for="edited_client_id">Nova ID:</label>
                <input type="text" id="edited_client_id" name="edited_client_id" value="<?php echo $editedId; ?>" required><br>

                <label for="edited_client_nom">Nou Nom:</label>
                <input type="text" id="edited_client_nom" name="edited_client_nom" value="<?php echo $editedNom; ?>" required><br>

                <label for="edited_client_cognom">Nou Cognom:</label>
                <input type="text" id="edited_client_cognom" name="edited_client_cognom" value="<?php echo $editedCognom; ?>" required><br>

                <label for="edited_client_telefon">Nou Telèfon:</label>
                <input type="text" id="edited_client_telefon" name="edited_client_telefon" value="<?php echo $editedTelefon; ?>" required><br>
                
                <label for="edited_client_cp">Nou Codi Postal:</label>
                <input type="number" id="edited_client_cp" name="edited_client_cp" value="<?php echo $editedCp; ?>" required><br>

                <label for="edited_client_visa">Nova VISA:</label>
                <input type="text" id="edited_client_visa" name="edited_client_visa" value="<?php echo $editedVisa; ?>" required><br>

                <label for="edited_client_gestorAssignat">Nou Gestor Assignat:</label>
                <select id="edited_client_gestorAssignat" name="edited_client_gestorAssignat" required><br>
                    <option value="" selected>Selecciona un gestor</option>
                        <?php foreach ($gestorsData as $gestorData): ?>
                            <?php $username = explode(':', $gestorData)[0]; ?>
                            <option value="<?php echo $username; ?>" <?php echo ($username === $selectedGestor) ? 'selected' : ''; ?>><?php echo $username; ?></option>
                        <?php endforeach; ?>
                </select>

                <button type="submit" name="edit_client">Modificar</button>
            </form>
        </section>
    <?php endif; ?>

    <footer>
        <p><a href="interficie.php">Torna a la pàgina anterior</a></p>
        <p><a href="logout.php">Tanca la sessió</a></p>
        <div class="diahora"> 
        <?php
            echo "<p>Nom usuari: ".$_SESSION['username']."</p>";
            echo "<p>Tipus d'usuari: " . $_SESSION['role'] . "</p>";
            date_default_timezone_set('Europe/Andorra');
            echo "<p>Data i hora: ".date('d/m/Y h:i:s')."</p>";    
        ?>
        </div>
    </footer>

</body>
</html>
