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

/*
	echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";
*/
?>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdPilote)
		{
			document.location="gestion.php?action=Modifier&IdPilote="+IdPilote;
		}
		function verifForm(form)
		{
			with(form)
			{
				var chaineErreur = "";
				if(form.Pil_PourcentageGains.value != "null")
					if(form.Pil_PourcentageGains.value > 100 || form.Pil_PourcentageGains.value < 0) chaineErreur =	"Pourcentage des gains";

				if (chaineErreur != "")
				{
					alert("Le champ suivant est incorrect :\n"+chaineErreur);
					return false;
				}
				else
				{
					form.verificationJs.value = true;
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
			$infoPilote="";
		}
		else
		{
			$IdPilote = $_GET["IdPilote"];

			$requeteInfoPilote = "	SELECT 	IdPilote, Pil_Nom, Pil_Satisfaction, Pil_PourcentageGains
									FROM pilote
									WHERE IdPilote = '$IdPilote'";
			$resultatInfoPilote=mysql_query($requeteInfoPilote)or die(mysql_error());
			$infoPilote=mysql_fetch_assoc($resultatInfoPilote);
		}
	}
	else
	{
?>
<table align="center"><tr><td><img src="../images/warningpetit.gif"></td><td>Il y a une ou plusieurs erreurs dans le formulaire :</td></tr></table>
<div align="center" class="erreur"><BR>
  <?php
		$infoPilote = $_SESSION["Post"];

		foreach ($_SESSION["Codes"] as $codeErreur)
		{
			$requeteMessageErreur="	SELECT MsgEr_Message
									FROM message_erreur
									WHERE MsgEr_Code = $codeErreur";
			$resultatMessageErreur=mysql_query($requeteMessageErreur)or die(mysql_error());
			$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
			echo ($messageErreur["MsgEr_Message"]);?>
  <BR>
  <?php
 		}
	}
?>
</div>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdManager" value="<?php echo $IdManager;?>">
<input type="hidden" name="IdPilote" value="<?php echo $infoPilote["IdPilote"]; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
<div align="center">
	<table border="1">
		<tr>
			<th colspan="3"><?php echo $infoPilote['Pil_Nom'];?></td>
		</tr>
<?php
	if($Man_Niveau > 1)
	{
?>
		<tr>
			<th>Pourcentage gains<font color="#FF0000">*</font> :</th>
			<td colspan="2"><input type="text" size="3" name="Pil_PourcentageGains" value="<?php echo $infoPilote['Pil_PourcentageGains'];?>"> %</td>
		</tr>
<?php
	}
	else
	{
?>
			<input type="hidden" name="Pil_PourcentageGains" value="null">
<?php
	}
?>
	</table>
<br>
	<table>
		<tr>
			<td align="center" colspan="3"><br>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]; ?>"><br>
			</td>
			<td align="center" colspan="3"><br>
		<?php echo ($_GET["action"]=="Ajouter")? "<input type=\"reset\" value=\"Effacer saisie\">":
			"<input type=\"button\" onclick=\"annulModif(".$infoPilote['IdPilote'].")\" value=\"Annuler les modifications\">";?><br>
			</td>
		</tr>
	</table>
</div>
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
