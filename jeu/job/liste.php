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
	<title>UTR : Liste des Job</title>
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
		<th colspan="6">Liste des jobs&nbsp;|&nbsp;<a href="gestion.php?action=Ajouter">Ajouter un job</a></th>
	</tr>
	<tr>
		<th>Niv</th>
		<th>Nom Masculin</th>
		<th>Nom Féminin</th>
		<th>Salaire</th>
		<th>Gestion</th>
	</tr>
<?php
		$requeteInfoJobs = "	SELECT	IdJob,
												Job_NomMasculin,
												Job_NomFéminin,
												Job_Niveau,
												Job_Salaire
									FROM job
									ORDER BY Job_Niveau";
		$resultatInfoJobs = mysql_query($requeteInfoJobs)or die("Requete Info Job :<br>$requeteInfoJobs<br><br>".mysql_error());

		while($infoJob = mysql_fetch_assoc($resultatInfoJobs))
		{
?>
<tr>
	<td><?php echo $infoJob['Job_Niveau']?></td>
	<td><?php echo $infoJob['Job_NomMasculin']?></td>
	<td><?php echo $infoJob['Job_NomFéminin']?></td>
	<td><?php echo $infoJob['Job_Salaire']?> &euro;</td>
	<td><a href="gestion.php?action=Modifier&IdJob=<?php echo $infoJob['IdJob']?>">Modifier</a>&nbsp;|&nbsp;<a href="traitement.php?action=Supprimer&IdJob=<?php echo $infoJob['IdJob']?>">Supprimer</a></td>
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
