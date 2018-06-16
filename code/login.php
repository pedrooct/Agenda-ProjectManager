<?php
session_start();
include_once 'api/access.php';
require_once 'api/ask.php';
?>

<html lang="pt-PT">
<head>
	<meta charset="utf-8">
	<meta name="description" content="login">
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
			<a style="display: inline-flex !important" class="pure-menu-heading" href="">Bem-vindo! faça o seu login</a>
		</div>
	</div>
	<div class="content">
		<div class="pure-g">
			<div class=".pure-u-1-1 pure-u-sm-1-2">
				<form class="pure-form pure-form-stacked" action="login.php" method="POST">
					<label for="email">Login:</label>
					<input name="email" type="text" class="pure-input-rounded" placeholder="E-mail de registo" <?php echo isset($_POST['email']) ? "value='" .$_POST['email']."'":""; ?> required >
					<input name="password" type="password" class="pure-input-rounded" placeholder="Password">
					<button type="submit" name="login" class="pure-button pure-button-primary">login</button>
					<button type="submit" name="recover" class="pure-button pure-button-primary">Recuperar password</button>
				</form>
				<form class="pure-form pure-form-stacked" method="POST">
					<button type="submit" name="registo" class="pure-button pure-button-primary">Registar</button>
				</form>
			</div>
		</div>
	</div>
	<?php
	if(isset($_POST['login']))
	{
		if(empty($_POST['password']))
		{
			echo '<div class="error">OOOPS! Esqueceu-se de algo </div>';
		}
		else
		{
			$email = $_POST['email'];
			$ask = new Ask();
			$response_ask = $ask->isValid($email);
			if($response_ask != 0)
			{
				$password= $_POST['password'];
				$login = new Login($email,$password);
				$response= $login->login();
				if(!$response){
					echo '<div class="error">OOOPS! Algo correu mal no processo, palavra-passe ou email errados ? </div>';
				}
				else {
					$_SESSION['email']=$email;
					$response=json_decode($response);
					$_SESSION['name'] = $response->user_name;
					$_SESSION['perfil_id'] = $response->perfil_id;
					header("location: dashboard");
				}
			}
			else {
				echo '<div class="error">OOOPS! Email não é valido </div>';
			}
		}
	}
	if(isset($_POST['registo']))
	{
		header("location: http://app.test/lpi/code/preregisto");
	}
	if(isset($_POST['recover']))
	{
		if(isset($_POST['email']))
		{
			$email= $_POST['email'];
			$ask= new Ask();

			$response_ask=$ask->isEmailValid($email);
			if($response_ask != -1 )
			{
				if(!$ask->verifyCodePassLost($email."passlost"))
				{
					$ask->geraPassLostCode($email."passlost");
					$acessdb= new AccessDB('UPDATE utilizador SET is_email_valid=0 , is_email_pessoal_valid=0 WHERE email='."'".$email."'");
					$response = $acessdb->atualizar();
					$pass = new Mail();
					$response=$pass->mail_Send_recover($email);
					if(!$response)
					{
						echo '<div class="error">OOOPS! Algo correu mal no processo </div>';
					}
					else {
						echo '<div class="success">Verifique o seu email para mais informações!</div>';
					}
				}
				else {
					echo '<div class="error">OOOPS! Ainda não passou o periodo de espera ! </div>';
				}
			}
			else {
				echo '<div class="error">OOOPS! Email invalido ! </div>';
			}
		}
		else {
			echo '<div class="error">OOOPS! Insira um Email </div>';
		}

	}
	?>
	<div class="footer">
		© 2018! Projecto Laboratório Pedro Costa Nº: 31179 & Paulo Bento Nº:33959 .
	</div>
</div>
</body>
</html>
