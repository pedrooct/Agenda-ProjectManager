<?php
session_start();
include_once 'api/access.php';
require_once 'api/ask.php';
/*
?>
<script type="text/javascript">
OpenWin("http://app.test/lpi/code/registoStudent");
</script>
<?php
<script type="text/javascript">
function OpenWin(link) {
	location.assign(link);
}
*/
?>

</script>
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
			<a style="display: inline-flex !important" class="pure-menu-heading" href="">Bem-vindo! Insira o código recebido no seu email</a>
		</div>
	</div>
	<div class="content">
		<div class="pure-g">
			<div class="l-box-lrg pure-u-2 pure-u-md-2-5">
				<form class="pure-form pure-form-stacked" method="POST">
					<input name="code" type="password" class="pure-input-rounded" placeholder="Insira o código" required >
					<button type="submit" name="codebt" class="pure-button pure-button-primary">Confirmar</button>
				</form>
			</div>
		</div>
	</div>
	<?php
	if(isset($_POST['codebt']))
	{
		$code=$_POST['code'];
		if(!empty($code))
		{
			$ask = new Ask();
			$response=$ask->verifyCode($code);
			if($response)
			{
				$_SESSION['code']=true;
				$_SESSION['close']=false;
				header('location: registoStudent');
			}
			echo '<div class="error">OOOPS! Aparenta estar a tentar usar um codigo invalido </div>';

		}
		else {
			echo '<div class="error">OOOPS! Aparenta não estar a inserir código nenhum!  </div>';
		}
	}
	?>
	<div class="footer">
		© 2018! Projecto Laboratório Pedro Costa Nº: 31179 & Paulo Bento Nº:33959 .
	</div>
</div>
</body>
</html>
