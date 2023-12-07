<?php
session_start();

// Verifica si el usuario está autenticado y es un cliente
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "client") {
    // Si no está autenticado o no es un cliente, redirige a la página de inicio
    header("Location: error_acces.php");
    exit();
}

// Incloure la classe del catàleg
include 'cataleg.php';

// Crear una instància del catàleg
$cataleg = new Cataleg();

// Obtenim l'usuari actual
$username = $_SESSION['username'];

// Ruta del fitxer de la cistella
$fitxerCistella = "/var/www/html/zbotiga/cistelles/$username/cistella_$username";
$fitxerComanda = "/var/www/html/zbotiga/comandes/$username/comandes_$username";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_comanda'])) {
 
    // Verificar si el archivo de la cistella existe
    if (file_exists($fitxerCistella)) {

        // Leer el contenido del archivo de la cistella
        $contingutCistella = file_get_contents($fitxerCistella);
        $liniesCistella = explode("\n", $contingutCistella);

        // Abrir el archivo de comandas en modo escritura
        $fitxerComanda = fopen($fitxerComanda, 'a');

        // Obtener la hora actual
        $horaComanda = date('Y-m-d/H-i-s');

        // Generar un identificador de comanda único
        $idComanda = uniqid();

        foreach ($liniesCistella as $linia) {
            if (!empty($linia)) {
                // Dividir la línea de la cistella en partes
                list($idProducte, $nom, $preu, $iva, $quantitat) = explode(":", $linia);

                // Escribir las dades de la comanda al archivo de comandas
                fwrite($fitxerComanda, "$idComanda:$horaComanda:$idProducte:$nom:$preu:$iva:$quantitat\n");
            }
        }

        // Cerrar el archivo de comandas
        fclose($fitxerComanda);

        // Eliminar el archivo de la cistella
        unlink($fitxerCistella);
    }
    header("Location: " . $_SERVER["PHP_SELF"]);
}


?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comandes</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <header>
        <h1>Comandes</h1>
        <nav>
            <a href="interficie.php">Torna a la interfície</a>
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
    <h2>Comandes Realitzades</h2>

    <a href="fitxerHTML2PDF.php?action=comandes"><button type="button">Descarrega en format PDF</button></a>

    <?php
    // Verificar si el archivo de comandes existe antes de intentar leerlo
    if (file_exists($fitxerComanda)) {
        // Leer el contenido del archivo y dividirlo en líneas
        $contingutComandes = file_get_contents($fitxerComanda);
        $liniesComandes = explode("\n", $contingutComandes);

        $comandes = [];

        foreach ($liniesComandes as $linia) {
            if (!empty($linia)) {
                // Dividir la línea en partes
                list($idComanda, $horaComanda, $idProducte, $nom, $preu, $iva, $quantitat) = explode(":", $linia);

                // Añadir a la lista de comandes
                $comandes[$idComanda][] = [
                    'nom' => $nom,
                    'quantitat' => $quantitat,
                    'preuSenseIVA' => $preu * $quantitat,
                    'valorIVA' => ($preu * $iva / 100) * $quantitat,
                    'preuAmbIVA' => $preu * $quantitat + ($preu * $iva / 100) * $quantitat,
                ];
            }
        }

        // Mostrar les taules per a cada comanda
        foreach ($comandes as $idComanda => $detallsComanda) {
            echo "<h3>Comanda $idComanda</h3>";
            echo "<table border='1'>";
            echo "<tr>
                    <th>Nom</th>
                    <th>Quantitat</th>
                    <th>Preu sense IVA</th>
                    <th>Valor de l'IVA</th>
                    <th>Preu final amb IVA</th>
                </tr>";

            $totalComanda = 0;

            foreach ($detallsComanda as $detall) {
                echo "<tr>
                        <td>{$detall['nom']}</td>
                        <td>{$detall['quantitat']}</td>
                        <td>{$detall['preuSenseIVA']} €</td>
                        <td>{$detall['valorIVA']} €</td>
                        <td>{$detall['preuAmbIVA']} €</td>
                    </tr>";

                // Sumar al total de la comanda
                $totalComanda += $detall['preuAmbIVA'];
            }

            echo "<tr>
            <th colspan='4'>Total Comanda</td>
            <td>{$totalComanda} €</td>
            </tr>
            </table><br>";
        }
    } 

    ?>
    </section>


</body>
</html>