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
	<title>UTR : Liste Manager</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
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
		<td>
<div align="center">
<?php
		$requeteManagers = "	SELECT 	IdManager AS IdGestionManager,
												Man_Nom,
												Man_Sexe,
												Man_Niveau,
												Man_Solde,
												Man_Reputation,
												Man_Chance,
												Man_IdJob,
												Job_NomMasculin,
												Job_NomFéminin,
												Job_Salaire,
												Man_IdSponsor,
												Spon_IdMarque,
												Marq_Libelle,
												Spon_Salaire,
												Man_IdJoueur,
												Jou_Pseudo
									FROM manager, job, joueur, sponsor, marque
									WHERE Man_IdJoueur = IdJoueur
									AND IdJob = Man_IdJob
									AND IdSponsor = Man_IdSponsor
									AND IdMarque = Spon_IdMarque";
		$resultatManagers = mysql_query($requeteManagers) or die(mysql_error());
?>
	<table border="0" class="liste">
		<tr>
			<th colspan="9" class="titre">Managers</th>
		</tr>
		<tr>
			<th class="titre">Id</th>
			<th class="titre">Nom</th>
			<th class="titre">Niveau</th>
			<th class="titre">Solde</th>
			<th class="titre">Réputation</th>
			<th class="titre">Chance</th>
			<th class="titre">Job/Sponsor</th>
			<th class="titre">Salaire</th>
			<th class="titre">Joueur</th>
		</tr>
<?php
	while($infoManager = mysql_fetch_assoc($resultatManagers))
	{
?>
		<tr class="piece">
			<td><a href="fiche.php?IdManager=<?php echo $infoManager['IdGestionManager']?>"><?php echo $infoManager['IdGestionManager']?></a></td>
			<td><a href="fiche.php?IdManager=<?php echo $infoManager['IdGestionManager']?>"><?php echo $infoManager['Man_Nom']?></a></td>
			<td><?php echo $infoManager['Man_Niveau']?></td>
			<td><?php echo $infoManager['Man_Solde']?> &euro;</td>
			<td><?php echo $infoManager['Man_Reputation']?></td>
			<td><?php echo $infoManager['Man_Chance']?></td>
			<td><?php echo ($infoManager['Man_Niveau'] == 1)?$infoManager['Job_Nom'.$infoManager['Man_Sexe']]:$infoManager['Marq_Libelle']?></td>
			<td><?php echo ($infoManager['Man_Niveau'] == 1)?$infoManager['Job_Salaire']:$infoManager['Spon_Salaire']?> &euro;</td>
			<td><a href="../joueur/fiche.php?IdJoueur=<?php echo $infoManager['Man_IdJoueur']?>"><?php echo $infoManager['Jou_Pseudo']?></a></td>
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
