<script>
function msg() {
    alert("OOOPS ! Algo correu mal na Confirmaçao do email");
}
</script>
<?php
require_once "../api/ask.php";
require_once "../api/access_db.php";
$pid=$_GET['pid'];
$ppid=$_GET['ppid'];
if($ppid==1)
{
  $db= new AccessDB("UPDATE utilizador SET is_email_valid=1 , is_email_pessoal_valid=1 WHERE perfil_id=".$pid);
  $response=$db->atualizar();
  if($response)
  {
    header('location: ../login');
  }
}
else {
  $db= new AccessDB("UPDATE utilizador SET is_email_valid=1, is_email_pessoal_valid=0 WHERE perfil_id=".$pid);
  $response=$db->atualizar();
  if($response)
  {
    header('location: ../login');
  }
}
?>
<script>
msg();
</script>
