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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>Moteur du jeu</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
</head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="3">
<?php
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td width="22%" valign="top">
<?php
	include("../../frame/menu.php");
?>
		</td>
    	<td width="78%">
<?php
	$requeteDatesActivations = "	SELECT 	UNIX_TIMESTAMP(UTR_DateTraitementCourse) AS UTR_DateTraitementCourse,
														UNIX_TIMESTAMP(UTR_DateDernierSalaire) AS UTR_DateDernierSalaire,
														UNIX_TIMESTAMP(UTR_DateCreationPiece) AS UTR_DateCreationPiece,
														UNIX_TIMESTAMP(UTR_DateCreationVoiture) AS UTR_DateCreationVoiture
											FROM utr";
	$resultatDatesActivation = mysql_query($requeteDatesActivations)or die(mysql_error());
	$dateActivation = mysql_fetch_assoc($resultatDatesActivation);

	if($_GET['ok'] == 1) echo "Tout s'est bien passé !<br><br>";
?>
<table border="1">
<form name="formulaire" action="traitement.php" method="POST">
<tr>
	<th colspan="3">Choisir les options</th>
</tr>
<tr>
	<td><input type="checkbox" id="checkbox1" name="UTR_TraiterCourse" checked></td>
	<td><label for="checkbox1">Traiter les courses</label></td>
	<td>Dernière activation : <?php echo date("\l\e j/m/Y à H:i",$dateActivation['UTR_DateTraitementCourse'])?></td>
</tr>
<tr>
	<td><input type="checkbox" id="checkbox2" name="UTR_VerserSalaire" checked></td>
	<td><label for="checkbox2">Verser les salaires</label></td>
	<td>Dernière activation : <?php echo date("\l\e j/m/Y à H:i",$dateActivation['UTR_DateDernierSalaire'])?></td>
</tr>
<tr>
	<td><input type="checkbox" id="checkbox3" name="UTR_CreerPieces" checked></td>
	<td><label for="checkbox3">Créer des pièces</label></td>
	<td>Dernière activation : <?php echo date("\l\e j/m/Y à H:i",$dateActivation['UTR_DateCreationPiece'])?></td>
</tr>
<tr>
	<td><input type="checkbox" id="checkbox4" name="UTR_CreerVoitures" checked></td>
	<td><label for="checkbox4">Créer des voitures</label></td>
	<td>Dernière activation : <?php echo date("\l\e j/m/Y à H:i",$dateActivation['UTR_DateCreationVoiture'])?></td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Lancer la machine !"></td>
</tr>
</form>
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
