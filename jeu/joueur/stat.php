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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>UTR : Statistiques</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2">
<?php
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td width="19%" valign="top">
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td width="81%" valign="top" align="center">

<?php
	if($Man_Niveau == 1)
	{
		$requeteInfoJoueur = "	SELECT 	Man_Nom,
													Man_Sexe,
													Man_Niveau,
													Man_Solde,
													Job_NomMasculin,
													Job_NomFéminin,
													Job_Salaire,
													Pil_Nom,
													Pil_Age,
													Pil_Reputation,
													Pil_Style,
													Pil_Chance,
													Pil_XPShifts,
													Pil_XPFreinage,
													Pil_XPVirage,
													Pil_XPSpe,
													Marq_Libelle,
													IdVoiture,
													ModVoi_NomModele,
													ModVoi_TypeCarburant,
													ModVoi_PrixNeuve
								FROM pilote, manager, job
								LEFT JOIN voiture ON Voit_IdManager = IdManager
								LEFT JOIN modele_voiture ON IdModeleVoiture = Voit_IdModele
								LEFT JOIN marque ON IdMarque = ModVoi_IdMarque
								WHERE Pil_IdManager ='$IdManager'
								AND IdManager = Pil_IdManager
								AND Man_IdJob = IdJob";
		$resultatInfoJoueur = mysql_query($requeteInfoJoueur)or die(mysql_error());

		$infoJoueur = mysql_fetch_assoc($resultatInfoJoueur);
?>
<br>
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="4" class="titre">Informations générales</td>
	</tr>
	<tr class="piece">
		<th>Nom du manager/pilote</th>
		<td><?php echo $infoJoueur['Man_Nom']?></td>
		<th>Solde</th>
		<td><?php echo $infoJoueur['Man_Solde']?> &euro;</td>
	</tr>
	<tr class="piece">
		<th>Job</th>
		<td><?php echo $infoJoueur['Job_Nom'.$infoJoueur['Man_Sexe']]?>&nbsp;|&nbsp;<a href="../job/changerJob.php">Changer</a></div></td>
		<th>Salaire</th>
		<td><?php echo $infoJoueur['Job_Salaire']?> &euro;</td>
	</tr>
</table>
<br />

<table border="0">
<tr><td>

<table border=0 class="liste">
	<tr>
		<th colspan="3" class="titre">Statistiques du Pilote</td>
	</tr>
	<tr class="piece">
		<th>Age</th>
		<td colspan="2"><?php echo $infoJoueur['Pil_Age'];?> ans</td>
	</tr>
	<tr class="piece">
		<th>Réputation</th>
		<td colspan="2"><?php echo $infoJoueur['Pil_Reputation'];?></td>
	</tr>
<?php
			if($Man_Niveau > 2)
			{
?>
	<tr class="piece">
		<th>Style</th>
		<td colspan="2"><div align="center"><?php echo $infoJoueur['Pil_Style'];?></div></td>
	</tr>
	<tr class="piece">
		<th>Chance</th>
		<td colspan="2"><div align="center"><?php echo $infoJoueur['Pil_Chance'];?></div></td>
	</tr>
<?php
			}
?>
	<tr>
		<th class="titre" colspan="3">Compétences</th>
	</tr>
	<tr class="piece">
		<th></th>
		<th class="titre">Niveau</th>
		<th class="titre">XP</th>
	</tr>
	<tr class="piece">
		<th>Shifts</th>
		<td><?php echo niveauAdd($infoJoueur['Pil_XPShifts'],1000);?></td>
		<td><?php echo $infoJoueur['Pil_XPShifts'];?> pts</td>
	</tr>
	<tr class="piece">
		<th>Freinage</th>
		<td><?php echo niveauAdd($infoJoueur['Pil_XPFreinage'],1000);?></td>
		<td><?php echo $infoJoueur['Pil_XPFreinage'];?> pts</td>
	</tr>
	<tr class="piece">
		<th>Virage</th>
		<td><?php echo niveauAdd($infoJoueur['Pil_XPVirage'],1000);?></td>
		<td><?php echo $infoJoueur['Pil_XPVirage'];?> pts</td>
	</tr>
	<tr class="piece">
		<th>Spécial</th>
		<td><?php echo niveauAdd($infoJoueur['Pil_XPSpe'],1000);?></td>
		<td><?php echo $infoJoueur['Pil_XPSpe'];?> pts</td>
	</tr>
	<tr class="piece">
		<th>Général</th>
		<td><?php echo 	niveauAdd($infoJoueur['Pil_XPShifts'],1000)+
					niveauAdd($infoJoueur['Pil_XPFreinage'],1000)+
					niveauAdd($infoJoueur['Pil_XPVirage'],1000)+
					niveauAdd($infoJoueur['Pil_XPSpe'],1000);?>
		</td>
		<td><?php echo 	$infoJoueur['Pil_XPShifts']+
					$infoJoueur['Pil_XPFreinage']+
					$infoJoueur['Pil_XPVirage']+
					$infoJoueur['Pil_XPSpe'];?> pts
		</td>
	</tr>
</table>

</td><td>

<?php
		$requeteInfoVoiture = "	SELECT 	IdVoiture,
													ModVoi_IdMarque,
													Marq_Libelle,
													ModVoi_NomModele,
													ModVoi_PrixNeuve,
													ModVoi_PoidsCarrosserie,
													ModVoi_TypeCarburant,
													Voit_IdManager
										FROM voiture, modele_voiture, marque
										WHERE IdModeleVoiture = Voit_IdModele
										AND IdMarque = ModVoi_IdMarque
										AND IdVoiture = '".$infoJoueur['IdVoiture']."'
										GROUP BY IdVoiture";
		$resultatInfoVoiture=mysql_query($requeteInfoVoiture) or die(mysql_error());
		$infoVoiture=mysql_fetch_assoc($resultatInfoVoiture);

		$infoJoueur['Voit_Acceleration'] = 0;
		$infoJoueur['Voit_VitesseMax'] = 0;
		$infoJoueur['Voit_Freinage'] = 0;
		$infoJoueur['Voit_Turbo'] = 0;
		$infoJoueur['Voit_Adherence'] = 0;
		$infoJoueur['Voit_SoliditeMoteur'] = 0;
		$infoJoueur['Voit_AspectExterieur'] = 0;
		$infoJoueur['Voit_CapaciteMoteur'] = 0;
		$infoJoueur['Voit_CapaciteMax'] = 0;
		$infoJoueur['Voit_Poids'] = 0;
		$infoJoueur['Voit_PrixNeuve'] = 0;

		$requeteTypesPiece = "SELECT IdTypePiece, TypPi_Libelle, TypPi_Obligatoire FROM type_piece ORDER BY TypPi_Libelle";
		$resultatTypesPiece = mysql_query($requeteTypesPiece) or die(mysql_error());

		while($typePiece=mysql_fetch_assoc($resultatTypesPiece))
		{
			$IdTypePiece=$typePiece['IdTypePiece'];
			$TypPi_Libelle = $typePiece['TypPi_Libelle'];
			$TypPi_Obligatoire = $typePiece['TypPi_Obligatoire'];

			$requeteInfoPieceInstallee = "	SELECT	IdPieceDetachee,
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
																	PiDet_Qualite,
																	PiDet_QualiteMesuree,
																	PiDet_DateFabrication,
																	ModPi_PrixNeuve
														FROM piece_detachee, modele_piece, voiture, marque, type_piece
														WHERE IdModelePiece = PiDet_IdModele
														AND IdMarque = ModPi_IdMarque
														AND Voit_Id".str_replace(" ","",$TypPi_Libelle)." = IdPieceDetachee
														AND ModPi_IdTypePiece = '$IdTypePiece'
														AND IdTypePiece = ModPi_IdTypePiece
														AND IdVoiture = '".$infoJoueur['IdVoiture']."'";
			$infoPieceInstallee = mysql_fetch_assoc(mysql_query($requeteInfoPieceInstallee));
			if(mysql_error()){ echo mysql_error();exit;}
/*			echo"<pre>infoPieceInstallee[$TypPi_Libelle] Qualite".$infoPieceInstallee['PiDet_Qualite']."<br>";
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
/*			echo"<br>infoVoiture<br>";
			print_r($infoVoiture);
			echo"</pre>";*/
		}
?>
<table border=0 class="liste">
	<tr>
		<th colspan="4" class="titre">La voiture</th>
	</tr>
	<tr class="piece">
		<th width="151">Marque</th>
		<td width="67"><?php echo $infoJoueur['Marq_Libelle'];?></td>
	</tr>
	<tr class="piece">
		<th>Modèle</th>
		<td><?php echo $infoJoueur['ModVoi_NomModele'];?></td>
	</tr>
	<tr class="piece">
		<th>Prix Neuve</th>
		<td><?php echo $infoJoueur['ModVoi_PrixNeuve'];?> &euro;</td>
	</tr>
	<tr class="piece">
		<th>Type Carburant</th>
		<td><?php echo $infoJoueur['ModVoi_TypeCarburant'];?></td>
	</tr>
<?php
			if($Man_Niveau > 2)
			{
?>
	<tr>
		<th colspan="2" class="titre">Caractéristiques</th>
	</tr>
	<tr class="piece">
		<th>Accélération</th>
		<td><?php echo $infoVoiture['Voit_Acceleration'];?></td>
	</tr>
	<tr class="piece">
		<th>Vitesse Maximum</th>
		<td><?php echo $infoVoiture['Voit_VitesseMax'];?></td>
	</tr>
	<tr class="piece">
		<th>Freinage</th>
		<td><?php echo $infoVoiture['Voit_Freinage'];?></td>
	</tr>
	<tr class="piece">
		<th>Turbo</th>
		<td><?php echo $infoVoiture['Voit_Turbo'];?></td>
	</tr>
	<tr class="piece">
		<th>Adhérence</th>
		<td><?php echo $infoVoiture['Voit_Adherence'];?></td>
	</tr>
	<tr class="piece">
		<th>Solidité Moteur</th>
		<td><?php echo $infoVoiture['Voit_SoliditeMoteur'];?></td>
	</tr>
<?php
			}
?>
	<tr class="piece">
		<th>Aspect Extérieur</th>
		<td><?php echo $infoVoiture['Voit_AspectExterieur'];?></td>
	</tr>
	<tr class="piece">
		<th>Capacité Moteur</th>
		<td><?php echo $infoVoiture['Voit_CapaciteMoteur'];?></td>
	</tr>
	<tr class="piece">
		<th>Capacité Maximum</th>
		<td><?php echo $infoVoiture['Voit_CapaciteMax'];?></td>
	</tr>
	<tr class="piece">
		<th>Poids</th>
		<td><?php echo $infoVoiture['Voit_Poids'];?></td>
	</tr>
</table>

</td></tr></table>

<?php
		}
		else
		{
			$requeteInfoJoueur = " 	SELECT Man_Nom, Man_Solde, Man_Reputation, Marq_Libelle, Spon_Salaire, COUNT(IdVoiture) AS Man_NbVoiture, COUNT(IdPilote) AS Man_NbPilote
											FROM manager
											INNER JOIN sponsor ON IdSponsor = Man_IdSponsor
											INNER JOIN marque ON IdMarque = Spon_IdMarque
											LEFT JOIN voiture ON Voit_IdManager = IdManager
											LEFT JOIN pilote ON Pil_IdManager = IdManager
											WHERE IdManager = '$IdManager'
											GROUP BY IdManager";
			$resultatInfoJoueur = mysql_query($requeteInfoJoueur) or die("Requete Info Joueur niv2$requeteInfoJoueur".mysql_error());
			$infoJoueur = mysql_fetch_assoc($resultatInfoJoueur);
?>
<br>
<table border="0" class="liste">
	<tr class="piece">
		<th class="titre" colspan="5">Informations générales</th>
	</tr>
	<tr class="piece">
		<th>Nom du manager</th>
		<td><?php echo $infoJoueur['Man_Nom'];?></td>
		<th>Solde</th>
		<td colspan="2"><?php echo $infoJoueur['Man_Solde'];?> &euro;</td>
	</tr>
	<tr class="piece">
		<th colspan="2">Nombre de voitures possédées</th>
		<td colspan="3"><?php echo $infoJoueur['Man_NbVoiture']?></td>
	</tr>
	<tr class="piece">
		<th colspan="2">Nombre de pilotes engagés</th>
		<td colspan="3"><?php echo $infoJoueur['Man_NbPilote']?></td>
	</tr>
	<tr class="piece">
		<th>Sponsor</th>
		<td colspan="2"><?php echo $infoJoueur['Marq_Libelle']?>&nbsp;|&nbsp;<a href="../sponsor/changerSponsor.php">Changer</a></td>
		<th>Salaire</th>
		<td><?php echo $infoJoueur['Spon_Salaire']?> &euro;</td>
	</tr>
</table>
<?php
	}
?>
<br />
<div class="actions">
<br/>Classement général
<br />
<br />
	<table>
		<tr class="piece">
			<td colspan="2">Managers</td>
			<td colspan="1">Pilotes</td>
		</tr>
		<tr>
			<td valign="top">
<?php
	$requeteTop10ManagersReput = "	SELECT IdManager, Man_Nom, Man_Reputation
										FROM manager
										WHERE Man_Niveau < 3
										ORDER BY Man_Reputation DESC
										LIMIT 0,10";
	$resultatTop10ManagersReput = mysql_query($requeteTop10ManagersReput) or die("Requête Top 10 Managers Réputation".mysql_error());
?>
				<table border="0" class="liste">
					<tr>
						<th colspan="3" class="titre">Top 10 Réputation</th>
					</tr>
					<tr>
						<th class="titre">#</th>
						<th class="titre">Nom</th>
						<th class="titre">Réputation</th>
					</tr>
<?php
	$i = 1;
	while($infoManager = mysql_fetch_assoc($resultatTop10ManagersReput))
	{
?>
					<tr class="piece">
						<td><?php echo $i?></td>
						<td><?php echo $infoManager['Man_Nom']?></td>
						<td><?php echo $infoManager['Man_Reputation']?></td>
					</tr>
<?php
		$i++;
	}
?>
				</table>
			</td>
			<td valign="top">
<?php
	$requeteTop10ManagersSolde = "	SELECT IdManager, Man_Nom, Man_Solde
										FROM manager
										WHERE Man_Niveau < 3
										ORDER BY Man_Solde DESC
										LIMIT 0,10";
	$resultatTop10ManagersSolde = mysql_query($requeteTop10ManagersSolde) or die("Requête Top 10 Managers Solde".mysql_error());
?>
				<table border="0" class="liste">
					<tr>
						<th colspan="3" class="titre">Top 10 Solde</th>
					</tr>
					<tr>
						<th class="titre">#</th>
						<th class="titre">Nom</th>
						<th class="titre">Solde</th>
					</tr>
<?php
	$i = 1;
	while($infoManager = mysql_fetch_assoc($resultatTop10ManagersSolde))
	{
?>
					<tr class="piece">
						<td><?php echo $i?></td>
						<td><?php echo $infoManager['Man_Nom']?></td>
						<td><?php echo $infoManager['Man_Solde']?> &euro;</td>
					</tr>
<?php
		$i++;
	}
?>
				</table>
			</td>
			<td valign="top">
<?php
	$requeteTop10PilotesReput = "	SELECT IdPilote, Pil_Nom, Pil_Reputation
											FROM pilote
											ORDER BY Pil_Reputation DESC
											LIMIT 0,10";
	$resultatTop10PilotesReput = mysql_query($requeteTop10PilotesReput) or die("Requête Top 10 Pilotes Réputation".mysql_error());
?>
				<table border="0" class="liste">
					<tr>
						<th colspan="3" class="titre">Top 10 Réputation</th>
					</tr>
					<tr>
						<th class="titre">#</th>
						<th class="titre">Nom</th>
						<th class="titre">Réputation</th>
					</tr>
<?php
	$i = 1;
	while($infoPilote = mysql_fetch_assoc($resultatTop10PilotesReput))
	{
?>
					<tr class="piece">
						<td><?php echo $i?></td>
						<td><?php echo $infoPilote['Pil_Nom']?></td>
						<td><?php echo $infoPilote['Pil_Reputation']?></td>
					</tr>
<?php
		$i++;
	}
?>
				</table>
			</td>
			<td valign="top">
<?php
/*
	$requeteTop10PilotesSolde = "	SELECT IdPilote, Pil_Nom, Pil_Solde
										FROM pilote
										ORDER BY Pil_Solde DESC
										LIMIT 0,10";
	$resultatTop10PilotesSolde = mysql_query($requeteTop10PilotesSolde) or die("Requête Top 10 Pilotes Solde".mysql_error());
?>
				<table border="0" class="liste">
					<tr>
						<th colspan="3" class="titre">Top 10 Réputation</th>
					</tr>
					<tr>
						<th class="titre">#</th>
						<th class="titre">Nom</th>
						<th class="titre">Solde</th>
					</tr>
<?php
	$i = 1;
	while($infoPilote = mysql_fetch_assoc($resultatTop10PilotesSolde))
	{
?>
					<tr class="piece">
						<td><?php echo $i?></td>
						<td><?php echo $infoPilote['Pil_Nom']?></td>
						<td><?php echo $infoPilote['Pil_Solde']?> &euro;</td>
					</tr>
<?php
		$i++;
	}*/
?>
				</table>
			</td>
		</tr>
	</table>
</div>
                  <p>&nbsp;</p></td>
              </tr>
              <tr>
                <td valign="top">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            <p>&nbsp;</p></td>
        </tr>
      </table>

    </td>
	</tr>
	<tr>

    <td colspan="2"> <div align="center">
        <?php
	include("../../frame/piedpage.php");
?>
      </div></td>
	</tr>
</table>
</body>
</html>
