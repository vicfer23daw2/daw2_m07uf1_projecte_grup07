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

// Incloure la classe del catàleg
include 'Cataleg.php';

// Crear una instància del catàleg
$cataleg = new Cataleg();

// Acció d'afegir producte
if (isset($_POST['afegir_producte'])) {
    $nom = $_POST['nom_producte'];
    $preu = floatval($_POST['preu_producte']);
    $iva = floatval($_POST['iva_producte']);
    $disponibilitat = $_POST['disponibilitat_producte'];

    // Crear una nova instància de Producte
    $nouProducte = new Producte(count($cataleg->getProductes()) + 1, $nom, $preu, $iva, $disponibilitat);

    // Afegir el nou producte al catàleg
    $afegit = $cataleg->afegirProducte($nouProducte);

    if ($afegit) {
        echo "<p style='color: green;'>Producte afegit amb èxit.</p>";
    } else {
        echo "<p style='color: red;'>ERROR.</p>";
    }
}


// Acció d'esborrar producte
if (isset($_POST['esborrar_producte'])) {
    $idEsborrar = intval($_POST['id_esborrar_producte']);
    $esborrat = $cataleg->esborrarProducte($idEsborrar);

    if ($esborrat) {
        echo "<p style='color: green;'>Producte esborrat amb èxit.</p>";
    } else {
        echo "<p style='color: red;'>No s'ha trobat cap producte amb l'ID proporcionat.</p>";
    }
}

// Acció de modificar producte
if (isset($_POST['modificar_producte'])) {
    $idModificar = intval($_POST['id_modificar_producte']);
    $nouNom = $_POST['nou_nom_producte'];
    $nouPreu = floatval($_POST['nou_preu_producte']);
    $nouIVA = floatval($_POST['nou_iva_producte']);
    $novaDisponibilitat = $_POST['nova_disponibilitat_producte'];

    // Crida a la funció per modificar el producte
    $modificat = $cataleg->modificarProducte($idModificar, $nouNom, $nouPreu, $nouIVA, $novaDisponibilitat);

    if ($modificat) {
        echo "<p style='color: green;'>Producte modificat amb èxit.</p>";
    } else {
        echo "<p style='color: red;'>No s'ha trobat cap producte amb l'ID proporcionat.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interfície d'Usuari</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <header>
        <h1>Catàleg de Productes</h1>
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
        <!-- Secció d'Afegir Producte -->
        <h2>Afegir Producte</h2>
        <form method="post" action="gestio_cataleg.php">
            <label for="nom_producte">Nom del Producte:</label>
            <input type="text" id="nom_producte" name="nom_producte" required>

            <label for="preu_producte">Preu del Producte:</label>
            <input type="number" id="preu_producte" name="preu_producte" step="0.01" required>

            <label for="iva_producte">IVA del Producte:</label>
            <input type="number" id="iva_producte" name="iva_producte" step="0.01" required>

            <label for="disponibilitat_producte">Disponibilitat del Producte (si/no):</label>
            <select id="disponibilitat_producte" name="disponibilitat_producte" required>
                <option value="si">Si</option>
                <option value="no">No</option>
            </select>

            <button type="submit" name="afegir_producte">Afegir Producte</button>
        </form>
    </section>

    <section>
        <!-- Secció d'Esborrar Producte -->
        <h2>Esborrar Producte</h2>
        <form method="post" action="gestio_cataleg.php">
            <label for="id_esborrar_producte">ID del Producte a Esborrar:</label>
            <input type="number" id="id_esborrar_producte" name="id_esborrar_producte" required>

            <button type="submit" name="esborrar_producte">Esborrar Producte</button>
        </form>
    </section>

    <section>
        <!-- Secció de Modificar Producte -->
        <h2>Modificar Producte</h2>
        <form method="post" action="gestio_cataleg.php">
            <label for="id_modificar_producte">ID del Producte a Modificar:</label>
            <input type="number" id="id_modificar_producte" name="id_modificar_producte" required>
            <br><br>
            <label for="nou_nom_producte">Nom del Producte:</label>
            <input type="text" id="nou_nom_producte" name="nou_nom_producte">

            <label for="nou_preu_producte">Preu del Producte:</label>
            <input type="number" id="nou_preu_producte" name="nou_preu_producte" step="0.01">

            <label for="nou_iva_producte">IVA del Producte:</label>
            <input type="number" id="nou_iva_producte" name="nou_iva_producte" step="0.01">

            <label for="nova_disponibilitat_producte">Disponibilitat del Producte (si/no):</label>
            <select id="nova_disponibilitat_producte" name="nova_disponibilitat_producte">
                <option value="si">Si</option>
                <option value="no">No</option>
            </select>

            <button type="submit" name="modificar_producte">Modificar Producte</button>
        </form>
    </section>

    <section>
        <!-- Secció de Visualitzar Llista de Productes -->
        <h2>Visualitzar Llista de Productes</h2>
        <form method="post" action="gestio_cataleg.php">
            <button type="submit" name="visualitzar_productes">Visualitzar Productes</button>
            <a href="fitxerHTML2PDF.php?action=cataleg"><button type="button">Descarrega en format PDF</button></a>
        </form>

        <?php
        // Acció de visualitzar productes
        if (isset($_POST['visualitzar_productes'])) {
            echo "<h2>Llista de Productes</h2>";
            $cataleg->mostrarProductesOrdenats();
        }
        ?>
    </section>

</body>
</html>
