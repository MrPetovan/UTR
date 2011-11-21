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
	$Man_Niveau = $_SESSION['Jou_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Inventaire des pièces détachées</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
	<link href="../../include/formulaire.css" rel="stylesheet" type="text/css" />
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
		<td valign="top" align="center">
<br>
<a href="liste.php?page=stock&type=tous">Stock</a>&nbsp;|&nbsp;
<a href="liste.php?page=installee&type=tous">Pièces installées</a>&nbsp;|&nbsp;
<a href="liste.php?page=vente&type=tous">Pièces en vente</a>&nbsp;|&nbsp;
<a href="liste.php?page=neuf&type=tous">Pièces Neuves</a>&nbsp;|&nbsp;
<a href="liste.php?page=casseur&type=tous">Ferailleur</a>
<br />
<br />
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="4" class="titre">Type de pièce</th>
	</tr>
	<tr>
<?php
	$triType = $_GET['type'];

	if($triType=="tous")
	{
		$class = "select";
		$onMouseOver = "select";
		$onMouseOut = "select";
	}
	else
	{
		$class = "normal";
		$onMouseOver = "over";
		$onMouseOut = "normal";
	}
?>
		<td colspan="4" class="<?php echo $class?>" onMouseOver="this.className = '<?php echo $onMouseOver?>'; " onMouseOut="this.className = '<?php echo $onMouseOut?>'; " onClick="window.location = 'liste.php?page=<?php echo $_GET['page']?>&type=tous';">Tous les types
		</td>
	</tr>
	<tr>
<?php
	$resultatTypesPiece = mysql_query("SELECT IdTypePiece, TypPi_Libelle FROM type_piece ORDER BY TypPi_Libelle");
	$i=0;
	while($typePiece = mysql_fetch_assoc($resultatTypesPiece))
	{
		if($triType==$typePiece['IdTypePiece'])
		{
			$class = "select";
			$onMouseOver = "select";
			$onMouseOut = "select";
		}
		else
		{
			$class = "normal";
			$onMouseOver = "over";
			$onMouseOut = "normal";
		}

		if($i==4)
		{
			echo "</tr><tr>";
			$i=0;
		}
?>
		<td class="<?php echo $class?>" onMouseOver="this.className = '<?php echo $onMouseOver?>'; " onMouseOut="this.className = '<?php echo $onMouseOut?>'; " onClick="window.location = 'liste.php?page=<?php echo $_GET['page']?>&type=<?php echo $typePiece['IdTypePiece']?>';"><?php echo $typePiece['TypPi_Libelle']?></td>
<?php
		$i++;
	}
?>
	</tr>
</table>
<br />
<?php
	if($_GET['page']=='stock')
	{
?>
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="18" class="titre">Pièces en stock</th>
	</tr>
	<tr class="piece">
		<th class="titre">Modele</th>
		<th class="titre">Marque</th>
<?php
		if($triType == "tous")
		{
?>
		<th class="titre">Type</th>
<?php
		}
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
		<th class="titre"><img alt="Capacité Max" src="../../images/capamax.gif"></th>
		<th class="titre"><img alt="Poids" src="../../images/poids.gif"></th>
		<th class="titre"><img alt="Durée de vie Max" src="../../images/dureeviemax.gif"></th>
		<th class="titre"><img alt="Usure" src="../../images/usure.gif"></th>
		<th class="titre"><img alt="Qualité" src="../../images/qualite.gif"></th>
		<th class="titre"><img alt="Age" src="../../images/age.gif"></th>
		<th class="titre"><img alt="Prix" src="../../images/prix.gif"></th>
	</tr>
<?php
		$requetePieceStock="	SELECT	IdPieceDetachee,
												PiDet_IdModele,
												ModPi_IdMarque,
												Marq_Libelle,
												ModPi_NomModele,
												ModPi_IdTypePiece,
												TypPi_Libelle,
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
												PiDet_UsureMesuree,
												PiDet_QualiteMesuree,
												UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(PiDet_DateFabrication) AS PiDet_Age,
												ModPi_PrixNeuve
								FROM piece_detachee
								INNER JOIN modele_piece ON IdModelePiece = PiDet_IdModele
								INNER JOIN marque ON IdMarque = ModPi_IdMarque
								INNER JOIN type_piece ON IdTypePiece = ModPi_IdTypePiece
								LEFT JOIN vente ON IdPieceDetachee = Ven_IdItem AND Ven_IdTypeVente = 2
								WHERE PiDet_IdManager = '$IdManager'
								AND IdVente IS NULL";
		if($triType != "tous") $requetePieceStock .= " AND ModPi_IdTypePiece = '$triType'";
		$requetePieceStock .= " ORDER BY TypPi_Libelle, Marq_Libelle";
		$resultatPieceStock = mysql_query($requetePieceStock) or die("Requete Piece Stock : $requetePieceStock<br>".mysql_error());


		while($pieceStock=mysql_fetch_assoc($resultatPieceStock))
		{
			$requeteInfoVoiture = "	SELECT IdVoiture FROM voiture
											WHERE Voit_Id".ereg_replace(" ","",$pieceStock['TypPi_Libelle'])."='".$pieceStock['IdPieceDetachee']."'";
			$infoVoiture = mysql_fetch_assoc(mysql_query($requeteInfoVoiture));
			if($infoVoiture['IdVoiture']=="")
			{
?>
	<tr class="piece">
		<td><a href="fiche.php?IdPieceDetachee=<?php echo $pieceStock['IdPieceDetachee']; ?>"><?php echo $pieceStock['ModPi_NomModele'];?></a></td>
		<td><?php echo $pieceStock['Marq_Libelle'];?></td>
<?php
			if ($triType == "tous")
			{
?>
		<td><?php echo $pieceStock['TypPi_Libelle'];?></td>
<?php
			}
			if($Man_Niveau > 2)
			{
?>
		<td><?php if($pieceStock['ModPi_Acceleration']!="") echo (empty($pieceStock['PiDet_QualiteMesuree']))?"?":round($pieceStock['ModPi_Acceleration']*$pieceStock['PiDet_QualiteMesuree']/100,1);?></td>
		<td><?php if($pieceStock['ModPi_VitesseMax']!="") echo (empty($pieceStock['PiDet_QualiteMesuree']))?"?":round($pieceStock['ModPi_VitesseMax']*$pieceStock['PiDet_QualiteMesuree']/100,1);?></td>
		<td><?php if($pieceStock['ModPi_Freinage']!="") echo (empty($pieceStock['PiDet_QualiteMesuree']))?"?":round($pieceStock['ModPi_Freinage']*$pieceStock['PiDet_QualiteMesuree']/100,1);?></td>
		<td><?php if($pieceStock['ModPi_Turbo']!="") echo (empty($pieceStock['PiDet_QualiteMesuree']))?"?":round($pieceStock['ModPi_Turbo']*$pieceStock['PiDet_QualiteMesuree']/100,1);?></td>
		<td><?php if($pieceStock['ModPi_Adherence']!="") echo (empty($pieceStock['PiDet_QualiteMesuree']))?"?":round($pieceStock['ModPi_Adherence']*$pieceStock['PiDet_QualiteMesuree']/100,1);?></td>
		<td><?php if($pieceStock['ModPi_SoliditeMoteur']!="")echo (empty($pieceStock['PiDet_QualiteMesuree']))?"?":round($pieceStock['ModPi_SoliditeMoteur']*$pieceStock['PiDet_QualiteMesuree']/100,1);?></td>
<?php
			}
?>
		<td><?php echo $pieceStock['ModPi_AspectExterieur'];?></td>
		<td><?php echo $pieceStock['ModPi_CapaciteMoteur'];?></td>
		<td><?php echo $pieceStock['ModPi_CapaciteMax'];?></td>
		<td><?php echo $pieceStock['ModPi_Poids'];?></td>
		<td><?php echo (empty($pieceStock['PiDet_QualiteMesuree']))?"?":round(($pieceStock['ModPi_DureeVieMax']*$pieceStock['PiDet_QualiteMesuree']/100)*(1-$pieceStock['PiDet_UsureMesuree']/100),1);?> ans</td>
		<td><?php echo (empty($pieceStock['PiDet_UsureMesuree']))?"?":$pieceStock['PiDet_UsureMesuree'];?> %</td>
		<td><?php echo (empty($pieceStock['PiDet_QualiteMesuree']))?"?":$pieceStock['PiDet_QualiteMesuree'];?> %</td>
		<td><?php echo round($pieceStock['PiDet_Age']/(24*3600),0);?> jours</td>
		<td><?php echo $pieceStock['ModPi_PrixNeuve'];?> €</td>
	</tr>
<?php
			}
		}
?>
</table>
<?php
	}
	if($_GET['page']=='installee')
	{
?>
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="4" class="titre">Pièces utilisées</th>
	</tr>
	<tr class="piece">
		<th class="titre">Modele</th>
		<th class="titre">Marque</th>
		<th class="titre">Type</th>
		<th class="titre">Voiture équipée</th>
	</tr>
<?php
		$requetePieceUtilisees = "	SELECT 	IdPieceDetachee, ModPi_NomModele, ModPi_IdMarque, Marq_Libelle,
														ModPi_IdTypePiece, TypPi_Libelle
											FROM piece_detachee, modele_piece, marque, type_piece
											WHERE IdModelePiece = PiDet_IdModele
											AND IdTypePiece = ModPi_IdTypePiece
											AND IdMarque = ModPi_IdMarque
											AND PiDet_IdManager = '$IdManager'";
		if($triType != "tous") $requetePieceUtilisees .= " AND ModPi_IdTypePiece = '$triType'";
		$requetePieceUtilisees .= " ORDER BY TypPi_Libelle, Marq_Libelle";
		$resultatPieceUtilisees = mysql_query($requetePieceUtilisees)or die("Requete Pieces Utilisee : ".mysql_error());

		while($pieceUtilisee=mysql_fetch_assoc($resultatPieceUtilisees))
		{
			$requeteInfoVoiture = "	SELECT IdVoiture, ModVoi_NomModele, ModVoi_IdMarque, Marq_Libelle
									FROM voiture, modele_voiture, marque
									WHERE IdModeleVoiture = Voit_IdModele
									AND Voit_Id".str_replace(" ","",$pieceUtilisee['TypPi_Libelle'])."='".$pieceUtilisee['IdPieceDetachee']."'
									AND IdMarque = ModVoi_IdMarque";
			$resultatInfoVoiture = mysql_query($requeteInfoVoiture) or die (mysql_error());
			$infoVoiture = mysql_fetch_assoc($resultatInfoVoiture);
			if($infoVoiture['IdVoiture']!='')
			{
?>
	<tr class="piece">
		<td><a href="fiche.php?IdPieceDetachee=<?php echo $pieceUtilisee['IdPieceDetachee']?>"><?php echo $pieceUtilisee['ModPi_NomModele'];?></a></td>
		<td><?php echo $pieceUtilisee['Marq_Libelle'];?></td>
		<td><?php echo $pieceUtilisee['TypPi_Libelle'];?></td>
		<td><a href="../voiture/fiche.php?IdVoiture=<?php echo $infoVoiture['IdVoiture']?>&page=infos"><?php echo $infoVoiture['Marq_Libelle']." ".$infoVoiture['ModVoi_NomModele'];?></a></td>
	</tr>
<?php
			}
		}
?>
</table>
<?php
	}
	if($_GET['page']=='vente')
	{
		$requetePiecesVente = "	SELECT 	IdPieceDetachee,
												ModPi_NomModele,
												ModPi_IdMarque,
												Marq_Libelle,
												ModPi_IdTypePiece,
												TypPi_Libelle,
												IdManager,
												Man_Nom,
												Ven_Prix
									FROM piece_detachee, modele_piece, vente, manager, marque, type_piece
									WHERE IdModelePiece = PiDet_IdModele
									AND IdMarque = ModPi_IdMarque
									AND IdTypePiece = ModPi_IdTypePiece
									AND Ven_IdItem = IdPieceDetachee
									AND IdManager = PiDet_IdManager
									AND Ven_IdTypeVente = '2'
									AND ModPi_Niveau <= '".niveauDouble($infoManager['Man_Reputation'],1000)."'";
		if($triType != "tous") $requetePiecesVente .= " AND ModPi_IdTypePiece = '$triType'";
		$requetePiecesVente .= " ORDER BY Marq_Libelle, ModPi_NomModele";
		//echo $requetePiecesVente;
		$resultatPiecesVente = mysql_query($requetePiecesVente) or die (mysql_error());
?>
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="4" class="titre">Pièces en vente</th>
	</tr>
	<tr class="piece">
<?php
		if(mysql_num_rows($resultatPiecesVente) != 0)
		{
?>
		<th class="titre">Marque Modele</th>
<?php
			if ($triType == "tous")
			{
?>
		<th class="titre">Type</th>
<?php
			}
?>
		<th class="titre">Manager</th>
		<th class="titre">Prix</th>
<?php
		}
		else
		{
?>
		<td>Pas de pièces pour ce type</td>
<?php
		}
?>
	</tr>
<?php
	while($pieceVente=mysql_fetch_assoc($resultatPiecesVente))
	{
?>
	<tr class="piece">
		<td><a href="fiche.php?IdPieceDetachee=<?php echo $pieceVente['IdPieceDetachee'];?>"><?php echo $pieceVente['ModPi_NomModele']." ".$pieceVente['Marq_Libelle'];?></a></td>
<?php
		if ($triType == "tous")
		{
?>
		<td><?php echo $pieceVente['TypPi_Libelle'];?></td>
<?php
		}
?>
		<td><?php echo $pieceVente['Man_Nom'];?></td>
		<td><?php echo $pieceVente['Ven_Prix'];?> €</td>
	</tr>
<?php
	}
?>
</table>
<?php
	}
	if($_GET['page']=='neuf')
	{
?>
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="4" class="titre">Pièces Neuves</th>
	</tr>
	<tr class="piece">
		<th class="titre">Marque Modele</th>
<?php
		if ($triType == "tous")
		{
?>
		<th class="titre">Type</th>
<?php
		}
?>		<th class="titre">Prix</th>
	</tr>
<?php
		$requetePiecesNeuve = "	SELECT 	IdPieceDetachee,
													ModPi_NomModele,
													ModPi_IdMarque,
													Marq_Libelle,
													ModPi_IdTypePiece,
													TypPi_Libelle,
													Ven_Prix
										FROM piece_detachee, modele_piece, vente, marque, type_piece
										WHERE IdModelePiece = PiDet_IdModele
										AND IdMarque = ModPi_IdMarque
										AND IdTypePiece = ModPi_IdTypePiece
										AND Ven_IdItem = IdPieceDetachee
										AND PiDet_IdManager = '-1'
										AND Ven_IdTypeVente = '2'
										AND ModPi_Niveau <= '".niveauDouble($infoManager['Man_Reputation'],1000)."'";
		if($triType != "tous") $requetePiecesNeuve .= " AND ModPi_IdTypePiece = '$triType'";
		$requetePiecesNeuve .= " ORDER BY TypPi_Libelle, Ven_Prix DESC";
		$resultatPiecesNeuve = mysql_query($requetePiecesNeuve) or die (mysql_error());

		while($pieceNeuve=mysql_fetch_assoc($resultatPiecesNeuve))
		{
?>
	<tr class="piece">
		<td><a href="fiche.php?IdPieceDetachee=<?php echo $pieceNeuve['IdPieceDetachee'];?>"><?php echo $pieceNeuve['ModPi_NomModele']." ".$pieceNeuve['Marq_Libelle'];?></a></td>
<?php
			if ($triType == "tous")
			{
?>
		<td><?php echo $pieceNeuve['TypPi_Libelle'];?></td>
<?php
			}
?>
		<td><?php echo $pieceNeuve['Ven_Prix'];?> €</td>
	</tr>
<?php
		}
?>
</table>
<?php
	}
	if($_GET['page']=='casseur')
	{
?>
<table border="0" class="liste" class="liste">
	<tr class="piece">
		<th colspan="4" class="titre">Ferrailleur</th>
	</tr>
	<tr class="piece">
		<th class="titre">Marque Modele</th>
<?php
		if ($triType == "tous")
		{
?>
		<th class="titre">Type</th>
<?php
		}
?>
		<th class="titre">Prix</th>
		<th class="titre">Usure</th>
	</tr>
<?php
		$requetePiecesCasseur = "	SELECT 	IdPieceDetachee,
													ModPi_NomModele,
													ModPi_IdMarque,
													Marq_Libelle,
													ModPi_IdTypePiece,
													TypPi_Libelle,
													Ven_Usure,
													Ven_Prix
										FROM piece_detachee, modele_piece, vente, marque, type_piece
										WHERE IdModelePiece = PiDet_IdModele
										AND IdMarque = ModPi_IdMarque
										AND IdTypePiece = ModPi_IdTypePiece
										AND Ven_IdItem = IdPieceDetachee
										AND PiDet_IdManager = '-2'
										AND Ven_IdTypeVente = '2'
										AND ModPi_Niveau <= '".niveauDouble($infoManager['Man_Reputation'],1000)."'";
		if($triType != "tous") $requetePiecesCasseur .= " AND ModPi_IdTypePiece = '$triType'";
		$requetePiecesCasseur .= " ORDER BY TypPi_Libelle, Ven_Prix DESC";
		$resultatPiecesCasseur = mysql_query($requetePiecesCasseur) or die (mysql_error());

		while($pieceCasseur=mysql_fetch_assoc($resultatPiecesCasseur))
		{
?>
	<tr class="piece" align="center">
		<td><a href="fiche.php?IdPieceDetachee=<?php echo $pieceCasseur['IdPieceDetachee'];?>"><?php echo $pieceCasseur['ModPi_NomModele']." ".$pieceCasseur['Marq_Libelle'];?></a></td>
<?php
			if ($triType == "tous")
			{
?>
		<td><?php echo $pieceCasseur['TypPi_Libelle'];?></td>
<?php
			}
?>
		<td><?php echo $pieceCasseur['Ven_Prix'];?> €</td>
		<td><?php echo $pieceCasseur['Ven_Usure'];?> %</td>
	</tr>
<?php
		}
?>
</table>
<?php
	}
?>
<br />
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