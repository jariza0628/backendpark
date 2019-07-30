<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
date_default_timezone_set('America/Bogota');

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/PHPMailer/src/SMTP.php';

$app->get('/api/sendmail', function(Request $request, Response $response){
    sendMail();
    $data = [
        "MSJ" => "EJECUTADO"
    ];
    return $response->withStatus(200)
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($data));
});

function sendMail(){
    $mail = new PHPMailer;
    $mail->isSMTP(); 
    $mail->SMTPDebug = 2; 
    $mail->Host = "smtp.gmail.com."; 
    $mail->Port = "587"; // typically 587 
    $mail->SMTPSecure = 'tls'; // ssl is depracated
    $mail->SMTPAuth = true;
    $mail->Username = "jeffer.junior28@gmail.com";
    $mail->Password = "Junior19062891";
    $mail->setFrom("jefferariza@outlook.com", "Jeffer");
    $mail->addAddress("jefferariza@outlook.com", "Jeff");
    $mail->Subject = 'Any_subject_of_your_choice';
    $mail->msgHTML("test body"); // remove if you do not want to send HTML email
    $mail->AltBody = 'HTML not supported';
   //  $mail->addAttachment('docs/crochure.pdf'); //Attachment, can be skipped

    $mail->send();

}
?>