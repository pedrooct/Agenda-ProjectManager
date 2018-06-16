<?php
session_start();
include_once 'api/access.php';
require_once 'api/ask.php';

//error_reporting(0);

if(!isset($_SESSION['email']) || !isset($_SESSION['perfil_id']))
{
	header('location: login');
}
$email= $_SESSION['email'];
$name= $_SESSION['name'];
$perfil_id = $_SESSION['perfil_id'];
$ask= new Ask();
$isProfessor = $ask->isProfessor($perfil_id);
$eventsA= $ask->getEventsAproved($perfil_id);
$getimg= new Ask();
$img= $getimg->getUserImg($perfil_id);

?>
<html lang="pt-PT">
<head>
	<meta charset="utf-8">
	<meta name="description" content="Main Page">
	<title>web page of &ndash; Pedro Costa and Paulo Bento &ndash; Pure</title>

	<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css">
	<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
	<link rel="stylesheet" href="style/style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script
	src="https://code.jquery.com/jquery-3.2.1.min.js"
	integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
	crossorigin="anonymous"></script>
	<script src="js/jquery.min.js"></script>
	<script src="js/moment.min.js"></script>

	<link rel="stylesheet" href="style/fullcalendar.css">
	<script src="js/fullcalendar.min.js"></script>
	<script src="js/pt.js"></script>

</script>
<title></title>

</head>

<body>
	<div class="header">
		<div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
			<?php if($isProfessor)
			{
				$studentsData=$ask->getUserAlunos($perfil_id);

				?>
				<div id="mySidenav" class="sidenav">
					<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
					<a>Alunos</a>
					<?php
					while($row = mysqli_fetch_assoc($studentsData))
					{?>
						<a href=<?php echo "studentSelect?pid=".$row['perfil_id'];?>> <?php echo $row['user_name'];?>   </a>
						<?php
					}
					?>
				</div>
				<span style="font-size:30px;cursor:pointer;color: white;position: relative;top: 6px;" onclick="openNav()">&#9776; </span>
				<script>
				function openNav() {
					document.getElementById("mySidenav").style.width = "250px";
				}

				function closeNav() {
					document.getElementById("mySidenav").style.width = "0";
				}
				</script>
			<?php } ?>
			<p class="ptime" id="time"></p>
			<script>
			var d = new Date();
			var month = d.getMonth()+1;
			document.getElementById("time").innerHTML =" "+ d.getDate()+"/"+month+"/"+ d.getFullYear() ;
			</script>
			<a class="pure-menu-heading" href="dashboard"> Calendário!</a>
			<a href="dashboard">
				<img class="iconI" alt="Voltar" src="/lpi/resources/icons/gobackarrow.png" />
			</a>
			<form method="post" class="formbar">
				<input class="formbarinput" type="text" name="search" placeholder="Procurar...">
				<button type="submit" name="searchbt" class="searchbt">
					<img class="iconsearch" src="/lpi/resources/icons/search_icon.png" >
				</button>
				<?php
				if(isset($_POST['searchbt']))
				{
					$search=explode(" ",$_POST['search']);
					$search=implode("%20",$search);
					header('location: searching?search='.$search);
				}
				?>
			</form>
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
	if(!empty($_GET['search']) && strcmp($_GET['search'],"error")==0)
	{
		echo '<div class="error">OOOPS! Aparenta não estar a procurar por nada </div>';
	}
	?>
</div>

<div class="content">
	<div id="calendario"></div>
</div>
<?php
if($ask->isProfessor($perfil_id))
{
	$id=$ask->getUserId($perfil_id);
	$data=$ask->prepareHorario($id);
}
else {
	$pro=$ask->getProjectStudent($perfil_id);
	$data=$ask->prepareHorario($pro['orientador_id']);
}
?>
<script>

$(document).ready(function(){
	$('#calendario').fullCalendar({
		handleWindowResize: true,
		header:{
			left:'today,prev,next',
			center:'title',
			right:'month,basicWeek,basicDay,agendaWeek,agendaDay'
		},
		defaultView: 'agendaWeek',
		eventLimit: true,
		events:{
			url:'http://app.test/lpi/code/scripts/getEvents.php',
			type: 'POST',
			async: true,
		},
		businessHours: [
			<?php for($i=0; $i<sizeof($data);$i++)
			{
				echo "{";
					echo "dow:".$data[$i]["dow"].",";
					echo "start:'".$data[$i]["start"]."',";
					echo "end:'".$data[$i]["end"]."',";
					echo "},";
				}?>
			],
			eventClick:function(calEvent,jsEvent,view){
				alert('Titulo: ' + calEvent.title + "\n"+ 'Contacto:'+calEvent.contacto  +'\n' +'Sala:'+calEvent.sala +'\nNotas:' +calEvent.notas);
				$(this).css('border-color', 'black');
			}
		});
	});
	</script>

	<div class="dropdownadd">
		<img class="iconadd" src="/lpi/resources/icons/plus_icon.png" >
		<button class="dropbtnadd" onclick="open_add()"></button>
	</img>

	<div id=<?php echo "addDoc"; ?> class="overlay">
		<div class="popup">
			<h2>Envie um Documento</h2>
			<a class="close" href="" >&times;</a>
			<div class="content">
				<form method="post" class="pure-form pure-form-stacked" enctype="multipart/form-data" >
					<select name="email" class="pure-input-1-2" style="border-radius: 82px; width:100%;margin: 1em 0;" >
						<?php
						$ask = new Ask();
						if($isProfessor)
						{
							$dataalunos= $ask->getUserAlunos($perfil_id);
							while($row = mysqli_fetch_array($dataalunos))
							{
								echo'<option value="'.$row['email'].'">'.$row['user_name'].'</option>';
							}
						}
						else {
							$dataorientador= $ask->getUserOrientador($perfil_id);
							$datacoorientador= $ask->getUserCoOrientador($perfil_id);
							while($row = mysqli_fetch_array($dataorientador))
							{
								echo'<option value="'.$row['email'].'">'.$row['user_name'].'</option>';
							}
							while($row = mysqli_fetch_array($datacoorientador))
							{
								echo'<option value="'.$row['email'].'">'.$row['user_name'].'</option>';
							}
						}
						?>
					</select>
					<input name="file" type="file" class="pure-input-rounded" required>
					<textarea name="notas" type="text" class="pure-input-rounded" placeholder="Notas extras"></textarea>
					<button type="submit" name="sendFile" class="pure-button pure-button-primary">Enviar</button>
					<?php
					if(isset($_POST['sendFile']))
					{
						$ficheiro = $_FILES["file"]["tmp_name"];
						$nome_ficheiro = $_FILES["file"]["name"];
						$tipo = $_FILES["file"]["type"];
						if(!empty($ficheiro))
						{
							$send=new Ask();
							if($send->sendDoc($nome_ficheiro,$_POST['email'],$perfil_id,$ficheiro,$tipo,$_POST['notas']))
							{
								$mail=new Mail();
								if($mail->mail_send_doc($_POST['email'],$ficheiro,$tipo,$nome_ficheiro,$_POST['notas'],$perfil_id))
								{
									echo '<div class="success">Documento armazenado e enviado com sucesso ! </div>';
								}
								else {
									echo '<div class="error">OOOPS! Algo correu mal ao enviar o email !</div>';
								}
							}
							else {
								echo '<div class="error">OOOPS! Algo correu mal ao armazenar o documento !</div>';
							}
						}
						else {
							echo '<div class="error">OOOPS! Aparenta não ter inserido nenhum ficheiro!</div>';
						}
					}
					?>
				</form>
			</div>
		</div>
	</div>
	<div id=<?php echo "addEvent"; ?> class="overlay">
		<div class="popup">
			<h2>Adicione um Evento!</h2>
			<a class="close" href="" >&times;</a>
			<div class="content">
				<form method="post" class="pure-form pure-form-stacked" >
					<select name="email" class="pure-input-1-2" style="border-radius: 82px; width:100%;margin: 1em 0;" >
						<?php
						$ask = new Ask();
						if($isProfessor)
						{
							$dataalunos= $ask->getUserAlunos($perfil_id);
							while($row = mysqli_fetch_array($dataalunos))
							{
								echo'<option value="'.$row['email'].'">'.$row['user_name'].'</option>';
							}
						}
						else {
							$dataorientador= $ask->getUserOrientador($perfil_id);
							$datacoorientador= $ask->getUserCoOrientador($perfil_id);
							$ProjectID = $ask->getAlunoProjectID($perfil_id);
							while($row = mysqli_fetch_array($dataorientador))
							{
								echo'<option value="'.$row['email'].'">'.$row['user_name'].'</option>';
							}
							while($row = mysqli_fetch_array($datacoorientador))
							{
								echo'<option value="'.$row['email'].'">'.$row['user_name'].'</option>';
							}
						}
						?>
					</select>
					<input name="nomeevento" type="text" class="pure-input-rounded" placeholder="Dê nome ao evento" >
					<?php if($isProfessor){
						echo '<input name="sala" type="text" class="pure-input-rounded" placeholder="Sala do evento" >';
					}
					?>
					<input name="data" type="date" class="pure-input-rounded" placeholder="Data do evento" >
					<input name="hora_inicio" type="time" class="pure-input-rounded" placeholder="Insira a hora inicial">
					<input name="hora_fim" type="time" class="pure-input-rounded" placeholder="Insira a hora final">
					<select name="tipo" class="pure-input-1-2" style="border-radius: 82px; width:100%;margin: 1em 0;">
						<option value="reuniao">Tipo de reuniao(Default: Reuniao em pessoa):</option>
						<option value="skype">Skype</option>
						<option value="reuniao">Reunião em pessoa</option>
					</select>
					<textarea name="notas" type="text" class="pure-input-rounded" placeholder="Notas" style="border-radius: 82px; width:100%;margin: 1em 0;"></textarea>
					<button type="submit" name="addEvent" class="pure-button pure-button-primary">Enviar</button>
					<?php
					if(isset($_POST['addEvent']))
					{
						if(isset($_POST['email']) && !empty($_POST['nomeevento'])  && !empty($_POST['data']) && !empty($_POST['hora_inicio']) && !empty($_POST['hora_fim']))
						{
							if($ask->checkDate($_POST['data'],$_POST['hora_inicio'],$_POST['hora_fim']))
							{
								$email=$_POST['email'];
								$aux_error_email=$ask->analyzeEmail($email);
								if(strcmp($email,"error")!=0)
								{
									$sala=$_POST['sala'];
									if(strcmp($sala,"")==0)
									{
										$sala="A definir";
									}
									$auxpid= $ask->getUserPerfilId($_POST['email']);

									if($isProfessor)
									{
										$ProjectID = $ask->getAlunoProjectID($auxpid);
									}
									else {
										$ProjectID = $ask->getOrientadorProjectID($auxpid);
									}
									$contacto= $ask->getUserContacts($auxpid);
									$contacto=json_decode($contacto);

									$contacto_aux=$contacto->{'contacto'};
									$skype_id=$contacto->{'skype_id'};
									$cacifo_digital_id=$contacto->{'cacifo_digital_id'};

									$user_aux=$ask->checkUserProject($perfil_id);
									if($ask->checkUserProject($perfil_id))
									{
										$responseE= $ask->insertEvent($_POST['nomeevento'],$sala,$_POST['data'],$_POST['hora_inicio'],$_POST['hora_fim'],0,$_POST['tipo'],$contacto_aux,$_POST['notas'],$cacifo_digital_id,$skype_id,$perfil_id,$email,$ProjectID);
										if($responseE)
										{
											echo '<div class="success">Evento criado com sucesso ! </div>';
										}
										else {
											echo '<div class="error">OOOPS! Algo correu mal!</div>';
										}
									}
									else {
										echo '<div class="error">OOOPS! Ainda não está inserido em nenhum projeto!</div>';
									}
								}
								else {
									echo '<div class="error">OOOPS! Email que inseriu não é valido!</div>';
								}
							}
							else {
								echo '<div class="error">OOOPS! Parace que inseriu data ou horas invalidas !</div>';
							}

						}
						else {
							echo '<div class="error">OOOPS! Falta inserir campo obrigatorios</div>';
						}
					}
					?>
				</form>
			</div>
		</div>
	</div>

	<?php
	if($isProfessor)
	{
		?>
		<div id=<?php echo "addResult"; ?> class="overlay">
			<div class="popup">
				<h2>Resultado de um projeto!</h2>
				<a class="close" href="" >&times;</a>
				<div class="content">
					<form method="post" class="pure-form pure-form-stacked" >
						<select name="projectos" class="pure-input-1-2" style="border-radius: 82px; width:100%;margin: 1em 0;" >
							<?php
							$ask = new Ask();
							$dataprojetos= $ask->getProjectID($perfil_id);
							while($row = mysqli_fetch_array($dataprojetos))
							{
								echo'<option value="'.$row['id'].'">'.$row['nome'].'</option>';
							}
							?>
						</select>
						<input name="classificacao" type="text" class="pure-input-rounded" placeholder="Insira a classificacao do projeto" required>
						<textarea name="notas" type="text" class="pure-input-rounded" placeholder="Notas extras"></textarea>
						<button type="submit" name="addRes" class="pure-button pure-button-primary">Enviar</button>
						<?php
						if(isset($_POST['addRes']))
						{
							if(isset($_POST['projectos']) && !empty($_POST[	'classificacao']))
							{
								if($ask->insertResult($_POST['projectos'],$_POST['classificacao'],$_POST['notas']))
								{
									echo '<div class="success"> Resultado adicionado com sucesso. </div>';
								}
								else {
									echo '<div class="error">OOOPS! Algo correu mal.</div>';
								}
							}
							else {
								echo '<div class="error">OOOPS! Aparenta não ter preenchido os campos obrigatórios.</div>';
							}
						}
						?>
					</form>
				</div>
			</div>
		</div>
		<div id=<?php echo "addStudent"; ?> class="overlay">
			<div class="popup">
				<h2>Adicione um aluno!</h2>
				<a class="close" href="" >&times;</a>
				<div class="content">
					<form method="post" class="pure-form pure-form-stacked" >
						<input name="email" type="email" class="pure-input-rounded" placeholder="Insira o e-email da universidade de quem pretende convidar">
						<button type="submit" name="addS" class="pure-button pure-button-primary">Enviar</button>
						<?php
						if(isset($_POST['addS']))
						{
							$ask = new Ask();
							$response=$ask->analyzeEmail($_POST['email']);
							if(strcmp($response,"s")==0)
							{
								$response=$ask->isEmailValid($_POST['email']);
								if($response==-1)
								{
									$send= new Mail();
									$send->mail_send_code($_POST['email'],$perfil_id);
									echo '<div class="success"> Convite enviado com sucesso. </div>';
								}
								else {
									echo '<div class="error">OOOPS! Aparenta estar a tentar convidar um utilizador existente.</div>';
								}
							}
							else {
								echo '<div class="error">OOOPS! Aparenta estar a tentar usar um email invalido.</div>';
							}

						}
						?>
					</form>
				</div>
			</div>
		</div>
		<div id=<?php echo "addProject"; ?> class="overlay">
			<div class="popup">
				<h2>Adicione um projecto!</h2>
				<a class="close" href="" >&times;</a>
				<div class="content">
					<form method="post" class="pure-form pure-form-stacked" >
						<input name="nomep" type="text" class="pure-input-rounded" placeholder="Dê nome ao projeto" required>
						<input name="tema" type="text" class="pure-input-rounded" placeholder="Tema do projeto" required>
						<select name="area" class="pure-input-1-2" style="border-radius: 82px; width:100%;margin: 1em 0;" required>
							<option value="">Area de estudo:</option>
							<option value="Engenharia Informatica">Engenharia Informatica</option>
							<option value="Engenharia Eletrotecnica">Engenharia Eletrotecnica</option>
						</select>
						<input name="emailcoo" type="email" class="pure-input-rounded" placeholder="Insira o e-email do co-orientador se existir">
						<div id="emails">
							<input name="email[]" type="email" class="pure-input-rounded" placeholder="Insira o e-email da universidade de quem pretende convidar para o projeto" required>
						</div>
						<button type="button" name="addEMAIL" class="pure-button pure-button-primary" onClick="addEmail('emails');">Adicionar mais alunos</button>
						<button type="submit" name="addProject" class="pure-button pure-button-primary">Enviar</button>
						<?php
						if(isset($_POST['addProject']))
						{
							$emailproject=$_POST['email'];
							if(!empty($_POST['nomep']) && !empty($_POST['tema']) && isset($_POST['area']) && !empty($emailproject[0]))
							{
								$count=0;
								$ask = new Ask();
								for($i=0;$i<sizeof($emailproject);$i++)
								{
									$response=$ask->analyzeEmail($emailproject[$i]);
									if(strcmp($response,"s")==0)
									{
										$count++;
									}
								}
								if($count==sizeof($emailproject))
								{
									//função tem de retornar true
									$emailcoo=$_POST['emailcoo'];
									if(!isset($_POST['emailcoo']))
									{
										$emailcoo=null;
									}
									$responseP= $ask->insertProject($_POST['nomep'],$_POST['tema'],$_POST['area'],$_POST['emailcoo'],$emailproject,$perfil_id);
									if($responseP)
									{
										for($i=0;$i<sizeof($emailproject);$i++)
										{
											$send= new Mail();
											$send->mail_send_project($emailproject[$i],$perfil_id,$_POST['nomep']);
										}
										echo '<div class="success">Projecto criado com sucesso ! </div>';
									}
									elseif (strcmp($responseP,"error")==0) {
										echo '<div class="error">OOOPS! Campo co-orientador mal inserido !</div>';
									}
									else {
										echo '<div class="error">OOOPS! Algo correu mal(Pode ser email de aluno invalido) !</div>';
									}
								}
								else {
									echo '<div class="error">OOOPS! Aparenta estar a tentar usar um email invalido.</div>';
								}
							}
							else {
								echo '<div class="error">OOOPS! Aparenta não ter preenchido os campos obrigatórios.</div>';
							}
						}
						?>
					</form>
				</div>
			</div>
		</div>
		<div id="add_icon" class="dropdownadd-contentadd">
			<a href="<?php echo "#addProject"; ?>"> Adicionar projecto</a>
			<a href="<?php echo "#addStudent"; ?>"> Adicionar aluno</a>
			<a href="<?php echo "#addEvent"; ?>"> Adicionar evento</a>
			<a href="<?php echo "#addResult"; ?>"> Adicionar Resultado</a>
			<a href="<?php echo "#addDoc"; ?>"> Enviar Documento</a>
			<a href="estatisticas.php"> Ver estatistica</a>
		</div>
		<?php
	}
	else {
		?>
		<div id="add_icon" class="dropdownadd-contentadd">
			<a href="<?php echo "#addEvent"; ?>"> Adicionar evento</a>
			<a href="<?php echo "#addDoc"; ?>"> Enviar Documento</a>
		</div>
		<?php
	}
	?>
</div>
<script>
/*Quando o utilizador clica no botão ativa as respetivas opções  */
function open_add() {
	document.getElementById("add_icon").classList.toggle("show");
}
// Fecha quando o utilizador clica fora do botão
window.onclick = function(e) {
	if (!e.target.matches('.dropbtnadd')) {
		var myDropdown = document.getElementById("add_icon");
		if (myDropdown.classList.contains('show')) {
			myDropdown.classList.remove('show');
		}
	}
}
var counterE = 1;
var limitE = 4;
function addEmail(divName){

	if (counterE == limitE)  {
		alert("Atingiu o máximo de Alunos que pode convidar");
	}
	else {
		var newdiv = document.createElement('div');
		newdiv.innerHTML = "<input name='email[]' type='email' class='pure-input-rounded' placeholder='Insira o e-email da universidade de quem pretende convidar para o projeto' >";
		document.getElementById(divName).appendChild(newdiv);
		counterE++;
	}
}
</script>
<div style=" z-index: 5;" class="footer">
	© 2018! Laboratório projecto integrado Pedro Costa Nº: 31179 & Paulo Bento Nº: 33595.
</div>
</body>
</html>
