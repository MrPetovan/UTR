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

	function msTOkmh($vitesse)
	{
	return($vitesse*3.6);
	}
?>
<html>
<head>
	<title>UTR : Fiche de pilote</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
	<script language="JavaScript">
		function confirmSuppr(form)
		{
			var action=form.action.value;
			//if(action=="Supprimer") confirmation = "Etes-vous sûr de vouloir supprimer ce pilote ?";
			var confirmation = "Etes-vous sûr de vouloir "+action+" ce pilote ?";
			if(confirm(confirmation))
			{
				form.method="POST";
				return true;
			}
			else
			{
				return false;
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
	if(isset($_GET['IdPilote']))
	{
		$IdPilote = $_GET['IdPilote'];

		$requeteInfoPilote="	SELECT 	IdPilote,
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
												Pil_PourcentageGains,
												Pil_IdManager
									FROM pilote
									WHERE IdPilote ='$IdPilote'";
		$resultatInfoPilote=mysql_query($requeteInfoPilote) or die(mysql_error());
		$infoPilote=mysql_fetch_assoc($resultatInfoPilote);

		$requeteVictoires="	SELECT COUNT(IdInscriptionCourse) AS Pil_NombreVictoires
									FROM inscription_course
								WHERE IC_IdPilote = '$IdPilote'
									AND IC_Position = '1'";
		$resultatVictoires = mysql_query($requeteVictoires) or die(mysql_error());
		$nbVictoires = mysql_fetch_row($resultatVictoires);

		$infoPilote['Pil_NbVictoires'] = $nbVictoires[0];

		switch($infoPilote['Pil_IdManager'])
		{
			case $IdManager :
				$submitForm = "Renvoyer ce pilote";
				$actionForm = "Renvoyer";
				break;
			case 0 :
				$submitForm = "Engager ce pilote";
				$actionForm = "Engager";
				break;
			default :
				$submitForm = "";
				$actionForm = "";
		}
?>
<div align="center">
<table border="0" width="80%">
<tr><td>

	<table border="1">
		<tr>
			<th colspan="3">Fiche du pilote <?php echo $infoPilote['Pil_Nom'];?></th>
		</tr>
		<tr>
			<th colspan="1">Nom :</th>
			<td colspan="2"><?php echo $infoPilote['Pil_Nom'];?></td>
		</tr>
		<tr>
			<th colspan="1">Age :</th>
			<td colspan="2"><?php echo $infoPilote['Pil_Age'];?> ans</td>
		</tr>
		<tr>
			<th colspan="1">Réputation :</th>
			<td colspan="2"><?php echo $infoPilote['Pil_Reputation'];?></td>
		</tr>
		<tr>
			<th colspan="1">Style :</th>
			<td colspan="2"><?php echo $infoPilote['Pil_Style'];?></td>
		</tr>
		<tr>
			<th colspan="1">Chance :</th>
			<td colspan="2"><?php echo $infoPilote['Pil_Chance'];?> %</td>
		</tr>
		<tr>
			<th colspan="1">Victoires :</th>
			<td colspan="2"><?php echo $infoPilote['Pil_NbVictoires']?></td>
		</tr>
<?php
	if($Man_Niveau > 1)
	{
?>
		<tr>
			<th colspan="1">Satisfaction :</th>
			<td colspan="2"><?php echo $infoPilote['Pil_Satisfaction'];?> %</td>
		</tr>
		<tr>
			<th colspan="1">Argent gagné :</th>
			<td colspan="2"><?php echo $infoPilote['Pil_Solde'];?> &euro;</td>
		</tr>
		<tr>
			<th colspan="1">Pourcentage Gains :</th>
			<td colspan="2"><?php echo $infoPilote['Pil_PourcentageGains'];?> %</td>
		</tr>
<?php
	}
?>
		<tr>
			<th>Compétences</th>
			<th>Niveau</th>
			<th>XP</th>
		</tr>
		<tr>
		<th>Shifts</th>
			<td><?php echo niveauAdd($infoPilote['Pil_XPShifts'],1000);?></td>
			<td><?php echo $infoPilote['Pil_XPShifts'];?></td>
		</tr>
		<tr>
			<th>Freinage</th>
			<td><?php echo niveauAdd($infoPilote['Pil_XPFreinage'],1000);?></td>
			<td><?php echo $infoPilote['Pil_XPFreinage'];?></td>
		</tr>
		<tr>
			<th>Virage</th>
			<td><?php echo niveauAdd($infoPilote['Pil_XPVirage'],1000);?></td>
			<td><?php echo $infoPilote['Pil_XPVirage'];?></td>
		</tr>
		<tr>
			<th>Spécial</th>
			<td><?php echo niveauAdd($infoPilote['Pil_XPSpe'],1000);?></td>
			<td><?php echo $infoPilote['Pil_XPSpe'];?></td>
		</tr>
		<tr>
			<th>Général</th>
			<td><?php echo 	niveauAdd($infoPilote['Pil_XPShifts'],1000)+
						niveauAdd($infoPilote['Pil_XPFreinage'],1000)+
						niveauAdd($infoPilote['Pil_XPVirage'],1000)+
						niveauAdd($infoPilote['Pil_XPSpe'],1000);?></td>
			<td><?php echo 	$infoPilote['Pil_XPShifts']+
             		$infoPilote['Pil_XPFreinage']+
						$infoPilote['Pil_XPVirage']+
						$infoPilote['Pil_XPSpe'];?></td>
		</tr>
	</table>
	<br>
<?php
	$requeteEngagements = "	SELECT IdInscriptionCourse, IdVoiture, Marq_Libelle, ModVoi_NomModele, IdCourse, Cou_Nom, Cou_Date
									FROM inscription_course, voiture, modele_voiture, marque, course
									WHERE IdVoiture = IC_IdVoiture
									AND IdModeleVoiture = Voit_IdModele
									AND IdMarque = ModVoi_IdMarque
									AND IdCourse = IC_IdCourse
									AND IC_IdPilote = '$IdPilote'
									AND IC_Position IS NULL";
	$resultatEngagements = mysql_query($requeteEngagements) or die(mysql_error());
	$nbCoursesPrevues = mysql_num_rows($resultatEngagements);
?>
	<table border="1">
		<tr>
			<th colspan="3">Engagements</th>
		</tr>
		<tr>
<?php
	if($nbCoursesPrevues == 0)
	{
?>
			<td>Aucune course prévue</td>
<?php
	}
	else
	{
?>			<th>Course</th>
			<th>Voiture</th>
			<th>Date</th>
		</tr>
<?php
		while($infoEngagement = mysql_fetch_assoc($resultatEngagements))
		{
?>
		<tr>
			<td><a href="../course/fiche.php?IdCourse=<?php echo $infoEngagement['IdCourse']?>"><?php echo $infoEngagement['Cou_Nom']?></a></td>
			<td><a href="../voiture/fiche.php?IdVoiture=<?php echo $infoEngagement['IdVoiture']?>&page=infos"><?php echo $infoEngagement['Marq_Libelle']." ".$infoEngagement['ModVoi_NomModele']?></a></td>
			<td><?php echo implode(" / ",array_reverse(explode("-",$infoEngagement['Cou_Date'])))?></td>
		</tr>
<?php
		}
	}
?>
	</table>
	<?php
	$requetePalmares= "	SELECT IdInscriptionCourse, IdVoiture, Marq_Libelle, ModVoi_NomModele, IdCourse, Cou_Nom, Cou_Date, IC_Position
								FROM inscription_course, voiture, modele_voiture, marque, course
								WHERE IdVoiture = IC_IdVoiture
								AND IdModeleVoiture = Voit_IdModele
								AND IdMarque = ModVoi_IdMarque
								AND IdCourse = IC_IdCourse
								AND IC_IdPilote = '$IdPilote'
								AND IC_Position IS NOT NULL";
	$resultatPalmares = mysql_query($requetePalmares) or die(mysql_error());
			$nbCoursesCourures = mysql_num_rows($resultatPalmares);
?>
<br>
	<table border="1">
		<tr>
			<th colspan="4">Palmares</th>
		</tr>
		<tr>
<?php
	if($nbCoursesCourures == 0)
	{
?>
			<td>Aucune course courue</td>
<?php
	}
	else
	{
?>
			<th>Course</th>
			<th>Voiture</th>
			<th>Date</th>
			<th>Position</th>
		</tr>
<?php
		while($infoPalmares = mysql_fetch_assoc($resultatPalmares))
		{
?>
		<tr>
			<td><a href="../course/fiche.php?IdCourse=<?php echo $infoPalmares['IdCourse']?>"><?php echo $infoPalmares['Cou_Nom']?></a></td>
			<td><a href="../voiture/fiche.php?IdVoiture=<?php echo $infoPalmares['IdVoiture']?>&page=infos"><?php echo $infoPalmares['Marq_Libelle']." ".$infoPalmares['ModVoi_NomModele']?></a></td>
			<td><?php echo implode(" / ",array_reverse(explode("-",$infoPalmares['Cou_Date'])))?></td>
			<td><?php echo $infoPalmares['IC_Position']?></td>
		</tr>
<?php
		}
	}
?>
	</table>
</td><td>

	<table border="1">
		<tr><th>Actions possibles</th></tr>

		<tr>
			<td>

<?php
		if($Man_Niveau > 1)
		{
?>
<form action="gestion.php" method="get">
	<input type="hidden" name="IdPilote" value="<?php echo $infoPilote['IdPilote'];?>">
	<input type="hidden" name="action" value="Modifier">
	<input type="submit" value="Changer les gains">
<?php
			if($submitForm != "")
			{
?>
</form>
<form action="traitement.php" method="get" onSubmit="return confirmSuppr(this)">
	<input type="hidden" name="IdManager" value="<?php echo $IdManager;?>">
	<input type="hidden" name="reponse" value="Oui">
	<input type="hidden" name="IdPilote" value="<?php echo $infoPilote['IdPilote'];?>">
	<input type="submit" value="<?php echo $submitForm;?>">
	<input type="hidden" name="action" value="<?php echo $actionForm;?>">
<?php
			}
			if($Man_Niveau > 2)
			{
?>
</form>
<form action="traitement.php" method="get" onSubmit="return confirmSuppr(this)">
	<input type="hidden" name="reponse" value="Oui">
	<input type="hidden" name="IdPilote" value="<?php echo $infoPilote['IdPilote'];?>">
	<input type="submit" name="action" value="Supprimer">

<?php
			}
		}
?>
			</td>
		</tr>
</form>
	</table>

</td></tr></table>

<?php
	}
?>
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
