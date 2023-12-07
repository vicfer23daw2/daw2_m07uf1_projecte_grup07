<?php
session_start();
// Verifica si el usuario está autenticado y es un administrador
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "gestor") {
    // Si no está autenticado o no es un administrador, redirige a la página de inicio
    header("Location: error_acces.php");
    exit();
}
?>
<?php
// Ruta al archivo de usuarios
$usersFile = './dades/users';

// Lee el archivo de usuarios
$usersData = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Filtra los usuarios por el rol de "client" y el nombre de usuario del gestor
$clientsData = array_filter($usersData, function($userData) {
    $userDataArray = explode(':', $userData);
    return $userDataArray[2] === "client" && $userDataArray[10] === $_SESSION["username"];
});

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
        <h1>Clients Assignats a:  <?php echo strtoupper($_SESSION["username"]); ?></h1>
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
        <!-- Lista de Clients en formato de tabla -->
        <h2>Llista de Clients</h2>
        <table border='1'>
            <tr>
                <th>ID</th>
                <th>Nom d'Usuari</th>
                <th>Correu Electrònic</th>
                <th>Nom</th>
                <th>Cognom</th>
                <th>Telèfon</th>
                <th>Codi Postal</th>
                <th>VISA</th>
                <th>Gestor Assignat</th>
            </tr>
            <?php foreach ($clientsData as $clientData): ?>
                <?php list($username, $password, $role, $email, $id, $nom, $cognom, $telefon, $cp, $visa, $gestorAssignat) = explode(':', $clientData); ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $username; ?></td>
                    <td><?php echo $email; ?></td>
                    <td><?php echo $nom; ?></td>
                    <td><?php echo $cognom; ?></td>
                    <td><?php echo $telefon; ?></td>
                    <td><?php echo $cp; ?></td>
                    <td><?php echo $visa; ?></td>
                    <td><?php echo $gestorAssignat; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>

</body>
</html>
