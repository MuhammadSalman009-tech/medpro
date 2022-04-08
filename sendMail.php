<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;



require 'vendor/autoload.php';


$template = file_get_contents('content.html');

if (isset($_POST['email'])) {
    $template = str_replace(
        array("<!-- #{FromState} -->", "<!-- #{FromEmail} -->"),
        array("Email:", $_POST['email']),
        $template
    );
}

if (isset($_POST['message'])) {
    $template = str_replace(
        array("<!-- #{MessageState} -->", "<!-- #{MessageDescription} -->"),
        array("Message:", $_POST['message']),
        $template
    );
}

// In a regular expression, the character \v is used as "anything", since this character is rare
preg_match("/(<!-- #\{BeginInfo\} -->)([^\v]*?)(<!-- #\{EndInfo\} -->)/", $template, $matches, PREG_OFFSET_CAPTURE);
foreach ($_POST as $key => $value) {
    if ($key != "counter" && $key != "email" && $key != "message" && $key != "form-type" && $key != "g-recaptcha-response" && !empty($value)) {
        $info = str_replace(
            array("<!-- #{BeginInfo} -->", "<!-- #{InfoState} -->", "<!-- #{InfoDescription} -->"),
            array("", ucfirst($key) . ':', $value),
            $matches[0][0]
        );

        $template = str_replace("<!-- #{EndInfo} -->", $info, $template);
    }
}
$subject = "A message from your site visitor";
$template = str_replace(
    array("<!-- #{Subject} -->", "<!-- #{SiteName} -->"),
    array($subject, $_SERVER['SERVER_NAME']),
    $template
);

$mail = new PHPMailer();

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
//SMTP::DEBUG_OFF = off (for production use)
//SMTP::DEBUG_CLIENT = client messages
//SMTP::DEBUG_SERVER = client and server messages
$mail->SMTPDebug = SMTP::DEBUG_OFF;

//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';
//Use `$mail->Host = gethostbyname('smtp.gmail.com');`
//if your network does not support SMTP over IPv6,
//though this may cause issues with TLS

//Set the SMTP port number:
// - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
// - 587 for SMTP+STARTTLS
$mail->Port = 465;

//Set the encryption mechanism to use:
// - SMTPS (implicit TLS on port 465) or
// - STARTTLS (explicit TLS on port 587)
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = 'salmansumra009@gmail.com';

//Password to use for SMTP authentication
$mail->Password = 'Friends@009';

//Set who the message is to be sent from
//Note that with gmail you can only use your account address (same as `Username`)
//or predefined aliases that you have configured within your account.
//Do not use user-submitted addresses in here
$mail->setFrom('salmansumra009@gmail.com', 'Muhmmad Salman');

//Set an alternative reply-to address
//This is a good place to put user-submitted addresses
$mail->addReplyTo('salmansumra009@gmail.com', 'Muhmmad Salman');

//Set who the message is to be sent to
$mail->addAddress('salmansumra009@gmail.com', 'John Doe');



// if (isset($_POST['name'])) {
//     $mail->FromName = $_POST['name'];
// } else {
//     $mail->FromName = "Site Visitor";
// }


$mail->Subject = $subject;

$mail->msgHTML($template);
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo ("<script>location.href = 'index.php?message=Successfully sent!';</script>");
}
