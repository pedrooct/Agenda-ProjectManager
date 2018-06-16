<?php
require_once '/vagrant/public/lpi/code/api/ask.php';
require_once '/vagrant/public/lpi/code/api/send_email.php';
require_once "/vagrant/public/lpi/code/api/vendor/autoload.php";
/*
sudo crontab -e
0 0 * * * php /vagrant/public/lpi/code/scripts/cronJobDaily.php
*/


$start= new Mail();
$get= new Ask();

$data= $get->getUserInfoCronJobDaily();
if(empty($data))
{
  return false ;
}
while($user = mysqli_fetch_assoc($data))
{
  $Euser= $get->getEventCronJobDaily($user['id']);
  $start->mailSendCronJob($Euser,$user);
}
return true;
?>
