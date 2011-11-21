<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	include('../../include/Xp.php');
	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];
	if($Man_Niveau==1)
	{
		$IdPilote=mysql_fetch_row(mysql_query("SELECT IdPilote FROM pilote WHERE Pil_IdManager='$IdManager'"));

		header("location:fiche.php?IdPilote=".$IdPilote[0]);
		exit;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Liste Pilote</title>
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
		<td>
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td>
<div align="center">
<?php


	$requeteInfoPilotes = "	SELECT 	IdPilote,
												Pil_Nom,
												Pil_Age,
												Pil_Reputation,
												Pil_Satisfaction,
												Pil_Solde,
												Pil_XPShifts,
												Pil_XPFreinage,
												Pil_XPVirage,
												Pil_XPSpe,
												Pil_Style,
												Pil_Chance,
												Pil_PourcentageGains
										FROM pilote
										WHERE Pil_IdManager = '$IdManager'
										ORDER BY Pil_nom";
		$resultatInfoPilotes = mysql_query($requeteInfoPilotes);
	echo mysql_error();
?>
<table border="1" width="90%">
	<tr>
		<th colspan="18">Pilotes oisifs</th>
	</tr>
	<tr>
		<th>Nom</th>
		<th>Age</th>
		<th>Reputation</th>
		<th>Satisfaction</th>
		<th>Niveau Total</th>
		<th>XP Total</th>
		<th>Style</th>
		<th>Chance</th>
		<th>% Gains</th>
	</tr>
<?php
	while($pilote = mysql_fetch_assoc($resultatInfoPilotes))
	{
?>
	<tr>
		<td><a href="fiche.php?IdPilote=<?php echo $pilote['IdPilote']; ?>"><?php echo $pilote['Pil_Nom'];?></a></td>
		<td><?php echo $pilote['Pil_Age'];?></td>
		<td><?php echo $pilote['Pil_Reputation'];?></td>
		<td><?php echo $pilote['Pil_Satisfaction'];?></td>
		<td><?php echo 	niveauAdd($pilote['Pil_XPShifts'],1000)+
					niveauAdd($pilote['Pil_XPFreinage'],1000)+
					niveauAdd($pilote['Pil_XPVirage'],1000)+
					niveauAdd($pilote['Pil_XPSpe'],1000);?></td>
		<td><?php echo 	$pilote['Pil_XPShifts']+
             	$pilote['Pil_XPFreinage']+
					$pilote['Pil_XPVirage']+
					$pilote['Pil_XPSpe'];?></td>
		<td><?php echo $pilote['Pil_Style'];?></td>
		<td><?php echo $pilote['Pil_Chance'];?></td>
		<td><?php echo $pilote['Pil_PourcentageGains'];?></td>
	</tr>
<?php
	}
?>
</table>
<br>
<hr>
<br>
<?php
	$requeteInfoPilotesLibres="	SELECT 	IdPilote,
														Pil_Nom,
														Pil_Age,
														Pil_Reputation,
														Pil_Satisfaction,
														Pil_Solde,
														Pil_XPShifts,
														Pil_XPFreinage,
														Pil_XPVirage,
														Pil_XPSpe,
														Pil_Style,
														Pil_Chance,
														Pil_PourcentageGains
											FROM pilote
											WHERE Pil_IdManager = '0'
											ORDER BY Pil_nom";
	$resultatInfoPilotesLibres = mysql_query($requeteInfoPilotesLibres) or die (mysql_error());
?>
<table border="1" width="90%">
	<tr>
		<th colspan="18">Pilotes libres</th>
	</tr>
	<tr>
		<th>Nom</th>
		<th>Age</th>
		<th>Reputation</th>
		<th>Satisfaction</th>
		<th>Niveau Total</th>
		<th>XP Total</th>
		<th>Style</th>
		<th>Chance</th>
		<th>% Gains</th>
	</tr>
<?php
	while($piloteLibre = mysql_fetch_assoc($resultatInfoPilotesLibres))
	{
?>
	<tr>
		<td><a href="fiche.php?IdPilote=<?php echo $piloteLibre['IdPilote']; ?>"><?php echo $piloteLibre['Pil_Nom'];?></a></td>
		<td><?php echo $piloteLibre['Pil_Age'];?></td>
		<td><?php echo $piloteLibre['Pil_Reputation'];?></td>
		<td><?php echo $piloteLibre['Pil_Satisfaction'];?></td>
		<td><?php echo 	niveauAdd($piloteLibre['Pil_XPShifts'],1000)+
					niveauAdd($piloteLibre['Pil_XPFreinage'],1000)+
					niveauAdd($piloteLibre['Pil_XPVirage'],1000)+
					niveauAdd($piloteLibre['Pil_XPSpe'],1000);?></td>
		<td><?php echo 	$piloteLibre['Pil_XPShifts']+
             	$piloteLibre['Pil_XPFreinage']+
					$piloteLibre['Pil_XPVirage']+
					$piloteLibre['Pil_XPSpe'];?></td>
		<td><?php echo $piloteLibre['Pil_Style'];?></td>
		<td><?php echo $piloteLibre['Pil_Chance'];?></td>
		<td><?php echo $piloteLibre['Pil_PourcentageGains'];?></td>
	</tr>
<?php
	}
?>
</table>
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
