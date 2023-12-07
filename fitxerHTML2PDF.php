<?php
session_start();
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true); // Permite analizar HTML5
$options->set("isPhpEnabled", true); // Permite ejecutar PHP en el contenido HTML

$dompdf = new Dompdf($options);

// Obtenim l'usuari actual
$username = $_SESSION['username'];

// Rutas
$usersFile = './dades/users';
$fitxerComanda = "/var/www/html/zbotiga/comandes/$username/comandes_$username";

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

// Incluye la clase del catálogo
include 'Cataleg.php';

// Crea una instancia del catálogo
$cataleg = new Cataleg();

if (isset($_GET['action']) && $_GET['action'] === 'tabla_clients') {
    // Contenido HTML con la tabla
    $html = "
    <html lang='ca'>
    <head>
        <meta charset='UTF-8'>
        <title>Llista de Clients</title>
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
            }
        </style>
    </head>
    <body>
        <h2>Llista de Clients</h2>
        <table>
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
            </tr>";

    // Agrega filas de la tabla
    foreach ($clientsData as $clientData) {
        list($username, $password, $role, $email, $id, $nom, $cognom, $telefon, $cp, $visa, $gestorAssignat) = explode(':', $clientData);
        $html .= "
            <tr>
                <td>$id</td>
                <td>$username</td>
                <td>$email</td>
                <td>$nom</td>
                <td>$cognom</td>
                <td>$telefon</td>
                <td>$cp</td>
                <td>$visa</td>
                <td>$gestorAssignat</td>
            </tr>";
    }

    $html .= "
        </table>
    </body>
    </html>";

    // Carga el HTML en Dompdf
    $dompdf->loadHtml($html);
    // Configura el tamaño del papel y orientación
    $dompdf->setPaper('A4', 'landscape');
    // Renderiza el PDF
    $dompdf->render();
    // Envía el PDF 
    $dompdf->stream("dades_clients.pdf", array("Attachment" => true));

} else if (isset($_GET['action']) && $_GET['action'] === 'tabla_gestors') {

    // Contenido HTML con la tabla de Gestors
    $html = "
    <html lang='ca'>
    <head>
        <meta charset='UTF-8'>
        <title>Llista de Gestors</title>
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
            }
        </style>
    </head>
    <body>
        <h2>Llista de Gestors</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom d'Usuari</th>
                <th>Correu Electrònic</th>
                <th>Nom</th>
                <th>Cognom</th>
                <th>Telèfon</th>
            </tr>";

    // Agrega filas de la tabla de Gestors
    foreach ($gestorsData as $gestorData) {
        list($username, $password, $role, $email, $id, $nom, $cognom, $telefon) = explode(':', $gestorData);
        $html .= "
            <tr>
                <td>$id</td>
                <td>$username</td>
                <td>$email</td>
                <td>$nom</td>
                <td>$cognom</td>
                <td>$telefon</td>
            </tr>";
    }

    $html .= "
        </table>
    </body>
    </html>";

    // Carga el HTML en Dompdf
    $dompdf->loadHtml($html);
    // Configura el tamaño del papel y orientación
    $dompdf->setPaper('A4', 'landscape');
    // Renderiza el PDF
    $dompdf->render();
    // Envía el PDF 
    $dompdf->stream("dades_gestors.pdf", array("Attachment" => true));

} else if (isset($_GET['action']) && $_GET['action'] === 'cataleg') {
    ob_start();  // Inicia el almacenamiento en búfer de salida
    ?>
    <html lang='ca'>
    <head>
        <meta charset='UTF-8'>
        <title>Llista de Productes</title>
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
            }
        </style>
    </head>
    <body>
        <h2>Llista de Productes</h2>
        <?php
        $cataleg->mostrarProductesOrdenats();
        ?>
    </body>
    </html>
    <?php
    $html = ob_get_clean();  // Obtiene y limpia el contenido del búfer

    // Carga el HTML en Dompdf
    $dompdf->loadHtml($html);
    // Configura el tamaño del papel y orientación
    $dompdf->setPaper('A4', 'landscape');
    // Renderiza el PDF
    $dompdf->render();
    // Envía el PDF 
    $dompdf->stream("dades_productes.pdf", array("Attachment" => true));

} else if (isset($_GET['action']) && $_GET['action'] === 'comandes') {
    // Contenido HTML con la tabla de Comandes Realitzades
    $html = "
    <html lang='ca'>
    <head>
        <meta charset='UTF-8'>
        <title>Comandes Realitzades</title>
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
            }
        </style>
    </head>
    <body>
        <h2>Comandes Realitzades</h2>
        <section>";

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
        // Mostrar las tablas por cada comanda
        foreach ($comandes as $idComanda => $detallsComanda) {
            $html .= "<h3>Comanda $idComanda</h3>";
            $html .= "<table border='1'>";
            $html .= "<tr>
                    <th>Nom</th>
                    <th>Quantitat</th>
                    <th>Preu sense IVA</th>
                    <th>Valor de l'IVA</th>
                    <th>Preu final amb IVA</th>
                </tr>";

            $totalComanda = 0;

            foreach ($detallsComanda as $detall) {
                $html .= "<tr>
                        <td>{$detall['nom']}</td>
                        <td>{$detall['quantitat']}</td>
                        <td>{$detall['preuSenseIVA']} €</td>
                        <td>{$detall['valorIVA']} €</td>
                        <td>{$detall['preuAmbIVA']} €</td>
                    </tr>";

                // Sumar al total de la comanda
                $totalComanda += $detall['preuAmbIVA'];
            }

            $html .= "<tr>
            <th colspan='4'>Total Comanda</th>
            <td>{$totalComanda} €</td>
            </tr>
            </table><br>";
        }
    }

    $html .= "</section>
    </body>
    </html>";

    // Carga el HTML en Dompdf
    $dompdf->loadHtml($html);
    // Configura el tamaño del papel y orientación
    $dompdf->setPaper('A4', 'landscape');
    // Renderiza el PDF
    $dompdf->render();
    // Envía el PDF 
    $dompdf->stream("comandes_realitzades.pdf", array("Attachment" => true));
}
?>
