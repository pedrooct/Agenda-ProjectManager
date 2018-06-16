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
$Utilziadoremail=$_SESSION['email'];

if(!empty($eventoID))
{
  $db= new AccessDB("UPDATE evento SET estado=1 WHERE id=".$eventoID);
  $response=$db->atualizar();
  if($response)
  {
    $emailme= new Mail();
    if(!$emailme->mail_send_evento($eventoID,$Utilziadoremail,$userSend))
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
