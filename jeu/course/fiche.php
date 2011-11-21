<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	include('../../include/fonctions.php');

	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];

	if(isset($_GET['IdCourse']))
	{
		$IdCourse = $_GET['IdCourse'];

		$requeteInfoCourse = "	SELECT 	IdCourse,
										Cou_Nom,
										Cou_Date,
										Cou_IdType,
										TypeCou_Libelle,
										Cou_NbTours,
										Cou_PrixEngagement,
										Cou_PrixInscription,
										Cou_DensiteCirculation,
										Cou_NbCompetiteursMax,
										Cou_NiveauMin,
										Cou_NiveauMax,
										Cou_Commentaires,
										Cou_IdTronconDepart,
										Cou_IdManager,
										Man_Nom,
										CURRENT_DATE() AS Cou_DateJour
								FROM course, type_course, manager
								WHERE Cou_IdType = IdTypeCourse
								AND IdManager = Cou_IdManager
								AND IdCourse ='$IdCourse'";
		$resultatInfoCourse=mysql_query($requeteInfoCourse) or die(mysql_error());

		$infoCourse=mysql_fetch_assoc($resultatInfoCourse);

		$requeteInscriptionCourse="	SELECT IdInscriptionCourse, IC_IdPilote, Pil_Nom, IC_IdVoiture, Marq_Libelle, ModVoi_NomModele, SUM(Pari_Montant) AS Pari_Montant, IC_Position, IC_Temps
												FROM inscription_course
												INNER JOIN pilote ON IdPilote = IC_IdPilote
												INNER JOIN voiture ON IdVoiture = IC_IdVoiture
												INNER JOIN modele_voiture ON IdModeleVoiture = Voit_IdModele
												INNER JOIN marque ON IdMarque = ModVoi_IdMarque
												LEFT JOIN pari ON Pari_IdInscriptionCourse = IdInscriptionCourse
												WHERE IC_IdCourse = '$IdCourse'
												GROUP BY IdInscriptionCourse
												ORDER BY IC_Position";

		$resultatInscriptionCourse = mysql_query($requeteInscriptionCourse) or die(mysql_error());
		$Cou_NbCompetiteurs = mysql_num_rows($resultatInscriptionCourse);

		$infoInscriptionCourse = mysql_fetch_assoc($resultatInscriptionCourse);
		$coursePassee = !is_null($infoInscriptionCourse['IC_Position']) && $infoInscriptionCourse['IC_Position'] != '';
		//echo "CoursePassee = $coursePassee";
?>
<html>
<head>
	<title>UTR : Fiche de course</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
	<script language="JavaScript">
		function confirmSuppr(form)
		{
			var action=form.action.value;
			var confirmation = "Etes-vous sûr de vouloir "+action+" cette course ?";
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
		function cacherElements(indice)
		{
			eval("var formulaire_el=document.getElementById(\"formulaire"+indice+"\")");

			if(formulaire_el != '' && formulaire_el.length == null)
			{
					formulaire_el.style.display='none';
			}
			else
			{
				for (i=0;i<formulaire_el.length;i++)	formulaire_el[i].style.display='none';
			}
		}
		function montrerElement(indice)
		{
			eval("var formulaire_el=document.getElementById(\"formulaire"+indice+"\")");

			if (formulaire_el!=''&&formulaire_el.length==null)formulaire_el.style.display='';
			else
			{
				for (i=0;i<formulaire_el.length;i++)	formulaire_el[i].style.display='';
			}
		}
		function formulaire(indice)
		{
			for(var i=1; i<= 3 ; i++)
				if(i != indice) cacherElements(i);
				else montrerElement(i);
		}
	</script>
</head>
<body <?php echo !$coursePassee?"onLoad=\"formulaire(0)\"":""?>>
<table width="100%">
	<tr>
		<td colspan="3">
<?php
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td width="110" valign="top">
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td valign="top" align="center">
			<table border="0" class="liste">
				<tr class="piece">
					<th colspan="3" class="titre">Fiche de la course "<?php echo $infoCourse['Cou_Nom']?>"</th>
				</tr>
				<tr class="piece">
					<th>Organisateur :</td>
					<td><?php echo $infoCourse['Man_Nom']?></td>
				</tr>
				<tr class="piece">
					<th>Date de la course :</td>
					<td><?php echo implode(" / ",array_reverse(explode("-",$infoCourse['Cou_Date'])))?></td>
				</tr>
				<tr class="piece">
					<th>Type de course :</th>
					<td><?php echo $infoCourse['TypeCou_Libelle']?></td>
				</tr>
				<tr class="piece">
					<th>Nombre de tours :</th>
					<td><?php echo $infoCourse['Cou_NbTours']?></td>
				</tr>
				<tr class="piece">
					<th>Prix Inscription :</th>
					<td><?php echo $infoCourse['Cou_PrixInscription']?> &euro;</td>
				</tr>
				<tr class="piece">
					<th>Prix Engagement :</th>
					<td><?php echo $infoCourse['Cou_PrixEngagement']?> &euro;</td>
				</tr>
				<tr class="piece">
					<th>Densité de circulation :</th>
					<td><?php echo $infoCourse['Cou_DensiteCirculation']?> %</td>
				</tr>
				<tr class="piece">
					<th>Nombre maximum de compétiteurs :</th>
					<td><?php echo $infoCourse['Cou_NbCompetiteursMax']?></td>
				</tr>
				<tr class="piece">
					<th>Niveau minimum :</th>
					<td><?php echo $infoCourse['Cou_NiveauMin']?></td>
				</tr>
				<tr class="piece">
					<th>Niveau maximum :</th>
					<td><?php echo $infoCourse['Cou_NiveauMax']?></td>
				</tr>
				<tr class="piece">
					<th>Prix maximum au vainqueur :</th>
					<td><?php echo $Cou_NbCompetiteurs*$infoCourse['Cou_PrixEngagement']?> &euro;</td>
				</tr>
				<tr class="piece">
					<th>Intérêt de la police :</th>
					<td><?php echo round(($Cou_NbCompetiteurs/12)*100,2)?> % de chance</td>
				</tr>
				<tr class="piece">
					<th colspan="2" class="titre">Commentaires</th>
				</tr>
				<tr class="piece">
					<td colspan="2"><?php echo nl2br(stripslashes($infoCourse['Cou_Commentaires']))?></td>
				</tr>

			</table>
			<br>
<!--Tracé du circuit-->
			<table border="0" class="liste">
				<tr class="piece">
					<th colspan="3" class="titre">Tracé du circuit</th>
				</tr>
				<tr class="piece">
					<th class="titre">Secteur</th>
					<th class="titre">Longueur</th>
					<th class="titre">Vitesse Maximum</th>
				</tr>
<?php
	$infoTroncon['Tron_IdTronconSuivant'] = $infoCourse['Cou_IdTronconDepart'];
	do
	{
		$IdTroncon = $infoTroncon['Tron_IdTronconSuivant'];
		$requeteInfoTroncon = "	SELECT Sec_Nom, Sec_Longueur, Sec_VitesseMaximum, Tron_IdTronconSuivant
										FROM troncon, secteur
										WHERE Tron_IdSecteur = IdSecteur
										AND IdTroncon = '$IdTroncon'";
		$resultatInfoTroncon = mysql_query($requeteInfoTroncon);
		$infoTroncon = mysql_fetch_assoc($resultatInfoTroncon);
?>
				<tr class="piece">
					<td><?php echo $infoTroncon['Sec_Nom']?></td>
					<td><?php echo $infoTroncon['Sec_Longueur']?> m</td>
					<td><?php echo msTOkmh($infoTroncon['Sec_VitesseMaximum'])?> km/h</td>
				</tr>
<?php

	}while(!empty($infoTroncon['Tron_IdTronconSuivant']));
?>
				<tr class="piece">
					<th>Total</th>
					<td><?php echo longueurCourse($infoCourse['Cou_IdTronconDepart'])?> m</td>
					<td>
<?php
	$difficulte = difficulteCourse($infoCourse['Cou_IdTronconDepart'])/longueurCourse($infoCourse['Cou_IdTronconDepart']);
	if($difficulte > 110)
		echo "Facile";
	else if($difficulte > 100)
		echo "Moyen";
	else if($difficulte > 90)
		echo "Difficile";
	else echo "Très difficile";
	//echo $requeteInscriptionCourse;
?>
					</td>
				</tr>
			</table>
			<br>
			<table border="0" class="liste">
				<tr class="piece"><th colspan="6" class="titre">Participants inscrits</th></tr>
				<tr class="piece">
<?php
	if($Cou_NbCompetiteurs == 0)
	{
?>
					<td>Pas d'inscrits</td>
				</tr>
<?php
	}
	else
	{
?>
					<th class="titre">Pilote</th>
					<th class="titre">Voiture</th>
					<th class="titre">Montant des paris</th>
<?php
		if($coursePassee)
		{
?>
					<th class="titre">Position</th>
					<th class="titre">Temps</th>
					<th class="titre">Différence</th>
<?php
	}
?>
				</tr>
<?php

		$tempsPremier = $infoInscriptionCourse['IC_Temps'];

		do
		{
?>
				<tr class="piece">
					<td><a href="../pilote/fiche.php?IdPilote=<?php echo $infoInscriptionCourse['IC_IdPilote']?>"><?php echo $infoInscriptionCourse['Pil_Nom']?></a></td>
					<td><a href="../voiture/fiche.php?IdVoiture=<?php echo $infoInscriptionCourse['IC_IdVoiture']?>&page=infos"><?php echo $infoInscriptionCourse['Marq_Libelle']." ".$infoInscriptionCourse['ModVoi_NomModele']?></a></td>
					<td><?php echo (empty($infoInscriptionCourse['Pari_Montant']))?"0":$infoInscriptionCourse['Pari_Montant'];?> &euro;</td>
<?php
			if($coursePassee)
			{
?>
					<td><?php echo $infoInscriptionCourse['IC_Position']?></td>
					<td>
<?php
			if($infoInscriptionCourse['IC_Temps']=="0")
				echo "Non couru";
			else echo affichageTemps($infoInscriptionCourse['IC_Temps']);
?>
					</td>
					<td>-&nbsp;
<?php
			if($infoInscriptionCourse['IC_Temps']=="0")
				echo "N/A";
			else echo affichageTemps($infoInscriptionCourse['IC_Temps'] - $tempsPremier);
?>
					</td>
<?php
		}
?>
				</tr>
<?php
		}while($infoInscriptionCourse = mysql_fetch_assoc($resultatInscriptionCourse));
	}
?>
			</table>
<form action="traitement.php" method="POST">
<input type="hidden" name="IdManager" value="<?php echo $IdManager;?>">
<input type="hidden" name="IdCourse" value="<?php echo $infoCourse['IdCourse']; ?>">
<input type="hidden" name="Cou_PrixInscription" value="<?php echo $infoCourse['Cou_PrixInscription']; ?>">
<br>
<!--////////////////////////////////////
//Formulaire d'inscription à la course//
/////////////////////////////////////-->
<?php
	if( ! $coursePassee )
	{
?>
	<table width="100%" border="0" id="formulaire1" class="liste">
		<tr class="piece">
			<th>Inscription</th>
		</tr>
		<tr class="piece">
			<td>Pilotes disponibles :
<?php
	$piloteDisponible = Array();

  $requetePilotes= "	SELECT IdPilote, Pil_Nom
								FROM pilote
								WHERE Pil_IdManager = '$IdManager'";
	$resultatPilotes=mysql_query($requetePilotes)or die(mysql_error());

	while($infoPilote = mysql_fetch_assoc($resultatPilotes))
	{
		$IdPilote = $infoPilote['IdPilote'];

		$requeteCoursesPilote = "	SELECT Cou_Date
											FROM pilote
											LEFT JOIN inscription_course ON IC_IdPilote = IdPilote
											LEFT JOIN course ON IdCourse = IC_IdCourse
											WHERE IdPilote = '$IdPilote'";
		$resultatCoursesPilote = mysql_query($requeteCoursesPilote)or die(mysql_error());

		$datesCourses = Array();
		while($infoCoursesPilote = mysql_fetch_row($resultatCoursesPilote))
			array_push($datesCourses,$infoCoursesPilote[0]);

		if(!in_array($infoCourse['Cou_Date'],$datesCourses))
			array_push($piloteDisponible,$infoPilote);
	}

	if(count($piloteDisponible)==0)
	{
?>
Aucun pilote libre ce jour-là
<?php
	}
	else
	{
?>
			<select name="IC_IdPilote">
<?php
		for($i=0;$i< count($piloteDisponible);$i++)
		{
?>
				<option value="<?php echo $piloteDisponible[$i]['IdPilote']?>"><?php echo $piloteDisponible[$i]['Pil_Nom']?></option>
<?php
		}
?>
			</select>
<?php
	}
?>
			</td>
		</tr>
		<tr class="piece">
			<td>Voitures disponibles :
<?php
	$voitureDisponible = Array();

	$requeteVoitures= "	SELECT IdVoiture, ModVoi_NomModele, Marq_Libelle
								FROM voiture
								INNER JOIN modele_voiture ON IdModeleVoiture = Voit_IdModele
								INNER JOIN marque ON IdMarque = ModVoi_IdMarque
								LEFT JOIN vente ON Ven_IdItem = IdVoiture
								WHERE Voit_IdManager = '$IdManager'
								AND IdVente IS NULL";
	$resultatVoitures=mysql_query($requeteVoitures)or die(mysql_error());


	while($infoVoiture = mysql_fetch_assoc($resultatVoitures))
	{
		$IdVoiture = $infoVoiture['IdVoiture'];
		if(dispoVoiture($IdVoiture))
		{
			$requeteCoursesVoiture = "	SELECT Cou_Date
												FROM voiture
												LEFT JOIN inscription_course ON IC_IdVoiture = IdVoiture
												LEFT JOIN course ON IdCourse = IC_IdCourse
												WHERE IdVoiture = '$IdVoiture'";
			$resultatCoursesVoiture = mysql_query($requeteCoursesVoiture)or die(mysql_error());

			$datesCourses = Array();
			while($infoCoursesVoiture = mysql_fetch_row($resultatCoursesVoiture))
				array_push($datesCourses,$infoCoursesVoiture[0]);

			if(!in_array($infoCourse['Cou_Date'],$datesCourses))
				array_push($voitureDisponible,$infoVoiture);
		}
	}

	if(count($voitureDisponible)==0)
	{
?>
Aucune voiture libre ce jour-là
<?php
	}
	else
	{
?>
			<select name="IC_IdVoiture">
<?php
		for($i=0;$i< count($voitureDisponible);$i++)
		{
?>
				<option value="<?php echo $voitureDisponible[$i]['IdVoiture']?>"><?php echo $voitureDisponible[$i]['Marq_Libelle']." ".$voitureDisponible[$i]['ModVoi_NomModele']?></option>
<?php
		}
?>
			</select>
<?php
	}
?>			</td>
		</tr>
		<tr class="piece">
			<td><input type="Submit" name="action" value="Inscrire" <?php if(count($piloteDisponible)*count($voitureDisponible)!="1")echo "disabled";?>> Prix d'inscription : <?php echo $infoCourse['Cou_PrixInscription']?></td>
		</tr>
	</table>
<!--/////////////////////////////////////////
//Formulaire de désinscription de la course//
//////////////////////////////////////////-->
	<table width="100%" border="0" id="formulaire2" class="liste">
		<tr class="piece">
			<th>Désincrire un de ses pilotes</th>
		</tr>
		<tr>
			<td>Pilote à désinscrire :
<?php
	$requetePilotesInscrits = "	SELECT IdPilote, Pil_Nom
								FROM pilote
								LEFT JOIN inscription_course ON IC_IdPilote = IdPilote
								WHERE Pil_IdManager='$IdManager'
								AND IC_IdCourse='$IdCourse'";
	$resultatPilotesInscrits = mysql_query($requetePilotesInscrits) or die ( mysql_error());
	if(mysql_num_rows($resultatPilotesInscrits)==0)
	{
?>
Aucun pilote inscrit
<?php
	}
	else
	{
?>
			<select name="IC_IdPiloteDesinscrire">
<?php
		while($piloteInscrit = mysql_fetch_assoc($resultatPilotesInscrits))
		{
?>
				<option value="<?php echo $piloteInscrit['IdPilote']?>"><?php echo $piloteInscrit['Pil_Nom']?></option>
<?php
		}
	}
?>
		</tr>
		<tr class="piece">
			<td><input type="Submit" name="action" value="Désincrire" <?php if(mysql_num_rows($resultatPilotesInscrits)!="1")echo "disabled";?>></td>
		</tr>
	</table>
<!--//////////////////
//Formulaire de pari//
///////////////////-->
	<table width="100%" border="0" id="formulaire3" class="liste">
		<tr class="piece">
			<th>Parier sur un pilote</th>
		</tr>
		<tr class="piece">
			<td>
<?php
	if($Cou_NbCompetiteurs == 0) echo"Aucun pilote ne s'est inscrit.";
	else
	{
?>
				Parier&nbsp;&nbsp;<input type="text" size="5" name="Pari_Montant" value="0">&nbsp;&nbsp;&euro; sur&nbsp;&nbsp;<select name="IdInscriptionCourse">
<?php
	}
	$resultatInscriptionCourse = mysql_query($requeteInscriptionCourse) or die(mysql_error());
	while($infoInscriptionCourse = mysql_fetch_assoc($resultatInscriptionCourse))
	{
?>
		<option value="<?php echo $infoInscriptionCourse['IdInscriptionCourse']?>"><?php echo $infoInscriptionCourse['Pil_Nom']?> sur <?php echo $infoInscriptionCourse['Marq_Libelle']." ".$infoInscriptionCourse['ModVoi_NomModele']?></option>
<?php
	}
?>
		</tr>
		<tr class="piece">
			<td><input type="Submit" name="action" value="Parier" <?php echo ($Cou_NbCompetiteurs==0)?"disabled":"";?>></td>
		</tr>
	</table>
<?php
	}
?>
</form>

		</td>
		<td valign="top">
<div class="actions">Actions possibles<br /><br />
<?php
	if( !$coursePassee )//$infoCourse['Cou_Date'] >= $infoCourse['Cou_DateJour']
	{
?>
	<input type="button" onClick="formulaire(1)" value="Inscrire un de ses pilotes"><br>
	<input type="button" onClick="formulaire(2)" value="Désincrire un de ses pilotes"><br>
	<input type="button" onClick="formulaire(3)" value="Parier sur un pilote">
<?php
	}
	if($Man_Niveau > 2 || $IdManager == $infoCourse['Cou_IdManager'])
	{
?>
<form action="traitement.php" method="get" onSubmit="return confirmSuppr(this)">
	<input type="hidden" name="reponse" value="Oui">
	<input type="hidden" name="IdCourse" value="<?php echo $infoCourse['IdCourse'];?>">
	<input type="submit" name="action" value="Supprimer">
</form>
<?php
	}
?>
</div>
<?php
	}
?>
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
