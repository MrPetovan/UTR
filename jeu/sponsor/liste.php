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
	$IdModelePiece = $_SESSION['IdModelePiece'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Liste des sponsors</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
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
		<td valign="top">
<div align="center">
<table border="1" width="90%">
	<tr>
		<th colspan="6">Liste des sponsors&nbsp;|&nbsp;<a href="gestion.php?action=Ajouter">Ajouter un sponsor</a></th>
	</tr>
	<tr>
		<th>Niv</th>
		<th>Marque</th>
		<th>Salaire</th>
		<th>Gestion</th>
	</tr>
<?php
		$requeteInfoSponsors= "	SELECT	IdSponsor,
													Spon_IdMarque,
													Marq_Libelle,
													Spon_Niveau,
													Spon_Salaire
										FROM sponsor, marque
										WHERE IdMarque = Spon_IdMarque
										ORDER BY Spon_Niveau";
		$resultatInfoSponsors = mysql_query($requeteInfoSponsors)or die("Requete Info Sponsor :<br>$requeteInfoSponsors<br><br>".mysql_error());

		while($infoSponsor = mysql_fetch_assoc($resultatInfoSponsors))
		{
?>
<tr>
	<td><?php echo $infoSponsor['Spon_Niveau']?></td>
	<td><?php echo $infoSponsor['Marq_Libelle']?></td>
	<td><?php echo $infoSponsor['Spon_Salaire']?> &euro;</td>
	<td><a href="gestion.php?action=Modifier&IdSponsor=<?php echo $infoSponsor['IdSponsor']?>">Modifier</a>&nbsp;|&nbsp;<a href="traitement.php?action=Supprimer&IdSponsor=<?php echo $infoSponsor['IdSponsor']?>">Supprimer</a></td>
</tr>
<?php
		}
?>
</table>
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
