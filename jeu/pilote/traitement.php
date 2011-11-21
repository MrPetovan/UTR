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
/*
	echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";
	exit;
*/
	if (isset($_GET["action"]))
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title></title>
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
			<input type="hidden" name="IdManager" value="<?php echo $_GET['IdManager'];?>">
			<input type="hidden" name="IdPilote" value="<?php echo $_GET['IdPilote'];?>">
			<input type="hidden" name="action" value="<?php echo $_GET['action'];?>">
			<table width="75%" border="1" align="center">
				<tr>
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

	if (isset($_POST["action"]))
	{
		$IdManager = $_POST['IdManager'];
		$IdPilote = $_POST['IdPilote'];
		$Pil_PourcentageGains = $_POST['Pil_PourcentageGains'];

		$verificationJs=$_POST['verificationJs'];

//requête supprimant une voiture
		if($_POST["action"]=="Supprimer")
		{
			if($_POST["reponse"]=="Oui")
			{
				$requeteSupprimerPilote = "	DELETE FROM pilote
											WHERE IdPilote='$IdPilote'";
				mysql_query($requeteSupprimerPilote);

				header("Location: liste.php");
				exit;
			}
			else
			{
				header("Location: fiche.php?IdPilote=".$IdPilote);
				exit;
			}
		}
		if($_POST["action"]=="Engager")
		{
			if($_POST["reponse"]=="Oui")
			{
				$requeteEngagerPilote="	UPDATE pilote
											SET Pil_IdManager = '$IdManager'
										WHERE IdPilote = '$IdPilote'";
				mysql_query($requeteEngagerPilote);

//Gestion satisfaction

				header("Location: liste.php");
				exit;
			}
			else
			{
				header("Location: fiche.php?IdPilote=".$IdPilote);
				exit;
			}
		}
		if($_POST["action"]=="Renvoyer")
		{
			if($_POST["reponse"]=="Oui")
			{
				$requeteRenvoyerPilote = "	UPDATE pilote
											SET Pil_IdManager = '0'
											WHERE IdPilote='$IdPilote'";
				mysql_query($requeteRenvoyerPilote);

//Gestion satisfaction

				header("Location: liste.php");
				exit;
			}
			else
			{
				header("Location: fiche.php?IdPilote=".$IdPilote);
				exit;
			}
		}

//Gestion des erreurs de saisie si la vérification n'a pas été faite en JavaScript
		if($verificationJs == "false")
		{
			$codesErreur = "";
			if($Pil_PourcentageGains != "null")
				if($Pil_PourcentageGains > 100 || $Pil_PourcentageGains < 0) $codesErreur = "102";

			if($codesErreur != "")
			{
				session_start();
				$_SESSION["Post"] = $_POST;
				$_SESSION["Erreur"] = 1;

				$codesErreur=explode(",",$codesErreur);
				$_SESSION["Codes"]=$codesErreur;
				$action=$_POST["action"];

				header("Location: gestion.php?IdPilote=".$IdPilote."&".session_name()."=".session_id()."&action=$action");
				exit();
			}
		}
		if($_POST["action"]=="Modifier")
		{
			$requeteModifierPilote = "	UPDATE pilote
										SET	Pil_PourcentageGains = '$Pil_PourcentageGains'
										WHERE IdPilote='$IdPilote'";
			mysql_query($requeteModifierPilote) or die(mysql_error());

//Gestion de la satisfaction

			header("Location: fiche.php?IdPilote=$IdPilote");
			exit;
		}
	}
?>