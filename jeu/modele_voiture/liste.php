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
	$IdModeleVoiture = $_SESSION['IdModeleVoiture'];



	if($Man_Niveau < 3)
	{
		header("location:../../index.php");
		exit;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Liste Modèles Voiture</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
</head>
<body>
<table width="100%" border="0">
	<tr>
		<td colspan="2">
<?php
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td width="14%">
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td valign="top" align="center">
		<br>
<table border="0" width="90%" class="liste">
	<tr>
		<th colspan="5" class="titre">Liste des modèles de voiture<?php echo ($Man_Niveau >= 3)?" | <a href=\"gestion.php?action=Ajouter\">Ajouter un modèle</a>":""?></th>
	</tr>
	<tr class="piece">
		<th class="titre">Marque | Modèle</th>
		<th class="titre">Niveau</th>
		<th class="titre">Prix</th>
		<th class="titre">Action</th>
	</tr>
<?php
		$requeteInfoModelesVoiture = "	SELECT	IdModeleVoiture,
																ModVoi_IdMarque,
																Marq_Libelle,
																ModVoi_NomModele,
																ModVoi_Niveau,
																ModVoi_PrixNeuve
													FROM modele_voiture, marque
													WHERE IdMarque = ModVoi_IdMarque
													ORDER BY Marq_Libelle, ModVoi_NomModele";
		$resultatInfoModelesVoiture = mysql_query($requeteInfoModelesVoiture)or die("Requete Info Modeles Voiture : $requeteInfoModelesVoiture<br>".mysql_error());

		while($infoModeleVoiture = mysql_fetch_assoc($resultatInfoModelesVoiture))
		{
?>
	<tr class="piece">
		<td><a href="fiche.php?IdModeleVoiture=<?php echo $infoModeleVoiture['IdModeleVoiture']?>"><?php echo $infoModeleVoiture['Marq_Libelle']." | ".$infoModeleVoiture['ModVoi_NomModele']?></a></td>
		<td><?php echo $infoModeleVoiture['ModVoi_Niveau']?></td>
		<td><?php echo $infoModeleVoiture['ModVoi_PrixNeuve']?> &euro;</td>
		<td><a href="../voiture/gestion.php?action=Ajouter&IdModeleVoiture=<?php echo $infoModeleVoiture['IdModeleVoiture']?>">Ajouter une voiture</a></td>
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
