<?php
session_start();
require_once "api/access.php";
require_once "api/access_db.php";
require_once "api/ask.php";

if(empty($_GET['id']))
{
	header('location: login');
}

$perfil_id=$_GET['id'];
$flag= $_SESSION['recuperarpass'];
?>

<html lang="pt-PT">
<head>
	<meta charset="utf-8">
	<meta name="description" content="Recuperação de palavra-passe">
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
			<a style="display: inline-flex !important; padding: 0.5em 0.5em;" class="pure-menu-heading" href="">Escreva uma nova palavra passe !</a>
		</div>
	</div>
	<div class="content">
		<div class="pure-g">
			<div class=".pure-u-1-1 pure-u-sm-1-2">
				<?php
				if(!$flag)
				{
					?>
					<form class="pure-form pure-form-stacked" method="POST">
						<fieldset>
							<label for="password" >Insira uma nova palavra-passe:</label>
							<input name="password" type="password" class="pure-input-rounded" placeholder="Password" required>
							<input name="n_password" type="password" class="pure-input-rounded" placeholder="Confirmar Password"required>
						</fieldset>

						<button type="submit" name="registo" class="pure-button pure-button-primary">Atualizar palavra-passe</button>
					</form>

					<?php
					if(isset($_POST['registo']))
					{
						$pass= $_POST['password'];
						$passn= $_POST['n_password'];
						$ask= new Ask();
						if($ask->analyzePassword($pass,$passn))
						{
							$getmail = new Ask();
							$response_email = $getmail->getEmailPerfil($perfil_id);
							$recuperar= new Recuperacao($response_email,$pass,$perfil_id,$passn);
							$response=$recuperar->recuperar();
							if($response)
							{
								$_SESSION['recuperarpass']=true;
								header('location: recuperarpassword?id='.$perfil_id);
							}
							else if (!$response) {
								echo '<div class="error">OOOPS! Algo correu mal!! </div>';
							}
						}
						else {
							echo '<div class="error">OOOPS! As duas palavra-passe aparentao não cumprir os parametros necessários</div>';
						}
					}
				}
				elseif($flag)
				{
					echo '<form class="pure-form pure-form-stacked" method="POST">';
					echo '<div class="success">Aviso : O processo pode demorar vários minutos. Verifique o seu email para mais informações! Pode fechar a página !</div>';
					echo '<button type="submit" name="reenviar" class="pure-button pure-button-primary">Não recebeu?  clique aqui!</button>';
					echo '</form>';
					if(isset($_POST['reenviar']))
					{
						$_SESSION['recuperarpass']=false;
						header('location: recuperarpassword?id='.$perfil_id);
					}
				}
				?>
			</div>
		</div>
	</div>
	<div class="footer">
		© 2018! Projecto Laboratório Pedro Costa Nº: 31179 & Paulo Bento Nº:33959 .
	</div>
</body>
</html>
