<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
		exit;
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Liste Joueur</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
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
		<td valign="top">
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td>
<div align="center">
<?php
	$requeteInfoJoueurs = "	SELECT	IdJoueur AS IdGestionJoueur,
												Jou_Pseudo,
												Jou_Login,
												Jou_Email,
												DATE_FORMAT(Jou_DateInscription,'%d/%m/%Y %H:%i:%s') AS Jou_DateInscription,
												DATE_FORMAT(Jou_DernierLogin,'%d/%m/%Y %H:%i:%s') AS Jou_DernierLogin,
												Jou_CodeInscription
									FROM joueur
									ORDER BY IdJoueur";
	$resultatInfoJoueurs = mysql_query($requeteInfoJoueurs)or die("Requete Info Joueurs : $requeteInfoJoueurs<br>".mysql_error());
?>
<table border="1" width="90%" class="liste">
	<tr>
		<th colspan="18">Liste des joueurs | <a href="gestion.php?action=Envoyer">Mail général</a></th>
	</tr>
	<tr>
		<th class="titre">Id</th>
		<th class="titre">Pseudo</th>
		<th class="titre">E-Mail</th>
		<th class="titre">Date Inscription</th>
		<th class="titre">Dernier Login</th>
		<th class="titre">Inscription validée</th>
	</tr>
<?php
	while($infoJoueur = mysql_fetch_assoc($resultatInfoJoueurs))
	{
?>
	<tr class="piece">
		<td><a href="fiche.php?IdGestionJoueur=<?php echo $infoJoueur['IdGestionJoueur']; ?>"><?php echo $infoJoueur['IdGestionJoueur'];?></a></td>
		<td><a href="fiche.php?IdGestionJoueur=<?php echo $infoJoueur['IdGestionJoueur']; ?>"><?php echo $infoJoueur['Jou_Pseudo'];?></a></td>
		<td><a href="mailto:<?php echo $infoJoueur['Jou_Pseudo'];?> <<?php echo $infoJoueur['Jou_Email'];?>>"><?php echo $infoJoueur['Jou_Email'];?></a></td>
		<td><?php echo $infoJoueur['Jou_DateInscription']?></td>
		<td><?php echo $infoJoueur['Jou_DernierLogin']?></td>
		<td><?php echo (empty($infoJoueur['Jou_CodeInscription']))?"Oui":"Non";?></td>
	</tr>
<?php
	}
?>
</table>
<br>
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
