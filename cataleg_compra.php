<?php
session_start();
// Verifica si el usuario está autenticado y es un administrador
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "client") {
    // Si no está autenticado o no es un administrador, redirige a la página de inicio
    header("Location: error_acces.php");
    exit();
}
?>
<?php
// Incloure la classe del catàleg
include 'Cataleg.php';

// Crear una instància del catàleg
$cataleg = new Cataleg();
$productes = $cataleg->getProductes();

?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cistella</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <header>
        <h1>Cistella de Compra</h1>
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
        <h2>Cataleg de Productes</h2>
        <form method="post" action="cistella.php">
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Preu</th>
                    <th>IVA</th>
                    <th>Disponibilitat</th>
                    <th>Seleccionar</th>
                    <th>Quantitat</th>
                </tr>

                <?php foreach ($productes as $producte) : ?>
                    <tr>
                        <td><?= $producte->getId(); ?></td>
                        <td><?= $producte->getNom(); ?></td>
                        <td><?= $producte->getPreu(); ?>€</td>
                        <td><?= $producte->getIVA(); ?>%</td>
                        <td><?= $producte->getDisponibilitat(); ?></td>
                        <td>
                            <?php if ($producte->getDisponibilitat() === 'si') : ?>
                                <input type="checkbox" name="productes_seleccionats[]" value="<?= $producte->getId(); ?>">
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($producte->getDisponibilitat() === 'si') : ?>
                                <input type="number" name="quantitats[<?= $producte->getId(); ?>]" value="0" min="0">
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table><br>

            <button type="submit" name="finalitzar_compra">Finalitzar Compra</button>
        </form>
    </section>

</body>
</html>
