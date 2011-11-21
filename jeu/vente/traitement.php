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

/*	echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";
	exit;*/

	if (isset($_GET["action"]))
	{
		if($_GET["action"]=="Supprimer")
		{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Supprimer une vente</title>
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
	include("../../frame/menu.php");
?>
		</td>
		<td>
		<form method="POST" action="traitement.php">
			<input type="hidden" name="IdVente" value="<?php echo $_GET["IdVente"]; ?>">
			<input type="hidden" name="action" value="Supprimer">
			<table width="75%" border="1" align="center">
				<tr>
					<td><img src="../images/warningpetit.gif">
					</td>
					<td></td>
					<td align="center">
<?php
				$codeErreur="101";
				$requeteMessageErreur="	SELECT Err_Message
										FROM erreur
										WHERE IdErreur = $codeErreur";
				$resultatMessageErreur=mysql_query($requeteMessageErreur) or die(mysql_error());
				$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
				echo ($messageErreur["Err_Message"]." \n");
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
		$IdVente = $_POST["IdVente"];
		$Ven_IdItem = $_POST['Ven_IdItem'];
		$Ven_IdTypeVente = $_POST['Ven_IdTypeVente'];
		$Ven_Prix = trim($_POST['Ven_Prix']);
		$Ven_Usure = (isset($_POST['Ven_Usure']))?"'".trim($_POST['Ven_Usure'])."'":"NULL";
		$Ven_Qualite = (isset($_POST['Ven_Qualite']))?"'".trim($_POST['Ven_Qualite'])."'":"NULL";

		$verificationJs=$_POST['verificationJs'];

		$requetePresenceVente = "	SELECT	IdVente,
														Ven_IdItem,
														Ven_IdTypeVente,
														Ven_Prix
											FROM vente
											WHERE IdVente = '$IdVente'";
		$resultatPresenceVente = mysql_query($requetePresenceVente) or die("Requete Presence Vente :".mysql_error());
		$infoVente = mysql_fetch_assoc($resultatPresenceVente);

		if(isset($infoVente['IdVente']))
		{

//requête supprimant une vente
			if($_POST["action"]=="Supprimer")
			{
				if($_POST["reponse"]=="Oui")
				{
					$requeteSupprimerVente = "	DELETE FROM vente
														WHERE IdVente='$IdVente'";
					mysql_query($requeteSupprimerVente)or die(mysql_error());

					switch($infoVente['Ven_IdTypeVente'])
					{
						case 1 :
							$location = "../voiture/fiche.php?IdVoiture=".$infoVente['Ven_IdItem']."&page=infos";
							break;
						case 2 :
							$location = "../piece/fiche.php?IdPieceDetachee=".$infoVente['Ven_IdItem'];
							break;
					}
				}
			}
			if($_POST["action"]=="Acheter")
			{
				if($_POST["reponse"]=="Oui")
				{
					$requetePresenceVente = "	SELECT	IdVente,
																	Ven_IdItem,
																	Ven_IdTypeVente,
																	Ven_Prix
														FROM vente
														WHERE IdVente = '$IdVente'";
					$resultatPresenceVente = mysql_query($requetePresenceVente) or die("Requete Presence Vente :".mysql_error());
					$infoVente = mysql_fetch_assoc($resultatPresenceVente);

					if(isset($infoVente['IdVente']))
					{
						$requeteSoldeManager ="	SELECT Man_Solde
														FROM manager
														WHERE IdManager = '$IdManager'";
						$resultatSoldeManager = mysql_query($requeteSoldeManager);
						$soldeManager = mysql_fetch_row($resultatSoldeManager);

						$Ven_Prix = $infoVente['Ven_Prix'];

		//Pas assez de sous !
						if($Ven_Prix > $soldeManager[0])
						{
							$_SESSION["Post"] = $_POST;
							$_SESSION["Erreur"] = 1;

							$_SESSION["Codes"]="210".$infoVente['Ven_IdTypeVente'];

							switch($infoVente['Ven_IdTypeVente'])
							{
								case 1 :
									//$IdVoiture = $_POST['IdVoiture'];
									$IdVoiture = $infoVente['Ven_IdItem'];
									$location = "../voiture/fiche.php?IdVoiture=$IdVoiture&page=infos";
									break;
								case 2 :
									//$IdPieceDetachee = $_POST['IdPieceDetachee'];
									$IdPieceDetachee = $infoVente['Ven_IdItem'];

									$location = "../piece/fiche.php?IdPieceDetachee=$IdPieceDetachee";
							}
						}
						else
						{
							$requetePayerVente="	UPDATE manager
														SET Man_Solde = Man_Solde - '$Ven_Prix'
														WHERE IdManager = '$IdManager'";
							mysql_query($requetePayerVente);

							$requeteSupprimerVente = " DELETE FROM vente
															WHERE IdVente ='$IdVente'";
							mysql_query($requeteSupprimerVente);

							switch($infoVente['Ven_IdTypeVente'])
						{
								case 1 :
									$IdVoiture = $infoVente['Ven_IdItem'];
									$requeteAcheterVoiture = "	UPDATE voiture
																	SET Voit_IdManager = '$IdManager'
																	WHERE IdVoiture = '$IdVoiture'";
									mysql_query($requeteAcheterVoiture) or die(mysql_error());

									$location = "../voiture/fiche.php?IdVoiture=$IdVoiture&page=infos";
									break;
								case 2 :
									$IdPieceDetachee = $infoVente['Ven_IdItem'];
									$requeteAcheterPieceDetachee = "	UPDATE piece_detachee
																				SET PiDet_IdManager = '$IdManager'
																				WHERE IdPieceDetachee = '$IdPieceDetachee'";
									mysql_query($requeteAcheterPieceDetachee) or die(mysql_error());

									$location = "../piece/fiche.php?IdPieceDetachee=$IdPieceDetachee";
							}
						}
					}
				}
				else
				{
					switch($infoVente['Ven_IdTypeVente'])
					{
						case 1 :
							//$IdVoiture = $_POST['IdVoiture'];
							$IdVoiture = $infoVente['Ven_IdItem'];
							$location = "../voiture/fiche.php?IdVoiture=$IdVoiture&page=infos";
							break;
						case 2 :
						//$IdPieceDetachee = $_POST['IdPieceDetachee'];
							$IdPieceDetachee = $infoVente['Ven_IdItem'];

							$location = "../piece/fiche.php?IdPieceDetachee=$IdPieceDetachee";
					}
					//$location = "../piece/fiche.php?IdPieceDetachee=$IdPieceDetachee";
				}
			}
		}
		else	$location = "erreur.php";

//Gestion des erreurs de saisie si la vérification n'a pas été faite en JavaScript
		if($verificationJs == "false")
		{
			$codesErreur = ",";
			$codesErreur .= is_Number($Ven_Prix,'',"701").",";
			if($Ven_IdTypeVente == 2)
			{
				$codesErreur .= is_Number(ereg_replace("'","",$Ven_Usure),'',"702").",";
				$codesErreur .= is_Number(ereg_replace("'","",$Ven_Qualite),'',"703").",";
			}

			$codesErreur = ereg_replace(",{2,}",",",$codesErreur);
			$codesErreur = substr($codesErreur,1,-1);

			if($codesErreur != "")
			{
				session_start();
				$_SESSION["Post"] = $_POST;
				$_SESSION["Erreur"] = 1;

				$codesErreur=explode(",",$codesErreur);
				$_SESSION["Codes"]=$codesErreur;
				$action=$_POST["action"];

				header("Location: gestion.php");
				exit();
			}
		}
		if($_POST["action"]=="Vendre")
		{
			$requeteAjouterVente= "	INSERT INTO vente(Ven_IdItem, Ven_IdTypeVente, Ven_Prix, Ven_Usure, Ven_Qualite)
									VALUES('$Ven_IdItem','$Ven_IdTypeVente','$Ven_Prix',$Ven_Usure,$Ven_Qualite)";
			mysql_query($requeteAjouterVente) or die(mysql_query());

			switch($Ven_IdTypeVente)
			{
				case 1 :
					$location = "../voiture/fiche.php?IdVoiture=$Ven_IdItem&page=infos";
					break;
				case 2 :
					$location = "../piece/fiche.php?IdPieceDetachee=".$Ven_IdItem;
					break;
			}
		}

		if($_POST["action"]=="Modifier")
		{
			$requeteModifierVente="	UPDATE vente
									SET Ven_Prix = '$Ven_Prix',
										Ven_Usure = $Ven_Usure,
										Ven_Qualite = $Ven_Qualite
									WHERE IdVente = '$IdVente'";
			mysql_query($requeteModifierVente) or die(mysql_query());

			switch($Ven_IdTypeVente)
			{
				case 1 :
					$location = "../voiture/fiche.php?IdVoiture=".$Ven_IdItem;
					break;
				case 2 :
					$location = "../piece/fiche.php?IdPieceDetachee=".$Ven_IdItem;
					break;
			}
		}

//		echo "Location : ".$location;
		header("Location: ".$location);
		exit;
	}
?>