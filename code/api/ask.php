<?php
// este ficehiro deve ser usado para fazer perguntas em relação ao estado de variaveis ou à base de dados
require_once "access_db.php";
require_once "access.php";


class Ask{



  public function updateInfoStudent($perfil_id,$user_name,$email,$pemail,$password,$formacao,$foto,$foto_ex,$contacto,$skype_id,$cacifo_id,$notificacao)
  {
    $newcrypt= new Access($email,$password);
    $hashedpass= $newcrypt->encrypt_password();
    $hashedpass=json_decode($hashedpass);
    $hashedpass=$hashedpass->{'hashed'};
    if(!empty($foto))
    {
      $fotothumb=$this->generateThumbnail($foto);
      $sql="UPDATE utilizador SET user_name='$user_name',email_pessoal='$pemail',password='$hashedpass',formacao_avancada='$formacao',foto='$fotothumb',foto_tipo='$foto_ex',contacto='$contacto',skype_id='$skype_id',cacifo_digital_id='cacifo_id',notificacao='$notificacao' where perfil_id=".$perfil_id;
      $aux = new AccessDB($sql);
      if(!$aux->atualizar())
      {
        return false;
      }
      return true;
    }
    $sql="UPDATE utilizador SET user_name='$user_name',email_pessoal='$pemail',password='$hashedpass',formacao_avancada='$formacao',contacto='$contacto',skype_id='$skype_id',cacifo_digital_id='$cacifo_id',notificacao='$notificacao' where perfil_id=".$perfil_id;
    $aux = new AccessDB($sql);
    if(!$aux->atualizar())
    {
      return false;
    }
    return true;
  }
  public function updateInfoProf($user_name,$email,$password,$foto,$foto_ex,$contacto,$skype_id,$cacifo_id,$notificacao)
  {
    $newcrypt= new Access($email,$password);
    $hashedpass= $newcrypt->encrypt_password();
    $hashedpass=json_decode($hashedpass);
    $hashedpass=$hashedpass->{'hashed'};
    if(!empty($foto))
    {
      $fotothumb=$this->generateThumbnail($foto);
      $sql="UPDATE utilizador SET user_name='$user_name',password='$hashedpass',foto='$fotothumb',foto_tipo='$foto_ex',contacto='$contacto',skype_id='$skype_id',cacifo_digital_id='cacifo_id',notificacao='$notificacao' where perfil_id=".$perfil_id;
      $aux = new AccessDB($sql);
      if(!$aux->atualizar())
      {
        return false;
      }
      return true;
    }
    $sql="UPDATE utilizador SET user_name='$user_name',password='$hashedpass',contacto='$contacto',skype_id='$skype_id',cacifo_digital_id='$cacifo_id',notificacao='$notificacao' where perfil_id=".$perfil_id;
    $aux = new AccessDB($sql);
    if(!$aux->atualizar())
    {
      return false;
    }
    return true;

  }
  /*
  * Função que retorna todos os perfil ids dos alunos com um projeto de uma area especifica
  */
  public function getAllPerfilIDsAlunosFormacaoAvancada($formacao_avancada){
    $sql="SELECT perfil_id FROM utilizador WHERE formacao_avancada=".$formacao_avancada." AND id IN(SELECT utilizador_id FROM projeto)";
    $get = new AccessDB($sql);
    $response=$get->procurar();
    if (empty($response)) {
      return false;
    }
    while ($row=mysqli_fetch_array($response)) {
      $aux=$row['perfil_id'];
      $aux=$aux[0].$aux[1];
      if(strcmp($aux,'11')==0)
      $perfil_id[]=$row['perfil_id'];
    }
    return $perfil_id;
  }

  /*
  * Função que retorna todos os perfil ids dos alunos com um projeto de uma area especifica
  */
  public function getAllPerfilIDsAlunosArea($area){
    $sql="SELECT perfil_id FROM utilizador WHERE id IN (select utilizador_id from projeto where area="."'".$area."'".")";
    $get = new AccessDB($sql);
    $response=$get->procurar();
    if (empty($response)) {
      return false;
    }
    while ($row=mysqli_fetch_array($response)) {
      $aux=$row['perfil_id'];
      $aux=$aux[0].$aux[1];
      if(strcmp($aux,'11')==0)
      $perfil_id[]=$row['perfil_id'];
    }
    return $perfil_id;
  }

  /*
  * Função que retorna todos os perfil ids dos alunos
  */
  public function getAllPerfilIDsAlunos(){
    $sql="SELECT utilizador_id FROM projeto";
    $get = new AccessDB($sql);
    $response=$get->procurar();
    while ($row=mysqli_fetch_array($response)) {
      $id[]=$row['utilizador_id'];
    }
    $size=sizeof($id);
    for ($i=0; $i < $size; $i++) {
      $get = new AccessDB("SELECT perfil_id FROM utilizador WHERE id=".$id[$i]);
      $response=$get->procurar();
      while ($row=mysqli_fetch_array($response)) {
        $aux=$row['perfil_id'];
        $aux=$aux[0].$aux[1];
        if(strcmp($aux,'11')==0)
        $perfil_id[]=$row['perfil_id'];
      }
    }
    return $perfil_id;
  }

  /*
  * Funcao que retorna a data de inicio de um projeto
  */
  public function getDataProjeto($perfil_id){
    $projeto_id = $this->getIDProjeto($perfil_id);
    $get = new AccessDB("SELECT data FROM projeto WHERE id=".$projeto_id);
    $response=$get->procurar();
    $data=mysqli_fetch_assoc($response);
    $data=$data['data'];
    return $data;
  }

  /*
  * Funcao que retorna a data final de um projeto, na tabela de resultado
  */
  public function getDataResultado($perfil_id){
    $projeto_id = $this->getIDProjeto($perfil_id);
    $get = new AccessDB("SELECT data FROM resultado WHERE projeto_id=".$projeto_id);
    $response=$get->procurar();
    $data=mysqli_fetch_assoc($response);
    $data=$data['data'];
    return $data;
  }
  /*
  * Funcao que retorna o id de um projeto
  */
  public function getIDProjeto($perfil_id){
    $id= $this->getUserId($perfil_id);
    $get = new AccessDB("SELECT id FROM projeto WHERE utilizador_id=".$id);
    $response=$get->procurar();
    $id=mysqli_fetch_assoc($response);
    $id=$id['id'];
    return $id;
  }

  public function searchOnlyStudent($search)
  {
    if(!is_numeric($search))
    {
      $response=$this->getUserInfoNameWFetch($search);
      return $response;
    }
    if(is_numeric($search))
    {
      $response=$this->getUserInfo($search);
      return $response;
    }
    return false;
  }
  //função de procura para a barra de pesquisa
  public function searchbar($search,$isProfessor)
  {
    $cache= new AcessCached();
    $packet=array();
    $search=explode("%20",$search);
    $search=implode(" ",$search);
    $isProfessorS=($isProfessor) ? 'true' : 'false';
    if($cache->getCacheMem("searchbar".$search.$isProfessorS))
    {
      return json_decode($cache->getCacheMem("searchbar".$search.$isProfessorS));
    }
    if($isProfessor)
    {
      if(!is_numeric($search))
      {
        $response=$this->getUserInfoNamePojectConnection($search,$_SESSION['perfil_id']);
        $packet[0]="utilizador";
        while($row=mysqli_fetch_assoc($response))
        {
          $packet[1][]=$row;
        }
        if(empty($packet[1]))
        {
          $packet[1][]="";
        }
        $response=$this->getEventSearch($search,$_SESSION['perfil_id']);
        $packet[2]="evento";
        while($row=mysqli_fetch_assoc($response))
        {
          $packet[3][]=$row;
        }
        if(strcmp($this->analyzeEmail($search),"error")!=0 && $this->isEmailValid($search)!=-1)
        {
          $response=$this->getUserPerfilId($search);
          $response=$this->getUserInfoNoFetch($response);
          $packet[4]="email";
          while($row=mysqli_fetch_assoc($response))
          {
            $packet[5][]=$row;
          }
        }
        $cache->saveCacheMem("searchbar".$search.$isProfessorS,json_encode($packet));
        return json_decode(json_encode($packet));
      }
      if(is_numeric($search))
      {
        $response=$this->getUserConnectionProfessor($search);
        $packet[0]="utilizador";
        while($row=mysqli_fetch_assoc($response))
        {
          $packet[1][]=$row;
        }
        $cache->saveCacheMem("searchbar".$search.$isProfessorS,json_encode($packet));
        return json_decode(json_encode($packet));
      }
    }
    else {
      if(!is_numeric($search))
      {
        $packet[0]="";
        $packet[1]="";
        $response=$this->getEventSearch($search,$_SESSION['perfil_id']);
        $packet[2]="evento";
        while($row=mysqli_fetch_assoc($response))
        {
          $packet[3][]=$row;
        }
        $cache->saveCacheMem("searchbar".$search.$isProfessorS,json_encode($packet));
        return json_decode(json_encode($packet));
      }
    }
  }
  /**
  * Função que envia um documento para a base de dados, mongoDB - AINDA NAO ACABADA
  */
  public function sendDoc($nome,$email,$perfil_id,$ficheiro,$tipo,$notas){
    $mongo = new AccessMongo();
    $accessdb_mongo = $mongo->insertDoc($nome,$ficheiro,$tipo,$notas,$perfil_id);
    if (empty($accessdb_mongo)) {
      return false;
    }
    if($this->saveDocSend($email,$perfil_id,$accessdb_mongo));
    {
      return true;
    }
    return false;
  }

  //Função que armazena no sistema o ID mongo , o utilizador e o receptor do documento
  public function saveDocSend($email,$perfil_id,$mongoId)
  {
    $id= $this->getUserId($perfil_id);
    $idRe= $this->getUserPerfilId($email);
    $idRe= $this->getUserId($idRe);
    $uID = new AccessDB("INSERT INTO files (utilizador_id,mongoID,receptor_id) values ('$id','$mongoId','$idRe')");
    if(!$uID->adicionar())
    {
      return false;
    }
    return true;
  }
  /**
  * Função que retorna o documento de um utilizador - AINDA NAO ACABADA
  */
  public function getDocumento($mongoId){
    $mongo = new AccessMongo();
    $documento=$mongo->getDocumentoMongo($mongoId);
    return $documento;
  }

  public function insertResult($pID,$classificacao,$notas)
  {
    $currentdate=date("Y/m/d");
    $currentdate=explode("/",$currentdate);
    $currentdate=implode("-",$currentdate);
    $uID = new AccessDB("INSERT INTO resultado (classificacao,notas,data,projeto_id) values ('$classificacao','$notas','$currentdate','$pID')");
    if(!$uID->adicionar())
    {
      return false;
    }
    return true;
  }
  public function insertProject($nome,$tema,$area,$coorientador,$emails,$perfil_idP)
  {
    $currentdate=date("Y/m/d");
    $currentdate=explode("/",$currentdate);
    $currentdate=implode("-",$currentdate);
    $project_id=null;
    if(!empty($coorientador))
    {
      $ask = new Ask();
      $response=$ask->analyzeEmail($emailproject[$i]);
      if(strcmp($response,"s")==0 || strcmp($response,"error")==0 )
      {
        return "error";
      }
      $query_idCOO= new AccessDB("SELECT id from utilizador where email="."'".$coorientador."'");
      $coorientador=$query_idCOO->procurar();
      $coorientador=mysqli_fetch_assoc($coorientador);
      $coorientador=$coorientador['id'];
    }
    $query_idP= new AccessDB("SELECT id from utilizador where perfil_id=".$perfil_idP);
    $IDP=$query_idP->procurar();
    $IDP=mysqli_fetch_assoc($IDP);
    $IDP=$IDP['id'];
    if(sizeof($emails)==1)
    {
      $query_idS= new AccessDB("SELECT id from utilizador where email="."'".$emails[0]."'");
      $IDS=$query_idS->procurar();
      $IDS=mysqli_fetch_assoc($IDS);
      $IDS=$IDS['id'];
      $uID = new AccessDB("INSERT INTO projeto (nome,area,tema,tipo,data,utilizador_id,coorientador_id,orientador_id,projeto_id) values ('$nome', '$area','$tema',0,'$currentdate','$IDS','$coorientador','$IDP','$project_id')");
      $aux=$uID->adicionar();
      if(!$aux)
      {
        return false;
      }
    }
    else {
      for($i=0;$i<sizeof($emails);$i++)
      {
        $query_idS= new AccessDB("SELECT id from utilizador where email="."'".$emails[$i]."'");
        $IDS=$query_idS->procurar();
        $IDS=mysqli_fetch_assoc($IDS);
        $IDS=$IDS['id'];
        $uID = new AccessDB("INSERT INTO projeto (nome,area,tema,tipo,utilizador_id,coorientador_id,orientador_id,projeto_id) values ('$nome', '$area','$tema',1,'$IDS','$coorientador','$IDP','$project_id')");
        $aux=$uID->adicionar();
        if(!$aux)
        {
          return false;
        }
        if($i==0)
        {
          $project_id=$aux;
        }
      }
    }
    return true;
  }


  //Funcao que permite ao professor/aluno inserir um evento
  //$email_convidado - no caso de ser professor a a convidar é email de aluno se for aluno a marcar evento é o email do orientador
  public function insertEvent($nome, $sala, $data, $hora_inicio, $hora_fim, $estado, $tipo, $contacto, $notas, $cacifo_digital_id, $skype_id, $perfil_id,$email_convidado ,$projectID)
  {
    $query_pid = new AccessDB("SELECT id from utilizador where perfil_id=".$perfil_id);
    $UID=$query_pid->procurar();
    $UID= mysqli_fetch_assoc($UID);
    $UID=$UID['id'];

    $email_convite = new AccessDB("SELECT id from utilizador where email="."'".$email_convidado."'");
    $PID=$email_convite->procurar();
    $PID= mysqli_fetch_assoc($PID);
    $PID=$PID['id'];

    if ($this->checkEventoExists($nome, $sala, $data, $hora_inicio, $hora_fim,$UID,$PID,$projectID)) {
      return false;
    }
    $query = new AccessDB("INSERT INTO evento (nome, sala, data, hora_inicio, hora_fim, estado, tipo, contacto, notas, cacifo_digital_id, skype_id, utilizador_id, convidado_id, project_id) values ('$nome', '$sala', '$data', '$hora_inicio', '$hora_fim', '$estado', '$tipo', '$contacto', '$notas', '$cacifo_digital_id', '$skype_id', '$UID', '$PID', '$projectID')");
    $aux=$query->adicionar();
    if (!$aux) {
      return false;
    }
    $mail= new Mail();
    if($mail->mail_send_evento_notify($email_convidado))
    {
      return true;
    }
    return true;
  }
  public function checkEventoExists($nome, $sala, $data, $hora_inicio, $hora_fim,$utilizador_id,$orientador_id,$project_id){
    $data=explode("-",$data);
    $data=$data[2].'-'.$data[1].'-'.$data[0];
    $data=date_create($data);
    foreach ($data as $key) {
      break;
    }
    $query = new AccessDB("SELECT * from evento"); //, DATE_FORMAT(data, '%d/%m/%Y') AS 'data'
    $aux=$query->procurar();
    if (!empty($auxa)) {
      while($row = mysqli_fetch_array($aux))
      {
        if (empty($row)) {
          return false;
        }
        $date=date_create($row['data']);
        foreach ($date as $key1) {
          break;
        }
        $diff=date_diff($key,$key1);

        if (strcmp($sala,$row['$sala'])==0 && $diff==0 && $hora_inicio==$row['hora_inicio'] && $hora_fim==$row['hora_fim'] && $orientador_id==$row['convidado_id']) {
          $query = new AccessDB("SELECT formacao_avancada from utilizador where id=".$row['utilizador_id']);
          $RUID=$query->procurar();
          $RUID= mysqli_fetch_assoc($RUID);
          $query = new AccessDB("SELECT formacao_avancada from utilizador where id=".$utilizador_id);
          $TestUID=$query->procurar();
          $TestUID= mysqli_fetch_assoc($TestUID);
          if($RUID!=$TestUID){
            return false;
          }
          else {
            return true;
          }
        }
      }
    }
    return false;
  }

  /**
  * procura a hora hora_inicio e hora_fim para um derterminado aluno, retorna false, se não existir retorna data
  */
  public function hora_inicio_fim_phd($perfil_ID, $tipo){
    $query_perfilID = new AccessDB("SELECT hora_inicio, hora_fim FROM evento WHERE perfil_id=".$perfil_id);
    $aux=$query_perfilID->procurar();

    while($row = mysqli_fetch_array($aux))
    {
      $hora_inicio=$row['hora_inicio'];
      $hora_fim=$row['hora_fim'];
    }

    $data=array(
      "hora_inicio" => $row['hora_inicio'],
      "hora_fim" => $row['hora_fim']
    );
    $data=json_encode($data);

    if (empty($data)) {
      return false;
    }
    return $data;
  }

  /**
  * Função que insere o horário de um professor na base de dados
  */
  public function horarioLivreJson($data,$email){
    $sql = "SELECT perfil_id FROM utilizador WHERE email="."'".$email."'";
    $aux = new AccessDB($sql);
    $ax=$aux->procurar();
    $perfil_id=mysqli_fetch_assoc($ax);
    if (empty($perfil_id)) {
      return false;
    }
    $mongo = new AccessMongo();
    $mongoId=$mongo->saveHorario($data,$perfil_id);

    $sql="UPDATE utilizador SET horario_livre='$mongoId' where email="."'".$email."'";
    $aux = new AccessDB($sql);
    $aux=$aux->atualizar();
    if(!$aux) {
      return false;
    }
    return true;
  }
  public function horarioLivreJsonUpdate($data,$perfil_id){
    $sql = "SELECT horario_livre FROM utilizador WHERE perfil_id=".$perfil_id;
    $aux = new AccessDB($sql);
    $ax=$aux->procurar();
    $mongoId=mysqli_fetch_assoc($ax);
    if (empty($mongoId)) {
      return false;
    }
    $mongo = new AccessMongo();
    $mongoId=$mongo->updateHorario($data,$mongoId);
    if($mongoId)
    {
      return true;
    }
    return false;
  }
  public function prepareHorario($id)
  {
    $aux=$this->getHorarioID($id);
    if(!$aux)
    {
      return false;
    }
    $horarioAux=array();
    $horario=array();
    $data=json_decode($aux);
    for($i=0;$i<sizeof($data);$i++)
    {
      $aux=explode("-",$data[$i]);
      $horarioAux[]=$aux;
    }
    for($i=0;$i<sizeof($horarioAux);$i+=2)
    {
      if(strcmp($horarioAux[$i][0],"segunda")==0)
      {
        $dow=1;
        $start=$horarioAux[$i][1];
        $end=$horarioAux[$i+1][1];
        $horario[]= array('dow' => "[".$dow."]",
        'start'=> $start,
        'end'=> $end
      );
    }
    if(strcmp($horarioAux[$i][0],"terca")==0)
    {
      $dow=2;
      $start=$horarioAux[$i][1];
      $end=$horarioAux[$i+1][1];
      $horario[]= array('dow' => "[".$dow."]",
      'start'=> $start,
      'end'=> $end
    );
  }
  if(strcmp($horarioAux[$i][0],"quarta")==0)
  {
    $dow=4;
    $start=$horarioAux[$i][1];
    $end=$horarioAux[$i+1][1];
    $horario[]= array('dow' => "[".$dow."]",
    'start'=> $start,
    'end'=> $end
  );
}
if(strcmp($horarioAux[$i][0],"quinta")==0)
{
  $dow=5;
  $start=$horarioAux[$i][1];
  $end=$horarioAux[$i+1][1];
  $horario[]= array('dow' => "[".$dow."]",
  'start'=> $start,
  'end'=> $end
);
}
if(strcmp($horarioAux[$i][0],"sexta")==0)
{
  $dow=6;
  $start=$horarioAux[$i][1];
  $end=$horarioAux[$i+1][1];
  $horario[]= array('dow' => "[".$dow."]",
  'start'=> $start,
  'end'=> $end
);
}
}
return $horario;
}
/**
* Função que retorna o horario de um professor com base no ID
*/
public function getHorarioID($id){
  $sql = "SELECT horario_livre FROM utilizador WHERE id=".$id;
  $aux = new AccessDB($sql);
  $aux=$aux->procurar();
  if(!$aux){
    return false;
  }
  $mongoId=mysqli_fetch_assoc($aux);
  if (empty($mongoId)) {
    return false;
  }

  $mongo = new AccessMongo();
  $horario=$mongo->getHorarioMongo($mongoId['horario_livre']);
  return $horario;
}

/**
* Função que retorna o horario de um professor
*/
public function getHorario($email){
  $sql = "SELECT horario_livre FROM utilizador WHERE email="."'".$email."'";
  $aux = new AccessDB($sql);
  $aux=$aux->procurar();
  $mongoId=mysqli_fetch_assoc($aux);
  if (empty($mongoId)) {
    return false;
  }

  $mongo = new AccessMongo();
  $horario=$mongo->getHorarioMongo($mongoId['horario_livre']);
  return $horario;
}
//função para criar thumbnail da imagem
function generateThumbnail($img)
{
  $width = 300;
  $height = 300;
  $quality = 85;
  $imageBlob= $this->getImage($img);
  $imagick = new Imagick();
  $imagick->readImageBlob($imageBlob);
  $imagick->setImageFormat('jpeg');
  $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
  $imagick->setImageCompressionQuality($quality);
  $imagick->thumbnailImage($width, $height, false, false);
  return base64_encode($imagick->getImageBlob());
}

public function checkUserProject($perfil_id)
{
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id FROM projeto WHERE utilizador_id=".$id." OR orientador_id=".$id);
  $response=$get->procurar();
  $aux=mysqli_fetch_assoc($response);
  if(!empty($aux['id']))
  {
    return true;
  }
  return false;
}
public function getEventsPendingSent($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome,sala,DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim',tipo,contacto,skype_id,notas,cacifo_digital_id,utilizador_id,convidado_id,project_id FROM evento WHERE estado=0 AND utilizador_id=".$id." AND data > CURDATE() ORDER BY data DESC");
  $response=$get->procurar();
  return $response;

}
public function getEventsPendingRecieved($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome,sala,DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim',tipo,contacto,skype_id,notas,cacifo_digital_id,utilizador_id,convidado_id,project_id FROM evento WHERE estado=0 AND convidado_id=".$id." AND data > CURDATE() ORDER BY data DESC");
  $response=$get->procurar();
  return $response;

}

public function getEventsAprovedCalendar($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome AS 'title',CONCAT(data,' ',hora_inicio) AS 'start', CONCAT(data,' ',hora_fim) AS 'end', notas ,sala, contacto FROM evento WHERE estado=1 AND (utilizador_id=".$id." OR convidado_id=".$id.") ORDER BY data");
  $response=$get->procurar();
  return $response;

}


public function getEventsAproved($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome,sala,DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim',tipo,contacto,skype_id,notas,cacifo_digital_id,utilizador_id,convidado_id,project_id FROM evento WHERE estado=1 AND (utilizador_id=".$id." OR convidado_id=".$id.") AND data > CURDATE() ORDER BY data DESC");
  $response=$get->procurar();
  return $response;

}

/**
* Retorna a data, hora inicio e hora fim, da primeira reuneao de um derterminado aluno com o seu orientador
*/
public function getEventFirst($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim' FROM evento WHERE estado=1 AND (utilizador_id=".$id." OR convidado_id=".$id.") ORDER BY data ASC limit 1");
  $response=$get->procurar();
  return $response;
}
/**
* Retorna a data, hora inicio e hora fim, da ultima reuneao de um derterminado aluno com o seu orientador
*/
public function getEventLast($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim' FROM evento WHERE estado=1 AND (utilizador_id=".$id." OR convidado_id=".$id.") ORDER BY data DESC limit 1");
  $response=$get->procurar();
  return $response;
}
/**
* Retorna a data, hora inicio e hora fim, de todas as reuneoes de um derterminado aluno com o seu orientador
*/
public function getEventHorasTotal($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT DATE_FORMAT(data, '%d/%m/%Y') AS 'data', TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim' FROM evento WHERE estado=1 AND (utilizador_id=".$id." OR convidado_id=".$id.")");
  $response=$get->procurar();
  return $response;
}
/**
* Retorna o numero total de ocorrencias
*/
public function getNumTotalEvents($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT COUNT(id) FROM evento WHERE estado=1 AND (utilizador_id=".$id." OR convidado_id=".$id.")");
  $response=$get->procurar();
  return $response;
}
/**
* Retorna todos os alunos de um orientador
*/
public function getAllStudentsOrientador($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,utilizador_id FROM projeto WHERE orientador_id=".$id);//." OR coorientador_id=".$id
  $response=$get->procurar();
  return $response;
}

/*
* Funcao que retorna os eventos pendentes de um orientador, com info realtiva a nome, sala, data, hora_inicio, hora_fim e convidado_id
*/
public function getOrientadorEventsPendingJson($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome,sala,DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim', utilizador_id,convidado_id FROM evento WHERE estado=0 AND (utilizador_id=".$id." OR convidado_id=".$id.") AND data > CURDATE() ORDER BY data");
  $response=$get->procurar();
  while($row = mysqli_fetch_assoc($response))
  {
    $nome[]=$row['nome'];
    $sala[]=$row['sala'];
    $data[]=$row['data'];
    $hora_inicio[]=$row['hora_inicio'];
    $hora_fim[]=$row['hora_fim'];
    $convidado_id[]=$row['hora_fim'];
  }

  $data=array(
    "nome" => $nome,
    "sala" => $sala,
    "data" => $data,
    "hora_inicio" => $hora_inicio,
    "hora_fim" => $hora_fim,
    "convidado_id" => $convidado_id
  );
  $data=json_encode($data);

  if (empty($data)) {
    return false;
  }
  return $data;
}
/*
* Funcao que retorna os eventos aprovados de um orientador, com info realtiva a nome, sala, data, hora_inicio, hora_fim e convidado_id
*/
public function getOrientadorEventsAprovedJson($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome,sala,DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim', utilizador_id,convidado_id FROM evento WHERE estado=1 AND (utilizador_id=".$id." OR convidado_id=".$id.")  ORDER BY data");
  $response=$get->procurar();
  while($row = mysqli_fetch_assoc($response))
  {
    $nome[]=$row['nome'];
    $sala[]=$row['sala'];
    $data[]=$row['data'];
    $hora_inicio[]=$row['hora_inicio'];
    $hora_fim[]=$row['hora_fim'];
    $convidado_id[]=$row['hora_fim'];
  }

  $data=array(
    "nome" => $nome,
    "sala" => $sala,
    "data" => $data,
    "hora_inicio" => $hora_inicio,
    "hora_fim" => $hora_fim,
    "convidado_id" => $convidado_id
  );
  $data=json_encode($data);

  if (empty($data)) {
    return false;
  }
  return $data;
}
//Retorna os orientadores de um aluno
public function getUserConnectionProfessor($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT user_name,email,email_pessoal,is_email_pessoal_valid,formacao_avancada,foto,foto_tipo,tipo,contacto,skype_id,cacifo_digital_id,horario_livre,notificacao FROM utilizador WHERE id=(SELECT utilizador_id from projeto where utilizador_id=".$id.")");
  $response=$get->procurar();
  return $response;


}
//Retorna os orientadores de um aluno
public function getUserOrientador($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT user_name,email,email_pessoal,perfil_id FROM utilizador WHERE id=(select orientador_id from projeto where utilizador_id=".$id.")");
  $response=$get->procurar();
  return $response;

}
//Retorna os coorientadores de um aluno
public function getUserCoOrientador($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT user_name,email,email_pessoal,perfil_id FROM utilizador WHERE id=(select coorientador_id from projeto where utilizador_id=".$id.")");
  $response=$get->procurar();
  return $response;
}
//Retorna os alunos de um orientador
public function getUserAlunos($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT user_name,email,email_pessoal,perfil_id FROM utilizador WHERE id IN (SELECT utilizador_id from projeto where orientador_id=".$id.")");
  $response=$get->procurar();
  return $response;
}
public function getProjectID($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome,area,tema FROM projeto WHERE orientador_id=".$id);
  $response=$get->procurar();
  return $response;
}
public function getProject($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome,area,tema, utilizador_id , orientador_id, DATE_FORMAT(data, '%d/%m/%Y') AS 'data'FROM projeto WHERE (utilizador_id=".$id." OR orientador_id=".$id.")");
  $response=$get->procurar();
  return $response;
}
public function getProjectStudent($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome,area,tema, utilizador_id , orientador_id, DATE_FORMAT(data, '%d/%m/%Y') AS 'data'FROM projeto WHERE utilizador_id=".$id);
  $response=$get->procurar();
  return mysqli_fetch_assoc($response);
}

public function getProjectArea($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome,area,tema, utilizador_id , orientador_id, DATE_FORMAT(data, '%d/%m/%Y') AS 'data'FROM projeto WHERE (utilizador_id=".$id." OR orientador_id=".$id.")");
  $response=$get->procurar();
  return mysqli_fetch_assoc($response);
}

public function getProjectAreaProf($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT area FROM projeto WHERE orientador_id=".$id);
  $response=$get->procurar();
  return $response;
}

public function getEvent($id){
  $get = new AccessDB("SELECT id,nome,sala,DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim',tipo,contacto,skype_id,notas,cacifo_digital_id,convidado_id,project_id FROM evento WHERE id=".$id);
  $response=$get->procurar();
  return mysqli_fetch_assoc($response);
}
public function getEventSearch($nome,$perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id,nome,sala,DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim',tipo,contacto,skype_id,notas,cacifo_digital_id,convidado_id,project_id FROM evento WHERE estado=1 AND (utilizador_id=".$id." OR convidado_id=".$id.") AND nome like "."'%".$nome."%'");
  $response=$get->procurar();
  return $response;
}
public function getEventCronJobDaily($id){
  $get = new AccessDB("SELECT id,nome,sala,DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim',tipo,contacto,skype_id,notas,cacifo_digital_id,convidado_id,project_id FROM evento WHERE estado=1 AND (utilizador_id=".$id." OR convidado_id=".$id.") AND DAY(data) = DAY(CURDATE()) ORDER BY data ");
  $response=$get->procurar();
  return $response;
}
public function getEventCronJobWeekly($id){
  $get = new AccessDB("SELECT id,nome,sala,DATE_FORMAT(data, '%d/%m/%Y') AS 'data',TIME_FORMAT(hora_inicio, '%H:%i') AS 'hora_inicio',TIME_FORMAT(hora_fim, '%H:%i') AS 'hora_fim',tipo,contacto,skype_id,notas,cacifo_digital_id,convidado_id,project_id FROM evento WHERE estado=1 AND (utilizador_id=".$id." OR convidado_id=".$id.") AND YEARWEEK(data)=YEARWEEK(NOW()) ORDER BY data");
  $response=$get->procurar();
  return $response;
}

public function getProjectBasedID($id){
  $get = new AccessDB("SELECT nome,area,tema FROM projeto WHERE id=".$id);
  $response=$get->procurar();
  return mysqli_fetch_assoc($response);
}
//Retorna todos os dados do utilizador
public function getUserInfo($perfil_id){
  $get = new AccessDB("SELECT user_name,perfil_id,email,email_pessoal,is_email_pessoal_valid,formacao_avancada,foto,foto_tipo,tipo,contacto,skype_id,cacifo_digital_id,horario_livre,notificacao FROM utilizador WHERE perfil_id=".$perfil_id);
  $response=$get->procurar();
  return mysqli_fetch_assoc($response);
}
public function getUserInfoNoFetch($perfil_id){
  $get = new AccessDB("SELECT user_name,email,email_pessoal,is_email_pessoal_valid,formacao_avancada,foto,foto_tipo,tipo,contacto,skype_id,cacifo_digital_id,horario_livre,notificacao FROM utilizador WHERE perfil_id=".$perfil_id);
  $response=$get->procurar();
  var_dump($get);
  return $response;
}
public function getUserInfoNamePojectConnection($nome,$perfilidProf){
  $id= $this->getUserId($perfilidProf);
  $get = new AccessDB("SELECT user_name,email,email_pessoal,is_email_pessoal_valid,formacao_avancada,foto,foto_tipo,tipo,contacto,skype_id,cacifo_digital_id,horario_livre,notificacao FROM utilizador WHERE user_name like "."'%".$nome."%'"." AND id=(SELECT utilizador_id from projeto where orientador_id=".$id.")");
  $response=$get->procurar();
  return $response;
}
public function getUserInfoName($nome){
  $get = new AccessDB("SELECT user_name,email,email_pessoal,is_email_pessoal_valid,formacao_avancada,foto,foto_tipo,tipo,contacto,skype_id,cacifo_digital_id,horario_livre,notificacao FROM utilizador WHERE user_name like "."'%".$nome."%'");
  $response=$get->procurar();
  return $response;
}
public function getUserInfoNameEstatistica($nome){
  $get = new AccessDB("SELECT user_name,email,email_pessoal,is_email_pessoal_valid,formacao_avancada,foto,foto_tipo,tipo,contacto,skype_id,cacifo_digital_id,horario_livre,notificacao FROM utilizador WHERE user_name like "."'%".$nome."%'");
  $response=$get->procurar();
  return mysqli_fetch_assoc($response);

}
public function getUserInfoID($id){
  $get = new AccessDB("SELECT id,perfil_id,user_name,email,email_pessoal,formacao_avancada,foto,foto_tipo,tipo,contacto,skype_id,cacifo_digital_id,horario_livre,notificacao FROM utilizador WHERE id=".$id);
  $response=$get->procurar();
  return mysqli_fetch_assoc($response);
}

public function getUserInfoCronJobDaily(){
  $get = new AccessDB("SELECT id,perfil_id,user_name,email,email_pessoal,is_email_pessoal_valid,formacao_avancada,foto,foto_tipo,tipo,contacto,skype_id,cacifo_digital_id,horario_livre,notificacao FROM utilizador WHERE is_email_valid=1 AND notificacao=1");
  $response=$get->procurar();
  return $response;
}

public function getUserInfoCronJobWeekly(){
  $get = new AccessDB("SELECT id,perfil_id,user_name,email,email_pessoal,is_email_pessoal_valid,formacao_avancada,foto,foto_tipo,tipo,contacto,skype_id,cacifo_digital_id,horario_livre,notificacao FROM utilizador WHERE is_email_valid=1 AND notificacao=0");
  $response=$get->procurar();
  return $response;
}

public function getUserContacts($perfil_id){
  $get = new AccessDB("SELECT contacto,skype_id,cacifo_digital_id FROM utilizador WHERE perfil_id="."'".$perfil_id."'");
  $response=$get->procurar();

  while($row = mysqli_fetch_array($response))
  {
    $contacto=$row['contacto'];
    $skype_id=$row['skype_id'];
    $cacifo_digital_id=$row['cacifo_digital_id'];
  }

  $data=array(
    "contacto" => $contacto,
    "skype_id" => $skype_id,
    "cacifo_digital_id" => $cacifo_digital_id
  );
  $data=json_encode($data);

  if (empty($data)) {
    return false;
  }
  return $data;
}
public function getUserImg($perfil_id){
  $get = new AccessDB("SELECT foto,foto_tipo FROM utilizador WHERE perfil_id=".$perfil_id);
  $response=$get->procurar();
  return mysqli_fetch_assoc($response);

}

public function getUserDocsSent($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT mongoID FROM files WHERE utilizador_id=".$id);
  $response=$get->procurar();
  return $response;
}
public function getUserDocsReceive($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT mongoID FROM files WHERE receptor_id=".$id);
  $response=$get->procurar();
  return $response;

}
public function getUserId($perfil_id){
  $get = new AccessDB("SELECT id FROM utilizador WHERE perfil_id=".$perfil_id);
  $response=$get->procurar();
  $id=mysqli_fetch_assoc($response);
  $id=$id['id'];
  return $id;
}
public function getUserPerfilBasedId($id){
  $get = new AccessDB("SELECT perfil_id FROM utilizador WHERE id=".$id);
  $response=$get->procurar();
  $pid=mysqli_fetch_assoc($response);
  //$pid=$pid['id'];
  return $pid;
}
public function checkIsUserPerfilId($id,$perfil_id){
  $get = new AccessDB("SELECT perfil_id FROM utilizador WHERE id=".$id);
  $response=$get->procurar();
  $pid=mysqli_fetch_assoc($response);
  $pid=$pid['perfil_id'];
  if($perfil_id==$pid)
  {
    return true;
  }
  return false;
}
public function getUserPerfilId($email){
  $get = new AccessDB("SELECT perfil_id FROM utilizador WHERE email="."'".$email."'");
  $response=$get->procurar();
  $pid=mysqli_fetch_assoc($response);
  $pid=$pid['perfil_id'];
  return $pid;
}
public function getAlunoProjectID($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id FROM projeto WHERE utilizador_id=".$id);
  $response=$get->procurar();
  $id=mysqli_fetch_assoc($response);
  $id=$id['id'];
  return $id;
}
public function getOrientadorProjectID($perfil_id){
  $id= $this->getUserId($perfil_id);
  $get = new AccessDB("SELECT id FROM projeto WHERE orientador_id=".$id);
  $response=$get->procurar();
  $id=mysqli_fetch_assoc($response);
  $id=$id['id'];
  return $id;
}

public function getName($perfil_id)
{
  $get = new AccessDB("SELECT user_name from utilizador where perfil_id=".$perfil_id);
  $response=$get->procurar();
  $nome=mysqli_fetch_assoc($response);
  $nome=$nome['user_name'];
  return $nome;
}


/**
* Codifica imagem em binario para inserir na bd
*/
public function storeImage($foto){
  $imageData = base64_encode(file_get_contents($foto));
  return $imageData;
}
public function getImage($foto){
  $imageData = @file_get_contents($foto);
  return $imageData;
}
public function DecodeImage($foto){
  $image = base64_decode($foto);
  return $image;
}


/**
* Verifica se uma imagem é de facto uma imagem
*/
public function isImage($image,$ext){
  for ($i=0; $i < strlen($ext); $i++) {
    if ($ext[$i]=='/') {
      break;
    }
  }
  $typeprefix=substr($ext,$i+1,strlen($ext));
  if($typeprefix != "jpg" && $typeprefix != "png" && $typeprefix != "jpeg" && $typeprefix != "gif" ) {
    return false;
  }
  return true;
}

/**
* verifica se um perfil Id, existe, se existe, retorna false, se não existir retorna true
*/
public function checkPerfilID($num_perfil_ID){
  $query_perfilID = new AccessDB("SELECT perfil_id from utilizador where perfil_id=".$num_perfil_ID);
  $aux=$query_perfilID->procurar();

  $ax=mysqli_fetch_assoc($aux);
  if (empty($ax)) {
    return true;
  }
  return false;
}

public function isProfessor($professorid){

  $aux=substr($professorid, 0, 2);
  if($aux == 11)
  {
    return false;
  }
  return true;

}
public function getMaxPerfilID()
{
  $max=0;
  $query_email= new AccessDB("SELECT MAX(perfil_id) AS max from utilizador");
  $aux=$query_email->procurar();
  //while($row = mysqli_fetch_array($aux)){
  $ax=mysqli_fetch_assoc($aux);
  /*$aux_max=$row['perfil_id'];
  if($aux_max>$max)
  {
  $max=$aux_max;
}*/
//}
return $ax;
}

//função que verifica se a data é valida , juntamente com a hora!
public function checkDate($userdate,$timeInicio,$timeFim)
{
  $currentdate=date("d/m/Y");
  $userdate=explode("/",$userdate);
  $userdate=implode("-",$userdate);
  $currentdate=explode("/",$currentdate);
  $currentdate=implode("-",$currentdate);

  $dateuser = date_create($userdate);
  $datecurrent = date_create($currentdate);
  if($dateuser == $datecurrent)
  {
    if($this->checkTime($timeInicio,$timeFim))
    {
      return true;
    }
    return false;
  }
  elseif ($dateuser < $datecurrent) {
    return false;
  }
  elseif ($dateuser > $datecurrent) {

    if($this->checkUserTime($timeInicio,$timeFim))
    {
      return true;
    }
    return false;
  }
}
public function checkUserTime($timeInicio,$timeFim)
{
  if(strtotime($timeInicio) >= strtotime($timeFim))
  {
    return false;
  }
  return true;

}
// usa tempo UNIX para comparar as horas
public function checkTime($timeInicio,$timeFim)
{
  if(strtotime($timeInicio) >= strtotime($timeFim))
  {
    return false;
  }
  if(time() < strtotime($timeInicio))
  {
    return true;
  }
  return false;

}

public function checkCacifoURL($url)
{
  $urlpre="https://elearning.ufp.pt/";
  $size=strlen($urlpre);
  $count=0;
  for($i =0; $i<$size;$i++)
  {
    if($urlpre[$i]!=$url[$i])
    {
      return false;
    }
    $count++;
    if($count==$size)
    {
      return true;
    }
  }
}

public function getEmailPrefix($email)
{
  for ($i=0; $i < strlen($email); $i++) {
    if ($email[$i]=='@') {
      break;
    }
  }
  $emailprefix=substr($email,0,$i);
  return $emailprefix;
}
public function getEmailSubfix($email)
{
  for ($i=0; $i < strlen($email); $i++) {
    if ($email[$i]=='@') {
      break;
    }
  }
  $emailsubfix=substr($email,$i,strlen($email));
  return $emailsubfix;
}
public function getEmail($id)
{
  $query_email= new AccessDB("SELECT email from utilizador where id=".$id);
  $aux=$query_email->procurar();
  /*while($row = mysqli_fetch_array($aux)){
  $email=$row['email'];
}*/
/*FALTA RESOLVER AINDA!!!*/
$email=mysqli_fetch_assoc($aux);
return $email;
}
public function getEmailPerfil($pid)
{
  $query_email= new AccessDB("SELECT email from utilizador where perfil_id=".$pid);
  $aux=$query_email->procurar();
  while($row = mysqli_fetch_array($aux))
  {
    $email=$row['email'];
  }
  return $email;
}
public function isEmailValid($email)
{
  $id= -1;
  $ask= new Ask();
  $query_email= new AccessDB("SELECT id from utilizador where email = "."'".$email."'");
  $aux=$query_email->procurar();
  while($row = mysqli_fetch_array($aux))
  {
    $id=$row['id'];
  }
  return $id;
}
public function isValid($email)
{
  $is = 0;
  $query_email = new AccessDB("SELECT is_email_valid from utilizador where email= "."'".$email."'");
  $aux = $query_email->procurar();
  $is=mysqli_fetch_assoc($aux);
  /*while($row = mysqli_fetch_array($aux))
  {
  $is=$row['is_email_valid'];
}*/
return $is;
}
public function analyzeEmail($email)
{
  $ask= new Ask();
  $sub = $ask->getEmailSubfix($email);
  $pre = $ask->getEmailPrefix($email);
  if(strcmp($sub,'@ufp.edu.pt')==0)
  {
    if(is_numeric($pre))
    {
      return "s";
    }
    return "p";
  }
  return "error";
}
public function verifyCode($code)
{
  $ask = new AcessCached();
  $response=$ask->getCodeMemcached($code);
  if($response)
  {
    return true;
  }
  return false;
}
public function verifyCodePassLost($code)
{
  $ask = new AcessCached();
  $response=$ask->getMemcached($code);
  if($response)
  {
    return true;
  }
  return false;
}

//Analisa a palavra-passe para cumprir certos criterios , tem de ter numeros, letras e maior que 8
public function analyzePassword($password,$cpassword)
{
  if(strlen($password) < 7)
  {
    return false;
  }
  if(is_numeric($password))
  {
    return false;
  }
  if(ctype_alpha($password))
  {
    return false;
  }
  if(!$this->isPasswordEqual($password,$cpassword))
  {
    return false;
  }
  return true;
}

//verifica se as palavras passes sao iguais
public function isPasswordEqual($password,$cpassword)
{
  if(strcmp($password,$cpassword)==0)
  {
    return true;
  }
  return false;
}
public function analyzeCellPhoneNumber($number)
{
  if(!is_numeric($number))
  {
    return false;
  }
  if(strlen($number) != 9)
  {
    return false;
  }
  return true;
}
/**
* Gera códigos para registo aleatórios entre 00000 99999
*/
public function geraCodigoRegisto(){
  $code=rand(10000,99999);
  $ax = new AcessCached();
  $state=$ax->saveCodeMemcached($code);
  if (strcmp($state,"ok")==0) {
    return $code;
  }
  return false;
}
public function geraPassLostCode($code){
  $ax = new AcessCached();
  $state=$ax->saveCodeMemcached($code);
  if (strcmp($state,"ok")==0) {
    return $code;
  }
  return false;
}


}

?>
