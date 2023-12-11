<?php
session_start();
// Verifica si el usuario está autenticado y es un client
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "client") {
    // Si no está autenticado o no es un cleint, redirige a la página de inicio
    header("Location: index.php");
    exit();
}
?>

<?php

// Incloure la classe del catàleg
include 'cataleg.php';

// Crear una instància del catàleg
$cataleg = new Cataleg();
$productes = $cataleg->getProductes();

// Inicialitzar variables
$productesSeleccionats = [];
$quantitats = [];
$preuTotalSenseIVA = 0;
$preuTotalIVA = 0;

// Processar formulari si s'ha enviat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalitzar_compra'])) {
    $productesSeleccionats = $_POST['productes_seleccionats'];
    $quantitats = $_POST['quantitats'];

    // Calcular preus
    foreach ($productesSeleccionats as $idProducte) {
        $producte = $cataleg->getProducteById($idProducte);
        $preuTotalSenseIVA += $producte->getPreu() * $quantitats[$idProducte];
        $preuTotalIVA += ($producte->getPreu() * $producte->getIVA() / 100) * $quantitats[$idProducte];
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
        <h1>Cistella de Compra - Confirmació</h1>
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

            <?php foreach ($productesSeleccionats as $idProducte) : ?>
                <?php
                $producte = $cataleg->getProducteById($idProducte);
                $preuSenseIVA = $producte->getPreu() * $quantitats[$idProducte];
                $valorIVA = ($producte->getPreu() * $producte->getIVA() / 100) * $quantitats[$idProducte];
                $preuAmbIVA = $preuSenseIVA + $valorIVA;
                ?>

                <tr>
                    <td><?= $producte->getNom(); ?></td>
                    <td><?= $quantitats[$idProducte]; ?></td>
                    <td><?= $preuSenseIVA; ?>€</td>
                    <td><?= $valorIVA; ?>€</td>
                    <td><?= $preuAmbIVA; ?>€</td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Resum de la Cistella</h2>
        <p>Preu total sense IVA: <?= $preuTotalSenseIVA; ?>€</p>
        <p>Preu total de l'IVA: <?= $preuTotalIVA; ?>€</p>
        <p>Preu final amb IVA inclòs: <?= $preuTotalSenseIVA + $preuTotalIVA; ?>€</p>
    </section>
    <hr>


    <footer>
        <p><a href="cistella.php">Torna a la pàgina anterior</a></p>
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
