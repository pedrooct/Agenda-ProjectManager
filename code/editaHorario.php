<?php
session_start();
include_once 'api/ask.php';

if(!isset($_SESSION['email']) || !isset($_SESSION['perfil_id']) )
{
	header('location: login');
}

$semail= $_SESSION['email'];
$name= $_SESSION['name'];
$perfil_id = $_SESSION['perfil_id'];


$get=new Ask();
$data= $get->getUserInfo($perfil_id);
$isProfessor=$get->isProfessor($perfil_id);
$img= $get->getUserImg($perfil_id);

if(!$isProfessor)
{
	header('location: api/logout');
}



?>
<html lang="pt-PT">
<head>
	<meta charset="utf-8">
	<meta name="description" content="Registo">
	<title>web page of &ndash; Pedro Costa and Paulo Bento &ndash; Pure</title>


	<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css">
	<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
	<link rel="stylesheet" href="style/style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title></title>

</head>
<body>
	<div class="header">
		<div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
			<a style="display: inline-flex !important; " class="pure-menu-heading" href="">Horário de atendimento</a>
			<div class="dropdown">
				<?php
				if(empty($img['foto']))
				{?>
					<img class="icon" src="/lpi/resources/icons/account_icon.png" >
					<?php
				}
				else {
					echo '<img class="icon" src="data:'.$img['foto_tipo'].';base64, '.$img['foto'].'" alt="imagem perfil" style="border-radius: 100px;" />';
				}
				?>
				<button class="dropbtn"></button>
			</img>
			<div id="user_icon" class="dropdown-content">
				<a href="profile"> Perfil/Definições</a>
				<a href="api/logout"> logout</a>
			</div>
		</div>
		<a href="calendar">
			<img class="icon" alt="Modo calendário" src="/lpi/resources/icons/calendar_google_icon.png" />
		</a>
	</div>
	<?php
	if(isset($_POST['confirm']))
	{
		$horario=array();
		if(!empty($_POST['myTimesSeg']))
		{
			$seg=$_POST['myTimesSeg'];
			for($i=0;$i<sizeof($seg);$i++)
			{
				$horario[] = 'segunda-'.$seg[$i];
			}
		}
		if(!empty($_POST['myTimesTer']))
		{
			$ter=$_POST['myTimesTer'];
			for($i=0;$i<sizeof($ter);$i++)
			{
				$horario[] = 'terca-'.$ter[$i];
			}
		}
		if(!empty($_POST['myTimesQua']))
		{
			$qua=$_POST['myTimesQua'];
			for($i=0;$i<sizeof($qua);$i++)
			{
				$horario[] = 'quarta-'.$qua[$i];
			}
		}
		if(!empty($_POST['myTimesQui']))
		{
			$qui=$_POST['myTimesQui'];
			for($i=0;$i<sizeof($qui);$i++)
			{
				$horario[] = 'quinta-'.$qui[$i];
			}

		}
		if(!empty($_POST['myTimesSex']))
		{
			$sex=$_POST['myTimesSex'];
			for($i=0;$i<sizeof($sex);$i++)
			{
				$horario[] = 'sexta-'.$sex[$i];
			}
		}
		$schedule= new Ask();
		$response=$schedule->horarioLivreJsonUpdate($horario,$perfil_id);
		if($response)
		{
			echo '<div class="success">Alterações guardadas com sucesso !</div>';
		}
		echo '<div class="error">OOOPS! Ocorreu um problema  </div>';
	}
	?>
</div>
<div class="content">
	<div class="pure-g">
		<form class="pure-form pure-form-stacked" enctype="multipart/form-data" method="POST">
			<div class=".pure-u-1-1 pure-u-sm-1-2 pure-u-md-1-2 pure-u-lg-1-4">
				<div class="columns">
					<ul class="price">
						<li class="headerBox">Segunda-Feira</li>
						<li class="grey"> Horas: </li>
						<div id="SegT" class="grey">
						</div>
						<li class="grey">
							<input name="BtSSegunda"  type="button" class="pure-button pure-button-primary" value="Inserir horas" onClick="addTimeSeg('SegT');">
						</li>
					</ul>
				</div>
			</div>
			<div class=".pure-u-1-1 pure-u-sm-1-2 pure-u-md-1-2 pure-u-lg-1-4">
				<div class="columns">
					<ul class="price">
						<li class="headerBox">Terça-Feira</li>
						<li class="grey"> Horas: </li>
						<div id="TercT" class="grey">
						</div>
						<li class="grey">
							<input name="BtSTerca"  type="button" class="pure-button pure-button-primary" value="Inserir horas" onClick="addTimeTer('TercT');">
						</li>
					</ul>
				</div>
			</div>
			<div class=".pure-u-1-1 pure-u-sm-1-2 pure-u-md-1-2 pure-u-lg-1-4">
				<div class="columns">
					<ul class="price">
						<li class="headerBox">Quarta-Feira</li>
						<li class="grey"> Horas: </li>
						<div id="QuarT" class="grey">
						</div>
						<li class="grey">
							<input name="BtSQuarta"  type="button" class="pure-button pure-button-primary" value="Inserir horas" onClick="addTimeQua('QuarT');">
						</li>
					</ul>
				</div>
			</div>
			<div class=".pure-u-1-1 pure-u-sm-1-2 pure-u-md-1-2 pure-u-lg-1-4">
				<div class="columns">
					<ul class="price">
						<li class="headerBox">Quinta-Feira</li>
						<li class="grey"> Horas: </li>
						<div id="QuinT" class="grey">
						</div>
						<li class="grey">
							<input name="BtSQuinta"  type="button" class="pure-button pure-button-primary" value="Inserir horas" onClick="addTimeQui('QuinT');">
						</li>
					</ul>
				</div>
			</div>
			<div class=".pure-u-1-1 pure-u-sm-1-2 pure-u-md-1-2 pure-u-lg-1-4">
				<div class="columns">
					<ul class="price">
						<li class="headerBox">Sexta-Feira</li>
						<li class="grey"> Horas: </li>
						<div id="SexT" class="grey">
						</div>
						<li class="grey">
							<input name="BtSSexta"  type="button" class="pure-button pure-button-primary" value="Inserir horas" onClick="addTimeSex('SexT');">
						</li>
					</ul>
				</div>
			</div>
			<div  class=".pure-u-sm-1-3" style="margin-left: 0.5%;">
				<button type="submit" name="confirm" class="pure-button pure-button-primary">Confirmar</button>
			</div>
		</form>
	</div>
</div>
<script>
var counterS = 1;
var limitS = 6;
var counterT = 1;
var limitT = 6;
var counterQ = 1;
var limitQ = 6;
var counterQI = 1;
var limitQI = 6;
var counterSX = 1;
var limitSX = 6;
function addTimeSeg(divName){

	if (counterS == limitS)  {
		alert("Atingiu o máximo de Horas que pode inserir por dia na segunda feira");
	}
	else {
		var newdiv = document.createElement('li');
		newdiv.innerHTML = "<p>De:</p>"+"<input name='myTimesSeg[]' type='time' class='pure-input-rounded' >"+"<p>Ate:</p>"+"<input name='myTimesSeg[]' type='time' class='pure-input-rounded'>";
		document.getElementById(divName).appendChild(newdiv);
		counterS++;
	}
}
function addTimeTer(divName){

	if (counterT == limitT)  {
		alert("Atingiu o máximo de Horas que pode inserir por dia na Terça feira");
	}
	else {
		var newdiv = document.createElement('li');
		newdiv.innerHTML = "<p>De:</p>"+"<input name='myTimesTer[]' type='time' class='pure-input-rounded' >"+"<p>Ate:</p>"+"<input name='myTimesTer[]' type='time' class='pure-input-rounded'>";
		document.getElementById(divName).appendChild(newdiv);
		counterT++;
	}
}
function addTimeQua(divName){

	if (counterQ == limitQ)  {
		alert("Atingiu o máximo de Horas que pode inserir por dia na Quarta feira");
	}
	else {
		var newdiv = document.createElement('li');
		newdiv.innerHTML = "<p>De:</p>"+"<input name='myTimesQua[]' type='time' class='pure-input-rounded' >"+"<p>Ate:</p>"+"<input name='myTimesQua[]' type='time' class='pure-input-rounded'>";
		document.getElementById(divName).appendChild(newdiv);
		counterQ++;
	}
}
function addTimeQui(divName){

	if (counterQI == limitQI)  {
		alert("Atingiu o máximo de Horas que pode inserir por dia na Quinta feira");
	}
	else {
		var newdiv = document.createElement('li');
		newdiv.innerHTML = "<p>De:</p>"+"<input name='myTimesQui[]' type='time' class='pure-input-rounded' >"+"<p>Ate:</p>"+"<input name='myTimesQui[]' type='time' class='pure-input-rounded'>";
		document.getElementById(divName).appendChild(newdiv);
		counterQI++;
	}
}
function addTimeSex(divName){

	if (counterSX == limitSX)  {
		alert("Atingiu o máximo de Horas que pode inserir por dia na Sexta feira");
	}
	else {
		var newdiv = document.createElement('li');
		newdiv.innerHTML = "<p>De:</p>"+"<input name='myTimesSex[]' type='time' class='pure-input-rounded' >"+"<p>Ate:</p>"+"<input name='myTimesSex[]' type='time' class='pure-input-rounded'>";
		document.getElementById(divName).appendChild(newdiv);
		counterSX++;
	}
}
</script>

<div class="footer">
	© 2018! Projecto Laboratório Pedro Costa Nº: 31179 & Paulo Bento Nº:33959 .
</div>

</body>
</html>
