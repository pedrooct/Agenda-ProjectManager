<?php
session_start();
include_once 'api/access.php';
include_once 'api/ask.php';

if(strcmp($_SESSION['type'],"s")!=0 && $_SESSION['code']!=true )
{
	header('location: preregisto');
}
$type=$_SESSION['type'];
$semail=$_SESSION['email'];
$flag= $_SESSION['close'];
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
			<a style="display: inline-flex !important" class="pure-menu-heading" href="">Faça o seu Registo</a>
		</div>
		<?php
		if(isset($_POST['registo']))
		{
			if(!empty($_POST['nome']) && !empty($_POST['unome']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['contacto']) && !empty($_POST['cpassword']) && !isset($_POST['Formacao']))
			{
				$ask = new Ask();
				$response = $ask->analyzePassword($_POST['password'],$_POST['cpassword']);
				if($response)
				{
					if(strcmp($semail,$_POST['email'])==0)
					{
						$response = $ask->analyzeCellPhoneNumber($_POST['contacto']);
						if ($response)
						{
							$email= $_POST['email'];
							$pemail= $_POST['emailp'];
							$password= $_POST['password'];
							$user_name= $_POST['nome'].' '.$_POST['unome'];
							if(empty($_FILES["foto"]))
							{
								$foto = false;
								$foto_ex= false;
							}
							else {
								$foto = $_FILES["foto"]["tmp_name"];
								$foto_ex= $_FILES["foto"]["type"];
								$ask= new Ask();
								if(!$ask->isImage($foto,$foto_ex))
								{
									$foto=false;
									$foto_ex=false;
								}
							}
							$contacto= $_POST['contacto'];
							$skype_id= $_POST['skype_id'];
							if(!empty($_POST['cacifo_id']) && $ask->checkCacifoURL($_POST['cacifo_id']))
							{
								$cacifo_id= $_POST['cacifo_id'];
							}
							else {
								$cacifo_id="";
							}
							$notificacao= $_POST['notificacao'];
							$formacao= $_POST['formacao'];
							$registo = new RegistoAluno($user_name,$email,$pemail,$password,$formacao,$foto,$foto_ex ,$contacto,$skype_id,$cacifo_id,$notificacao);
							if($registo->registoAluno())
							{
								$_SESSION['close']=true;
								header('location: registoStudent');
							}
							else {
								echo '<div class="error">OOOPS! Ocorreu um problema  </div>';
							}
						}
						else {
							echo '<div class="error">OOOPS! Numero de conctato invalido </div>';
						}
					}
					else {
						echo '<div class="error">OOOPS! Aparenta estar a tentar trocar o email inserido! </div>';
					}

				}
				else {
					echo '<div class="error">OOOPS! Palavra-passe aparenta estar diferente ou não cumpre os parametros necessários </div>';
				}
			}
			else {
				echo '<div class="error">OOOPS! Aparenta não estar a preencher campos obrigatórios </div>';
			}
		}
		?>
	</div>
	<div class="content">
		<div class="pure-g">
			<div class="l-box-lrg pure-u-2 pure-u-md-2-5">
				<?php
				if(!$flag)
				{
					?>
					<form class="pure-form pure-form-stacked" enctype="multipart/form-data" method="POST">
						<label for="foto">Registo:</label>
						<img id="uploadPreview" class="imgpreview" src="/lpi/resources/icons/account_icon.png" style="width: 120px; height: 120px;" />
						<label for="upload" class="file-upload__label">Insira uma foto de perfil </label>
						<input id="upload" name="foto" class="file-upload__input" type="file" onchange="PreviewImage();">
						<input name="nome" type="text" class="pure-input-rounded" placeholder="Insira o seu primeiro nome" <?php echo isset($_POST['nome']) ? "value='" .$_POST['nome']."'":""; ?> required >
						<input name="unome" type="text" class="pure-input-rounded" placeholder="Insira o seu ultimo nome" <?php echo isset($_POST['unome']) ? "value='" .$_POST['unome']."'":""; ?> required >
						<input name="email" type="email" class="pure-input-rounded" placeholder="E-mail de registo" value=<?php echo $semail; ?> readonly required >
						<input name="emailp" type="email" class="pure-input-rounded" placeholder="E-mail pessoal(opcional)" <?php echo isset($_POST['emailp']) ? "value='" .$_POST['emailp']."'":""; ?> >
						<input name="contacto" type="text" class="pure-input-rounded" placeholder="Insira o seu conctato " <?php echo isset($_POST['contacto']) ? "value='" .$_POST['contacto']."'":""; ?>required>
						<input name="skype_id" type="text" class="pure-input-rounded" placeholder="Insira o seu Skype Id " <?php echo isset($_POST['skype_id']) ? "value='" .$_POST['skype_id']."'":""; ?>>
						<label> Como obter o link
							<a href=<?php echo "#popup"; ?>> ? </a>
						</label>
						<div id=<?php echo "popup"; ?> class="overlay">
							<div class="popup">
								<h2>Como obter o link do cacifo digital.</h2>
								<a class="close" href="" >&times;</a>
								<div class="content">
									<p>Em primeiro entre no elearning e entre na secção da sua cadeira</p>
									<img  src="/lpi/resources/printcacifo1.png">
									<p>Clique em acções da pasta mãe e selecione "editar detalhes"</p>
									<img  src="/lpi/resources/printcacifo2.png">
									<p>Copie o link que aparece na caixa para a respetiva área da página</p>
									<img  src="/lpi/resources/printcacifo3.png">
								</div>
							</div>
						</div>
						<input name="cacifo_id" type="text" class="pure-input-rounded" placeholder="Insira o seu Cacifo digital URL " <?php echo isset($_POST['cacifo_id']) ? "value='" .$_POST['cacifo_id']."'":""; ?>>
						<select name="notificacao" class="pure-input-1-2" style="border-radius: 82px; width:100%;margin: 1em 0;">
							<option value="1">Notificação de eventos(Default: Diariamente):</option>
							<option value="1">Diariamente - No dia anterior</option>
							<option value="0">Semanalmente(Domingos) - No inicio de cada semana</option>
						</select>
						<select name="formacao" class="pure-input-1-2" style="border-radius: 82px; width:100%;margin: 1em 0;" required>
							<option value="">Formação avançada:</option>
							<option value="0">Mestrado</option>
							<option value="1">Doutoramento</option>
							<option value="2">Pós-Doutoramento</option>
						</select>
						<label for="password">Password tem de ter 8 caracteres, numeros e letras</label>
						<input name="password" type="password" class="pure-input-rounded" placeholder="Password" required>
						<input name="cpassword" type="password" class="pure-input-rounded" placeholder="Confirme a sua Password" required>
						<button type="submit" name="registo" class="pure-button pure-button-primary">Registar</button>
					</form>
					<?php
				}
				else {
					echo '<div class="success">Verifique o seu email para mais instruções.. Pode fechar esta página </div>';
				}
				?>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	/*window.onload=function(){
	document.getElementById("uploadPreview").style.display='none';

}*/
function PreviewImage() {
	var oFReader = new FileReader();
	oFReader.readAsDataURL(document.getElementById("upload").files[0]);

	oFReader.onload = function (oFREvent) {
		document.getElementById("uploadPreview").src = oFREvent.target.result;
		//document.getElementById("uploadPreview").style.display='block';
	};
};
</script>

<div class="footer">
	© 2018! Projecto Laboratório Pedro Costa Nº: 31179 & Paulo Bento Nº:33959 .
</div>
</div>
</body>
</html>
