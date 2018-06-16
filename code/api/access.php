<?php

/*$aux="ola123";
$hash=hash("sha256",$aux);
$test=password_hash("123",PASSWORD_DEFAULT);//$password=password_hash("ola",$hash);
echo "pass: ".$test;*/

//session_start();
require_once 'access_db.php';
require_once 'send_email.php';
require_once 'ask.php';
// Class para acesso a pagina , isto , login , registo , recuperação
/*
* Classe genérica de acesso
*/
class Access
{

  private $email;
  private $password;

  public function __construct($email,$password)
  {
    $this->email=$email;
    $this->password=$password;
  }

  /*
  * Esta função encripta a password inserida pelo utilizador, acrescenta tambem, concatenando á password inserida, o número do email, com que o aluno foi inscrito, o email da universidade,
  *efetua a encriptação da palavra passe mais número e guarda o salt,na base de dados para recuperação no login
  */
  public function encrypt_password(){
    $options=array('cost'=>11);
    $pass=$this->getPassword();
    $mail=$this->getEmail();
    for ($i=0; $i < strlen($mail); $i++) {
      if ($mail[$i]=='@') {
        break;
      }
    }

    $num_aluno=substr($mail,0,$i);
    $aux=$this->getPassword().$num_aluno;

    $hashed=password_hash($aux, PASSWORD_BCRYPT, $options);
    if (password_verify($aux,$hashed)) {
      if (password_needs_rehash($hashed,PASSWORD_DEFAULT, $options)) {
        $hashed=password_hash($aux,PASSWORD_DEFAULT, $options);
      }
    }

    $data=array(
      "hashed" => $hashed,
      "salt" => $num_aluno
    );
    $data=json_encode($data);

    return $data;//$hashed;
  }
  public function encrypt_password_new($pass){
    $options=array('cost'=>11);
    $mail=$this->getEmail();
    $i=0;
    for ($i=0; $i < strlen($mail); $i++) {
      if ($mail[$i]=='@') {
        break;
      }
    }

    $num_aluno=substr($mail,0,$i);
    $aux=$pass.$num_aluno;

    $hashed=password_hash($aux, PASSWORD_BCRYPT, $options);
    if (password_verify($aux,$hashed)) {
      if (password_needs_rehash($hashed,PASSWORD_DEFAULT, $options)) {
        $hashed=password_hash($aux,PASSWORD_DEFAULT, $options);
      }
    }

    $data=array(
      "hashed" => $hashed
    );
    $data=json_encode($data);

    return $data;//$hashed;
  }

  public function getEmail(){
    return $this->email;
  }

  public function setEmail($email){
    $this->email=$email;
  }

  public function getPassword(){
    return $this->password;
  }

  public function setPassword($password){
    $this->password=$password;
  }
}

/**
*Login - classe que permite executar o login, quer para o utilizar (aluno), quer para o orientador
*/
class Login extends Access
{

  public function __construct($email,$password)
  {
    parent::__construct($email,$password);
  }

  /**
  * Função que permite efetuar o login na apliacação
  */
  public function login(){

    $stuff = new AccessDB("SELECT id,user_name,perfil_id,password,email,is_email_valid FROM utilizador");
    $stuff = $stuff->procurar();
    $email=$this->getEmail();
    while($row = mysqli_fetch_array($stuff))
    {
      //$pass=str_replace($row['hash'],"",$row['password']);
      if(strcmp($row['email'],$email)==0 && $row['is_email_valid']==1) //$_POST['user_name']
      {
        $aux = new Ask();
        $prefix=$aux->getEmailPrefix($email);
        $pass=$this->getPassword().$prefix;
        if(password_verify($pass,$row['password'])) //$_POST['password'] $row['password']
        {
          $data=array(
            "user_name" => $row['user_name'],
            "perfil_id" => $row['perfil_id']
          );
          $data=json_encode($data);
          return $data;//"ok"
        }
      }
    }
    return false;
  }

}

/**
*Registo - classe que permite executar o registo
*/
class Registo extends Access
{
  private $perfil_id;
  private $user_name;
  private $is_email_valid;
  private $tipo;
  private $foto;
  private $foto_ex;
  private $contacto;
  private $skype_id;
  private $cacifo_digital_id;
  private $notificacao;

  public function __construct($perfil_id, $user_name, $email,$password, $is_email_valid, $tipo, $foto,$foto_ex, $contacto, $skype_id,$cacifo_digital_id,$notificacao)
  {
    $this->user_name=$user_name;
    $this->is_email_valid=$is_email_valid;
    $this->tipo=$tipo;
    $this->foto=$foto;
    $this->foto_ex= $foto_ex;
    $this->contacto=$contacto;
    $this->skype_id=$skype_id;
    $this->cacifo_digital_id=$cacifo_digital_id;
    $this->notificacao=$notificacao;
    parent::__construct($email,$password);
  }

  public function tipoUtilizador($email)
  {
    for ($i=0; $i < strlen($email); $i++) {
      if ($email[$i]=='@') {
        break;
      }
    }
    $mailprefix=substr($email,0,$i);
    if(is_numeric($mailprefix))
    {
      //indica que o tipo é aluno
      return 1;
    }
    //indica que o tipo é professor
    return 0;
  }

  /**
  * Gera um perfil_id, para cada utilizador
  */
  public function gerarPerfilID($email)
  {
    for ($i=0; $i < strlen($email); $i++) {
      if ($email[$i]=='@') {
        break;
      }
    }
    $mailprefix=substr($email,0,$i);
    $rand=rand(1000,10000);

    $min = new Ask();
    $respmin=$min->getMaxPerfilID();
    $responsecheck= new Ask();
    if(is_numeric($mailprefix))
    {
      //indica que o tipo é aluno
      $num_perfil_ID='11'.$rand;
      while (1) {
        $aux=$responsecheck->checkPerfilID($num_perfil_ID);
        if ($aux==false) {
          $rand=rand($respmin,10000+$respmin);
          $num_perfil_ID='11'.$rand;
        }else {
          return $num_perfil_ID;
        }
      }
      //indica que o tipo é professor
    }
    else {
      $num_perfil_ID='00'.$rand;
      while (1) {
        $aux=$responsecheck->checkPerfilID($num_perfil_ID);
        if ($aux==false) {
          $rand=rand($respmin,10000+$respmin);
          $num_perfil_ID='00'.$rand;
        }else {
          return $num_perfil_ID;
        }
      }
    }
  }

  public function getUser_name(){
    return $this->user_name;
  }

  public function setUser_name($user_name){
    $this->user_name=$user_name;
  }

  public function getTipo(){
    return $this->tipo;
  }

  public function setTipo($tipo){
    $this->tipo=$tipo;
  }

  public function getFoto(){
    return $this->foto;
  }

  public function setFoto($foto){
    $this->foto=$foto;
  }
  public function getFoto_ex(){
    return $this->foto_ex;
  }

  public function setFoto_ex($foto_ex){
    $this->foto_ex=$foto_ex;
  }

  public function getContacto(){
    return $this->contacto;
  }

  public function setContacto($contacto){
    $this->contacto=$contacto;
  }

  public function getPerfil_id(){
    return $this->perfil_id;
  }

  public function setPerfil_id($perfil_id){
    $this->perfil_id=$perfil_id;
  }
  public function getIs_email_valid(){
    return $this->is_email_valid;
  }

  public function setIs_email_valid($is_email_valid){
    $this->is_email_valid=$is_email_valid;
  }
  public function getSkype_id(){
    return $this->skype_id;
  }

  public function setSkype_id($skype_id){
    $this->skype_id=$skype_id;
  }

  public function getCacifo_digital_id(){
    return $this->cacifo_digital_id;
  }

  public function setCacifo_digital_id($cacifo_digital_id){
    $this->cacifo_digital_id=$cacifo_digital_id;
  }

  public function getNotificacao(){
    return $this->notificacao;
  }

  public function setNotificacao($notificacao){
    $this->notificacao=$notificacao;
  }
}

/**
* Classe de registo para aluno
*/
class RegistoProf extends Registo
{

  public function __construct($user_name, $email, $password, $foto,$foto_ex, $contacto, $skype_id, $cacifo_digital_id, $notificacao)
  {
    parent::__construct($this->gerarPerfilID($email), $user_name, $email,$password, 0, 0, $foto,$foto_ex,$contacto, $skype_id,$cacifo_digital_id,$notificacao);
  }

  public function registoProf(){
    $ax=$this->encrypt_password();
    $obj=json_decode($ax);
    $this->setPassword($obj->{'hashed'});
    $this->setPerfil_id($this->gerarPerfilID($this->getEmail()));

    $perfil_id=$this->getPerfil_id();
    $user_name=$this->getUser_name();
    $password=$this->getPassword();
    $email=$this->getEmail();
    $is_email_valid=$this->getIs_email_valid();
    $password=$this->getPassword();
    $tipo=$this->getTipo();
    $foto=$this->getFoto();
    if ($foto==false) {
      $foto=null;
    }else {
      $foto_aux = new Ask();
      $foto=$foto_aux->generateThumbnail($foto);
    }
    $foto_ex=$this->getFoto_ex();
    if ($foto_ex==false) {
      $foto_ex=null;
    }
    $contacto=$this->getContacto();
    $skype_id=$this->getSkype_id();
    $cacifo_digital_id=$this->getCacifo_digital_id();
    $notificacao=$this->getNotificacao();

    $sql="INSERT INTO utilizador (perfil_id, user_name, email, is_email_valid, password, tipo, foto,foto_tipo,contacto, skype_id, cacifo_digital_id, notificacao) VALUES ('$perfil_id','$user_name','$email','$is_email_valid','$password','$tipo','$foto','$foto_ex','$contacto','$skype_id','$cacifo_digital_id','$notificacao')";
    $aux = new AccessDB($sql);
    $ax=$aux->adicionar();
    if ($ax==false) {
      return false;
    }
    $mail_Send_confirmation = new Mail();
    $mail_Send_confirmation->mail_Send_confirmation($email);
    return true;
  }

}

/**
* Classe de registo para Alunos
*/
class RegistoAluno extends Registo
{

  private $formacao_avancada;
  private $email_pessoal;
  private $is_email_pessoal_valid;

  function __construct($user_name, $email, $email_pessoal, $password, $formacao_avancada, $foto,$foto_ex, $contacto, $skype_id, $cacifo_digital_id, $notificacao)
  {
    $this->formacao_avancada=$formacao_avancada;
    $this->email_pessoal=$email_pessoal;
    $this->is_email_pessoal_valid=0;
    parent::__construct($this->gerarPerfilID($email), $user_name, $email,$password, 0, 1, $foto,$foto_ex, $contacto, $skype_id,$cacifo_digital_id,$notificacao);
  }

  public function registoAluno(){
    $ax=$this->encrypt_password();
    $obj=json_decode($ax);
    $this->setPassword($obj->{'hashed'});
    $this->setPerfil_id($this->gerarPerfilID($this->getEmail()));

    $perfil_id=$this->getPerfil_id();
    $user_name=$this->getUser_name();
    $password=$this->getPassword();
    $email=$this->getEmail();
    $email_pessoal=$this->getEmail_pessoal();
    $is_email_valid=$this->getIs_email_valid();
    $is_email_pessoal_valid=$this->getIs_email_pessoal_valid();
    $password=$this->getPassword();
    $tipo=$this->getTipo();
    $formacao_avancada=$this->getFormacao_avancada();
    $foto=$this->getFoto();
    if ($foto==false) {
      $foto=null;
    }else {
      $foto_aux = new Ask();
      $foto=$foto_aux->generateThumbnail($foto);
    }
    $foto_ex=$this->getFoto_ex();
    if ($foto_ex==false) {
      $foto_ex=null;
    }
    $contacto=$this->getContacto();
    $skype_id=$this->getSkype_id();
    $cacifo_digital_id=$this->getCacifo_digital_id();
    $notificacao=$this->getNotificacao();
    $sql="INSERT INTO utilizador (perfil_id, user_name, email, email_pessoal,is_email_valid, is_email_pessoal_valid,password, tipo, formacao_avancada,foto,foto_tipo,contacto, skype_id, cacifo_digital_id, notificacao) VALUES ('$perfil_id','$user_name','$email','$email_pessoal','$is_email_valid','$is_email_pessoal_valid','$password','$tipo','$formacao_avancada','$foto','$foto_ex','$contacto','$skype_id','$cacifo_digital_id','$notificacao')";
    $aux = new AccessDB($sql);
    $aux->adicionar();
    if ($aux==false) {
      return false;
    }
    $mail_Send_confirmation = new Mail();
    $mail_Send_confirmation->mail_Send_confirmation($email);
    return true;
  }

  public function getFormacao_avancada(){
    return $this->formacao_avancada;
  }

  public function setFormacao_avancada($formacao_avancada){
    $this->formacao_avancada=$formacao_avancada;
  }

  public function getEmail_pessoal(){
    return $this->email_pessoal;
  }

  public function setEmail_pessoal($email_pessoal){
    $this->email_pessoal=$email_pessoal;
  }

  public function getIs_email_pessoal_valid(){
    return $this->is_email_pessoal_valid;
  }

  public function setIs_email_pessoal_valid($is_email_pessoal_valid){
    $this->is_email_pessoal_valid=$is_email_pessoal_valid;
  }
}


/**
*Recuperação - classe que permite executar a recuperação da password, quer para o utilizar (aluno), que para o orientador
*/
class Recuperacao extends Access
{
  //nota: funcao chama-se recuperar, id, password velha, password nova
  private $perfil_id;
  private $password_nova;

  public function __construct($email,$password,$perfil_id,$password_nova)
  {
    $this->perfil_id=$perfil_id;
    $this->password_nova=$password;
    parent::__construct($email,$password_nova);
  }

  public function recuperar()
  {
    $sendEmail=$this->getEmail();
    $password=$this->getPassword();
    $new_password=$this->getPassword_nova();
    $ax=$this->encrypt_password_new($new_password);
    $obj=json_decode($ax);
    $this->setPassword($obj->{'hashed'});
    $password_nova=$obj->{'hashed'};

    $perfil_id=$this->getPerfil_id();
    $sql="UPDATE utilizador SET password='$password_nova' WHERE perfil_id=".$perfil_id;
    $upd=new AccessDB($sql);
    $upd->atualizar();

    $mail_Send_recover = new Mail();
    if($mail_Send_recover->mail_Send_recover_confirmation($sendEmail))
    {
      return true;
    }
    return $sendEmail;
  }

  public function getPerfil_id(){
    return $this->perfil_id;
  }
  public function setPerfil_id($pid){
    return $this->$perfil_id=$id;
  }

  public function getPassword_nova(){
    return $this->password_nova;
  }

  public function setPassword_nova($password_nova){
    $this->password_nova=$password_nova;
  }
}
?>
