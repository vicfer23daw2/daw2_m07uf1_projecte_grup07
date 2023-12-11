<?php
	require_once 'vendor/autoload.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\SMTP;

    #
    # AQUEST EXEMPLE ÉS VÀLID UTILITZANT EL COMPTE DE L'ESCOLA FJE.EDU
    # Heu d'activar "Accés d'aplicacions menys segures" del teu compte de correu:
    # 	1- Entra al correu de l'escola
    #   2- Fes clic amb el botó de l'esquerra del ratolí a sobre de la icona rodona amb la teva inicial que hi ha a la part superior a de la dreta
    #	3- Selecciona "Gestiona el teu Compte de Google" 
    #	4- Selecciona "Seguretat" a la llista de l'esquerra
    #	5- Troba dins de la pàgina "Seguretat" l'opció "Accés d'aplicacions menys segures"
    #	6- Activa l'opicó "Accés d'aplicacions menys segures"
    #
    try {
        // Instanciant un objecte de la classe PHPMailer
        $mail = new PHPMailer();
        // Activa debug --> https://phpmailer.github.io/PHPMailer/classes/PHPMailer-PHPMailer-SMTP.html
        $mail->SMTPDebug=0; // 0 - 4 -> Des de 0 que no fa debug fins al màxim debug amb el valor 4 
        //SMTP
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Username = "456982.clot@fje.edu"; // El teu compte de fje.edu
        $mail->Password = "3c641b79"; // El teu password del teu compte de fje.edu
        $mail->SMTPSecure = "STARTTLS";
        $mail->Port = 587;
        $mail->Host = "smtp-mail.outlook.com";                          
        //Missatge
        $mail->SetFrom("456982.clot@fje.edu","Raül March Vehil"); //El teu ccompte de fje.edu i el teu nom i cognoms
        $mail->addAddress("456982.clot@fje.edu","Raül March Vehil"); //"442816.clot@fje.edu","Víctor Fernández Morenilla" El compte al qual s'envia el correu, i el nom i cognoms del receptor del correu
        $mail->isHTML(true);
        $mail->Subject = "petició d'addició/modificació/esborrament de client";
        $mail->Body = "miamsfinasgnskbnsknbksbnks";
        $mail->send();
        echo "Missatge enviat";
    }
    catch (Exception $e) {
        echo "Error d'enviament del missatge: " . $mail->ErrorInfo; //El missatge d'error depén del nivell de debug indicat a SMTPDebug
    }
?>
