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
	$Man_Niveau = $_SESSION['Jou_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<html>
<head>
	<title>UTR : <?php echo $_GET['action']?> une pièce détachée</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
  <script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdPieceDetachee)
		{
			document.location="gestion.php?action=Modifier&IdPieceDetachee="+IdPieceDetachee;
		}
		function verifForm(form)
		{
			with(form)
			{
				var chaineErreur = "";

				chaineErreur +=	is_Number(PiDet_Usure.value,"","L'usure de la pièce");
				chaineErreur +=	is_Number(PiDet_Qualite.value,"","La qualité de la pièce");

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
	if(isset($_GET["action"])&& !isset($_SESSION["Erreur"]))
	{

		if($_GET["action"]=="Ajouter")
		{
			$infoPieceDetachee="";

			$infoPieceDetachee['PiDet_Usure'] = 0;
			$infoPieceDetachee['PiDet_Qualite'] = 100;

			$IdModelePiece = $_GET['IdModelePiece'];
		}
		else
		{
			$IdPieceDetachee = $_GET["IdPieceDetachee"];

			$requeteInfoPieceDetachee="	SELECT 	IdPieceDetachee,
																PiDet_IdModele,
																PiDet_Usure,
																PiDet_UsureMesuree,
																PiDet_Qualite,
																PiDet_QualiteMesuree,
																UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(PiDet_DateFabrication) AS PiDet_Age,
																PiDet_IdManager
													FROM piece_detachee
													WHERE IdPieceDetachee = '$IdPieceDetachee'";
			$resultatInfoPieceDetachee=mysql_query($requeteInfoPieceDetachee)or die(mysql_error());
			$infoPieceDetachee=mysql_fetch_assoc($resultatInfoPieceDetachee);
			$PiDet_Usure = $infoPieceDetachee['PiDet_Usure'];
			$PiDet_Qualite = $infoPieceDetachee['PiDet_Qualite'];

			$IdModelePiece = $infoPieceDetachee['PiDet_IdModele'];
		}
	}
	else
	{
?>
Il y a une ou plusieurs erreurs dans le formulaire :
<BR />
  <?php
  		$IdModelePiece = $_GET['IdModelePiece'];

		$infoPieceDetachee = $_SESSION["Post"];

		foreach ($_SESSION["Codes"] as $codeErreur)
		{
			$requeteMessageErreur="	SELECT Err_Message
											FROM erreur
											WHERE IdErreur = $codeErreur";
			$resultatMessageErreur=mysql_query($requeteMessageErreur)or die(mysql_error());
			$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
			echo ($messageErreur["Err_Message"]);
?>
<BR />
  <?php
 		}
 		unset($_SESSION['Post']);
 		unset($_SESSION['Erreur']);
 		unset($_SESSION['Codes']);
	}
	$requeteInfoModelePiece ="	SELECT	ModPi_NomModele,
														ModPi_IdMarque,
														Marq_Libelle,
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
														ModPi_PrixNeuve
											FROM modele_piece, type_piece, marque
											WHERE IdTypePiece = ModPi_IdTypePiece
											AND IdMarque = ModPi_IdMarque
											AND IdModelePiece = '$IdModelePiece'";
		$resultatInfoModelePiece = mysql_query($requeteInfoModelePiece) or die ("Requête Info Modèle Pièce : ".mysql_error());
		$infoModelePiece = mysql_fetch_assoc($resultatInfoModelePiece);
?>
</div>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdPieceDetachee" value="<?php echo $infoPieceDetachee["IdPieceDetachee"]?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]?>">
<input type="hidden" name="verificationJs" value="false">
	<table border="1">
		<tr>
			<th colspan="3"><?php echo ($_GET['action']=="Ajouter")?"Ajouter une pièce":"Modifier la pièce n°".$infoPieceDetachee['IdPieceDetachee'];?></td>
		</tr>
		<tr>
			<th>Marque :</th>
			<td><?php echo $infoModelePiece['Marq_Libelle']?></td>
		</tr>
		<tr>
			<th>Modèle :</th>
			<td><input type="hidden" name="PiDet_IdModele" value="<?php echo $IdModelePiece?>"><?php echo $infoModelePiece['ModPi_NomModele']?></td>
		</tr>
		<tr>
			<th>Type :</th>
			<td><?php echo $infoModelePiece['TypPi_Libelle'];?></td>
		</tr>
		<tr>
			<th>Usure :</th>
			<td><input type="text" size="3" name="PiDet_Usure" value="<?php echo $infoPieceDetachee['PiDet_Usure'];?>"> %</td>
		</tr>
		<tr>
			<th>Qualité :</th>
			<td><input type="text" size="3" name="PiDet_Qualite" value="<?php echo $infoPieceDetachee['PiDet_Qualite'];?>"> %</td>
		</tr>
		<tr>
			<th>Manager :</th>
			<td><select name="PiDet_IdManager">
<?php
	$requeteManagers = "	SELECT IdManager, Man_Nom, Man_Niveau FROM manager ORDER BY Man_Niveau, Man_Nom";
	$resultatManagers = mysql_query($requeteManagers);
	while($infoManager = mysql_fetch_assoc($resultatManagers))
	{
?>
				<option value="<?php echo $infoManager['IdManager']?>"<?php echo ($infoManager['IdManager']==$infoPieceDetachee['PiDet_IdManager'])?" selected":"";?>>(Niv <?php echo $infoManager['Man_Niveau']?>) <?php echo $infoManager['Man_Nom']?></option>
<?php
	}
?>
			</select></td>
		</tr>
		<tr>
			<th><img alt="Poids" src="../../images/poids.gif"></th>
			<td><?php echo $infoModelePiece['ModPi_Poids'];?> kg</td>
		</tr>
		<tr>
			<th><img alt="Age" src="../../images/age.gif"></th>
			<td><?php echo round($infoPieceDetachee['PiDet_Age'] / 86400,0);?> jours</td>
		</tr>
		<tr>
			<th><img alt="Durée de vie Max" src="../../images/dureeviemax.gif"></th>
			<td><?php echo $infoModelePiece['ModPi_DureeVieMax']?></td>
		</tr>
		<tr>
			<th><img alt="Prix" src="../../images/prix.gif"></th>
			<td><?php echo $infoModelePiece['ModPi_PrixNeuve'];?> &euro;</td>
		</tr>
	</table>
<br>
	<table border="1" id="Modifier">
		<tr>
			<th>Caractéristiques</th>
			<th><img alt="Accélération" height="20" src="../../images/acc.gif"></th>
			<th><img alt="Vitesse Max" src="../../images/vmax.gif"></th>
			<th><img alt="Freinage" src="../../images/frein.gif"></th>
			<th><img alt="Turbo" src="../../images/turbo.gif"></th>
			<th><img alt="Adhérence" src="../../images/adh.gif"></th>
			<th><img alt="Solidité Moteur" src="../../images/solmot.gif"></th>
			<th><img alt="Aspect Extérieur" src="../../images/aspect.gif"></th>
			<th><img alt="Capacité Moteur" src="../../images/capa.gif"></th>
			<th><img alt="Capacité Max" src="../../images/capamax.gif"></th>

		</tr>
		<tr>
			<th>Données standard</th>
			<td><?php echo $infoModelePiece['ModPi_Acceleration'];?></td>
			<td><?php echo $infoModelePiece['ModPi_VitesseMax'];?></td>
			<td><?php echo $infoModelePiece['ModPi_Freinage'];?></td>
			<td><?php echo $infoModelePiece['ModPi_Turbo'];?></td>
			<td><?php echo $infoModelePiece['ModPi_Adherence'];?></td>
			<td><?php echo $infoModelePiece['ModPi_SoliditeMoteur'];?></td>
			<td><?php echo $infoModelePiece['ModPi_AspectExterieur'];?></td>
			<td><?php echo $infoModelePiece['ModPi_CapaciteMoteur'];?></td>
			<td><?php echo $infoModelePiece['ModPi_CapaciteMax'];?></td>

		</tr>
	</table>
	<table>
		<tr>
			<td align="center" colspan="3"><br>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]?>"><br>
			</td>
			<td align="center" colspan="3"><br>
				<input type="reset" value="<?php echo($_GET['action']=="Ajouter")?"Effacer saisie":"Annuler les modifications";?>"><br>
			</td>
		</tr>
	</table>
</div>
</form>
<div align="center"><font color="#FF0000">*</font> : Champ obligatoire
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
