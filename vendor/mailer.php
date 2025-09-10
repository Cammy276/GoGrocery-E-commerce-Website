<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require __DIR__ . '/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

$mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP(); // Set mailer to use SMTP
$mail->SMTPAuth = true; // Enable SMTP authentication

$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587; // TCP port to connect to
<<<<<<< Updated upstream
$mail->Username = "jacquelinelim0513@gmail.com"; // SMTP username
$mail->Password = "obix dyyj czhp pequ"; // SMTP password
//$mail->Username = "meiq4336@gmail.com"; // SMTP username
//$mail->Password = "odud gvbt iorq qxde"; // SMTP password
=======
// $mail->Username = "jacquelinelim0513@gmail.com"; // SMTP username
// $mail->Password = "obix dyyj czhp pequ"; // SMTP password

$mail->Username = "meiq4336@gmail.com"; // SMTP username
$mail->Password = "lrbx dvnc ewct nxuu"; // SMTP password
>>>>>>> Stashed changes

$mail->isHTML(true);

return $mail;