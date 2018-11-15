<?php
session_start();
use JeroenDesloovere\VCard\VCard;
require_once "../api/ask.php";
require_once "../api/send_email.php";
require_once "../api/vendor/autoload.php";

$perfil_id=$_GET['id'];
$ask=new Ask();
$data= $ask->getUserInfo($perfil_id);
$img=$ask->DecodeImage($data['foto']);


$vcard = new VCard();
$vcard->addName($data['user_name']);
// add work data
$vcard->addRole($data['formacacao_avancada']);
$vcard->addEmail($data['email']);
$vcard->addPhoneNumber($data['contacto'], 'PREF;WORK');
$vcard->addLabel('ufp');
$vcard->addURL($data['cacifo_digital_id']);
if(!empty($img))
{
  $vcard->addPhotoContent($img);
}
//return vcard as a string
$sendVcard=base64_encode($vcard->getOutput());

$mail=new Mail();
if(!$mail->mail_sendVcard($_SESSION['perfil_id'],$sendVcard))
{
    header("location: ../dashboard");
}
else {
  header('location: ../dashboard');
}
?>
