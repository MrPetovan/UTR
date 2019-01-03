<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdManager']))
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
	<title>UTR : Fiche d'un modele de voiture</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
	<script language="JavaScript">
		function confirmSuppr(form)
		{
			var action=form.action.value;
			//if(action=="Supprimer") confirmation = "Etes-vous sûr de vouloir supprimer ce modèle de voiture ?";
			var confirmation = "Etes-vous sûr de vouloir "+action+" ce modèle ?";
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
		<td valign="top">
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td>
<?php
	if(isset($_GET['IdModeleVoiture']))
	{
		$IdModeleVoiture = $_GET['IdModeleVoiture'];

		$requeteInfoModeleVoiture = "	SELECT	IdModeleVoiture,
															ModVoi_IdMarque,
															Marq_Libelle,
															ModVoi_NomModele,
															ModVoi_Niveau,
															ModVoi_PrixNeuve,
															ModVoi_PoidsCarrosserie,
															ModVoi_TypeCarburant,
															ModVoi_IdInjection,
															ModVoi_IdRefroidissement,
															ModVoi_IdBlocMoteur,
															ModVoi_IdTransmission,
															ModVoi_IdJantes,
															ModVoi_IdPneus,
															ModVoi_IdFreins,
															ModVoi_IdAmortisseurs,
															ModVoi_IdSpoiler,
															ModVoi_IdOptiques,
															ModVoi_IdAileron,
															ModVoi_IdChassis,
															ModVoi_IdPucedeContrôle,
															ModVoi_IdNOS,
															ModVoi_IdNéons,
															ModVoi_IdSono
												FROM modele_voiture, marque
												WHERE IdMarque = ModVoi_IdMarque
												AND IdModeleVoiture ='$IdModeleVoiture'";
		$resultatInfoModeleVoiture=mysql_query($requeteInfoModeleVoiture) or die(mysql_error());
		$infoModeleVoiture=mysql_fetch_assoc($resultatInfoModeleVoiture);

		$requeteTypesPiece = "SELECT IdTypePiece, TypPi_Libelle, TypPi_Obligatoire FROM type_piece ORDER BY TypPi_Libelle";
		$resultatTypesPiece = mysql_query($requeteTypesPiece) or die(mysql_error());

		while($typePiece=mysql_fetch_assoc($resultatTypesPiece))
		{
			$IdTypePiece = $typePiece['IdTypePiece'];
			$TypPi_Libelle = $typePiece['TypPi_Libelle'];

			$requeteInfoPieceDefaut= "	SELECT	IdModelePiece,
															ModPi_IdMarque,
															Marq_Libelle,
															ModPi_NomModele,
															ModPi_Acceleration,
															ModPi_VitesseMax,
															ModPi_Freinage,
															ModPi_Turbo,
															ModPi_Adherence,
															ModPi_SoliditeMoteur,
															ModPi_AspectExterieur,
															ModPi_CapaciteMoteur,
															ModPi_CapaciteMax,
															ModPi_Poids,
															ModPi_DureeVieMax,
															ModPi_PrixNeuve
												FROM modele_piece, marque, modele_voiture
												WHERE IdMarque = ModPi_IdMarque
												AND ModVoi_Id".ereg_replace(" ","",$TypPi_Libelle)." = IdModelePiece
												AND IdModeleVoiture = '$IdModeleVoiture'";
			$resultatPieceDefaut = mysql_query($requeteInfoPieceDefaut)or die("Requete Info Piece Defaut : $requeteInfoPieceDefaut<br>".mysql_error());
			$infoPieceDefaut = mysql_fetch_assoc($resultatPieceDefaut);

			$pieceDefaut[$IdTypePiece]=$infoPieceDefaut;
			$pieceDefaut[$IdTypePiece]['TypPi_Libelle'] = $TypPi_Libelle;

			$infoModeleVoiture['ModVoi_Acceleration'] += $infoPieceDefaut['ModPi_Acceleration'];
			$infoModeleVoiture['ModVoi_VitesseMax'] += $infoPieceDefaut['ModPi_VitesseMax'];
			$infoModeleVoiture['ModVoi_Freinage'] += $infoPieceDefaut['ModPi_Freinage'];
			$infoModeleVoiture['ModVoi_Turbo'] += $infoPieceDefaut['ModPi_Turbo'];
			$infoModeleVoiture['ModVoi_Adherence'] += $infoPieceDefaut['ModPi_Adherence'];
			$infoModeleVoiture['ModVoi_SoliditeMoteur'] += $infoPieceDefaut['ModPi_SoliditeMoteur'];
			$infoModeleVoiture['ModVoi_AspectExterieur'] += $infoPieceDefaut['ModPi_AspectExterieur'];
			$infoModeleVoiture['ModVoi_CapaciteMoteur'] += $infoPieceDefaut['ModPi_CapaciteMoteur'];
			$infoModeleVoiture['ModVoi_CapaciteMax'] += $infoPieceDefaut['ModPi_CapaciteMax'];
			$infoModeleVoiture['ModVoi_Poids'] += $infoPieceDefaut['ModPi_Poids'];
			$infoModeleVoiture['ModVoi_PrixNeuve'] += $infoPieceDefaut['ModPi_PrixNeuve'];
		}
?>
<div align="center">
<table border="0" width="80%">
<tr><td>

	<table border="1">
		<tr>
			<th colspan="3">Fiche du modèle <?php echo $infoModeleVoiture['ModVoi_NomModele']?></th>
		</tr>
		<tr>
			<th colspan="1">Marque :</th>
			<td colspan="2"><?php echo $infoModeleVoiture['Marq_Libelle']?></td>
		</tr>
		<tr>
			<th colspan="1">Niveau :</th>
			<td colspan="2"><?php echo $infoModeleVoiture['ModVoi_Niveau']?></td>
		</tr>
		<tr>
			<th colspan="1">Type Carburant :</th>
			<td colspan="2"><?php echo $infoModeleVoiture['ModVoi_TypeCarburant']?></td>
		</tr>
		<tr>
			<th colspan="1">Prix Neuve :</th>
			<td colspan="2"><?php echo $infoModeleVoiture['ModVoi_PrixNeuve']?></td>
		</tr>
		<tr>
			<th colspan="1">Poids Carrosserie :</th>
			<td colspan="2"><?php echo $infoModeleVoiture['ModVoi_PoidsCarrosserie']?> kg</td>
		</tr>
		<tr>
			<th><img alt="Aspect Extérieur" src="../../images/aspect.gif"></th>
			<td><?php echo $infoModeleVoiture['ModVoi_AspectExterieur'];?></td>
		</tr>
		<tr>
			<th><img alt="Poids" src="../../images/poids.gif"></th>
			<td><?php echo $infoModeleVoiture['ModVoi_Poids']+$infoModeleVoiture['ModVoi_PoidsCarrosserie'];?> kg</td>
		</tr>
		<tr>
			<th><img alt="Prix" src="../../images/prix.gif"></th>
			<td><?php echo $infoModeleVoiture['ModVoi_PrixNeuve'];?> &euro;</td>
		</tr>
	</table>
<br>
	<table border="1">
		<tr>
			<th>Caractéristiques techniques</th>
			<th><img alt="Accélération" height="20" src="../../images/acc.gif"></th>
			<th><img alt="Vitesse Max" src="../../images/vmax.gif"></th>
			<th><img alt="Freinage" src="../../images/frein.gif"></th>
			<th><img alt="Turbo" src="../../images/turbo.gif"></th>
			<th><img alt="Adhérence" src="../../images/adh.gif"></th>
			<th><img alt="Solidité Moteur" src="../../images/solmot.gif"></th>
			<th><img alt="Capacité Moteur" src="../../images/capa.gif"></th>
			<th><img alt="Capacité Max" src="../../images/capamax.gif"></th>
		</tr>
		<tr>
			<th></th>
			<td><?php echo round($infoModeleVoiture['ModVoi_Acceleration'],2);?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_VitesseMax'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_Freinage'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_Turbo'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_Adherence'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_SoliditeMoteur'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_CapaciteMoteur'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_CapaciteMax'];?></td>
		</tr>
	</table>
	<br>
	<table border="1">
		<tr>
			<th colspan="2">Performances estimées</th>
		</tr>
		<tr>
			<th>Vitesse maximum :</th>
			<td><?php echo msTOKmh($infoModeleVoiture['ModVoi_VitesseMax']);?> km/h</td>
		</tr>
		<tr>
			<th>de 0 à 100 km/h :</th>
			<td><?php echo TempsAcc($infoModeleVoiture['ModVoi_Acceleration'], $infoModeleVoiture['ModVoi_VitesseMax']);?> s</td>
		</tr>
		<tr>
			<th>1000 m départ arrêté</th>
			<td>
<?php
	$vInit = 0;
	$vitesseMax = $infoModeleVoiture['ModVoi_VitesseMax'];
	$distance = 1000;

	$tempsVitesseMax = 2 * $infoModeleVoiture['ModVoi_Acceleration'] * $vitesseMax /($infoModeleVoiture['ModVoi_Acceleration']*$infoModeleVoiture['ModVoi_Acceleration']);
	$distanceVitesseMax = 	((-$infoModeleVoiture['ModVoi_Acceleration'] * $infoModeleVoiture['ModVoi_Acceleration']) / (12 * $vitesseMax)) * $tempsVitesseMax * $tempsVitesseMax * $tempsVitesseMax +
									($infoModeleVoiture['ModVoi_Acceleration'] / 2) * $tempsVitesseMax * $tempsVitesseMax + $vInit * $tempsVitesseMax;
	if($distanceVitesseMax > $distance)
	{
		//echo "Pas Vitesse max :";
		$tempsTotalTroncon = calcTemps($infoModeleVoiture['ModVoi_Acceleration'],$infoModeleVoiture['ModVoi_Acceleration'], $vitesseMax,$vInit, $distance);
	}
	else
	{
		//echo "Vitesse max :";
		$tempsTotalTroncon = $tempsVitesseMax + ($distance - $distanceVitesseMax)/ $vitesseMax;
	}
	echo round($tempsTotalTroncon,2);
?>
			&nbsp;s</td>
		</tr>
		<tr>
			<th colspan="2">Distances Freinage</th>
		</tr>
		<tr>
			<th>à 50 km/h:</th>
			<td><?php echo DistanceFreinage($infoModeleVoiture['ModVoi_Freinage'],50);?> m</td>
		</tr>
		<tr>
			<th>à 100 km/h:</th>
			<td><?php echo DistanceFreinage($infoModeleVoiture['ModVoi_Freinage'],100);?> m</td>
		</tr>
		<tr>
			<th>à 150 km/h:</th>
			<td><?php echo DistanceFreinage($infoModeleVoiture['ModVoi_Freinage'],150);?> m</td>
		</tr>
	</table>
<br>
	<table border="1">
		<tr>
			<th colspan="6">Pièces installées :</th>
		</tr>
		<tr>
			<th>Type</th>
			<th>Modèle</th>
			<th>Marque</th>
			<th>Duree de vie</th>
		</tr>
<?php
	foreach($pieceDefaut as $IdTypePiece => $infoPieceDefaut)
	{
?>
		<tr>
			<th><?php echo $infoPieceDefaut['TypPi_Libelle']; echo($infoPieceDefaut['TypPi_Obligatoire']==1)?"*":"";?></td>
<?php
		if(!empty($infoPieceDefaut['IdModelePiece']))
		{
?>
			<td><a href="../modele_piece/fiche.php?IdModelePiece=<?php echo $infoPieceDefaut['IdModelePiece']; ?>"><?php echo $infoPieceDefaut['ModPi_NomModele'];?></a></td>
			<td><?php echo $infoPieceDefaut['Marq_Libelle'];?></td>
			<td><?php echo $infoPieceDefaut['ModPi_DureeVieMax'];?></td>
<?php
		}
		else
		{
?>
			<td colspan="5"><font color="#C0C0C0">N/A</font></td>
		</tr>
<?php
		}
	}
?>
	</table>
</td>
</tr>
</table>
</td>
<td>
	<table border="1">
		<tr><th>Actions possibles</th></tr>
		<tr>
			<td>
<form action="gestion.php" method="get">
	<input type="hidden" name="IdModeleVoiture" value="<?php echo $infoModeleVoiture['IdModeleVoiture'];?>">
	<input type="submit" name="action" value="Modifier">
</form>
			</td>
		</tr>
<?php
	}
?>
	</table>
</td>
</tr></table>
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
