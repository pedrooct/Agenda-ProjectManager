<?php
require_once "../api/ask.php";
if(empty($_GET['mid']))
{
  header('location: verDocumento');
}
$ask= new Ask();
$mongoID= $_GET['mid'];
$doc=$ask->getDocumento($mongoID);
$file=base64_decode($doc->ficheiro);
$name=$doc->nome;
file_put_contents($name,$file);
header("Content-type: ".$doc->tipo);
header("Content-Disposition: attachment; filename=\"".$doc->nome."\"");
readfile($name);
unlink($name);


?>
