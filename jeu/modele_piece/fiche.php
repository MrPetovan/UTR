<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdManager']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<html>
<head>
	<title>UTR : Fiche d'un modele de pièce</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script language="JavaScript">
		function confirmSuppr(form)
		{
			var action=form.action.value;
			//if(action=="Supprimer") confirmation = "Etes-vous sûr de vouloir supprimer ce modele_piece ?";
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
		<td>
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td>
<?php
	if(isset($_GET['IdModelePiece']))
	{
		$IdModelePiece = $_GET['IdModelePiece'];

		$requeteInfoModelePiece="	SELECT	IdModelePiece,
														ModPi_IdMarque,
														Marq_Libelle,
														ModPi_NomModele,
														ModPi_Commentaires,
														ModPi_IdTypePiece,
														TypPi_Libelle,
														ModPi_Niveau,
														ModPi_Acceleration,
														ModPi_VitesseMax,
														ModPi_Freinage,
														ModPi_Turbo,
														ModPi_Adherence,
														ModPi_SoliditeMoteur,
														ModPi_AspectExterieur,
														ModPi_CapaciteMoteur,
														ModPi_CapaciteMax,
														ModPi_TypeCarburant,
														ModPi_Poids,
														ModPi_DureeVieMax,
														ModPi_PrixNeuve
										FROM modele_piece, marque, type_piece
										WHERE IdMarque = ModPi_IdMarque
										AND IdTypePiece = ModPi_IdTypePiece
										AND IdModelePiece ='$IdModelePiece'";
		$resultatInfoModelePiece=mysql_query($requeteInfoModelePiece) or die(mysql_error());
		$infoModelePiece=mysql_fetch_assoc($resultatInfoModelePiece);
?>
<a href="liste.php?IdTypePiece=<?php echo $infoModelePiece['ModPi_IdTypePiece']?>"><< Revenir à la liste</a>
<br>
<br>
<div align="center">
<table border="0" width="80%">
<tr><td>

	<table border="1">
		<tr>
			<th colspan="2">Fiche du Modèle <?php echo $infoModelePiece['ModPi_NomModele']?></th>
		</tr>
		<tr>
			<th>Marque :</th>
			<td><?php echo $infoModelePiece['Marq_Libelle']?></td>
		</tr>
		<tr>
			<th>Type de Piece :</th>
			<td><?php echo $infoModelePiece['TypPi_Libelle']?></td>
		</tr>
		<tr>
			<th>Niveau :</th>
			<td><?php echo $infoModelePiece['ModPi_Niveau']?></td>
		</tr>
		<tr>
			<th>Type Carburant :</th>
			<td><?php echo $infoModelePiece['ModPi_TypeCarburant']?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo ($infoModelePiece['ModPi_Commentaires']=="")?"Pas de commentaire":nl2br($infoModelePiece['ModPi_Commentaires']);?></td>
		</tr>
	</table>
	<br>
	<table border="1">
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
			<th><img alt="Poids" src="../../images/poids.gif"></th>
			<th><img alt="Durée de vie Max" src="../../images/dureeviemax.gif"></th>
			<th><img alt="Prix Neuve" src="../../images/prix.gif"></th>
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
			<td><?php echo $infoModelePiece['ModPi_Poids'];?> kg</td>
			<td><?php echo $infoModelePiece['ModPi_DureeVieMax'];?></td>
			<td><?php echo $infoModelePiece['ModPi_PrixNeuve'];?> &euro;</td>
		</tr>
	</table>
</td>
<td>
	<table border="1">
		<tr><th>Actions possibles</th></tr>
		<tr>
			<td>
<form action="gestion.php" method="get">
	<input type="hidden" name="IdModelePiece" value="<?php echo $infoModelePiece['IdModelePiece'];?>">
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
