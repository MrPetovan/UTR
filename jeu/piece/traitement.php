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

/*	echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";
	exit;
*/
	include('../../include/verif.php');
	if (isset($_GET["action"]))
	{
		if($_GET["action"]=="Supprimer")
		{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Confirmation de suppression</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
<!--	<link rel=stylesheet type="text/css" href="../style/style.css">-->
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
		<form method="POST" action="traitement.php">
			<input type="hidden" name="IdNews" value="<?php echo $_GET["IdNews"]; ?>">
			<input type="hidden" name="action" value="Supprimer">
			<table width="75%" border="1" align="center">
				<tr>
					<td><img src="../images/warningpetit.gif">
					</td>
					<td></td>
					<td align="center">
<?php
				$codeErreur="101";
				$requeteMessageErreur="	SELECT MsgEr_Message
										FROM message_erreur
										WHERE MsgEr_Code = $codeErreur";
				$resultatMessageErreur=mysql_query($requeteMessageErreur);
			echo mysql_error();
				$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
				echo ($messageErreur["MsgEr_Message"]." \n");
?>
					</td>
				</tr>
			</table>
<br>
			<table width="75%" border="1" align="center">
				<tr>
					<td align="center"><input type="submit" name="reponse" value="Oui"></td>
					<td align="center"><input type="submit" value="Non"></td>
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

<?php
		}
	}
	if (isset($_POST["action"]))
	{
		$IdPieceDetachee = $_POST["IdPieceDetachee"];
		$PiDet_IdModele = $_POST['PiDet_IdModele'];
		$PiDet_Usure = $_POST['PiDet_Usure'];
		$PiDet_Qualite = $_POST['PiDet_Qualite'];
		$PiDet_DateFabrication = $_POST['PiDet_DateFabrication'];
		$PiDet_IdManager = $_POST['PiDet_IdManager'];

		$verificationJs=$_POST['verificationJs'];

//requête supprimant une news
		if($_POST["action"]=="Supprimer")
		{
			if($_POST["reponse"]=="Oui")
			{
				$requeteSupprimerPiece = "	DELETE FROM piece_detachee
													WHERE IdPieceDetachee=".$IdPieceDetachee;
				mysql_query($requeteSupprimerPiece);

				header("Location: liste.php");
				exit;
			}
			else
			{
				header("Location: fiche.php?IdPieceDetachee=".$IdPieceDetachee);
				exit;
			}
		}

		if($_POST['action']=="Retirer")
		{
			$TypPi_PrixDemontage = $_POST['TypPi_PrixDemontage'];
			$TypPi_Libelle = $_POST['TypPi_Libelle'];
			$IdVoiture = $_POST['IdVoiture'];
			$requeteRetirerPiece= "	UPDATE voiture
											SET Voit_Id".$TypPi_Libelle."=NULL
											WHERE IdVoiture = '$IdVoiture'";
			mysql_query($requeteRetirerPiece) or die(mysql_error());

			$requeteMAJSolde= "	UPDATE manager
										SET Man_Solde = Man_Solde - $TypPi_PrixDemontage
										WHERE IdManager = '$IdManager'";
			mysql_query($requeteMAJSolde) or die(mysql_error());

			header("Location: fiche.php?IdPieceDetachee=$IdPieceDetachee");
			exit;
		}
		if($_POST['action']=="Estimer")
		{
			$TypPi_PrixEstimation = $_POST['TypPi_PrixEstimation'];
			$competenceMecano = 90;
			$competenceMecano = 100 - $competenceMecano;
			$requeteCaracPiece = "	SELECT PiDet_Qualite, PiDet_Usure
											FROM piece_detachee
											WHERE IdPieceDetachee = '$IdPieceDetachee'";
			$resultatCaracPiece = mysql_query($requeteCaracPiece)or die(mysql_error());
			$caracPiece = mysql_fetch_assoc($resultatCaracPiece);

			if($caracPiece['PiDet_Qualite']<=(100-$competenceMecano/2))$qualiteEstimee = $caracPiece['PiDet_Qualite'] +(rand((-$competenceMecano/2)*10,($competenceMecano/2)*10))/10;
			else $qualiteEstimee = rand(($caracPiece['PiDet_Qualite']-$competenceMecano/2)*10,1000)/10;

			if($caracPiece['PiDet_Usure']>$competenceMecano/2)$usureEstimee = $caracPiece['PiDet_Usure'] + (rand((-$competenceMecano/2)*10,($competenceMecano/2)*10))/10;
			else $usureEstimee = rand(0,($caracPiece['PiDet_Usure']+$competenceMecano/2)*10)/10;

			$requeteMAJPiece= "	UPDATE piece_detachee
										SET	PiDet_QualiteMesuree = '$qualiteEstimee',
												PiDet_UsureMesuree = '$usureEstimee'
										WHERE IdPieceDetachee = '$IdPieceDetachee'";
			mysql_query($requeteMAJPiece) or die(mysql_error());

			$requeteMAJSolde= "	UPDATE manager
										SET Man_Solde = Man_Solde - $TypPi_PrixEstimation
										WHERE IdManager = '$IdManager'";
			mysql_query($requeteMAJSolde) or die(mysql_error());

			header("Location: fiche.php?IdPieceDetachee=$IdPieceDetachee");
			exit;
		}

		if($_POST['action']=="Reparer")
		{
			$TypPi_PrixReparation = $_POST['TypPi_PrixReparation'];
			$competenceMecano = 90;
			$competenceMecano = 100 - $competenceMecano;
			$requeteCaracPiece = "	SELECT PiDet_UsureMesuree, PiDet_Usure
											FROM piece_detachee
											WHERE IdPieceDetachee = '$IdPieceDetachee'";
			$resultatCaracPiece = mysql_query($requeteCaracPiece)or die(mysql_error());
			$caracPiece = mysql_fetch_assoc($resultatCaracPiece);

			$UsureFinale = ($caracPiece['PiDet_Usure']*$competenceMecano/100);

			if($UsureFinale > $competenceMecano/2)$usureEstimee = $UsureFinale + (rand((-$competenceMecano/2)*10,($competenceMecano/2)*10))/10;
			else $usureEstimee = rand(0,($UsureFinale+$competenceMecano/2)*10)/10;

			$requeteMAJPiece= "	UPDATE piece_detachee
										SET	PiDet_Usure = '$UsureFinale',
												PiDet_UsureMesuree = '$usureEstimee'
										WHERE IdPieceDetachee = '$IdPieceDetachee'";
			mysql_query($requeteMAJPiece) or die(mysql_error());

			$requeteMAJSolde= "	UPDATE manager
										SET Man_Solde = Man_Solde - '$TypPi_PrixReparation'
										WHERE IdManager = '$IdManager'";
			mysql_query($requeteMAJSolde) or die(mysql_error());

			header("Location: fiche.php?IdPieceDetachee=$IdPieceDetachee");
			exit;
		}

		//Gestion des erreurs de saisie si la vérification n'a pas été faite en JavaScript
		if($verificationJs == "false");
		{
			$codesErreur=",";

			$codesErreur .= is_Number($PiDet_Usure,"","102").",";
			$codesErreur .= is_Number($PiDet_Qualite,"","103").",";

			$codesErreur = preg_replace("/,{2,}/",",",$codesErreur);
			$codesErreur = substr($codesErreur,1,-1);
		}
		if($codesErreur != "")
		{
			$_SESSION["Post"] = $_POST;
			$_SESSION["Erreur"] = 1;

			$codesErreur=explode(",",$codesErreur);
			$_SESSION["Codes"]=$codesErreur;
			$action=$_POST["action"];


			header("Location: gestion.php?IdPieceDetachee=".$IdPiece."&action=".$action."&IdModelePiece=".$PiDet_IdModele);
			exit();
		}

		if($_POST["action"]=="Ajouter")
		{
			$requeteAjouterPiece="	INSERT INTO piece_detachee( 	PiDet_IdModele,
																					PiDet_Usure,
																					PiDet_UsureMesuree,
																					PiDet_Qualite,
																					PiDet_QualiteMesuree,
																					PiDet_DateFabrication,
																					PiDet_IdManager)
											VALUES(	'$PiDet_IdModele',
														'$PiDet_Usure',
														NULL,
														'$PiDet_Qualite',
														NULL,
														NOW(),
														'$PiDet_IdManager')";
			mysql_query($requeteAjouterPiece)or die(mysql_error());

			header("Location: ../modele_piece/liste.php");
			exit;
		}
		if($_POST["action"]=="Modifier")
		{
			$requeteModifierPiece="	UPDATE piece_detachee
											SET	PiDet_IdModele = '$PiDet_IdModele',
													PiDet_Usure = '$PiDet_Usure',
													PiDet_UsureMesuree = NULL,
													PiDet_Qualite = '$PiDet_Qualite',
													PiDet_QualiteMesuree = NULL,
													PiDet_IdManager = '$PiDet_IdManager'
											WHERE IdPieceDetachee='$IdPieceDetachee'";
			mysql_query($requeteModifierPiece)or die(mysql_error());

			header("Location: ../modele_piece/liste.php");
			exit;
		}
	}
?>