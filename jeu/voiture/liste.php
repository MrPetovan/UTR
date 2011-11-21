<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Liste Voiture</title>
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
		<td valign="top" align="center">
<br />
<a href="liste.php?page=parking">Parking</a>&nbsp;|&nbsp;
<a href="liste.php?page=vente">Ventes</a>&nbsp;|&nbsp;
<a href="liste.php?page=neuf">Concessionaire</a>&nbsp;|&nbsp;
<a href="liste.php?page=occaz">Occasion</a>
<br />
<br />
<?php
	if($_GET['page']=='parking')
	{
		$requeteInfoVoitures= "	SELECT	IdVoiture, Voit_IdModele, ModVoi_NomModele, ModVoi_IdMarque, Marq_Libelle, ModVoi_TypeCarburant,
													ModVoi_PoidsCarrosserie, ModVoi_PrixNeuve
										FROM voiture
										INNER JOIN modele_voiture ON IdModeleVoiture = Voit_IdModele
										INNER JOIN marque ON IdMarque = ModVoi_IdMarque
										LEFT JOIN vente ON IdVoiture = Ven_IdItem AND Ven_IdTypeVente = 1
										WHERE Voit_IdManager = '$IdManager'
										AND IdVente IS NULL
										ORDER BY Marq_Libelle, ModVoi_NomModele";
		$resultatInfoVoitures = mysql_query($requeteInfoVoitures)or die (mysql_error());
?>
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="18" class="titre">Voitures disponibles</th>
	</tr>
	<tr class="piece">
		<th class="titre">Marque Modele</th>
		<th class="titre">Carburant</th>
<?php
		if($Man_Niveau > 2)
		{
?>
		<th class="titre"><img alt="Accélération" height="20" src="../../images/acc.gif"></th>
		<th class="titre"><img alt="Vitesse Max" src="../../images/vmax.gif"></th>
		<th class="titre"><img alt="Freinage" src="../../images/frein.gif"></th>
		<th class="titre"><img alt="Turbo" src="../../images/turbo.gif"></th>
		<th class="titre"><img alt="Adhérence" src="../../images/adh.gif"></th>
		<th class="titre"><img alt="Solidité Moteur" src="../../images/solmot.gif"></th>
<?php
		}
?>
		<th class="titre"><img alt="Aspect Extérieur" src="../../images/aspect.gif"></th>
		<th class="titre"><img alt="Capacité Moteur" src="../../images/capa.gif"></th>
		<th class="titre"><img alt="Poids" src="../../images/poids.gif"></th>
		<th class="titre"><img alt="Prix" src="../../images/prix.gif"></th>
	</tr>
<?php
		while($infoVoiture = mysql_fetch_assoc($resultatInfoVoitures))
		{
			$requeteTypesPiece = "SELECT IdTypePiece, TypPi_Libelle, TypPi_Obligatoire FROM type_piece ORDER BY TypPi_Libelle";
			$resultatTypesPiece = mysql_query($requeteTypesPiece) or die(mysql_error());

			while($typePiece=mysql_fetch_assoc($resultatTypesPiece))
			{
				$IdTypePiece=$typePiece['IdTypePiece'];
				$TypPi_Libelle = $typePiece['TypPi_Libelle'];
				$TypPi_Obligatoire = $typePiece['TypPi_Obligatoire'];

				$requeteInfoPieceInstallee= "	SELECT	IdPieceDetachee,
																	ModPi_IdMarque,
																	Marq_Libelle,
																	ModPi_NomModele,
																	ModPi_IdTypePiece,
																	TypPi_Libelle,
																	TypPi_PrixDemontage,
																	TypPi_PrixEstimation,
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
																	PiDet_Usure,
																	PiDet_UsureMesuree,
																	PiDet_Qualite,
																	PiDet_QualiteMesuree,
																	PiDet_DateFabrication,
																	ModPi_PrixNeuve,
																	PiDet_IdManager,
																	IdVente
														FROM piece_detachee
														INNER JOIN modele_piece ON IdModelePiece = PiDet_IdModele
														INNER JOIN marque ON IdMarque = ModPi_IdMarque
														INNER JOIN voiture ON Voit_Id".ereg_replace(" ","",$TypPi_Libelle)." = IdPieceDetachee
														INNER JOIN type_piece ON IdTypePiece = ModPi_IdTypePiece
														LEFT JOIN vente ON Ven_IdItem = IdVoiture AND Ven_IdTypeVente = '1'
														WHERE  ModPi_IdTypePiece = '$IdTypePiece'
														AND IdVoiture = '".$infoVoiture['IdVoiture']."'";
				$infoPieceInstallee = mysql_fetch_assoc(mysql_query($requeteInfoPieceInstallee));
				if(mysql_error()){ echo mysql_error();exit;}
				$pieceInstallee[$IdTypePiece]=$infoPieceInstallee;
				$pieceInstallee[$IdTypePiece]['TypPi_Libelle'] = $TypPi_Libelle;
				$pieceInstallee[$IdTypePiece]['TypPi_Obligatoire'] = $TypPi_Obligatoire;
/*				echo"<pre>infoPieceInstallee[$TypPi_Libelle] Qualite :".$infoPieceInstallee['PiDet_Qualite']." :<br>";
				print_r($infoPieceInstallee);*/
				$infoVoiture['Voit_Acceleration'] += $infoPieceInstallee['ModPi_Acceleration']*$infoPieceInstallee['PiDet_Qualite']/100;
				$infoVoiture['Voit_VitesseMax'] += $infoPieceInstallee['ModPi_VitesseMax']*$infoPieceInstallee['PiDet_Qualite']/100;
				$infoVoiture['Voit_Freinage'] += $infoPieceInstallee['ModPi_Freinage']*$infoPieceInstallee['PiDet_Qualite']/100;
				$infoVoiture['Voit_Turbo'] += $infoPieceInstallee['ModPi_Turbo']*$infoPieceInstallee['PiDet_Qualite']/100;
				$infoVoiture['Voit_Adherence'] += $infoPieceInstallee['ModPi_Adherence']*$infoPieceInstallee['PiDet_Qualite']/100;
				$infoVoiture['Voit_SoliditeMoteur'] += $infoPieceInstallee['ModPi_SoliditeMoteur']*$infoPieceInstallee['PiDet_Qualite']/100;
				$infoVoiture['Voit_AspectExterieur'] += $infoPieceInstallee['ModPi_AspectExterieur'];
				$infoVoiture['Voit_CapaciteMoteur'] += $infoPieceInstallee['ModPi_CapaciteMoteur'];
				$infoVoiture['Voit_CapaciteMax'] += $infoPieceInstallee['ModPi_CapaciteMax'];
				$infoVoiture['Voit_Poids'] += $infoPieceInstallee['ModPi_Poids'];
				$infoVoiture['Voit_PrixNeuve'] += $infoPieceInstallee['ModPi_PrixNeuve'];
/*				echo"<br>infoVoiture<br>";
				print_r($infoVoiture);
				echo"</pre>";*/
			}
?>
	<tr class="piece">
		<td><a href="fiche.php?IdVoiture=<?php echo $infoVoiture['IdVoiture']?>&page=infos"><?php echo $infoVoiture['Marq_Libelle']." ".$infoVoiture['ModVoi_NomModele'];?></a></td>
		<td><?php echo $infoVoiture['ModVoi_TypeCarburant'];?></td>
<?php
			if($Man_Niveau > 2)
			{
?>
		<td><?php echo round($infoVoiture['Voit_Acceleration'],2);?></td>
		<td><?php echo $infoVoiture['Voit_VitesseMax'];?></td>
		<td><?php echo $infoVoiture['Voit_Freinage'];?></td>
		<td><?php echo $infoVoiture['Voit_Turbo'];?></td>
		<td><?php echo $infoVoiture['Voit_Adherence'];?></td>
		<td><?php echo $infoVoiture['Voit_SoliditeMoteur'];?></td>
<?php
			}
?>
		<td><?php echo $infoVoiture['Voit_AspectExterieur'];?></td>
		<td><?php echo $infoVoiture['Voit_CapaciteMoteur']."/".$infoVoiture['Voit_CapaciteMax'];?></td>
		<td><?php echo $infoVoiture['Voit_Poids'] + $infoVoiture['ModVoi_PoidsCarrosserie'];?> kg</td>
		<td><?php echo $infoVoiture['Voit_PrixNeuve'];?> &euro;</td>
	</tr>
<?php
		}
?>
</table>
<?php
	}
	if($_GET['page']=='vente')
	{
		$requeteVoituresVente="	SELECT 	IdVoiture, ModVoi_NomModele, ModVoi_IdMarque, Marq_Libelle, ModVoi_TypeCarburant, IdManager, Man_Nom, Ven_Prix
										FROM voiture, modele_voiture, vente, manager, marque
										WHERE IdModeleVoiture = Voit_IdModele
										AND IdMarque = ModVoi_IdMarque
										AND Ven_IdItem = IdVoiture
										AND IdManager = Voit_IdManager
										AND Ven_IdTypeVente = '1'
										ORDER BY Marq_Libelle, ModVoi_NomModele";
		$resultatVoituresVente = mysql_query($requeteVoituresVente) or die (mysql_error());
?>
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="5" class="titre">Voitures en vente</th>
	</tr>
	<tr class="piece">
<?php
		if(mysql_num_rows($resultatVoituresVente) != 0 )
		{
?>
		<th class="titre">Marque Modele</th>
		<th class="titre">Carburant</th>
		<th class="titre">Manager</th>
		<th class="titre">Prix</th>
<?php
		}
		else
		{
?>
		<td>Pas de voitures en vente</td>
<?php
		}
?>
</tr>
<?php
		while($voitureVente=mysql_fetch_assoc($resultatVoituresVente))
		{
?>
	<tr class="piece">
		<td><a href="fiche.php?IdVoiture=<?php echo $voitureVente['IdVoiture']?>&page=infos"><?php echo $voitureVente['Marq_Libelle']." ".$voitureVente['ModVoi_NomModele'];?></a></td>
		<td><?php echo $voitureVente['ModVoi_TypeCarburant'];?></td>
		<td><?php echo $voitureVente['Man_Nom'];?></td>
		<td><?php echo $voitureVente['Ven_Prix']?> &euro;</td>
	</tr>
<?php
		}
?>
</table>
<?php
	}
	if($_GET['page']=='neuf')
	{
		$requeteVoituresNeuve="	SELECT 	IdVoiture, ModVoi_NomModele, ModVoi_IdMarque, Marq_Libelle, ModVoi_TypeCarburant, Ven_Prix
										FROM voiture, modele_voiture, vente, marque
										WHERE IdModeleVoiture = Voit_IdModele
										AND IdMarque = ModVoi_IdMarque
										AND Ven_IdItem = IdVoiture
										AND Ven_IdTypeVente = '1'
										AND Voit_IdManager = '-1'
										ORDER BY Marq_Libelle, ModVoi_NomModele";
		$resultatVoituresNeuve = mysql_query($requeteVoituresNeuve) or die (mysql_error());
?>
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="5" class="titre">Voitures neuves</th>
	</tr>
	<tr class="piece">
<?php
		if(mysql_num_rows($resultatVoituresNeuve) != 0 )
		{
?>
		<th class="titre">Marque Modele</th>
		<th class="titre">Carburant</th>
		<th class="titre">Prix</th>
<?php
		}
		else
		{
?>
		<td>Pas de voitures en vente</td>
<?php
		}
?>
</tr>
<?php
		while($voitureNeuve=mysql_fetch_assoc($resultatVoituresNeuve))
		{
?>
	<tr class="piece">
		<td><a href="fiche.php?IdVoiture=<?php echo $voitureNeuve['IdVoiture']?>&page=infos"><?php echo $voitureNeuve['Marq_Libelle']." ".$voitureNeuve['ModVoi_NomModele'];?></a></td>
		<td><?php echo $voitureNeuve['ModVoi_TypeCarburant'];?></td>
		<td><?php echo $voitureNeuve['Ven_Prix']?> &euro;</td>
	</tr>
<?php
		}
?>
</table>
<?php
	}
	if($_GET['page']=='occaz')
	{
		$requeteVoituresOccasion="	SELECT 	IdVoiture,
														ModVoi_NomModele,
														ModVoi_IdMarque,
														Marq_Libelle,
														ModVoi_TypeCarburant,
														Ven_Prix
											FROM voiture, modele_voiture, vente, marque
											WHERE IdModeleVoiture = Voit_IdModele
											AND IdMarque = ModVoi_IdMarque
											AND Ven_IdItem = IdVoiture
											AND Ven_IdTypeVente = '1'
											AND Voit_IdManager = '-2'
											ORDER BY Marq_Libelle, ModVoi_NomModele";
		$resultatVoituresOccasion = mysql_query($requeteVoituresOccasion) or die (mysql_error());
?>
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="5" class="titre">Voitures d'occasion</th>
	</tr>
	<tr class="piece">
<?php
		if(mysql_num_rows($resultatVoituresOccasion) != 0 )
		{
?>
		<th class="titre">Marque Modele</th>
		<th class="titre">Carburant</th>
		<th class="titre">Prix</th>
<?php
		}
		else
		{
?>
		<td>Pas de voitures en vente</td>
<?php
		}
?>
</tr>
<?php
		while($voitureOccasion=mysql_fetch_assoc($resultatVoituresOccasion))
		{
?>
	<tr class="piece">
		<td><a href="fiche.php?IdVoiture=<?php echo $voitureOccasion['IdVoiture']?>&page=infos"><?php echo $voitureOccasion['Marq_Libelle']." ".$voitureOccasion['ModVoi_NomModele'];?></a></td>
		<td><?php echo $voitureOccasion['ModVoi_TypeCarburant'];?></td>
		<td><?php echo $voitureOccasion['Ven_Prix']?> &euro;</td>
	</tr>
<?php
		}
?>
</table>
<?php
	}
?>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
<?php
	include("../../frame/piedpage.php");
?>
		</td>
	</tr>
</table>
</body>
</html>

