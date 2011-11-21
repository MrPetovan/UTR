<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^ E_NOTICE);
	$Jou_Niveau = $_SESSION['Jou_Niveau'];
	$IdJoueur = $_SESSION['IdJoueur'];

	/*echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";*/

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
										Cou_IdTronconDepart,
										Cou_IdJoueur,
										Jou_Login
								FROM course, type_course, joueur
								WHERE Cou_IdType = IdTypeCourse
								AND IdJoueur = Cou_IdJoueur
								AND IdCourse ='$IdCourse'";
		$infoCourse=mysql_fetch_assoc(mysql_query($requeteInfoCourse)) or die(mysql_error());

		$requeteInscriptionCourse = "	SELECT	IdInscriptionCourse, IC_IdPilote, Pil_Nom, IC_IdVoiture, Marq_Libelle,
															ModVoi_NomModele, SUM(Pari_Montant) AS Pari_Montant
												FROM inscription_course, pilote, marque, voiture, modele_voiture
												LEFT JOIN pari ON Pari_IdInscriptionCourse = IdInscriptionCourse
												WHERE IdPilote = IC_IdPilote
												AND IdVoiture = IC_IdVoiture
												AND IdMarque = ModVoi_IdMarque
												AND IdModeleVoiture = Voit_IdModele
												AND IC_IdCourse = '$IdCourse'
												GROUP BY IdInscriptionCourse";
		$resultatInscriptionCourse = mysql_query($requeteInscriptionCourse) or die(mysql_error());
		$Cou_NbCompetiteurs = mysql_num_rows($resultatInscriptionCourse);
	}
?>
<html>
<head>
	<title>UTR : Gérer une course</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
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
		function cacherElements(indice)
		{
			//alert("var formulaire_el=document.getElementById(\"formulaire"+indice+"\")");
			eval("var formulaire_el=document.getElementById(\"formulaire"+indice+"\")");
			//for (var i in formulaire_el) document.write("formulaire_el" + "." + i + "=" + formulaire_el[i] + "<BR>");

			if(formulaire_el != '' && formulaire_el.length == null)
			{
				//for (var i in formulaire_el.style) document.write("object.style" + "." + i + "=" + formulaire_el[i] + "<BR>");
				formulaire_el.style.display='none';
			}
			else
			{
				for (i=0;i<formulaire_el.length;i++)	formulaire_el[i].style.display='none';
			}
		}
		function montrerElement(indice)
		{
			//alert("var formulaire_el=document.getElementById(formulaire"+indice+")");
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
<body onLoad="formulaire(1)">
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
<form action="traitement.php" method="POST">
<input type="hidden" name="IdJoueur" value="<?php echo $IdJoueur;?>">
<input type="hidden" name="IdCourse" value="<?php echo $infoCourse["IdCourse"]; ?>">
<br>
<input type="button" onClick="formulaire(1)" value="Inscrire un de ses pilotes">
<input type="button" onClick="formulaire(2)" value="Désinscrire un de ses pilotes">
<input type="button" onClick="formulaire(3)" value="Parier sur un pilote">
<br>
<br>
	<table border="1">
		<tr>
			<th colspan="3">Fiche de la course "<?php echo $infoCourse['Cou_Nom']?>"</th>
		</tr>
		<tr><th colspan="2">Participants inscrits</th></tr>
		<tr>
			<th>#</th>
			<th>Pilote</th>
			<th>Voiture</th>
			<th>Montant des paris</th>
		</tr>
<?php
	$i=1;
	while($infoIncriptionCourse = mysql_fetch_assoc($resultatInscriptionCourse))
	{
?>
		<tr>
			<td><?php echo $i++?>
			<td><a href="../pilote/fiche.php?IdPilote=<?php echo $infoIncriptionCourse['IC_IdPilote']?>"><?php echo $infoIncriptionCourse['Pil_Nom']?></a></td>
			<td><a href="../voiture/fiche.php?IdVoiture=<?php echo $infoIncriptionCourse['IC_IdVoiture']?>"><?php echo $infoIncriptionCourse['Marq_Libelle']." ".$infoIncriptionCourse['ModVoi_NomModele']?></a></td>
			<td><?php echo(empty($infoIncriptionCourse['Pari_Montant']))?"0":$infoIncriptionCourse['Pari_Montant'];?> &euro;</td>
		</tr>
<?php
	}
?>
	</table>
	<br><br>
<!--////////////////////////////////////
//Formulaire d'inscription à la course//
/////////////////////////////////////-->
	<table width="100%" border="1" id="formulaire1">
		<tr>
			<th>Inscription</th>
		</tr>
		<tr>
			<td>Pilotes disponibles :
<?php
	$piloteDisponible = Array();

	$requetePilotes= "	SELECT IdPilote, Pil_Nom
						FROM pilote
						WHERE Pil_IdJoueur = '$IdJoueur'";
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
		<tr>
			<td>Voitures disponibles :
<?php
	$voitureDisponible = Array();

	$requeteVoitures= "	SELECT IdVoiture, ModVoi_NomModele, Marq_Libelle
								FROM voiture, modele_voiture, marque
								WHERE IdModeleVoiture = Voit_IdModele
								AND Voit_IdJoueur = '$IdJoueur'
								AND IdMarque = ModVoi_IdMarque";
	$resultatVoitures=mysql_query($requeteVoitures)or die(mysql_error());


	while($infoVoiture = mysql_fetch_assoc($resultatVoitures))
	{
		$IdVoiture = $infoVoiture['IdVoiture'];

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
		<tr>
			<td><input type="Submit" name="action" value="Inscrire" <?php if(count($piloteDisponible)*count($voitureDisponible)!="1")echo "disabled";?>> Prix d'inscription : <?php echo $infoCourse['Cou_PrixInscription']?></td>
		</tr>
	</table>

	<table width="100%" border="1" id="formulaire2">
		<tr>
			<td>Désinscrire un de ses pilotes</td>
		</tr>
		<tr>
			<td>Pilote à désinscrire :
<?php
	$requetePilotesInscrits = "	SELECT IdPilote, Pil_Nom
											FROM pilote
											LEFT JOIN inscription_course ON IC_IdPilote = IdPilote
											WHERE Pil_IdJoueur='$IdJoueur'
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
		<tr>
			<td><input type="Submit" name="action" value="Désinscrire" <?php if(mysql_num_rows($resultatPilotesInscrits)!="1")echo "disabled";?>></td>
		</tr>
	</table>

	<table width="100%" border="1" id="formulaire3">
		<tr>
			<td>Parier sur un pilote</td>
		</tr>
		<tr>
			<td>Parier&nbsp;&nbsp;<input type="text" size="5" name="Pari_Montant" value="0">&nbsp;&nbsp;&euro; sur&nbsp;&nbsp;<select name="IdInscriptionCourse">
<?php
	$resultatInscriptionCourse = mysql_query($requeteInscriptionCourse) or die(mysql_error());
	while($infoIncriptionCourse = mysql_fetch_assoc($resultatInscriptionCourse))
	{
?>
		<option value="<?php echo $infoIncriptionCourse['IdInscriptionCourse']?>"><?php echo $infoIncriptionCourse['Pil_Nom']?> sur <?php echo $infoIncriptionCourse['Marq_Libelle']." ".$infoIncriptionCourse['ModVoi_NomModele']?></option>
<?php
	}
?>
		</tr>
		<tr>
			<td><input type="Submit" name="action" value="Parier"></td>
		</tr>
	</table>
</form>
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
