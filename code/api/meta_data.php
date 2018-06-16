<?php

require_once 'access.php';
require_once 'access_db.php';
require_once 'ask.php';
//class com funçoes de analise e estatistica

/**
 *
 */
class Stats
{

  function __construct(){

  }

  /**
  * Função que responde um horário livre do orientador que seja semelhante ao horario livre do orientador
  */
  public function sugerirHorario($dia, $hora_inicio, $hora_fim, $perfil_id_orientador,$email_orientador){
    $horario_orientador = $this->getHorasLivresHorario($perfil_id_orientador,$email_orientador);
      if ($horario_orientador[$dia]==0 && strcmp($dia,$horario_orientador['dia'])==0 && strcmp($hora_inicio,$horario_orientador['hora_inicio'])==0 && strcmp($hora_fim,$horario_orientador['hora_fim'])==0) {
        $horario = array(
          'hora_inicio' => $horario_orientador['hora_inicio'],
          'hora_fim' => $horario_orientador['hora_fim']
        );
        return $horario;
      }
      return false;
  }

  public function horario_alunos_doutoramento($perfil_id){
    $ask = new Ask();
    $aux = $ask->hora_inicio_fim_phd($perfil_id);
    $aux=json_decode($aux);


  }

  /*
  * Retorna os ultimos 3 eventos de um aluno, esta função so pode ser usada pelo orientador
  */
  public function getHistoricoAluno($perfil_id){
    $ask = new Ask();
    $response=$ask->getEventsAproved($perfil_id);
    if (empty($response)) {
      return false;
    }

    $aux=array();
    while($row = mysqli_fetch_array($response))
    {
      $aux[]=$row['nome'];
      $aux[]=$row['sala'];
      $aux[]=$row['data'];
    }
    $aux_array=array();
    $size=sizeof($aux);
    $size--;
    $size_low=($size-9);
    for ($i=$size; $i > $size_low; $i-=3) {
      if (($i+1)%3==0) {
        $data_aux=explode("/",$aux[$i]);
        $data_aux=implode("-",$data_aux);
        $data_evento=date_create($data_aux);
        $currentdate=date("Y/m/d");
        $currentdate=explode("/",$currentdate);
        $currentdate=implode("-",$currentdate);
        $currentdate = date_create($currentdate);

        $diff=date_diff($data_evento,$currentdate);
        $diff=$diff->format("%R%a days");
        if ($diff>0) {
          $data=array(
            "nome" => $aux[$i-2],
            "estado" => $aux[$i-1],
            "data" => $aux[$i]
          );
        }
        else {
          $size_low-=3;
        }
      }
      $aux_array[]=$data;
    }

    return $aux_array;
  }

  /*
  * Retorna um json com as horas livres do professor
  */
  public function getHorasLivresHorario($perfil_id,$email){
    $ask = new Ask();
    $horario=$ask->getHorario($email);  //ex: segunda-10:00,segunda:12:00
    $horario=json_decode($horario);
    for ($i=0; $i < sizeof($horario); $i++) {
      $horario_livre[]=explode('-',$horario[$i]);
    }
    /*echo "<pre>";
    var_dump($horario_livre);
    echo "</pre>";*/

    $eventos=$ask->getOrientadorEventsAprovedJson($perfil_id);
    $eventos=json_decode($eventos);
    /*echo "<pre>";
    var_dump($eventos);
    echo "</pre>";*/

    $nome[]=$eventos->{'nome'};
    $sala[]=$eventos->{'sala'};
    $data[]=$eventos->{'data'};
    $hora_inicio[]=$eventos->{'hora_inicio'};
    $hora_fim[]=$eventos->{'hora_fim'};
    $convidado_id[]=$eventos->{'convidado_id'};

    $size=sizeof($data[0]);
    for ($i=0; $i < $size; $i++) {
      //$data=$data[0];
      $day[$i]=$this->switchDateAmerican($data[0][$i]);
      if (strcmp($day[$i],'Monday')==0) {
        $day[$i]='segunda';
      }elseif (strcmp($day[$i],'Tuesday')==0) {
        $day[$i]='terca';
      }elseif (strcmp($day[$i],'Wednesday')==0) {
        $day[$i]='quarta';
      }elseif (strcmp($day[$i],'Thursday')==0) {
        $day[$i]='quinta';
      }elseif (strcmp($day[$i],'Friday')==0) {
        $day[$i]='sexta';
      }elseif (strcmp($day[$i],'Saturday')==0) {
        $day[$i]='sabado';
      }elseif (strcmp($day[$i],'Sunday')==0) {
        $day[$i]='domingo';
      }
    }

    $stuff=array();
    $size=sizeof($day);
    for ($i=0; $i < $size; $i++) {
      if ($i%2==0) {
        $dia_horario_livre=$horario_livre[$i][0];
        $hora_inicio_horario_livre=$horario_livre[$i][1];
        $hora_fim_horario_livre=$horario_livre[$i+1][1];
      }
      if (strcmp($dia_horario_livre,$day[$i])==0) {
        if ('10:00'==$hora_inicio_horario_livre && '12:00'==$hora_fim_horario_livre) {//($hora_inicio[$i]==$hora_inicio_horario_livre && $hora_fim[$i]==$hora_fim_horario_livre) {
          if (strcmp($dia_horario_livre,"segunda")==0) {
            $contSegunda++;
            $stuff['dia']='segunda';
            $stuff['segunda']=$contSegunda;
            $stuff['hora_inicio']=$hora_inicio_horario_livre;
            $stuff['hora_fim']=$hora_fim_horario_livre;
          }elseif (strcmp($dia_horario_livre,"terca")==0) {
            $contTerca++;
            $stuff['dia']='terca';
            $stuff['terca']=$contTerca;
            $stuff['hora_inicio']=$hora_inicio_horario_livre;
            $stuff['hora_fim']=$hora_fim_horario_livre;
          }elseif (strcmp($dia_horario_livre,"quarta")==0) {
            $contQuarta++;
            $stuff['dia']='quarta';
            $stuff['quarta']=$contQuarta;
            $stuff['hora_inicio']=$hora_inicio_horario_livre;
            $stuff['hora_fim']=$hora_fim_horario_livre;
          }elseif (strcmp($dia_horario_livre,"quinta")==0) {
            $contQuinta++;
            $stuff['dia']='quinta';
            $stuff['quinta']=$contQuinta;
            $stuff['hora_inicio']=$hora_inicio_horario_livre;
            $stuff['hora_fim']=$hora_fim_horario_livre;
          }elseif (strcmp($dia_horario_livre,"sexta")==0) {
            $contSexta++;
            $stuff['dia']='sexta';
            $stuff['sexta']=$contSexta;
            $stuff['hora_inicio']=$hora_inicio_horario_livre;
            $stuff['hora_fim']=$hora_fim_horario_livre;
          }elseif (strcmp($dia_horario_livre,"sabado")==0) {
            $contSabado++;
            $stuff['sabado']=$contSabado;
            $stuff['hora_inicio']=$hora_inicio_horario_livre;
            $stuff['hora_fim']=$hora_fim_horario_livre;
          }elseif (strcmp($dia_horario_livre,"domingo")==0) {
            $contDomingo++;
            $stuff['domingo']=$contDomingo;
            $stuff['hora_inicio']=$hora_inicio_horario_livre;
            $stuff['hora_fim']=$hora_fim_horario_livre;
          }
        }
      }
    }
    return $stuff;
  }
  public function switchDateAmerican($data){
    $userdate=explode("/",$data);
    $data=$userdate[2]."-".$userdate[1]."-".$userdate[0];
    $timestamp=strtotime($data);
    $day = date('l', $timestamp);
    return $day;
  }


  /*
  * Função que retorna o tempo medio de todos os projetos que foram realizados
  */
  public function tempoMedioProjeto(){
    $ask = new Ask();
    $perfil_ids=$ask->getAllPerfilIDsAlunos();

    $size=sizeof($perfil_ids);
    for ($i=0; $i < $size; $i++) {
      $data_inicio=$ask->getDataProjeto($perfil_ids[$i]);
      $data_inicio=date_create($data_inicio);
      $data_fim=$ask->getDataResultado($perfil_ids[$i]);
      $data_fim=date_create($data_fim);
      $diff=date_diff($data_inicio,$data_fim);
      $time[] = $diff->format("%a");
    }

    $size=sizeof($time);
    if ($size==0) {
      return false;
    }
    for ($i=0; $i < $size; $i++) {
      $aux+=$time[$i];
    }
    return (double)$aux/$size;
  }

  /*
  * Função que retorna o tempo medio de todos os projetos que foram realizados
  */
  public function tempoMedioProjetoArea($area){
    $ask = new Ask();
    $perfil_ids=$ask->getAllPerfilIDsAlunosArea($area);

    $size=sizeof($perfil_ids);
    for ($i=0; $i < $size; $i++) {
      $data_inicio=$ask->getDataProjeto($perfil_ids[$i]);
      $data_inicio=date_create($data_inicio);
      $data_fim=$ask->getDataResultado($perfil_ids[$i]);
      $data_fim=date_create($data_fim);
      $diff=date_diff($data_inicio,$data_fim);
      $time[] = $diff->format("%a");
    }

    $size=sizeof($time);
    if ($size==0) {
      return false;
    }
    for ($i=0; $i < $size; $i++) {
      $aux+=$time[$i];
    }
    return (double)$aux/$size;
  }

  /*
  * Função que retorna o tempo medio de todos os projetos que foram realizados
  */
  public function tempoMedioProjetoFormacaoAvancada($formacao_avancada){
    $ask = new Ask();
    $perfil_ids=$ask->getAllPerfilIDsAlunosFormacaoAvancada($formacao_avancada);

    $size=sizeof($perfil_ids);
    for ($i=0; $i < $size; $i++) {
      $data_inicio=$ask->getDataProjeto($perfil_ids[$i]);
      $data_inicio=date_create($data_inicio);
      $data_fim=$ask->getDataResultado($perfil_ids[$i]);
      $data_fim=date_create($data_fim);
      $diff=date_diff($data_inicio,$data_fim);
      $time[] = $diff->format("%a");
    }

    $size=sizeof($time);
    if ($size==0) {
      return false;
    }
    for ($i=0; $i < $size; $i++) {
      $aux+=$time[$i];
    }
    return (double)$aux/$size;
  }

  /*
  * Função que retorna o tempo medio de todos os projetos que foram realizados
  */
  public function tempoMedioProjetoAreaDataInicial($area){
    $ask = new Ask();
    $perfil_ids=$ask->getAllPerfilIDsAlunosArea($area);
    $currentdate=date("Y/m/d");
    $currentdate=explode("/",$currentdate);
    $currentdate=implode("-",$currentdate);
    $currentdate = date_create($currentdate);
    $size=sizeof($perfil_ids);
    for ($i=0; $i < $size; $i++) {
      $data_inicio=$ask->getDataProjeto($perfil_ids[$i]);
      $data_inicio=date_create($data_inicio);
      $diff=date_diff($data_inicio,$currentdate);
      $data_inicio=$data_inicio->format('Y-m-d H:i:s');
      $data_inicio=explode("-",$data_inicio);
      $data_inicio[2]+=$diff;
      $data_inicio=date($data_inicio['date']);
      $data_inicio = date_create($data_inicio);
      $data_fim=$ask->getDataResultado($perfil_ids[$i]);
      $data_fim=date_create($data_fim);
      $diff=date_diff($data_inicio,$data_fim);
      $time[] = $diff->format("%a");
    }

    $size=sizeof($time);
    if ($size==0) {
      return false;
    }
    for ($i=0; $i < $size; $i++) {
      $aux+=$time[$i];
    }
    return (double)$aux/$size;
  }

  /*
  * Função que retorna o tempo medio de todos os projetos que foram realizados
  */
  public function tempoMedioProjetoFormacaoAvancadaDataInicial($formacao_avancada){
    $ask = new Ask();
    $perfil_ids=$ask->getAllPerfilIDsAlunosFormacaoAvancada($formacao_avancada);
    $currentdate=date("Y/m/d");
    $currentdate=explode("/",$currentdate);
    $currentdate=implode("-",$currentdate);
    $currentdate = date_create($currentdate);
    $size=sizeof($perfil_ids);
    for ($i=0; $i < $size; $i++) {
      $data_inicio=$ask->getDataProjeto($perfil_ids[$i]);
      $data_inicio=date_create($data_inicio);
      $data_inicio=date_diff($data_inicio,$currentdate);
      $data_inicio=$data_inicio->format('Y-m-d H:i:s');
      $data_inicio=explode("-",$data_inicio);
      $data_inicio[2]+=$diff;
      $data_inicio=date($data_inicio['date']);
      $data_inicio = date_create($data_inicio);
      $data_fim=$ask->getDataResultado($perfil_ids[$i]);
      $data_fim=date_create($data_fim);
      $diff=date_diff($data_inicio,$data_fim);
      $time[] = $diff->format("%a");
    }

    $size=sizeof($time);
    if ($size==0) {
      return false;
    }
    for ($i=0; $i < $size; $i++) {
      $aux+=$time[$i];
    }
    return (double)$aux/$size;
  }
  /**
  * Função que retorna a data média que um aluno pode demorar a completar o projeto
  */
  public function tempoMedioProjetoPrevisao($perfil_id){
    $ask = new Ask();
    $aux=$ask->getUserInfo($perfil_id);
    $area=$ask->getProjectArea($perfil_id);
    $area=$area['area'];
    $formacao_avancada=intval($aux['formacao_avancada']);;
    $media1=$this->tempoMedioProjetoAreaDataInicial($area);
    $media2=$this->tempoMedioProjetoFormacaoAvancadaDataInicial($formacao_avancada);
    $media=(double)(($media1+$media2)/2);
    return $media;
  }

  public function tempoEntrePrimeiraUltimaReuneao($perfil_id){
    $ask = new Ask();
    $primeira_reuneao = $ask->getEventFirst($perfil_id);
    while($row = mysqli_fetch_array($primeira_reuneao))
    {
      $data_primeira_reuneao=$row['data'];
      $hora_inicio_primeira_reuneao=$row['hora_inicio'];
      $hora_fim_primeira_reuneao=$row['hora_fim'];
    }
    $ultima_reuneao = $ask->getEventLast($perfil_id);
    while($row = mysqli_fetch_array($ultima_reuneao))
    {
      $data_ultima_reuneao=$row['data'];
      $hora_inicio_ultima_reuneao=$row['hora_inicio'];
      $hora_fim_ultima_reuneao=$row['hora_fim'];
    }
    $data_primeira_reuneao=explode("/",$data_primeira_reuneao);
    $data_primeira_reuneao=implode("-",$data_primeira_reuneao);
    $data_primeira_reuneao=date_create($data_primeira_reuneao);

    $data_ultima_reuneao=explode("/",$data_ultima_reuneao);
    $data_ultima_reuneao=implode("-",$data_ultima_reuneao);
    $data_ultima_reuneao=date_create($data_ultima_reuneao);

    $diff=date_diff($data_primeira_reuneao,$data_ultima_reuneao);
    $diff=$diff->format("%a");
    return $diff;
  }

  public function tempoInteracao($perfil_id){
    $ask = new Ask();
    $aux=0;
    $horas = $ask->getEventHorasTotal($perfil_id);
    while ($row = mysqli_fetch_array($horas)) {
      $data = $row['data'];
      $hora_inicio=$row['hora_inicio'];
      $hora_fim=$row['hora_fim'];
      $ts1 = strtotime(str_replace('/', '-', $data.' '.$hora_fim));
      $ts2 = strtotime(str_replace('/', '-', $data.' '.$hora_inicio));
      $diff = abs($ts1 - $ts2)/3600;
      $aux+=$diff;
    }
     //Este bloco é para a conversão do tempo em decimal de horas para minutos
    /*$whole = floor($aux);
    $fraction = $aux - $whole;
    $aux=$aux-$fraction;
    $fraction=$fraction*60;
    $aux=$aux.':'.round($fraction);*/
    return $aux;
  }

  public function tempoMedioEvento($perfil_id){
    $ask = new Ask();
    $aux = $ask->getNumTotalEvents($perfil_id);
    if ($aux!=false) {
      $aux=mysqli_fetch_assoc($aux);
      $aux=(int)$aux["COUNT(id)"];
      $tempo=$this->tempoInteracao($perfil_id);
      if ($aux!=0)
        $media=(double)$tempo/$aux;
      else
        $media=0;
    }
    $whole = floor($media);
    $fraction = $media - $whole;
    $media=$media-$fraction;
    $fraction=$fraction*60;
    if ($media<10) {
      $media='0'.$media;
    }
    if (round($fraction)<10) {
      $fraction='0'.$fraction;
    }
    $media=$media.':'.$fraction;
    return $media;
  }
}

  $stat = new Stats();
  //$aux=$stat->tempoEntrePrimeiraUltimaReuneao("114767");
  //$aux=$stat->tempoInteracao("114767");
  //$aux=$stat->tempoMedioEvento("114767");
  //$aux = $stat->getHistoricoAluno("114767");

  /*$stat = new Stats();
  $aux=$stat->getHorasLivresHorario('004056',"labproaulas@gmail.com");*/
  /*echo "<pre>";
  var_dump($aux);
  echo "</pre>";*/

  /*$aux=$stat->tempoMedioProjeto();
  echo $aux;*/

  //var_dump($aux);
  /*$aux=$stat->tempoMedioProjetoArea("Engenharia Informatica");

  /*$aux=$stat->tempoMedioProjetoFormacaoAvancada(0);
  echo $aux;*/

  //$aux=$stat->tempoMedioProjeto();
  //echo $aux;
  //$aux=$stat->tempoMedioProjetoPrevisao("114767");
  //echo $aux;

  /*$aux = $stat->sugerirHorario("segunda", "10:00", "12:00", '004056',"labproaulas@gmail.com");
  var_dump($aux);*/
 ?>
