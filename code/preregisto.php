<?php
session_start();
include_once 'api/access.php';
require_once 'api/ask.php';
/*
<script type="text/javascript">
OpenWin("http://app.test/lpi/code/registoProfessor");
</script>
<script type="text/javascript">
function OpenWin(link) {
	location.assign(link);
}
</script>
*/

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
			<a style="display: inline-flex !important; padding: 0.5em 0.5em;" class="pure-menu-heading" href="">Insira o seu email da Universidade </a>
		</div>
	</div>
	<div class="content">
		<div class="pure-g">
			<div class="l-box-lrg pure-u-2 pure-u-md-2-5">
				<form class="pure-form pure-form-stacked"  method="POST">
					<input name="preemail" type="text" class="pure-input-rounded" placeholder="E-mail de registo" <?php echo isset($_POST['preemail']) ? "value='" .$_POST['preemail']."'":""; ?> required >
					<button type="submit" name="preregisto" class="pure-button pure-button-primary">Inserir</button>
				</form>
			</div>
		</div>
	</div>
	<?php
	if(isset($_POST['preregisto']))
	{
		if(!empty($_POST['preemail']))
		{
			$email= $_POST['preemail'];
			$ask = new Ask();
			$response=$ask->isEmailValid($email);
			if($response== -1)
			{
				$ask = new Ask();
				$response=$ask->analyzeEmail($email);
				if(strcmp($response,"s")==0)
				{
					$_SESSION['email'] = $email;
					$_SESSION['type'] = $response;
					$_SESSION['code'] = false;
					header('location: validatecode');
				}
				if(strcmp($response,"p")==0)
				{
					$_SESSION['email'] = $email;
					$_SESSION['type'] = $response;
					$_SESSION['code'] = true;
					header('location: registoProfessor');
				}
				echo '<div class="error">OOOPS! Aparenta estar a tentar usar um email invalido.</div>';
			}
			else {
				echo '<div class="error">OOOPS! Aparenta estar a tentar usar um email que ja esta registado. </div>';
			}
		}
		else {
			echo '<div class="error">OOOPS! Aparenta não estar inserir nenhum email </div>';
		}

	}
	?>
	<div class="footer">
		© 2018! Projecto Laboratório Pedro Costa Nº: 31179 & Paulo Bento Nº:33959 .
	</div>
</div>
</body>
</html>
