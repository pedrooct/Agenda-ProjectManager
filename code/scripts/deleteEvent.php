<?php
session_start();
require_once "../api/ask.php";
require_once "../api/access_db.php";
require_once "../api/send_email.php";
?>
<script>
function msg() {
  alert("OOOPS ! Algo correu mal no envio de um dos emails !! Lamentamos");
}
</script>
<?php
if(!isset($_SESSION['perfil_id']))
{
  header('location: ../login');
}
$eventoID=$_GET['proid'];
$userSend= $_GET['PID'];

if(!empty($eventoID))
{
  $emailme= new Mail();
  if(!$emailme->mail_send_Erejeitado($eventoID,$userSend))
  {
    $db= new AccessDB("DELETE FROM evento WHERE id=".$eventoID);
    $response=$db->remover();
    if($response)
    {
      header('location: ../dashboard');
    }
  }
}
else {

  header('location: ../login');
}
?>
<script>
msg();
</script>
