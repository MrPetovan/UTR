<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	include('../../include/fonctions.php');
	include('../bin/fonctionMath.php');

	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<html>
<head>
	<title>UTR : Fiche d'une voiture</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<!--<link href="../../include/style.css" rel="stylesheet" type="text/css" />-->
	<script language="JavaScript">
		function confirmSuppr(form)
		{
			var confirmation;
			var action=form.action.value;
			if(action=="Supprimer") confirmation = "Etes-vous sûr de vouloir supprimer cette voiture ?";
			else confirmation = (action+" cette voiture ?");
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
	<script language="JavaScript" src="../../include/formulaire.js"></script>
	<style>
<!--	tr.voiture
		{
			background-image: url("http://localhost/UTR/images/Wallpaper_Angel_Fall_First.bmp");
			//background-color : #FF0000;
			background-color : #004080;
		}
		td.voiture
		{
			background-image: url("http://localhost/UTR/images/Wallpaper_Angel_Fall_First.bmp");
			//background-color : #FF0000;
			background-color : #004080;
		}-->
	</style>
</head>
<body>
<table width="100%"  cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="3">
<?php
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td width="14%" valign="top">
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td valign="top" align="center" style="padding : 10px;">
<br>
<?php
	if(isset($_GET['IdVoiture'])&& isset($_GET['page']))
	{
		$IdVoiture = $_GET['IdVoiture'];

		$infoVoiture = array();
		$pieceInstallee = array();

		infoVoiture($IdVoiture,$pieceInstallee,$infoVoiture);
		/*echo"<br>infoVoiture<br>";
		print_r($infoVoiture);
		echo"</pre>";*/

//Nombre de courses courues
		$requetePalmares= "	SELECT IdInscriptionCourse, IdPilote, Pil_Nom, IdCourse, Cou_Nom, Cou_Date, IC_Position
									FROM inscription_course
									INNER JOIN pilote ON IdPilote = IC_IdPilote
									INNER JOIN course ON IdCourse = IC_IdCourse
									WHERE IC_IdVoiture = '$IdVoiture'
									AND IC_Position IS NOT NULL
									ORDER BY Cou_Date DESC";
		$resultatPalmares = mysql_query($requetePalmares) or die("Requete Palmares : ".mysql_error());

		$nbCoursesCourues = mysql_num_rows($resultatPalmares);

//Nombre de victoires
		$requeteVictoires="	SELECT COUNT(IdInscriptionCourse) AS Voit_NombreVictoires
							FROM inscription_course
							WHERE IC_IdVoiture = '$IdVoiture'
							AND IC_Position = '1'";
		$resultatVictoires = mysql_query($requeteVictoires) or die("Requete Victoires : ".mysql_error());
		$nbVictoires = mysql_fetch_row($resultatVictoires);

		$infoVoiture['Voit_NbVictoires'] = $nbVictoires[0];

//Nombre d'engagements
		$requeteEngagements = "	SELECT IdInscriptionCourse, IdPilote, Pil_Nom, IdCourse, Cou_Nom, Cou_Date
										FROM inscription_course
										INNER JOIN pilote ON IdPilote = IC_IdPilote
										INNER JOIN course ON IdCourse = IC_IdCourse
										WHERE IC_IdVoiture = '$IdVoiture'
										AND IC_Position IS NULL
										ORDER BY Cou_Date DESC";
		$resultatEngagements = mysql_query($requeteEngagements) or die("Requete Engagements : ".mysql_error());

		$nbCoursesPrevues = mysql_num_rows($resultatEngagements);

		if(empty($infoVoiture['IdVente']))
		{
			//Voiture pas en vente
			switch($infoVoiture['Voit_IdManager'])
			{
				case $IdManager :
					$submitFormGestion = "Vendre cette voiture";
					$actionFormGestion = "Vendre";
					$submitFormTraitement = "";
					$actionFormTraitement = "";
					break;
				default :
					$submitFormGestion = "";
					$actionFormGestion = "";
					$submitFormTraitement = "";
					$actionFormTraitement = "";
					break;
			}
		}
		else
		{
			//Voiture en vente
			switch($infoVoiture['Voit_IdManager'])
			{
				case $IdManager :
					$submitFormGestion = "Modifier la vente";
					$actionFormGestion = "Modifier";
					$submitFormTraitement = "Annuler la vente";
					$actionFormTraitement = "Supprimer";
					break;
				default :
					$submitFormGestion = "";
					$actionFormGestion = "";
					$submitFormTraitement = "Acheter cette voiture";
					$actionFormTraitement = "Acheter";
					break;
			}
		}
?>
<div align="left">
<?php
		if($infoVoiture['IdVente'] != "")
		{
			if($infoVoiture['Voit_IdManager'] == "-1")
			{
?>
<a href="liste.php?page=neuf&type=<?php echo $infoPieceDetachee['ModPi_IdTypePiece']?>"><< Revenir au concessionaire</a>
<?php
			}
			elseif($infoVoiture['Voit_IdManager'] == "-2")
			{
?>
<a href="liste.php?page=casseur&type=<?php echo $infoPieceDetachee['ModPi_IdTypePiece']?>"><< Revenir au casseur</a>
<?php
			}
			else
			{
?>
<a href="liste.php?page=vente&type=<?php echo $infoPieceDetachee['ModPi_IdTypePiece']?>"><< Revenir aux ventes</a>
<?php
			}
		}
		else
		{
?>
<a href="liste.php?page=parking"><< Revenir au garage</a>
<?php
		}
?>
<br/><br />
<a href="liste.php?page=parking" align="left">&nbsp;Le garage</a>&nbsp;>&nbsp;<?php echo $infoVoiture['Marq_Libelle']." ".$infoVoiture['ModVoi_NomModele']?></div>
<br/>
<a href="fiche.php?IdVoiture=<?php echo $IdVoiture?>&page=infos">Infos</a>&nbsp;|&nbsp;
<a href="fiche.php?IdVoiture=<?php echo $IdVoiture?>&page=caracs">Caractéristiques</a>&nbsp;|&nbsp;
<a href="fiche.php?IdVoiture=<?php echo $IdVoiture?>&page=pieces">Pieces</a>&nbsp;|&nbsp;
<a href="fiche.php?IdVoiture=<?php echo $IdVoiture?>&page=planning">Planning</a>
<br>
<br>
<?php
		if($_GET['page']=='infos')
		{
			if($_SESSION['Erreur']==1)
			{
				$codeErreur = $_SESSION['Codes'];

				$requeteMessageErreur="	SELECT Err_Message
												FROM erreur
												WHERE IdErreur = $codeErreur";
				$resultatMessageErreur=mysql_query($requeteMessageErreur)or die(mysql_error());
				$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
				echo $messageErreur["Err_Message"];

				unset($_SESSION['Erreur']);
				unset($_SESSION['Codes']);
				echo "<br /><br />";
	 		}
?>
	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="2" class="titre">Fiche technique de la <?php echo $infoVoiture['Marq_Libelle']." ".$infoVoiture['ModVoi_NomModele'];?></td>
		</tr>
		<tr class="piece">
			<th>Marque</th>
			<td><?php echo $infoVoiture['Marq_Libelle'];?></td>
		</tr>
		<tr class="piece">
			<th>Modèle</th>
			<td><?php echo $infoVoiture['ModVoi_NomModele'];?></td>
		</tr>
		<tr class="piece">
			<th>Type Carburant</th>
			<td><?php echo $infoVoiture['ModVoi_TypeCarburant'];?></td>
		</tr>
		<tr class="piece">
			<th>Victoires/Courses</th>
			<td><?php echo $infoVoiture['Voit_NbVictoires']."/$nbCoursesCourues"?></td>
		</tr>
		<tr class="piece">
			<th><img alt="Aspect Extérieur" src="../../images/aspect.gif"></th>
			<td><?php echo $infoVoiture['Voit_AspectExterieur'];?></td>
		</tr>
		<tr class="piece">
			<th><img alt="Poids" src="../../images/poids.gif"></th>
			<td><?php echo $infoVoiture['Voit_Poids']+$infoVoiture['ModVoi_PoidsCarrosserie'];?> kg</td>
		</tr>
<?php
			if(empty($infoVoiture['IdVente']))
			{
?>
		<tr class="piece">
			<th><img alt="Prix" src="../../images/prix.gif"></th>
			<td><?php echo $infoVoiture['ModVoi_PrixNeuve'];?> &euro;</td>
		</tr>
<?php
			}
			else
			{
?>
		<tr class="piece">
			<th><img alt="Prix de vente" src="../../images/prix.gif"></th>
			<td><?php echo $infoVoiture['Ven_Prix'];?> &euro;</td>
		</tr>
<?php
			}
?>
		<tr class="piece">
			<th>Propriétaire</th>
			<td><?php echo $infoVoiture['Man_Nom']?></td>
		</tr>
<?php
			$EtatVoiture = dispoVoiture($IdVoiture);
?>
		<tr class="piece">
			<th>Etat :</th>
			<td class="<?php echo ($EtatVoiture==1)?"ok":"casse"?>">
<?php
			switch($EtatVoiture)
			{
				case -1 :
					echo "Pièce(s) cassée(s) !";
					break;
				case 0 :
					echo "Pièce(s) manquante(s) !";
					break;
				case 1 :
					echo "Voiture OK";
					break;
			}
?>
			</td>
		</tr>
	</table>
<?php
		}
		if($_GET['page']=='caracs')
		{
			if($Man_Niveau > 2)
			{
?>
	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="7" class="titre">Caractéristiques techniques</th>
		</tr>
		<tr class="piece">
			<th class="titre"><img alt="Accélération" height="20" src="../../images/acc.gif"></th>
			<th class="titre"><img alt="Vitesse Max" src="../../images/vmax.gif"></th>
			<th class="titre"><img alt="Freinage" src="../../images/frein.gif"></th>
			<th class="titre"><img alt="Turbo" src="../../images/turbo.gif"></th>
			<th class="titre"><img alt="Adhérence" src="../../images/adh.gif"></th>
			<th class="titre"><img alt="Solidité Moteur" src="../../images/solmot.gif"></th>
			<th class="titre"><img alt="Capacité Moteur" src="../../images/capa.gif"></th>
		</tr>
		<tr class="piece">
			<td><?php echo round($infoVoiture['Voit_Acceleration'],2);?></td>
			<td><?php echo $infoVoiture['Voit_VitesseMax'];?></td>
			<td><?php echo $infoVoiture['Voit_Freinage'];?></td>
			<td><?php echo $infoVoiture['Voit_Turbo'];?></td>
			<td><?php echo $infoVoiture['Voit_Adherence'];?></td>
			<td><?php echo $infoVoiture['Voit_SoliditeMoteur'];?></td>
			<td><?php echo $infoVoiture['Voit_CapaciteMoteur']."/".$infoVoiture['Voit_CapaciteMax'];?></td>
		</tr>
	</table>
<br>
<?php
			}
?>
	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="2" class="titre">Performances estimées</th>
		</tr>
		<tr class="piece">
			<th>Vitesse maximum</th>
			<td><?php echo msTOKmh($infoVoiture['Voit_VitesseMax']);?> km/h</td>
		</tr>
		<tr class="piece">
			<th>de 0 à 100 km/h</th>
			<td><?php echo TempsAcc($infoVoiture['Voit_Acceleration'], $infoVoiture['Voit_VitesseMax']);?> s</td>
		</tr>
		<tr class="piece">
			<th>1000 m départ arrêté</th>
			<td><?php echo MilleMetreArrete($infoVoiture['Voit_Acceleration'], $infoVoiture['Voit_VitesseMax']);?>&nbsp;s</td>
		</tr>
		<tr class="piece">
			<th colspan="2" class="titre">Distances Freinage</th>
		</tr>
		<tr class="piece">
			<th>à 50 km/h</th>
			<td><?php echo DistanceFreinage($infoVoiture['Voit_Freinage'],50);?> m</td>
		</tr>
		<tr class="piece">
			<th>à 100 km/h</th>
			<td><?php echo DistanceFreinage($infoVoiture['Voit_Freinage'],100);?> m</td>
		</tr>
		<tr class="piece">
			<th>à 130 km/h</th>
			<td><?php echo DistanceFreinage($infoVoiture['Voit_Freinage'],130);?> m</td>
		</tr>
		<tr class="piece">
			<th>à 150 km/h</th>
			<td><?php echo DistanceFreinage($infoVoiture['Voit_Freinage'],150);?> m</td>
		</tr>
	</table>
<?php
		}
		if($_GET['page']=='pieces')
		{
?>
	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="6" class="titre">Pièces installées</th>
		</tr>
		<tr class="piece">
			<th class="titre">Type</th>
			<th class="titre">Modèle</th>
			<th class="titre">Marque</th>
			<th class="titre">Usure</th>
			<th class="titre">Age</th>
			<th class="titre">Duree de vie</th>
			<th class="titre">Action</th>
		</tr>
<?php
	foreach($pieceInstallee as $IdTypePiece => $infoPiece)
	{
		//echo "Id Pièce : ".$infoPiece['IdPieceDetachee']." Oblig. = ".$infoPiece['TypPi_Obligatoire']."<br>";
		if($infoPiece['IdPieceDetachee'] != "")
			$classe = (dispoPiece($infoPiece['IdPieceDetachee']))?"piece":"casse";
		elseif($infoPiece['TypPi_Obligatoire']==1)
			$classe = "casse";
		else $classe="piece";
?>
		<tr class="<?php echo $classe?>">
			<th><?php echo $infoPiece['TypPi_Libelle']; echo($infoPiece['TypPi_Obligatoire']==1)?"*":"";?></td>
<?php
		if(!empty($infoPiece['IdPieceDetachee']))
		{
?>
			<td><a href="../piece/fiche.php?IdPieceDetachee=<?php echo $infoPiece['IdPieceDetachee']; ?>"><?php echo $infoPiece['ModPi_NomModele'];?></a></td>
			<td><?php echo $infoPiece['Marq_Libelle'];?></td>
			<td class="<?php echo $classe?>"><?php echo round($infoPiece['PiDet_Usure'],0);?> %</td>
			<td class="<?php echo $classe?>"><?php echo round($infoPiece['PiDet_Age']/(24*3600),0);?> jours</td>
			<td class="<?php echo $classe?>"><?php echo $infoPiece['ModPi_DureeVieMax'];?> ans</td>
			<td class="<?php echo $classe?>"><a href="changerPiece.php?IdVoiture=<?php echo $IdVoiture?>&IdType=<?php echo $IdTypePiece?>">Changer/<br />Retirer</a></td>
<?php
		}
		else
		{
?>
			<td colspan="5" class="<?php echo $classe?>"><font color="#C0C0C0">N/A</font></td>
			<td class="<?php echo $classe?>"><a href="changerPiece.php?IdVoiture=<?php echo $IdVoiture?>&IdType=<?php echo $IdTypePiece?>">Installer</a></td>
		</tr>
<?php
		}
	}
?>
	</table>
<?php
		}
		if($_GET['page']=='planning')
		{
?>
	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="3" class="titre">Engagements</th>
		</tr>
		<tr class="piece">
<?php
			if($nbCoursesPrevues == 0)
			{
?>
			<td>Aucune course prévue</td>
<?php
			}
			else
			{
?>
			<th class="titre">Course</th>
			<th class="titre">Pilote</th>
			<th class="titre">Date</th>
		</tr>
<?php
				while($infoEngagement = mysql_fetch_assoc($resultatEngagements))
				{
?>
		<tr class="piece">
			<td><a href="../course/fiche.php?IdCourse=<?php echo $infoEngagement['IdCourse']?>"><?php echo $infoEngagement['Cou_Nom']?></a></td>
			<td><a href="../pilote/fiche.php?IdPilote=<?php echo $infoEngagement['IdPilote']?>"><?php echo $infoEngagement['Pil_Nom']?></a></td>
			<td><?php echo implode(" / ",array_reverse(explode("-",$infoEngagement['Cou_Date'])))?></td>
		</tr>
<?php
				}
			}
?>
	</table>
<br>
	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="4" class="titre">Palmares</th>
		</tr>
		<tr class="piece">
<?php
			if($nbCoursesCourues == 0)
			{
?>
			<td>Aucune course courue</td>
<?php
			}
			else
			{
?>
			<th class="titre">Course</th>
			<th class="titre">Pilote</th>
			<th class="titre">Date</th>
			<th class="titre">Position</th>
		</tr>
<?php
				while($infoPalmares = mysql_fetch_assoc($resultatPalmares))
				{
?>
		<tr class="piece">
			<td><a href="../course/fiche.php?IdCourse=<?php echo $infoPalmares['IdCourse']?>"><?php echo $infoPalmares['Cou_Nom']?></a></td>
			<td><a href="../pilote/fiche.php?IdPilote=<?php echo $infoPalmares['IdPilote']?>"><?php echo $infoPalmares['Pil_Nom']?></a></td>
			<td><?php echo implode(" / ",array_reverse(explode("-",$infoPalmares['Cou_Date'])))?></td>
		<td><?php echo $infoPalmares['IC_Position']?></td>
		</tr>
<?php
				}
			}
?>
	</table>
<?php
		}
?>
		</td>
		<td valign="top" align="center" width="25%">
			<div class="actions">
		<br />Actions possibles<br />
<?php
		if($_GET['page']=='pieces' && $infoVoiture['Voit_IdManager']==$IdManager && empty($infoVoiture['IdVente']))
		{
?>
<form action="changerPiece.php" method="get">
	<input type="hidden" name="IdVoiture" value="<?php echo $infoVoiture['IdVoiture']; ?>">
	<input type="submit" value="Ajouter/Changer une pièce">
</form>
<?php
		}
		if(!empty($submitFormGestion)&& $nbCoursesPrevues == 0)
		{
?>
</form>
<form action="../vente/gestion.php" method="post">
	<input type="hidden" name="IdManager" value="<?php echo $IdManager;?>">
	<input type="hidden" name="IdVoiture" value="<?php echo $infoVoiture['IdVoiture'];?>">
	<input type="hidden" name="ModVoi_PrixNeuve" value="<?php echo $infoVoiture['ModVoi_PrixNeuve']?>">
	<input type="hidden" name="Ven_IdTypeVente" value="1">
	<input type="submit" value="<?php echo $submitFormGestion?>">
	<input type="hidden" name="action" value="<?php echo $actionFormGestion?>">
<?php
		}
		if(!empty($submitFormTraitement))
		{
?>
</form>
<form action="../vente/traitement.php" method="post">
	<input type="hidden" name="IdManager" value="<?php echo $IdManager;?>">
	<input type="hidden" name="IdVente" value="<?php echo $infoVoiture['IdVente'];?>">
	<input type="hidden" name="Ven_IdItem" value="<?php echo $infoVoiture['IdVoiture'];?>">
	<input type="hidden" name="Ven_IdTypeVente" value="1">
	<input type="submit" value="<?php echo $submitFormTraitement;?>">
	<input type="hidden" name="action" value="<?php echo $actionFormTraitement;?>">
	<input type="hidden" name="reponse" value="Oui">
<?php
		}
		if($Man_Niveau > 2)
		{
?>
</form>
<form action="traitement.php" method="get" onSubmit="return confirmSuppr(this)">
	<input type="hidden" name="reponse" value="Oui">
	<input type="hidden" name="IdVoiture" value="<?php echo $infoVoiture['IdVoiture'];?>">
	<input type="submit" name="action" value="Supprimer">

<?php
		}
	}
?>
</form>
		</div>
		</td>
	</tr>
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
