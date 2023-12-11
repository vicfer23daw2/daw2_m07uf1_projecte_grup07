<?php
session_start();
// Verifica si el usuario está autenticado y es un cliente
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "client") {
    // Si no está autenticado o no es un cliente, redirige a la página de error
    header("Location: error_acces.php");
    exit();
}

// Incloure la classe del catàleg
include 'cataleg.php';

// Crear una instància del catàleg
$cataleg = new Cataleg();

// Obtenim l'usuari actual
$username = $_SESSION['username'];

// Obtenim els productes seleccionats i les quantitats
$productesSeleccionats = $_POST['productes_seleccionats'];
$quantitats = $_POST['quantitats'];

// Ruta del fitxer de la cistella
$fitxerCistella = "/var/www/html/zbotiga/cistelles/$username/cistella_$username";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalitzar_compra'])) {
    
    // Obre el fitxer en mode escriptura
    $fitxer = fopen($fitxerCistella, 'a');

    // Calcular preus
    foreach ($productesSeleccionats as $idProducte) {
        $producte = $cataleg->getProducteById($idProducte);
        $preuTotalSenseIVA += $producte->getPreu() * $quantitats[$idProducte];
        $preuTotalIVA += ($producte->getPreu() * $producte->getIVA() / 100) * $quantitats[$idProducte];

        $nom = $producte->getNom();
        $preu = $producte->getPreu();
        $iva = $producte->getIVA();
        $quantitat = $quantitats[$idProducte];

        // Escriu les dades dels productes seleccionats al fitxer de la cistella
        fwrite($fitxer, "$idProducte:$nom:$preu:$iva:$quantitat\n");
    }

    // Tanca el fitxer de la cistella
    fclose($fitxer);
}

// Verifica si es fa una petició POST per buidar la cistella
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buidar_cistella'])) {
    // Ruta del fitxer de la cistella
    $fitxerCistella = "/var/www/html/zbotiga/cistelles/$username/cistella_$username";

    // Comprova si l'arxiu existeix i l'elimina
    if (file_exists($fitxerCistella)) {
        unlink($fitxerCistella);
    }
}


?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cistella Confirmació</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <header>
        <h1>Cistella</h1>
        <nav>
            <a href="interficie.php">Torna a l'interfície</a>
            <a href="cataleg_compra.php">Torna al Catàleg</a>
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
        <h2>Productes Escollits</h2>
        <table border="1">
            <tr>
                <th>Nom</th>
                <th>Quantitat</th>
                <th>Preu sense IVA</th>
                <th>Valor de l'IVA</th>
                <th>Preu final amb IVA</th>
            </tr>

        <?php

        // Inicializa para cuando este vacio

        $totalSenseIVA = 0;
        $totalIVA = 0;

        // Verificar si el archivo existe antes de intentar leerlo
        if (file_exists($fitxerCistella)) {
            // Leer el contenido del archivo y dividirlo en líneas
            $contingutCistella = file_get_contents($fitxerCistella);
            $liniesCistella = explode("\n", $contingutCistella);

            foreach ($liniesCistella as $linia) {
                if (!empty($linia)) {
                    // Dividir la línea en partes
                    list($idProducte, $nom, $preu, $iva, $quantitat) = explode(":", $linia);

                    // Calcular los valores necesarios
                    $preuSenseIVA = $preu * $quantitat;
                    $valorIVA = ($preu * $iva / 100) * $quantitat;
                    $preuAmbIVA = $preuSenseIVA + $valorIVA;

                    $totalSenseIVA += $preuSenseIVA;
                    $totalIVA += $valorIVA;

                    // Mostrar la fila en la tabla
                    echo "<tr>
                            <td>$nom</td>
                            <td>$quantitat</td>
                            <td>$preuSenseIVA €</td>
                            <td>$valorIVA €</td>
                            <td>$preuAmbIVA €</td>
                        </tr>";
                }
            }

        } else {
            echo '<p>No hi ha productes a la cistella.</p>';
        }
        echo '</table>';
        ?>

        <h2>Resum de la Cistella</h2>
        <p>Preu total sense IVA: <?= $totalSenseIVA; ?>€</p>
        <p>Preu total de l'IVA: <?= $totalIVA; ?>€</p>
        <p>Preu final amb IVA inclòs: <?= $totalSenseIVA + $totalIVA; ?>€</p>
         
        <!-- Formulari per confirmar la comanda -->
        <form method="post" action="comandes.php">
            <button type="submit" name="confirmar_comanda">Formalitzar la Comanda</button>
        </form><br>
        <!-- Formulari per buidar la cistella -->
        <form method='post' action=''>
            <input type='hidden' name='buidar_cistella' >
            <button type='submit'>Buidar tota la Cistella</button>
        </form>
    </section>
    

</body>
</html>
