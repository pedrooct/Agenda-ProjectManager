<?php
// funçoes de calendária e notificação por email !!
//session_start();
require_once 'access.php';
require_once 'access_db.php';
require_once 'ask.php';
require_once __DIR__ . "/vendor/autoload.php";

error_reporting(E_ALL & ~E_NOTICE);

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ICS {

  const DT_FORMAT = 'Ymd\THis';
  protected $properties = array();
  private $available_properties = array(
    'description',
    'dtend',
    'dtstart',
    'location',
    'summary',
    'url'
  );
  public function __construct($props) {
    $this->set($props);
  }
  public function set($key, $val = false) {
    if (is_array($key)) {
      foreach ($key as $k => $v) {
        $this->set($k, $v);
      }
    } else {
      if (in_array($key, $this->available_properties)) {
        $this->properties[$key] = $this->sanitize_val($val, $key);
      }
    }
  }
  public function to_string() {
    $rows = $this->build_props();
    return implode("\r\n", $rows);
  }
  private function build_props() {
    // Build ICS properties - add header
    $ics_props = array(
      'BEGIN:VCALENDAR',
      'VERSION:2.0',
      'PRODID:-//hacksw/handcal//NONSGML v1.0//EN',
      'CALSCALE:GREGORIAN',
      'BEGIN:VEVENT'
    );
    // Build ICS properties - add header
    $props = array();
    foreach($this->properties as $k => $v) {
      $props[strtoupper($k . ($k === 'url' ? ';VALUE=URI' : ''))] = $v;
    }
    // Set some default values
    $props['DTSTAMP'] = $this->format_timestamp('now');
    $props['UID'] = uniqid();
    // Append properties
    foreach ($props as $k => $v) {
      $ics_props[] = "$k:$v";
    }
    // Build ICS properties - add footer
    $ics_props[] = 'END:VEVENT';
    $ics_props[] = 'END:VCALENDAR';
    return $ics_props;
  }
  private function sanitize_val($val, $key = false) {
    switch($key) {
      case 'dtend':
      case 'dtstamp':
      case 'dtstart':
      $val = $this->format_timestamp($val);
      break;
      default:
      $val = $this->escape_string($val);
    }
    return $val;
  }
  private function format_timestamp($timestamp) {
    $dt = new DateTime($timestamp);
    return $dt->format(self::DT_FORMAT);
  }
  private function escape_string($str) {
    return preg_replace('/([\,;])/','\\\$1', $str);
  }
}

class Mail
{
  public function mail_send_Erejeitado($evento_id,$usersend)
  {
    $ask=new Ask();
    $InfoEvento=$ask->getEvent($evento_id);
    $InfoUser=$ask->getUserInfo($usersend);
    $InfoProjeto=$ask->getProjectBasedID($InfoEvento['project_id']);

    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
      //Server settings
      $mail->SMTPDebug = 0;                                 // Enable verbose debug output
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
      $mail->Password = 'Labproaulas10';                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;

      // TCP port to connect to
      //Recipients
      $mail->setFrom('labproaulas@gmail.com','Evento confirmado');
      $mail->addAddress($InfoUser['email']);     // Add recipient
      $mail->setFrom('labproaulas@gmail.com','Evento para o projeto - '.$InfoProjeto['nome']);
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = 'Notificacao de evento';
      $mail->Body  = "Evento com o nome ".$InfoEvento['nome']." para o projeto ".$InfoProjeto['nome']." foi rejeitado";

      $mail->send();

      $mail->ClearAddresses();  // each AddAddress add to list
      $mail->ClearCCs();
      $mail->ClearBCCs();

      if(!empty($InfoUser['email_pessoal']) && $InfoUser['is_email_pessoal_valid']==1 )
      {
        $mail->setFrom('labproaulas@gmail.com','Evento rejeitado');
        $mail->addAddress($InfoUser['email_pessoal']);     // Add recipient
        $mail->setFrom('labproaulas@gmail.com','Evento para o projeto - '.$InfoProjeto['nome']);
        $mail->isHTML(false);                                  // Set email format to HTML
        $mail->Subject = 'Notificacao de evento';
        $mail->Body  = "Evento com o nome ".$InfoEvento['nome']." para o projeto ".$InfoProjeto['nome']." foi rejeitado";
        $mail->send();
      }
    }
    catch (Exception $e)
    {
      echo $email.'Mailer Error:'. $mail->ErrorInfo;
      return false;
    }
  }


  public function mail_send_doc($email,$ficheiro,$tipo,$nome_ficheiro,$notas,$perfil_id)
  {
    $ficheiro=base64_encode(file_get_contents($ficheiro));
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
      //Server settings
      $mail->SMTPDebug = 0;                                 // Enable verbose debug output
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
      $mail->Password = 'Labproaulas10';                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;

      // TCP port to connect to
      $ask=new Ask();
      $user=$ask->getUserInfo($perfil_id);
      $receiverPID=$ask->getUserPerfilId($email);
      $receiverPID=$ask->getUserInfo($receiverPID);
      //Recipients
      $mail->setFrom('labproaulas@gmail.com','Documento');
      $mail->addAddress($email,$receiverPID['user_name']);     // Add recipient
      //$mail->addAddress('ellen@example.com');               // Name is optional
      //$mail->addReplyTo('info@example.com', 'Information');
      //$mail->addCC('cc@example.com');
      //$mail->addBCC('bcc@example.com');

      //Attachments
      //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
      //$mail->addAttachment(__DIR__."/".'recover.pdf', 'users&id');    // Optional name

      //Envia um texto com o link para rederecionar o utilizador para puder inserir uma nova palavra passe
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = 'Recebeu um documento de '.$user['user_name'];
      $mail->Body    = 'Recebeu um documento de '.$user['user_name'].'. Clique '.'<a '.'class="pure-button"'.' href=http://app.test/lpi/code/dashboard >aqui</a>'.' para ir para o seu dashboard. Nota: '.$notas;
      if(strcmp($tipo,"application/zip")!=0)
      {
        $mail->AddStringAttachment(base64_decode($ficheiro),$nome_ficheiro.".".$tipo ,'base64', $tipo);
      }
      $mail->send();
      if(!empty($receiverPID['email_pessoal']) && $receiverPID['is_email_pessoal_valid']==1 )
      {
        $mail->ClearAddresses();  // each AddAddress add to list
        $mail->ClearCCs();
        $mail->ClearBCCs();
        $mail->clearAttachments();
        $mail->setFrom('labproaulas@gmail.com','Documento');
        $mail->addAddress($receiverPID['email_pessoal'],$receiverPID['user_name']);
        $mail->isHTML(true);
        $mail->Subject = 'Recebeu um documento de '.$user['user_name'];
        $mail->Body    = 'Recebeu um documento '.$user['user_name'].'. Clique '.'<a '.'class="pure-button"'.' href=http://app.test/lpi/code/dashboard >aqui</a>'.' para ir para o seu dashboard. Nota: '.$notas;
        $mail->AddStringAttachment(base64_decode($ficheiro),$nome_ficheiro.".".$tipo ,'base64', $tipo);
        $mail->send();
      }
      return true;
    } catch (Exception $e) {
      echo $email.'Mailer Error:'. $mail->ErrorInfo;
      return false;
    }

  }
  public function mail_send_evento($evento_id,$utilizadoremail,$usersend)
  {
    $ask=new Ask();
    $InfoEvento=$ask->getEvent($evento_id);
    $InfoUser=$ask->getUserInfo($usersend);
    $InfoProjeto=$ask->getProjectBasedID($InfoEvento['project_id']);

    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
      //Server settings
      $mail->SMTPDebug = 0;                                 // Enable verbose debug output
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
      $mail->Password = 'Labproaulas10';                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;

      // TCP port to connect to
      //Recipients
      $mail->setFrom('labproaulas@gmail.com','Evento confirmado');
      $mail->addAddress($utilizadoremail);     // Add recipient
      $mail->setFrom('labproaulas@gmail.com','Evento para o projeto - '.$InfoProjeto['nome']);
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = 'Confirmacao de evento';
      $mail->Body  = "Em anexo e enviado o ficheiro de evento que pode adicionar ao seu calendário. Clique <a class='pure-button' href=http://app.test/lpi/code/login >aqui</a> para verificar o estado dos seu eventos.";

      $userdate=explode("/",$InfoEvento['data']);
      $userdate=implode("-",$userdate);

      $ics = new ICS(array(
        'location' => "UFP- sala: ".$InfoEvento['sala'],
        'description' => "Evento: ".$InfoEvento['nome']." para o projeto ".$InfoProjeto['nome'],
        'dtstart' => $userdate." ".$InfoEvento['hora_inicio'],
        'dtend' => $userdate." ".$InfoEvento['hora_fim'],
        'summary' => "Evento: ".$InfoEvento['nome'],
        'url' => $InfoEvento['cacifo_digital_id']
      ));
      $ics=base64_encode($ics->to_string());

      $mail->AddStringAttachment(base64_decode($ics),"Evento.ics",'base64', "text/calendar");
      //$mail->addAttachment();
      $mail->send();



      $mail->ClearAddresses();  // each AddAddress add to list
      $mail->ClearCCs();
      $mail->ClearBCCs();
      $mail->clearAttachments();

      $mail->setFrom('labproaulas@gmail.com','Evento confirmado');
      $mail->addAddress($InfoUser['email']);     // Add recipient
      $mail->setFrom('labproaulas@gmail.com','Evento para o projeto - '.$InfoProjeto['nome']);
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = 'Confirmacao de evento';
      $mail->Body  = "Em anexo e enviado o ficheiro de evento que pode adicionar ao seu calendário. Clique <a class='pure-button' href=http://app.test/lpi/code/login >aqui</a> para verificar o estado dos seu eventos.";

      $userdate=explode("/",$InfoEvento['data']);
      $userdate=implode("-",$userdate);

      $ics = new ICS(array(
        'location' => "UFP- sala: ".$InfoEvento['sala'],
        'description' => "Evento: ".$InfoEvento['nome']." para o projeto ".$InfoProjeto['nome'],
        'dtstart' => $userdate." ".$InfoEvento['hora_inicio'],
        'dtend' => $userdate." ".$InfoEvento['hora_fim'],
        'summary' => "Evento: ".$InfoEvento['nome'],
        'url' => $InfoEvento['cacifo_digital_id']
      ));

      $ics=base64_encode($ics->to_string());

      $mail->AddStringAttachment(base64_decode($ics),"Evento.ics",'base64', "text/calendar");
      //$mail->addAttachment();
      $mail->send();

      $mail->ClearAddresses();  // each AddAddress add to list
      $mail->ClearCCs();
      $mail->ClearBCCs();
      $mail->clearAttachments();

      if(!empty($InfoUser['email_pessoal']) && $InfoUser['is_email_pessoal_valid']==1 )
      {
        $mail->setFrom('labproaulas@gmail.com','Evento confirmado');
        $mail->addAddress($InfoUser['email_pessoal']);     // Add recipient
        $mail->setFrom('labproaulas@gmail.com','Evento para o projeto - '.$InfoProjeto['nome']);
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Confirmacao de evento';
        $mail->Body  = "Em anexo e enviado o ficheiro de evento que pode adicionar ao seu calendário. Clique <a class='pure-button' href=http://app.test/lpi/code/login >aqui</a> para verificar o estado dos seu eventos.";

        $userdate=explode("/",$InfoEvento['data']);
        $userdate=implode("-",$userdate);
        var_dump($InfoUser['email_pessoal']);


        $ics = new ICS(array(
          'location' => "UFP- sala: ".$InfoEvento['sala'],
          'description' => "Evento: ".$InfoEvento['nome']." para o projeto ".$InfoProjeto['nome'],
          'dtstart' => $userdate." ".$InfoEvento['hora_inicio'],
          'dtend' => $userdate." ".$InfoEvento['hora_fim'],
          'summary' => "Evento: ".$InfoEvento['nome'],
          'url' => $InfoEvento['cacifo_digital_id']
        ));
        $ics=base64_encode($ics->to_string());

        $mail->AddStringAttachment(base64_decode($ics),"Evento.ics",'base64', "text/calendar");
        //$mail->addAttachment();
        $mail->send();
      }
    }
    catch (Exception $e)
    {
      echo $email.'Mailer Error:'. $mail->ErrorInfo;
      return false;
    }
  }
  //função para notificar pedido de Reunião
  public function mail_send_evento_notify($email_convidado)
  {
    $ask=new Ask();
    //$InfoUserSender=$ask->getUserInfo($perfil_id);
    $InfoUserReciever=$ask->getUserPerfilId($email_convidado);
    $InfoUserReciever=$ask->getUserInfo($InfoUserReciever);

    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
      //Server settings
      $mail->SMTPDebug = 0;                                 // Enable verbose debug output
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
      $mail->Password = 'Labproaulas10';                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;

      // TCP port to connect to
      //Recipients
      $mail->setFrom('labproaulas@gmail.com','Pedido de evento');
      $mail->addAddress($InfoUserReciever['email'],$InfoUserReciever['user_name']);     // Add recipient
      $mail->setFrom('labproaulas@gmail.com','Notificacao de evento');
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = 'Notificacao de evento';
      $mail->Body  = "Foi-lhe enviado o pedido de reunião. Clique <a class='pure-button' href=http://app.test/lpi/code/login >aqui</a> para verificar o estado dos seu eventos.";

      $mail->send();

      $mail->ClearAddresses();  // each AddAddress add to list
      $mail->ClearCCs();
      $mail->ClearBCCs();
      $mail->clearAttachments();

      if(!empty($InfoUserReciever['email_pessoal']) && $InfoUserReciever['is_email_pessoal_valid']==1 )
      {
        $mail->setFrom('labproaulas@gmail.com','Pedido confirmado');
        $mail->addAddress($InfoUserReciever['email_pessoal'],$InfoUserReciever['user_name']);     // Add recipient
        $mail->setFrom('labproaulas@gmail.com','Pedido de notificação');
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Notificacao de evento';
        $mail->Body  = "Foi-lhe enviado o pedido de reunião. Clique <a class='pure-button' href=http://app.test/lpi/code/login >aqui</a> para verificar o estado dos seu eventos.";
        $mail->send();
      }
      return true;
    }
    catch (Exception $e)
    {
      echo $email.'Mailer Error:'. $mail->ErrorInfo;
      return false;
    }
  }



  //função para recuperar palavra-passe do utilizador
  public function mail_Send_recover($email)
  {
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
      //Server settings
      $mail->SMTPDebug = 0;                                 // Enable verbose debug output
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
      $mail->Password = 'Labproaulas10';                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;

      // TCP port to connect to

      $getInfo = new AccessDB("SELECT perfil_id,user_name,email FROM utilizador where email="."'".$email."'");
      $response = $getInfo->procurar();
      while($row = mysqli_fetch_array($response))
      {
        $perfil_id=$row['perfil_id'];
        $nome=$row['user_name'];
      }
      if(empty($perfil_id))
      {
        return false;
      }
      //Recipients
      $mail->setFrom('labproaulas@gmail.com','Recuperacao de palavra-passe');
      $mail->addAddress($email,$nome);     // Add recipient
      //$mail->addAddress('ellen@example.com');               // Name is optional
      //$mail->addReplyTo('info@example.com', 'Information');
      //$mail->addCC('cc@example.com');
      //$mail->addBCC('bcc@example.com');

      //Attachments
      //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
      //$mail->addAttachment(__DIR__."/".'recover.pdf', 'users&id');    // Optional name

      //Envia um texto com o link para rederecionar o utilizador para puder inserir uma nova palavra passe
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = 'Recuperacao de palavra-passe';
      $mail->Body    = 'Lamentamos saber que nao se lembra ou perdeu a sua palavra-passe. Clique '.'<a '.'class="pure-button"'.' href=http://app.test/lpi/code/recuperarpassword?id='. $perfil_id .'>aqui</a>'.' para recuperar a sua palavra-passe.(Só pode pedir para recuperar password de 8 em 8 horas)';
      //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

      $mail->send();
      //echo 'Message has been sent';
      return true;
    } catch (Exception $e) {
      echo $email.'Mailer Error:'. $mail->ErrorInfo;
      return false;
    }
  }

  public function mail_Send_confirmation($email)
  {
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
      //Server settings
      $mail->SMTPDebug = 0;                                 // Enable verbose debug output
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
      $mail->Password = 'Labproaulas10';                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;

      $getInfo = new AccessDB("SELECT tipo,user_name,perfil_id FROM utilizador where email="."'".$email."'");
      $response = $getInfo->procurar();
      while($row = mysqli_fetch_array($response))
      {
        $tipo= $row['tipo'];
        $pid=$row['perfil_id'];
        $nome=$row['user_name'];
      }
      if($tipo==1)
      {
        $getInfo = new AccessDB("SELECT email_pessoal FROM utilizador where email="."'".$email."'");
        $response = $getInfo->procurar();
        while($row = mysqli_fetch_array($response))
        {
          $pemail=$row['email_pessoal'];
        }

      }
      if(empty($pid))
      {
        return "error";
      }
      //Recipients
      $mail->setFrom('labproaulas@gmail.com','Confirmacao de conta');
      $mail->addAddress($email,$nome);     // Add recipient

      //Envia um texto com o link para rederecionar o utilizador para puder inserir uma nova palavra passe
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = 'Confirmacao de conta';
      if(empty($pemail))
      {
        $mail->Body    = 'Sr./Sra.'.$user_name.'. Bem vindo à nossa plataforma ! Clique '.'<a '.'class="pure-button"'.' href=http://app.test/lpi/code/scripts/confirmaccount?pid='.$pid.'&ppid='. 0 .'>aqui</a>'.' para confirmar a sua conta.';
        }
        else {
          $mail->Body    = 'Sr./Sra.'.$user_name.'. Bem vindo à nossa plataforma ! Clique '.'<a '.'class="pure-button"'.' href=http://app.test/lpi/code/scripts/confirmaccount?pid='.$pid.'&ppid='. 1 .'>aqui</a>'.' para confirmar a sua conta.';
          }
          //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

          $mail->send();
          //echo 'Message has been sent';
        } catch (Exception $e) {
          echo $email.'Mailer Error:'. $mail->ErrorInfo;
          return false;
        }
      }
      public function mail_Send_recover_confirmation($email)
      {
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
          //Server settings
          $mail->SMTPDebug = 0;                                 // Enable verbose debug output
          $mail->isSMTP();                                      // Set mailer to use SMTP
          $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
          $mail->SMTPAuth = true;                               // Enable SMTP authentication
          $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
          $mail->Password = 'Labproaulas10';                           // SMTP password
          $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
          $mail->Port = 587;

          $getInfo = new AccessDB("SELECT tipo,user_name,perfil_id FROM utilizador where email="."'".$email."'");
          $response = $getInfo->procurar();
          while($row = mysqli_fetch_array($response))
          {
            $tipo= $row['tipo'];
            $pid=$row['perfil_id'];
            $nome=$row['user_name'];
          }
          if($tipo==1)
          {
            $getInfo = new AccessDB("SELECT email_pessoal FROM utilizador where email="."'".$email."'");
            $response = $getInfo->procurar();
            while($row = mysqli_fetch_array($response))
            {
              $pemail=$row['email_pessoal'];
            }

          }
          if(empty($pid))
          {
            return false;
          }
          //Recipients
          $mail->setFrom('labproaulas@gmail.com','Recuperacao de conta');
          $mail->addAddress($email,$nome);     // Add recipient

          //Envia um texto com o link para rederecionar o utilizador para puder inserir uma nova palavra passe
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = 'Re-confirmacao de conta';
          if(empty($pemail))
          {
            $mail->Body    = 'Sr./Sra.'.$user_name.'. Clique '.'<a '.'class="pure-button"'.' href=http://app.test/lpi/code/scripts/confirmaccount?pid='.$pid.'&ppid='. 0 .'>aqui</a>'.' para recuperar a sua conta.';
          }
          else {
            $mail->Body    = 'Sr./Sra.'.$user_name.'. Clique '.'<a '.'class="pure-button"'.' href=http://app.test/lpi/code/scripts/confirmaccount?pid='.$pid.'&ppid='. 1 .'>aqui</a>'.' para recuperar a sua conta.';
          }
          //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
          $mail->send();
          //echo 'Message has been sent';
        } catch (Exception $e) {
          echo $email.'Mailer Error:'. $mail->ErrorInfo;
          return false;
        }
      }
      public function mail_send_code($email,$perfil)
      {
        // Import PHPMailer classes into the global namespace
        // These must be at the top of your script, not inside a function
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
          //Server settings
          $mail->SMTPDebug = 0;                                 // Enable verbose debug output
          $mail->isSMTP();                                      // Set mailer to use SMTP
          $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
          $mail->SMTPAuth = true;                               // Enable SMTP authentication
          $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
          $mail->Password = 'Labproaulas10';                           // SMTP password
          $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
          $mail->Port = 587;

          $getInfo = new AccessDB("SELECT user_name FROM utilizador where perfil_id=".$perfil);
          $response = $getInfo->procurar();
          while($row = mysqli_fetch_array($response))
          {
            $nome=$row['user_name'];
          }
          $getcode= new Ask();
          $code = $getcode->geraCodigoRegisto();
          if(!$code)
          {
            return false;
          }
          $mail->setFrom('labproaulas@gmail.com','Convite do professor/a '.$nome);
          $mail->addAddress($email);     // Add recipient

          //Envia um texto com o link para rederecionar o utilizador para puder inserir uma nova palavra passe
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = 'Convite para aderir a ';
          $mail->Body    = 'Sr./Sra. Bem vindo à nossa plataforma ! Clique '.'<a '.'class="pure-button"'.' href=http://app.test/lpi/code/preregisto >aqui</a>'.' para efetuar o seu registo. Código secreto : '.$code. '. Este código tem validade de 8horas e só é valido 1 vez';
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            //echo 'Message has been sent';


          } catch (Exception $e) {
            echo $email.'Mailer Error:'. $mail->ErrorInfo;
          }
        }
        public function mail_send_project($email,$perfil_id,$nome_projecto)
        {
          // Import PHPMailer classes into the global namespace
          // These must be at the top of your script, not inside a function
          $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
          try {
            //Server settings
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
            $mail->Password = 'Labproaulas10';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;

            $getInfo = new AccessDB("SELECT user_name FROM utilizador where perfil_id=".$perfil_id);
            $response = $getInfo->procurar();
            while($row = mysqli_fetch_array($response))
            {
              $nome=$row['user_name'];
            }
            $mail->setFrom('labproaulas@gmail.com','Convite do professor/a '.$nome);
            $mail->addAddress($email);     // Add recipient

            //Envia um texto com o link para rederecionar o utilizador para puder inserir uma nova palavra passe
            $mail->isHTML(false);                                  // Set email format to HTML
            $mail->Subject = 'Adicionado ao Projeto - '.$nome_projecto;
            $mail->Body    = 'Sr./Sra. Foi adicionado ao projeto - '.$nome_projecto.' por o professsor '.$nome.' !';
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            //echo 'Message has been sent';


          } catch (Exception $e) {
            echo $email.'Mailer Error:'. $mail->ErrorInfo;
          }
        }
        public function mailSendCronJob($cicloEvents,$user)
        {
          $ask=new Ask();
          $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
          try {
            //Server settings
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
            $mail->Password = 'Labproaulas10';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;

            // TCP port to connect to
            //Recipients
            $mail->setFrom('labproaulas@gmail.com','Evento confirmado');
            $mail->addAddress($user['email'],$user['user_name']);     // Add recipient
            $mail->setFrom('labproaulas@gmail.com','Notificacao de eventos');
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Notificacao de eventos confirmados';
            $mail->Body  = "Em anexo envia-mos todos os ficheiro de eventos confirmados que pode adicionar ao seu calendário. Clique <a class='pure-button' href=http://app.test/lpi/code/login >aqui</a> para verificar o estado dos seu eventos.";

            while ($evento= mysqli_fetch_assoc($cicloEvents)) {

              $InfoProjeto=$ask->getProjectBasedID($evento['project_id']);
              $userdate=explode("/",$evento['data']);
              $userdate=implode("-",$userdate);

              $ics = new ICS(array(
                'location' => "UFP- sala: ".$evento['sala'],
                'description' => "Evento: ".$evento['nome']." para o projeto ".$InfoProjeto['nome'],
                'dtstart' => $userdate." ".$evento['hora_inicio'],
                'dtend' => $userdate." ".$evento['hora_fim'],
                'summary' => "Evento: ".$evento['nome'],
                'url' => $evento['cacifo_digital_id']
              ));
              $ics=base64_encode($ics->to_string());

              $mail->AddStringAttachment(base64_decode($ics),"Evento.ics",'base64', "text/calendar");
              
            }

            //$mail->addAttachment();
            $mail->send();

            $mail->ClearAddresses();  // each AddAddress add to list
            $mail->ClearCCs();
            $mail->ClearBCCs();
            if(!empty($user['email_pessoal']) && $user['is_email_pessoal_valid']==1 )
            {
              $mail->setFrom('labproaulas@gmail.com','Evento confirmado');
              $mail->addAddress($user['email_pessoal'],$user['user_name']);     // Add recipient
              $mail->setFrom('labproaulas@gmail.com','Notificacao de eventos');
              $mail->isHTML(true);                                  // Set email format to HTML
              $mail->Subject = 'Notificacao de eventos confirmados';
              $mail->Body  = "Em anexo envia-mos todos os ficheiro de eventos confirmados que pode adicionar ao seu calendário. Clique <a class='pure-button' href=http://app.test/lpi/code/login >aqui</a> para verificar o estado dos seu eventos.";

              $mail->send();
              $mail->clearAttachments();
            }
          }
          catch (Exception $e)
          {
            echo $email.'Mailer Error:'. $mail->ErrorInfo;
            return false;
          }
        }
        public function mail_sendVcard($userPerfilID,$vcf)
        {
          $ask= new Ask();
          $data= $ask->getUserInfo($userPerfilID);
          $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
          try {
            //Server settings
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'labproaulas@gmail.com';                 // SMTP username
            $mail->Password = 'Labproaulas10';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;

            // TCP port to connect to
            //Recipients
            $mail->setFrom('labproaulas@gmail.com','Evento confirmado');
            $mail->addAddress($data['email'],$data['user_name']);     // Add recipient
            $mail->setFrom('labproaulas@gmail.com','Contacto .vcf');
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Envio de contacto vcf';
            $mail->Body  = "Em anexo e enviado o ficheiro de contacto que pode adicionar á sua lista de contacto! Clique <a class='pure-button' href=http://app.test/lpi/code/login >aqui</a> para voltar a página principal.";
            $mail->AddStringAttachment(base64_decode($vcf),"vCard.vcf",'base64', "text/x-vcard");
            //$mail->addAttachment();
            $mail->send();
          }
          catch (Exception $e)
          {
            echo $email.'Mailer Error:'. $mail->ErrorInfo;
            return false;
          }
        }
      }
      ?>
