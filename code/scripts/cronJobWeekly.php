<?php
require_once '/vagrant/public/lpi/code/api/ask.php';
require_once '/vagrant/public/lpi/code/api/send_email.php';
require_once "/vagrant/public/lpi/code/api/vendor/autoload.php";
/*
sudo crontab -e
0 0 * * 1 php /vagrant/public/lpi/code/scripts/cronJobWeekly.php
*/

$start= new Mail();
$get= new Ask();

$data= $get->getUserInfoCronJobWeekly();
if(empty($data))
{
  return false ;
}
while($user = mysqli_fetch_assoc($data))
{
  $Euser= $get->getEventCronJobWeekly($user['id']);
  $start->mailSendCronJob($Euser,$user);
}
return true;
?>
