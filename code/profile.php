<?php
session_start();
include_once 'api/access.php';
require_once 'api/ask.php';


if(!isset($_SESSION['email']) || !isset($_SESSION['perfil_id']))
{
	header('location: login');
}
$email= $_SESSION['email'];
$name= $_SESSION['name'];
$perfil_id = $_SESSION['perfil_id'];


$get=new Ask();
$data= $get->getUserInfo($perfil_id);
$isProfessor=$get->isProfessor($perfil_id);

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
	<title></title>

</head>

<body>
	<div class="header">
		<div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
			<?php if($isProfessor)
			{
				$studentsData=$get->getUserAlunos($perfil_id);

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
			<a class="pure-menu-heading" href=""> Perfil!</a>
			<a href="dashboard">
				<img class="iconI" alt="Voltar" src="/lpi/resources/icons/gobackarrow.png" />
			</a>
			<form method="post" class="formbar">
				<input type="text" name="search" placeholder="Procurar...">
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
				if(empty($data['foto']))
				{
					?>
					<img class="icon" src="/lpi/resources/icons/account_icon.png" >
					<?php
				}
				else {
					echo '<img class="icon" src="data:'.$data['foto_tipo'].';base64, '.$data['foto'].'" alt="imagem perfil" style="border-radius: 100px;" />';
				}
				?>
				<button class="dropbtn"></button>
			</img>
			<div id="user_icon" class="dropdown-content">
				<a href="dashboard"> Página inicial</a>
				<a href="api/logout"> logout</a>
			</div>
		</div>
		<a href="calendar">
			<img class="icon" alt="Modo calendário" src="/lpi/resources/icons/calendar_google_icon.png" />
		</a>
	</div>
</div>
<div class="content">
	<div class="pure-g">
		<div class="l-box-lrg .pure-u-1-1 pure-u-sm-1-2 pure-u-md-1-1 pure-u-lg-1-1">
			<div class="columns">
				<ul class="price">
					<li class="headerBox">
						<?php
						if($data['foto']==null)
						{
							echo '<img id="uploadPreview" class="imgpreview" src="/lpi/resources/icons/account_icon.png" style="width: 120px; height: 120px;" />';
						}
						else {
							echo '<img id="uploadPreview" class="imgpreview" src="data:'.$data['foto_tipo'].';base64, '.$data['foto'].'" alt="imagem perfil" style="width: 120px; height: 120px;" />';
						}
						?>
					</li>
					<li class="grey"> <?php echo $data['user_name'];?> </li>
					<li class="grey"> <?php echo "Email UFP: ".$data['email'];?> </li>
					<?php
					if ($data['tipo']==1) {
						if(!empty($data['email_pessoal']))
						{
							?>
							<li class="grey"> <?php echo "Email Pessoal: ".$data['email_pessoal'];?> </li>
							<?php
						}
					}
					if($data['tipo']==1)
					{
						if($data['formacao_avancada']==0)
						{
							echo '<li class="grey"> Formação: Mestrado </li>';
						}
						else if($data['formacao_avancada']==1)
						{
							echo '<li class="grey"> Formação: Doutoramento </li>';
						}
						else if($data['formacao_avancada']==2)
						{
							echo '<li class="grey">  Formação: Pós-Douturamentos </li>';
						}
					}
					?>
					<li class="grey"><?php echo "Contacto: ".$data['contacto']; ?></li>
					<?php if(!empty($data['skype_id']))
					{?>
						<li class="grey"><?php echo "SkypeID: "."@".$data['skype_id']; ?></li>
						<?php
					}
					if(!empty($data['cacifo_digital_id'])){
						?>
						<li class="grey">
							<a href= <?php echo $data['cacifo_digital_id']; ?>>elearningURL</a>
						</li>
						<?php
					}
					if($data['notificao']==0)
					{
						echo '<li class="grey"> Tipo de notificação: Semanalmente - No inicio de cada semana </li>';
					}
					else if($data['notificao']==1)
					{
						echo '<li class="grey"> Tipo de notificação: Diariamente - No dia anterior </li>';
					}
					?>
				</ul>
			</div>
		</div>
		<?php
		$projects=$get->getProject($perfil_id);
		while ($row = mysqli_fetch_assoc($projects)) {
			?>
			<div style="padding: 2em" class=".pure-u-1-1 pure-u-sm-1-2 pure-u-md-1-2 pure-u-lg-1-4">
				<div class="columns">
					<ul class="price">
						<li class="headerBox"> Projecto: <?php echo $row['nome'] ;?> </li>
						<li class="grey"> Tema: <?php echo $row['tema'];?> </li>
						<li class="grey"> Area: <?php echo $row['area'];?> </li>
						<li class="grey"> Iniciado a  <?php echo $row['data'];?> </li>
					</ul>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>
<div class="dropdownadd">
	<img class="iconadd" src="/lpi/resources/icons/plus_icon.png" >
	<button class="dropbtnadd" onclick="open_add()"></button>
</img>
<div id="add_icon" class="dropdownadd-contentadd">

	<?php if($isProfessor)
	{
		?>
		<a href="editaPerfilP.php"> Alterar Informação</a>
		<a href="editaHorario.php"> Alterar horario</a>
		<?php
	}
	else {
		?>
		<a href="editaPerfilS.php"> Alterar Informação</a>
		<?php
	}?>
	<a href="scripts/getVCF?id=<?php echo $perfil_id ?>"> Exportar como .VCF</a>
</div>
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
</script>
<div class="footer">
	© 2018! Laboratório projecto integrado Pedro Costa Nº: 31179 & Paulo Bento Nº: 33595.
</div>
</body>
</html>
