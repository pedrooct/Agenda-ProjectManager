<?php
session_start();
require_once '../api/ask.php';
header('content-type: application/json');

$perfil_id=$_SESSION['perfil_id'];

$ask= new Ask();

if($ask->isProfessor($perfil_id))
{
  $id=$ask->getUserId($perfil_id);
  $data=$ask->prepareHorario($id);
}
else {
  $pro=$ask->getProjectStudent($perfil_id);
  $data=$ask->prepareHorario($pro['orientador_id']);
}
echo json_encode($data);


?>
