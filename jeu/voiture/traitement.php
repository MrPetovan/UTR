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

	include('../../include/verif.php');
	include('../../include/fonctions.php');

	/*echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";
	exit;*/

/*	if (isset($_GET["action"]))
	{
		if($_GET["action"]=="Supprimer")
		{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Supprimer une voiture</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<base target="cadreDroit">
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
	include("../frame/menu.php");
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
				$resultatMessageErreur=mysql_query($requeteMessageErreur)or die(mysql_error());
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
</table>
</body>
</html>
<?php
		}
	}*/
	if (isset($_POST["action"]))
	{
		$IdVoiture = $_POST["IdVoiture"];
		$Voit_IdModele = $_POST['Voit_IdModele'];
		$IdInjection = $_POST['IdInjection'];
		$IdTurbo = $_POST['IdTurbo'];
		$IdRefroidissement = $_POST['IdRefroidissement'];
		$IdBlocMoteur = $_POST['IdBlocMoteur'];
		$IdTransmission = $_POST['IdTransmission'];
		$IdEchappement = $_POST['IdEchappement'];
		$IdJantes = $_POST['IdJantes'];
		$IdPneus = $_POST['IdPneus'];
		$IdFreins = $_POST['IdFreins'];
		$IdAmortisseurs = $_POST['IdAmortisseurs'];
		$IdCarrosserie = $_POST['IdCarrosserie'];
		$IdSpoiler = $_POST['IdSpoiler'];
		$IdOptiques = $_POST['IdOptiques'];
		$IdAileron = $_POST['IdAileron'];
		$IdChassis = $_POST['IdChassis'];
		$IdPucedeContrôle = $_POST['IdPucedeContrôle'];
		$IdNOS = $_POST['IdNOS'];
		$IdNéons = $_POST['IdNéons'];
		$IdSono = $_POST['IdSono'];

		$IdModeleVoiture = $_POST['IdModeleVoiture'];
		$Voit_IdManager = $_POST['Voit_IdManager'];

		$IdPieceChangement = $_POST['IdPieceChangement'];
		$IdTypePiece = $_POST['IdTypePiece'];
		$PrixTotal = $_POST['PrixTotal'];

		$verificationJs=$_POST['verificationJs'];

//requête supprimant une voiture
		if($_POST["action"]=="Supprimer")
		{
			if($_POST["reponse"]=="Oui")
			{
				$requeteSupprimerVoiture = "	DELETE FROM voiture
											WHERE IdVoiture=".$IdVoiture;
				mysql_query($requeteSupprimerVoiture);

				header("Location: liste.php");
				exit;
			}
			else
			{
				header("Location: fiche.php?IdVoiture=".$IdVoiture);
				exit;
			}
		}
		if($_POST["action"]=="Ajouter")
		{
			creerVoiture($Voit_IdModele,$IdManager);

			header("Location: ../modele_voiture/liste.php");
			exit;
		}
		if($_POST["action"]=="Acheter")
		{
			if($_POST["reponse"]=="Oui")
			{
				$requeteAcheterVoiture = "	UPDATE voiture
													SET Voit_IdManager = '$IdManager'
													WHERE IdVoiture = '$IdVoiture'";
				mysql_query($requeteAcheterVoiture);

				header("Location: liste.php");
				exit;
			}
			else
			{
				header("Location: fiche.php?IdVoiture=".$IdVoiture);
				exit;
			}
		}
		if($_POST["action"]=="Changer")
		{
			if($IdPieceChangement != "")
			{
				$requeteTypePiece= "	SELECT TypPi_Libelle
											FROM type_piece
											WHERE IdTypePiece = '$IdTypePiece'";
				$resultatTypePiece = mysql_query($requeteTypePiece) or die("Requete Type Piece :".mysql_error());
				$typePiece = mysql_fetch_row($resultatTypePiece);

				$TypPi_Libelle = $typePiece[0];

				$requeteModifierVoiture= "	UPDATE voiture
													SET	Voit_Id".str_replace(" ","",$TypPi_Libelle)." = '$IdPieceChangement'
													WHERE IdVoiture = $IdVoiture";
				mysql_query($requeteModifierVoiture) or die(mysql_error());

				$requetePayerChangements="	UPDATE manager
													SET Man_Solde = Man_Solde - $PrixTotal
													WHERE IdManager = $IdManager";
				mysql_query($requetePayerChangements)or die(mysql_error());
			}
			header("Location: fiche.php?IdVoiture=$IdVoiture&page=pieces");
			exit();
		}
//Gestion des erreurs de saisie si la vérification n'a pas été faite en JavaScript
/*		if($verificationJs == "false")
		{
			$codesErreur = is_NotNull($ModVoi_NomModele,"102");

			if($codesErreur != "")
			{
				session_start();
				$_SESSION["Post"] = $_POST;
				$_SESSION["Erreur"] = 1;

				$codesErreur=explode(",",$codesErreur);
				$_SESSION["Codes"]=$codesErreur;
				$action=$_POST["action"];

				header("Location: gestion.php?IdVoiture=".$IdVoiture."&".session_name()."=".session_id()."&action=$action");
				exit();
			}
		}*/

		if($_POST["action"]=="Modifier")
		{
			//Attribution à la voiture du pilote
			$requeteModifierVoiture = "	UPDATE voiture
										SET ModVoi_NomModele = '$ModVoi_NomModele'
										WHERE IdVoiture='$IdVoiture'";
			mysql_query($requeteModifierVoiture)or die(mysql_error());

			header("Location: fiche.php?IdVoiture=$IdVoiture");
			exit;
		}
	}
?>