<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];

	if($Man_Niveau < 3)
	{
		header("location:../../index.php");
		exit;
	}
?>
<html>
<head>
	<title>UTR : Modifier un joueur</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdJoueur)
		{
			document.location="gestion.php?action=Modifier&IdJoueur="+IdJoueur;
		}
function verifForm(form)
		{
			return true;
			with(form)
			{
				var chaineErreur = is_NotNull(Jou_Login.value,"Le login");
				chaineErreur += is_eMail(Jou_Email.value);
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
<table width="100%">
	<tr>
		<td colspan="3">
<?php
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td>
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td>
<?php

	if(isset($_GET["action"])&& !isset($_SESSION["Erreur"]))
	{
		if($_GET["action"]=="Ajouter")
		{
			$infoJoueur="";
		}
		elseif($_GET["action"]=="Modifier")
		{
			$IdGestionJoueur = $_GET["IdGestionJoueur"];

			$requeteInfoJoueur = "	SELECT	IdJoueur AS IdGestionJoueur,
														Jou_Pseudo,
														Jou_Login,
														Jou_Email,
														Jou_DateInscription,
														Jou_DernierLogin,
														Jou_CodeInscription
											FROM joueur
											WHERE IdJoueur = '$IdGestionJoueur'";
			$resultatInfoJoueur=mysql_query($requeteInfoJoueur)or die(mysql_error());
			$infoJoueur=mysql_fetch_assoc($resultatInfoJoueur);
		}
	}
	else
	{
?>
Il y a une ou plusieurs erreurs dans le formulaire :<BR>
  <?php
		$infoJoueur = $_SESSION["Post"];

		foreach ($_SESSION["Codes"] as $codeErreur)
		{
			$requeteMessageErreur="	SELECT Err_Message
											FROM erreur
											WHERE IdErreur = $codeErreur";
			$resultatMessageErreur=mysql_query($requeteMessageErreur)or die("Requete Message Erreur : $requeteMessageErreur<br>".mysql_error());
			$messageErreur=mysql_fetch_assoc($resultatMessageErreur);

			echo "<br>".$messageErreur["Err_Message"];
 		}
		unset($_SESSION['Erreur']);
		unset($_SESSION['Post']);
	}

	if(isset($_GET["IdGestionJoueur"]))
	{
?>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdGestionJoueur" value="<?php echo $infoJoueur['IdGestionJoueur']; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
	<table border="1">
		<tr>
			<th colspan="2">Modifier les informations du joueur n°<?php echo $infoJoueur['IdGestionJoueur'];?></td>
		</tr>
		<tr>
			<th>Pseudo<font color="#FF0000">*</font> :</th>
			<td><input type="text" name="Jou_Pseudo" value="<?php echo $infoJoueur['Jou_Pseudo'];?>"></td>
		</tr>
		<tr>
			<th>Login<font color="#FF0000">*</font> :</th>
			<td><input type="text" name="Jou_Login" value="<?php echo $infoJoueur['Jou_Login'];?>"></td>
		</tr>
		<tr>
			<th>Nouveau mot de passe :</th>
			<td><input type="password" name="Jou_MotDePasse" value="<?php echo $infoJoueur['Jou_MotDePasse']?>"></td>
		</tr>
		<tr>
			<th>Nouveau mot de passe (confirmation) :</th>
			<td><input type="password" name="Jou_MotDePasse2" value="<?php echo $infoJoueur['Jou_MotDePasse2']?>"></td>
		</tr>
		<tr>
			<th>E-mail<font color="#FF0000">*</font> :</th>
			<td><input type="text" size="30" name="Jou_Email" value="<?php echo $infoJoueur['Jou_Email'];?>"></td>
		</tr>
	</table>
<?php
	}
	else
	{
?>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
	<table border="1">
		<tr>
			<th colspan="2">Envoyer un mail collectif</td>
		</tr>
		<tr>
			<th>Sujet :</th>
			<td><input type="text" name="Mail_Sujet" size="90" value="UTR : "></td>
		</tr>
		<tr>
			<td colspan="2">Bonjour [pseudo],<br>ceci est un mail commun envoyé à tous les joueurs d'UTR.<br><hr>
				<textarea name="Mail_Texte" cols="100" rows="10"></textarea><br>
				<hr>
				Si vous ne souhaitez plus jouer à UTR, faites-le savoir au Webmaster.<br>-----<br>Le Pacha
			</td>
		</tr>
	</table>
<?php
	}
?>
<br>
	<table>
		<tr>
			<td>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]; ?>"><br>
			</td>
			<td>
<?php
	if($_GET['action']=="Envoyer")
	{
?>
				<input type="reset" value="Annuler les modifications">
<?php
	}
	else
	{
?>
				<input type="button" onclick="annulModif(<?php echo $infoJoueur['IdJoueur']?>)" value="Annuler les modifications">
<?php
	}
?>
			</td>
		</tr>
	</table>
</form>
<div align="center"><font color="#FF0000">*</font> : Champ obligatoire
</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
<?php
	include("../../frame/piedpage.php");
?>
		</td>
	</tr>
</table>
</body>
</html>
