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

	if(isset($_GET['IdPieceDetachee']))
	{
		$IdPieceDetachee = $_GET['IdPieceDetachee'];

		$requeteInfoPieceDetachee = "	SELECT	IdPieceDetachee,
															ModPi_IdMarque,
															Marq_Libelle,
															ModPi_NomModele,
															ModPi_Commentaires,
															ModPi_IdTypePiece,
															TypPi_Libelle,
															TypPi_PrixDemontage,
															TypPi_PrixEstimation,
															TypPi_PrixReparation,
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
															UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(PiDet_DateFabrication) AS PiDet_Age,
															ModPi_PrixNeuve,
															PiDet_IdManager,
															IdVente,
															Ven_Prix,
															Ven_Usure,
															Ven_Qualite
									FROM piece_detachee
									INNER JOIN modele_piece ON IdModelePiece = PiDet_IdModele
									LEFT JOIN type_piece ON IdTypePiece = ModPi_IdTypePiece
									LEFT JOIN marque ON IdMarque = ModPi_IdMarque
									LEFT JOIN vente ON Ven_IdItem = IdPieceDetachee AND Ven_IdTypeVente = '2'
									WHERE IdPieceDetachee = '$IdPieceDetachee'";
		$resultatInfoPieceDetachee = mysql_query($requeteInfoPieceDetachee)or die("Requete Info Piece : $requeteInfoPieceDetachee<br>".mysql_error());
		$infoPieceDetachee = mysql_fetch_assoc($resultatInfoPieceDetachee);

		$requeteMaxCaracs ="	SELECT	MAX(ModPi_Acceleration) AS ModPi_AccelerationMax,
												MAX(ModPi_VitesseMax) AS ModPi_VitesseMaxMax,
												MAX(ModPi_Freinage) AS ModPi_FreinageMax,
												MAX(ModPi_Turbo) AS ModPi_TurboMax,
												MAX(ModPi_Adherence) AS ModPi_AdherenceMax,
												MAX(ModPi_SoliditeMoteur) AS ModPi_SoliditeMoteurMax,
												MAX(ModPi_AspectExterieur) AS ModPi_AspectExterieurMax,
												MIN(ModPi_Acceleration) AS ModPi_AccelerationMin,
												MIN(ModPi_VitesseMax) AS ModPi_VitesseMaxMin,
												MIN(ModPi_Freinage) AS ModPi_FreinageMin,
												MIN(ModPi_Turbo) AS ModPi_TurboMin,
												MIN(ModPi_Adherence) AS ModPi_AdherenceMin,
												MIN(ModPi_SoliditeMoteur) AS ModPi_SoliditeMoteurMin,
												MIN(ModPi_AspectExterieur) AS ModPi_AspectExterieurMin
									FROM modele_piece
									WHERE ModPi_IdTypePiece = '".$infoPieceDetachee['ModPi_IdTypePiece']."'";
		$resultatMaxCaracs = mysql_query($requeteMaxCaracs) or die("Requete Max Caracs : ".mysql_error());
		$CaracMax = mysql_fetch_assoc($resultatMaxCaracs);

//Pièce n'existe plus
		if(!isset($infoPieceDetachee['IdPieceDetachee']))
		{
			header("Location: erreur.php");
			exit;
		}
?>
<html>
<head>
	<title>UTR : Fiche d'une pièce détachée</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script language="JavaScript">
		function confirmSuppr(form)
		{
			var confirmation;
			var action=form.action.value;
			if(action=="Supprimer") confirmation = "Etes-vous sûr de vouloir supprimer cette pièce détachée ?";
			else confirmation = (action+" cette pièce détachée ?");
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
		<td width="110" valign="top">
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td valign="top" align="center">
<?php
		if(isset($infoPieceDetachee['IdVente']))
		{
			$infoPieceDetachee['ModPi_PrixNeuve'] = $infoPieceDetachee['Ven_Prix'];
			$infoPieceDetachee['PiDet_QualiteMesuree'] = $infoPieceDetachee['Ven_Qualite'];
			$infoPieceDetachee['PiDet_UsureMesuree'] = $infoPieceDetachee['Ven_Usure'];
		}

		$requeteInfoVoiture = "	SELECT IdVoiture, ModVoi_NomModele, ModVoi_IdMarque, Marq_Libelle
										FROM voiture, modele_voiture, marque
										WHERE IdModeleVoiture = Voit_IdModele
										AND Voit_Id".str_replace(" ","",$infoPieceDetachee['TypPi_Libelle'])."='$IdPieceDetachee'
										AND IdMarque = ModVoi_IdMarque";
		$resultatInfoVoiture = mysql_query($requeteInfoVoiture) or die (mysql_error());
		$infoVoiture = mysql_fetch_assoc($resultatInfoVoiture);

		$dureeVieActuelle = $infoPieceDetachee['ModPi_DureeVieMax']*($infoPieceDetachee['PiDet_Qualite']/100) * sqrt(100 - $infoPieceDetachee['PiDet_Usure']) / sqrt(100);
		$dureeVieActuelle *= 365*24*60*60;

		if($infoPieceDetachee['PiDet_Age'] > $dureeVieActuelle) $PiDet_Casse = 1;
		else $PiDet_Casse = 0;

		if(empty($infoPieceDetachee['IdVente']))
		{
			//Pièce pas en vente
			switch($infoPieceDetachee['PiDet_IdManager'])
			{
				case $IdManager :
					$submitFormGestion = "Vendre cette pièce";
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
			//Pièce en vente
			switch($infoPieceDetachee['PiDet_IdManager'])
			{
				case $IdManager :
					$submitFormGestion = "Modifier la vente";
					$actionFormGestion = "Modifier";
					$submitFormTraitement = "Annuler la vente";
					$actionFormTraitement = "Supprimer";
					$confirmFormTraitement = "";
					break;
				default :
					$submitFormGestion = "";
					$actionFormGestion = "";
					$submitFormTraitement = "Acheter cette pièce";
					$actionFormTraitement = "Acheter";
					$confirmFormTraitement = "Etes-vous sûr de vouloir acheter cette pièce ?";
					break;
			}
		}
?>
<br>
<?php
//Affichage du message d'erreur
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
			echo "<br>";
 		}
?>
<div align="left">
<?php
		if($infoVoiture['IdVoiture']=="")
		{
			if($infoPieceDetachee['IdVente'] != "")
			{
				if($infoPieceDetachee['PiDet_IdManager'] == "-1")
				{
?>
<a href="liste.php?page=neuf&type=<?php echo $infoPieceDetachee['ModPi_IdTypePiece']?>"><< Revenir au magasin</a>
<?php
				}
				elseif($infoPieceDetachee['PiDet_IdManager'] == "-2")
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
<a href="liste.php?page=stock&type=<?php echo $infoPieceDetachee['ModPi_IdTypePiece']?>"><< Revenir à l'entrepôt</a>
<?php
			}
		}
		else
		{
?>
<a href="liste.php?page=installee&type=<?php echo $infoPieceDetachee['ModPi_IdTypePiece']?>"><< Revenir à l'entrepôt</a>
<br />
<br />
<a href="../voiture/fiche.php?IdVoiture=<?php echo $infoVoiture['IdVoiture'];?>&page=pieces"><< Revenir à la voiture</a>
<?php
		}
?>
</div>
<br />
<br />
	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="2">Fiche technique de la pièce <?php echo $infoPieceDetachee['ModPi_NomModele']?></td>
		</tr>
		<tr class="piece">
			<th>Marque :</th>
			<td><?php echo $infoPieceDetachee['Marq_Libelle']?></td>
		</tr>
		<tr class="piece">
			<th>Modèle :</th>
			<td><?php echo $infoPieceDetachee['ModPi_NomModele']?></td>
		</tr>
		<tr class="piece">
			<th>Type :</th>
			<td><?php echo $infoPieceDetachee['TypPi_Libelle'];?></td>
		</tr>
		<tr class="piece">
			<th>État :</th>
		<?php if ($PiDet_Casse):?>
			<td class="casse">Pièce Cassée !</td>
		<?php elseif ($infoPieceDetachee['PiDet_Usure'] == 0) :?>
			<td>Neuf</td>
		<?php else :?>
			<td>En usage</td>
		<?php endif;?>
		</tr>
		<tr class="piece">
			<th>Voiture équipée :</th>
			<td>
<?php
	if($infoPieceDetachee['IdVente'] != "")
	{
?>
		Aucune => Pièce en vente
<?php
	}
	elseif($infoVoiture['IdVoiture']=="")
	{
?>
		Aucune => Pièce en stock
<?php
	}
	else
	{
?>
		<a href="../voiture/fiche.php?IdVoiture=<?php echo $infoVoiture['IdVoiture'];?>"><?php echo $infoVoiture['Marq_Libelle']." ".$infoVoiture['ModVoi_NomModele'];?></a></td>
<?php
	}
?>
		</tr>
		<tr class="piece">
			<th><img alt="Aspect Extérieur" title="Aspect Extérieur" src="../../images/aspect.gif"></th>
			<td>
<?php
		if($infoPieceDetachee['ModPi_AspectExterieur']!="")
		{
			echo "[";
			for($i = 0;$i < 10;$i++)
				echo ($i < ($infoPieceDetachee['ModPi_AspectExterieur']*10 / $CaracMax['ModPi_AspectExterieurMax']-$CaracMax['ModPi_AspectExterieurMin']+1))?"O":"-";
			echo "]";
		}
?>
			</td>
		</tr>
		<tr class="piece">
			<th><img alt="Poids" title="Poids" src="../../images/poids.gif"></th>
			<td><?php echo $infoPieceDetachee['ModPi_Poids'];?> kg</td>
		</tr>
		<tr class="piece">
			<th><img alt="Age" title="Age" src="../../images/age.gif"></th>
			<td><?php echo round($infoPieceDetachee['PiDet_Age']/(24*3600),0);?> jours</td>
		</tr>
		<tr class="piece">
			<th><img alt="Prix" title="Prix" src="../../images/prix.gif"></th>
			<td><?php echo $infoPieceDetachee['ModPi_PrixNeuve']?> €</td>
		</tr>
		<tr class="piece">
			<td colspan="2"><?php echo nl2br($infoPieceDetachee['ModPi_Commentaires'])?></td>
		</tr>
	</table>
<br />

	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="2">Caractéristiques</th>
		</tr>
<?php
		if($infoPieceDetachee['ModPi_Acceleration']!="")
		{
?>
		<tr class="piece">
			<th><img alt="Accélération" title="Accélération" height="20" src="../../images/acc.gif"></th>
			<td>[
<?php
			for($i = 0;$i < 10;$i++)
				echo ($i < (($infoPieceDetachee['ModPi_Acceleration']-$CaracMax['ModPi_AccelerationMin'])*10 / ($CaracMax['ModPi_AccelerationMax']-$CaracMax['ModPi_AccelerationMin']+1)))?"O":"-";
?>
			]</td>
		</tr>
<?php
		}
		if($infoPieceDetachee['ModPi_VitesseMax']!="")
		{
?>
		<tr class="piece">
			<th><img alt="Vitesse Max" title="Vitesse Max" src="../../images/vmax.gif"></th>
			<td>[
<?php
			for($i = 0;$i < 10;$i++)
				echo ($i < (($infoPieceDetachee['ModPi_VitesseMax']-$CaracMax['ModPi_VitesseMaxMin'])*10 / ($CaracMax['ModPi_VitesseMaxMax']-$CaracMax['ModPi_VitesseMaxMin']+1)))?"O":"-";
?>
			]</td>
		</tr>
<?php
		}
		if($infoPieceDetachee['ModPi_Freinage']!="")
		{
?>
		<tr class="piece">
			<th><img alt="Freinage" title="Freinage" src="../../images/frein.gif"></th>
			<td>[
<?php
			for($i = 0;$i < 10;$i++)
				echo ($i < (($infoPieceDetachee['ModPi_Freinage']-$CaracMax['ModPi_FreinageMin'])*10 / ($CaracMax['ModPi_FreinageMax']-$CaracMax['ModPi_FreinageMin']+1)))?"O":"-";
?>
			]</td>
		</tr>
<?php
		}
		if($infoPieceDetachee['ModPi_Turbo']!="")
		{
?>
		<tr class="piece">
			<th><img alt="Turbo" title="Turbo" src="../../images/turbo.gif"></th>
			<td>[
<?php
			for($i = 0;$i < 10;$i++)
				echo ($i < ($infoPieceDetachee['ModPi_Turbo']-$CaracMax['ModPi_TurboMin']*10 / ($CaracMax['ModPi_TurboMax']-$CaracMax['ModPi_TurboMin']+1)))?"O":"-";
?>
			]</td>
		</tr>
<?php
		}
		if($infoPieceDetachee['ModPi_Adherence']!="")
		{
?>
		<tr class="piece">
			<th><img alt="Adhérence" title="Adhérence" src="../../images/adh.gif"></th>
			<td>[
<?php
			for($i = 0;$i < 10;$i++)
				echo ($i < (($infoPieceDetachee['ModPi_Adherence']-$CaracMax['ModPi_AdherenceMin'])*10 / ($CaracMax['ModPi_AdherenceMax']-$CaracMax['ModPi_AdherenceMin']+1)))?"O":"-";
?>
			]</td>
		</tr>
<?php
		}
		if($infoPieceDetachee['ModPi_SoliditeMoteur']!="")
		{
?>
		<tr class="piece">
			<th><img alt="Solidité Moteur" title="Solidité Moteur" src="../../images/solmot.gif"></th>
			<td>[
<?php
			for($i = 0;$i < 10;$i++)
				echo ($i < (($infoPieceDetachee['ModPi_SoliditeMoteur']-$CaracMax['ModPi_SoliditeMoteurMin'])*10 / ($CaracMax['ModPi_SoliditeMoteurMax']-$CaracMax['ModPi_SoliditeMoteurMin']+1)))?"O":"-";
?>
			]</td>
		</tr>
<?php
		}
		if($infoPieceDetachee['ModPi_CapaciteMoteur']!="")
		{
?>
		<tr class="piece">
			<th><img alt="Capacité Moteur" title="Capacité Moteur" src="../../images/capa.gif"></th>
			<td><?php echo $infoPieceDetachee['ModPi_CapaciteMoteur'];?></td>
		</tr>
<?php
		}
		if($infoPieceDetachee['ModPi_CapaciteMax']!="")
		{
?>
		<tr class="piece">
			<th><img alt="Capacité Max" title="Capacité Max" src="../../images/capamax.gif"></th>
			<td><?php echo $infoPieceDetachee['ModPi_CapaciteMax'];?></td>
		</tr>
<?php
		}
		if($infoPieceDetachee['ModPi_DureeVieMax']!="")
		{
?>
		<tr class="piece">
			<th><img alt="Durée de vie Max" title="Durée de vie Max" src="../../images/dureeviemax.gif"></th>
			<td><?php echo $infoPieceDetachee['ModPi_DureeVieMax'];?></td>
		</tr>
<?php
		}
?>
		<tr class="piece">
			<th><img alt="Usure" title="Usure" src="../../images/usure.gif"></th>
			<td><?php echo ($infoPieceDetachee['PiDet_UsureMesuree']=="")?"?":$infoPieceDetachee['PiDet_UsureMesuree'];?> %</td>
		</tr>
		<tr class="piece">
			<th><img alt="Qualité" title="Qualité" src="../../images/qualite.gif"></th>
			<td><?php echo ($infoPieceDetachee['PiDet_QualiteMesuree']=='')?"?":$infoPieceDetachee['PiDet_QualiteMesuree'];?> %</td>
		</tr>
	</table>
<br>
	<table border="0" class="liste">
		<tr class="piece">
			<th>Caractéristiques</th>
			<th><img alt="Accélération" title="Accélération" height="20" src="../../images/acc.gif"></th>
			<th><img alt="Vitesse Max" title="Vitesse Max" src="../../images/vmax.gif"></th>
			<th><img alt="Freinage" title="Freinage" src="../../images/frein.gif"></th>
			<th><img alt="Turbo" title="Turbo" src="../../images/turbo.gif"></th>
			<th><img alt="Adhérence" title="Adhérence" src="../../images/adh.gif"></th>
			<th><img alt="Solidité Moteur" title="Solidité Moteur" src="../../images/solmot.gif"></th>
			<th><img alt="Capacité Moteur" title="Capacité Moteur" src="../../images/capa.gif"></th>
			<th><img alt="Capacité Max" title="Capacité Max" src="../../images/capamax.gif"></th>
			<th><img alt="Durée de vie Max" title="Durée de vie Max" src="../../images/dureeviemax.gif"></th>
			<th><img alt="Usure" title="Usure" src="../../images/usure.gif"></th>
			<th><img alt="Qualité" title="Qualité" src="../../images/qualite.gif"></th>
		</tr>
		<tr class="piece">
			<th>Données standard</th>
			<td><?php echo $infoPieceDetachee['ModPi_Acceleration'];?></td>
			<td><?php echo $infoPieceDetachee['ModPi_VitesseMax'];?></td>
			<td><?php echo $infoPieceDetachee['ModPi_Freinage'];?></td>
			<td><?php echo $infoPieceDetachee['ModPi_Turbo'];?></td>
			<td><?php echo $infoPieceDetachee['ModPi_Adherence'];?></td>
			<td><?php echo $infoPieceDetachee['ModPi_SoliditeMoteur'];?></td>
			<td rowspan="2"><?php echo $infoPieceDetachee['ModPi_CapaciteMoteur'];?></td>
			<td rowspan="2"><?php echo $infoPieceDetachee['ModPi_CapaciteMax'];?></td>
			<td><?php echo $infoPieceDetachee['ModPi_DureeVieMax'];?></td>
			<td rowspan="2"><?php echo ($infoPieceDetachee['PiDet_UsureMesuree']=="")?"?":$infoPieceDetachee['PiDet_UsureMesuree'];?> %</td>
			<td>100 %</td>
		</tr>
		<tr class="piece">
			<th>Données mesurées</th>
			<td><?php if($infoPieceDetachee['ModPi_Acceleration']!="") echo ($infoPieceDetachee['PiDet_QualiteMesuree']=="")?"?":round($infoPieceDetachee['ModPi_Acceleration']*$infoPieceDetachee['PiDet_QualiteMesuree']/100,1);?></td>
			<td><?php if($infoPieceDetachee['ModPi_VitesseMax']!="") echo ($infoPieceDetachee['PiDet_QualiteMesuree']=="")?"?":round($infoPieceDetachee['ModPi_VitesseMax']*$infoPieceDetachee['PiDet_QualiteMesuree']/100,1);?></td>
			<td><?php if($infoPieceDetachee['ModPi_Freinage']!="") echo ($infoPieceDetachee['PiDet_QualiteMesuree']=="")?"?":round($infoPieceDetachee['ModPi_Freinage']*$infoPieceDetachee['PiDet_QualiteMesuree']/100,1);?></td>
			<td><?php if($infoPieceDetachee['ModPi_Turbo']!="") echo ($infoPieceDetachee['PiDet_QualiteMesuree']=="")?"?":round($infoPieceDetachee['ModPi_Turbo']*$infoPieceDetachee['PiDet_QualiteMesuree']/100,1);?></td>
			<td><?php if($infoPieceDetachee['ModPi_Adherence']!="") echo ($infoPieceDetachee['PiDet_QualiteMesuree']=="")?"?":round($infoPieceDetachee['ModPi_Adherence']*$infoPieceDetachee['PiDet_QualiteMesuree']/100,1);?></td>
			<td><?php if($infoPieceDetachee['ModPi_SoliditeMoteur']!="")echo ($infoPieceDetachee['PiDet_QualiteMesuree']=="")?"?":round($infoPieceDetachee['ModPi_SoliditeMoteur']*$infoPieceDetachee['PiDet_QualiteMesuree']/100,1);?></td>
			<td><?php echo ($infoPieceDetachee['PiDet_QualiteMesuree']=='')?"?":round(($infoPieceDetachee['ModPi_DureeVieMax']*$infoPieceDetachee['PiDet_QualiteMesuree']/100)* sqrt(100 - $infoPieceDetachee['PiDet_UsureMesuree'])/sqrt(100),1);?></td>
			<td><?php echo ($infoPieceDetachee['PiDet_QualiteMesuree']=='')?"?":$infoPieceDetachee['PiDet_QualiteMesuree'];?> %</td>
		</tr>
	</table>
</div>
<?php
	}
?>
</td>
<td valign="top">
<div class="actions">Actions possibles<br /><br />
<?php
	if($infoPieceDetachee['PiDet_IdManager']==$IdManager)
	{
		if($infoPieceDetachee['PiDet_UsureMesuree'] != '0' && !$PiDet_Casse)
		{
?>
<form action="traitement.php" method="post">
	<input type="hidden" name="IdPieceDetachee" value="<?php echo $infoPieceDetachee['IdPieceDetachee'];?>">
	<input type="hidden" name="TypPi_PrixReparation" value="<?php echo ($infoPieceDetachee['TypPi_PrixReparation']*$infoPieceDetachee['PiDet_UsureMesuree'])?>">
	<input type="hidden" name="action" value="Reparer">
	<input type="submit" value="Réparer la pièce (<?php echo $infoPieceDetachee['TypPi_PrixReparation']*$infoPieceDetachee['PiDet_UsureMesuree']?> €)"<?php echo ($infoManager['Man_Solde'] < $infoPieceDetachee['TypPi_PrixReparation']*$infoPieceDetachee['PiDet_UsureMesuree'])?" disabled":""?>>
</form>
<?php
		}
		if($infoPieceDetachee['TypPi_PrixEstimation'] != '' && $infoPieceDetachee['IdVente'] == "" && !$PiDet_Casse)
		{
?>
<form action="traitement.php" method="post">
	<input type="hidden" name="IdPieceDetachee" value="<?php echo $infoPieceDetachee['IdPieceDetachee'];?>">
	<input type="hidden" name="TypPi_PrixEstimation" value="<?php echo $infoPieceDetachee['TypPi_PrixEstimation']?>">
	<input type="hidden" name="action" value="Estimer">
	<input type="hidden" name="TypPi_PrixEstimation" value="<?php echo $infoPieceDetachee['TypPi_PrixEstimation']?>">
	<input type="submit" value="Estimer les caractéristiques (<?php echo $infoPieceDetachee['TypPi_PrixEstimation']?> €)"<?php echo ($infoManager['Man_Solde'] < $infoPieceDetachee['TypPi_PrixEstimation'])?" disabled":""?>>
</form>
<?php
		}
		if(!empty($infoVoiture['IdVoiture']))
		{
?>
<form action="traitement.php" method="post">
	<input type="hidden" name="IdPieceDetachee" value="<?php echo $infoPieceDetachee['IdPieceDetachee'];?>">
	<input type="hidden" name="TypPi_PrixDemontage" value="<?php echo $infoPieceDetachee['TypPi_PrixDemontage']?>">
	<input type="hidden" name="TypPi_Libelle" value="<?php echo str_replace(" ","",$infoPieceDetachee['TypPi_Libelle'])?>">
	<input type="hidden" name="IdVoiture" value="<?php echo $infoVoiture['IdVoiture']?>">

	<input type="hidden" name="action" value="Retirer">
	<input type="submit" value="Retirer la pièce de la voiture (<?php echo $infoPieceDetachee['TypPi_PrixDemontage']?> €)"<?php echo ($infoManager['Man_Solde'] < $infoPieceDetachee['TypPi_PrixDemontage'])?" disabled":""?>>
</form>
<?php
		}
	}
	if(!empty($submitFormGestion)&& empty($infoVoiture['IdVoiture']) && !$PiDet_Casse)
	{
?>
<form action="../vente/gestion.php" method="post">
	<input type="hidden" name="IdManager" value="<?php echo $IdManager;?>">
	<input type="hidden" name="IdPieceDetachee" value="<?php echo $infoPieceDetachee['IdPieceDetachee'];?>">
	<input type="hidden" name="IdVente" value="<?php echo $infoPieceDetachee['IdVente'];?>">
	<input type="hidden" name="Ven_IdTypeVente" value="2">
	<input type="submit" value="<?php echo $submitFormGestion?>">
	<input type="hidden" name="action" value="<?php echo $actionFormGestion?>">
</form>
<?php
	}
	if(!empty($submitFormTraitement) && !$PiDet_Casse)
	{
?>
<form action="../vente/traitement.php" method="post"<?php echo ($confirmFormTraitement!="")?" onSubmit=\"return confirm('$confirmFormTraitement')\"":""?>>
	<input type="hidden" name="IdManager" value="<?php echo $IdManager;?>">
	<input type="hidden" name="IdVente" value="<?php echo $infoPieceDetachee['IdVente'];?>">
	<input type="submit" value="<?php echo $submitFormTraitement;?>">
	<input type="hidden" name="action" value="<?php echo $actionFormTraitement;?>">
	<input type="hidden" name="reponse" value="Oui">
</form>
<?php
	}
	if($Man_Niveau > 2)
	{
?>
<form action="traitement.php" method="get" onSubmit="return confirmSuppr(this)">
	<input type="hidden" name="reponse" value="Oui">
	<input type="hidden" name="IdPieceDetachee" value="<?php echo $infoPieceDetachee['IdPieceDetachee'];?>">
	<input type="submit" name="action" value="Supprimer">
</form>
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