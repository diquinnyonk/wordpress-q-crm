<?php

include("../../../../wp-load.php");
include(QCRM_URL . "core/Q_crud.php");
//$users 	= get_users('blog_id=1');


$subject 	= $_POST['subject'];
$email_to   = $_POST['email_to'];
$email_from = $_POST['email_from'];
$textarea 	= $_POST['textarea'];


$mail = new PHPMailer;


$mail->From = $email_from;
$mail->FromName = $email_from;
$mail->addAddress($email_to);     // Add a recipient
$mail->addAddress('paul@nzime.com');               // Name is optional
$mail->addReplyTo($email_to, 'Information');
$mail->addCC($email_to);
$mail->addBCC($email_to);

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $subject;
$mail->Body    = $textarea;
$mail->AltBody = $textarea;

$return = array();
if(!$mail->send()) {
	$return['success'] = 'no';
    $return['message'] = 'Message could not be sent.';
    $return['error']   = 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    $return['success'] = 'yes';
}


print_r(json_encode($return));


