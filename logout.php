<?php
// Inicia la sessió
session_start();

// Verifica si l'usuari està autenticat
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirigeix a la pàgina principal si no hi ha una sessió d'usuari
    exit();
}

// Lògica de logout
if (isset($_POST['confirmar_logout'])) {
    session_start();
	//Alliberant variables de sessió. Esborra el contingut de les variables de sessió del fitxer de sessió. Buida l'array $_SESSION. No esborra cookies
	session_unset();
	//Destrucció de la cookie de sessió dins del navegador
	$cookie_sessio = session_get_cookie_params();
	setcookie("PHPSESSID","",time()-3600,$cookie_sessio['path'], $cookie_sessio['domain'], $cookie_sessio['secure'], $cookie_sessio['httponly']); //Neteja cookie de sessió
	//Destrucció de la informació de sessió (per exemple, el fitxer de sessió  o l'identificador de sessió) 
	session_destroy();
	header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmació de Logout</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <header>
        <h1>Confirmació de Logout</h1>
        <div class="userlogeado">
            <?php
                echo "Nom usuari: ".$_SESSION['username']."<br>";
                echo "Tipus d'usuari: " . $_SESSION['role'] ;  
            ?>
        </div>
    </header>

    <section>
        <p>Estàs segur que vols fer logout?</p>
        <form method="post" action="logout.php">
            <button type="submit" name="confirmar_logout">Sí, fer logout</button>
            <a href="interficie.php">No, tornar enrere</a>
        </form>
    </section>

</body>
</html>

