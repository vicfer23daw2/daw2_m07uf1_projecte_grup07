<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION["username"])) {
    // Si no está autenticado, redirige a la página de inicio de sesión
    header("Location: login.php");
    exit();
}

// Obtén el rol del usuario desde la sesión
$role = $_SESSION["role"];
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interfície</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <header>
        <h1>Benvingut <?php echo strtoupper($username); ?></h1>
        <div class="userlogeado">
            <?php
                echo "Nom usuari: ".$_SESSION['username']."<br>";
                echo "Tipus d'usuari: " . $_SESSION['role'] ;
                echo '<a href="logout.php">Tanca la sessió</a>';   
            ?>
        </div>
    </header>

    <section>
    <!-- Contenido específico de la interfaz según el rol del usuario -->
    <?php
    if ($role === "admin") {
        echo '
        <ul>
            <li><a href="admin_edit.php">Modifica usuari Admin</a></li>
            <li><a href="gestio_gestors.php">Gestio d&apos;usuaris Gestors</a></li>
            <li><a href="gestio_clients.php">Gestio d&apos;usuaris Clients</a></li>
        </ul>';
    } elseif ($role === "gestor") {
        echo '
        <ul>
            <li><a href="clients_assignats.php">Llista de Clients Assignats</a></li>
            <li><a href="correu.php">Correu al Admin</a></li>
            <li><a href="gestio_cataleg.php">Gestió del catàleg de Productes</a></li>
            <li><a href="gestor_comandes.php">Gestionar Comandes de clients</a></li>
        </ul>';
    } elseif ($role === "client") {
        echo '
        <ul>
            <li><a href="perfil_client.php">Les meves dades</a></li>
            <li><a href="correu.php">Correu al meu Gestor</a></li>
            <li><a href="cataleg_compra.php">Catàleg de Compra</a></li>
            <li><a href="cistella.php">La meva Cistella</a></li>
            <li><a href="comandes.php">Les meves Comandes</a></li>
        </ul>';
    }
    ?>
    </section>

</body>
</html>
