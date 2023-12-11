<?php
session_start();

// Verifica si el usuario está autenticado y es el rol correcto
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "client") {
    // Si no está autenticado, redirige a la página de error
    header("Location: error_acces.php");
    exit();
}


// Obté les dades de tots els usuaris des de l'arxiu
$usersData = file('./dades/users', FILE_IGNORE_NEW_LINES);

// Funció per obtenir les dades d'un usuari per nom d'usuari
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

// Obté les dades de l'usuari autenticat
$usuari = $_SESSION['username'];
$dadesUsuari = getUserData($usuari, $usersData);

// Separa les dades de l'usuari
list($username, $password, $role, $email, $id, $nom, $cognom, $telefon, $cp, $visa, $gestor) = explode(':', $dadesUsuari);

?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuari</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <header>
        <h1>Perfil de <?php echo $username; ?></h1>
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
        <ul>
            <li>ID: <?php echo $id; ?></li>
            <li>Nom d'usuari: <?php echo $username; ?></li>
            <li>Tipus d'usuari: <?php echo $role; ?></li>
            <li>Correu electrònic: <?php echo $email; ?></li>
            <li>Nom: <?php echo $nom; ?></li>
            <li>Cognom: <?php echo $cognom; ?></li>
            <li>Telèfon: <?php echo $telefon; ?></li>
            <li>Codi Postal: <?php echo $cp; ?></li>
            <li>VISA: <?php echo $visa; ?></li>
            <li>Gestor Assignat: <?php echo $gestor; ?></li>
        </ul>
    </section>

</body>
</html>
