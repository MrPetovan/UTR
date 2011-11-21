<?php
	session_name("UTR");
	session_start();
	error_reporting(E_ALL ^ E_NOTICE);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>UTR : Inscription</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script type="text/javascript" language="JavaScript" src="../include/verif.js"></script>
	<script language="JavaScript">
		function verifForm(form)
		{
			with (form)
			{
				var chaineErreur = is_NotNull(Jou_Login.value,"Le login");
				chaineErreur += is_eMail(Jou_Email.value);
				chaineErreur += is_NotNull(Jou_MotDePasse.value,"Le mot de passe");
				chaineErreur += is_NotNull(Jou_MotDePasse2.value,"Le 2e mot de passe");
				if(!is_Null(Jou_MotDePasse.value) && !is_Null(Jou_MotDePasse2.value))
					if(Jou_MotDePasse.value != Jou_MotDePasse2.value)
						chaineErreur += "\t- Les deux mots de passe entrés sont différents\n";

				if (chaineErreur != "")
				{
					alert("Il y a une ou plusieurs erreurs de saisie :\n"+chaineErreur);
					return false;
				}
				else
				{
					verificationJs.value = true;
					return true;
				}
			}
		}
	</script>
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>

    <td colspan="3">
      <?php
	include("../frame/titre.php");
?>
    </td>
	</tr>
	<tr>
		<td width="164" valign="top">
<?php
	include("../frame/menu.php");
?>
		</td>
		<td width="693">
<?php
	include("../include/connexion.inc.php");
?>
		<br>
			<table width="100%" height="34" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="11"><img src="/UTR/design/spacer.gif" width="9" height="24"></td>
					<td width="100%" height="34"><div align="left">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td background="/UTR/design/nav.jpg" height="34" width="485">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Formulaire d'inscription<b></td>
              					<td align="right" background="/UTR/design/navtile.jpg"><img height="34" src="/UTR/design/navdroite.gif" width="2"></td>
							</tr>
						</table>
					</div>
					</td>
				</tr>
			</table>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="2%"><img src="/UTR/design/spacer.gif" width="9" height="64"></td>
					<td width="98%" valign="top">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
<?php
	if(!isset($_SESSION['Erreur_Inscription']))
	{
		$erreur="";
	}
	else
	{
		$erreur = $_SESSION['Post'];

		if($_SESSION['Erreur_Inscription'] == 1)
		{

?>
		<br><br>Ce login existe déjà, veuillez en choisir un autre.</b><br>
<?php
			unset($_SESSION['Erreur_Inscription']);
			unset($_SESSION['Post']);
		}
		else
		{
			echo "<br>Il y a des erreurs dans le formulaire : <br>";
			foreach ($_SESSION["Codes"] as $codeErreur)
			{
				$requeteMessageErreur = "SELECT Err_Message
										FROM erreur
										WHERE IdErreur = $codeErreur";
				$resultatMessageErreur=mysql_query($requeteMessageErreur)or die(mysql_error());
				$messageErreur=mysql_fetch_assoc($resultatMessageErreur);

				echo "<br>".$messageErreur["Err_Message"];?>
<?php
	 		}
		}
	}
?>
									<br><blockquote>Une fois l'inscription enregistrée, un mail vous sera envoyé à l'adresse e-mail que vous aurez entrée. Ce mail contient un lien qu'il vous faudra cliquer pour valider l'inscription.</blockquote>
								</td>
							</tr>
							<tr>
								<td valign="top">
<form name="inscription" action="traitementInscription.php" method="POST" onSubmit="return verifForm(this)">
<input type="hidden" name="verificationJs" value="false">
									<table border="1" cellpadding="1" cellspacing="0" bordercolor="#780000">
									<tr>
										<th>Nom du manager : </th>
										<td colspan="3"><input type="text" name="Man_Nom" value="<?php echo $erreur['Man_Nom']?>"></td>
									</tr>
									<tr>
										<th>Login (8 caractères max) : </th>
										<td colspan="3"><input type="text" size="8" maxlength="8" name="Jou_Login" value="<?php echo $erreur['Jou_Login']?>"></td>
									</tr>
									<tr>
										<th>Mot de passe (8 caractères max) : </th>
										<td width="114"><input type="password" size="8" maxlength="8" name="Jou_MotDePasse" value="<?php echo $erreur['Jou_MotDePasse']?>"></td>
										<th width="198">Mot de passe (vérification) : </th>
										<td width="118"><input type="password" size="8" maxlength="8" name="Jou_MotDePasse2" value="<?php echo $erreur['Jou_MotDePasse2']?>"></td>
									</tr>
									<tr>
										<th>E-Mail : </th>
										<td colspan="3"><input type="text" size="20" name="Jou_Email" value="<?php echo $erreur['Jou_Email']?>"></td>
									</tr>
									<tr>
										<td colspan="4"><input name="submit" type="submit" value="S'inscrire"></td>
									</tr>
								</td>
							</tr>
						</table>
</form>
					</td>
				</tr>
			</table>
			<br>
		</td>
	</tr>
	<tr>
		<td colspan="2"> <div align="center">
<?php
	include("../frame/piedpage.php");
?>
		</div></td>
	</tr>
</table>
</body>
</html>