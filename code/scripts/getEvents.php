<?php
session_start();
require_once '../api/ask.php';
header('content-type: application/json');

$perfil_id=$_SESSION['perfil_id'];

$ask= new Ask();
$packet=array();
$Devents=$ask->getEventsAprovedCalendar($perfil_id);
while ($row = mysqli_fetch_assoc($Devents)) {
  $packet[]=$row;
}
echo json_encode($packet);


?>
