<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/verif.php');
	include('../../include/connexion.inc.php');

/*	echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";
	exit;*/

	if (isset($_GET["action"]))
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--	<link rel=stylesheet type="text/css" href="../style/style.css">-->
  <link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
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
			<input type="hidden" name="IdCourse" value="<?php echo $_GET['IdCourse'];?>">
			<input type="hidden" name="action" value="<?php echo $_GET['action'];?>">
			<table width="75%" border="1" align="center">
				<tr>
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

	if (isset($_POST["action"]))
	{
		$IdManager = $_POST['IdManager'];
		$IdCourse = $_POST['IdCourse'];
		$Cou_Nom = addslashes(trim($_POST['Cou_Nom']));
		$Cou_Date = implode("-",array_reverse(explode("/",$_POST['Cou_Date'])));
		$Cou_IdType = $_POST['Cou_IdType'];
		$Cou_NbTours = trim($_POST['Cou_NbTours']);
		$Cou_PrixInscription = $_POST['Cou_PrixInscription'];
		$Cou_PrixEngagement = $_POST['Cou_PrixEngagement'];
		$Cou_DensiteCirculation = $_POST['Cou_DensiteCirculation'];
		$Cou_NbCompetiteursMax = $_POST['Cou_NbCompetiteursMax'];
		$Cou_NiveauMin = $_POST['Cou_NiveauMin'];
		$Cou_NiveauMax = $_POST['Cou_NiveauMax'];
		$Cou_Commentaires = htmlspecialchars(addslashes($_POST['Cou_Commentaires']));
		$troncons = $_SESSION['troncons'];
		$Sec_Boucle = $_POST['Sec_Boucle'];

		$IC_IdPilote = $_POST['IC_IdPilote'];
		$IC_IdVoiture = $_POST['IC_IdVoiture'];
		$IdInscriptionCourse = $_POST['IdInscriptionCourse'];

		$Pari_Montant = $_POST['Pari_Montant'];

		$verificationJs=$_POST['verificationJs'];

		if($_POST["action"]=="Supprimer")
		{
			if($_POST["reponse"]=="Oui")
			{

				$requeteInscriptionCourse="	SELECT IdInscriptionCourse, IdManager
											FROM inscription_course, pilote, manager
											WHERE IdPilote = IC_IdPilote
											AND IdManager = Pil_IdManager
											AND IC_IdCourse = '$IdCourse'";
				$resultatInscriptionCourse = mysql_query($requeteInscriptionCourse)or die(mysql_error());
				while($infoInscriptionCourse = mysql_fetch_assoc($resultatInscriptionCourse))
				{
//Remboursement paris
					$requetePari= "	SELECT IdPari, Pari_Montant, Pari_IdManager
									FROM pari
									WHERE Pari_IdInscriptionCourse = '".$infoInscriptionCourse."'";
					$resultatPari = mysql_query($requetePari)or die(mysql_error());

					while($infoPari = mysql_fetch_assoc($resultatPari))
					{
						$requeteRembourserPari = "	UPDATE manager
													SET Man_Solde = Man_Solde + ".$infoPari['Pari_Montant']."
													WHERE IdManager = '".$infoPari['Pari_IdManager']."'";
						mysql_query($requeteRembourserPari)or die(mysql_error());
//Suppression des lignes de la table pari
						$requeteSupprimerPari="	DELETE FROM pari
												WHERE IdPari = '".$infoPari['IdPari']."'";
						mysql_query($requeteSupprimerPari)or die(mysql_error());
					}
//Suppression des lignes d'inscription_course
					$requeteSupprimerInscriptionCourse = "	DELETE FROM inscription_course
															WHERE IdInscriptionCourse = '".$infoInscriptionCourse['IdInscriptionCourse']."'";
					mysql_query($requeteSupprimerInscriptionCourse)or die(mysql_error());
				}

//Suppression troncons
				$IdTronconDepart=mysql_fetch_row(mysql_query("SELECT Cou_IdTronconDepart FROM course WHERE IdCourse = '$IdCourse'"))or die(mysql_error());
				$IdTronconSuivant=$IdTronconDepart[0];

				while(!empty($IdTronconSuivant))
				{
					$IdTroncon =mysql_fetch_row(mysql_query("SELECT Tron_IdTronconSuivant FROM troncon WHERE IdTroncon = '$IdTronconSuivant'"));
					mysql_query("DELETE FROM troncon WHERE IdTroncon = '$IdTronconSuivant'");

					$IdTronconSuivant = $IdTroncon[0];
				}

//Suppression de la course
				$requeteSupprimerCourse = "	DELETE FROM course
											WHERE IdCourse='$IdCourse'";
				mysql_query($requeteSupprimerCourse)or die(mysql_error());

				header("Location: liste.php");
				exit;
			}
			else
			{
				header("Location: fiche.php?IdCourse=".$IdCourse);
				exit;
			}
		}
		if($_POST['action']=="Inscrire")
		{
			$requeteInscription = "	INSERT INTO inscription_course(IC_IdPilote, IC_IdVoiture, IC_IdCourse)
									VALUES('$IC_IdPilote','$IC_IdVoiture','$IdCourse')";
			mysql_query($requeteInscription) or die(mysql_error());

//Transfert d'argent : paiement du droit d'inscription
			$requetePayerInscription= "	UPDATE manager
													SET Man_Solde = Man_Solde - $Cou_PrixInscription
													WHERE IdManager = '".$_SESSION['IdManager']."'";
			mysql_query($requetePayerInscription)or die(mysql_error());

			header("Location:fiche.php?IdCourse=".$IdCourse);
			exit;
		}
//////////////////////////////////
//Traitement des désinscriptions//
//////////////////////////////////
		if($_POST['action']=="Désinscrire")
		{
			$IC_IdPilote = $_POST['IC_IdPiloteDesinscrire'];

//On regarde si des paris ont été lancés sur le pilote
			$requetePari= "	SELECT IdPari, Pari_Montant, Pari_IdManager
									FROM pari, inscription_course
									WHERE IdInscriptionCourse = Pari_IdInscriptionCourse
									AND IC_IdPilote ='$IC_IdPilote'
									AND IC_IdCourse = '$IdCourse'";
			$resultatPari = mysql_query($requetePari)or die(mysql_error());

			while($infoPari = mysql_fetch_assoc($resultatPari))
			{
//Remboursement des paris
				$requeteRembourserPari = "	UPDATE manager
											SET Man_Solde = Man_Solde + ".$infoPari['Pari_Montant']."
											WHERE IdManager = '".$infoPari['Pari_IdManager']."'";
				mysql_query($requeteRembourserPari)or die(mysql_error());
//Suppression des lignes de la table pari
				$requeteSupprimerPari="	DELETE FROM pari
										WHERE IdPari = '".$infoPari['IdPari']."'";
				mysql_query($requeteSupprimerPari)or die(mysql_error());
			}

			$requeteDesinscrire = "	DELETE FROM inscription_course
									WHERE IC_IdPilote ='$IC_IdPilote'
									AND IC_IdCourse = '$IdCourse'";
			mysql_query($requeteDesinscrire)or die(mysql_error());

			header("Location:fiche.php?IdCourse=".$IdCourse);
			exit;
		}
////////////////////////
//Traitement des paris//
////////////////////////
		if($_POST['action']=="Parier")
		{
//On regarde si il n'y a pas déjà un pari sur le même pilote
			$requetePari= "	SELECT IdPari, Pari_Montant
									FROM pari
									WHERE Pari_IdManager = '$IdManager'
									AND Pari_IdInscriptionCourse = '$IdInscriptionCourse'";
			$resultatPari = mysql_query($requetePari)or die(mysql_error());
			//Si non, on crée un pari
			if(mysql_num_rows($resultatPari)==0)
			{
				$requeteAjouterPari = "	INSERT INTO pari(Pari_IdManager, Pari_IdInscriptionCourse, Pari_Montant)
										VALUES('$IdManager','$IdInscriptionCourse','$Pari_Montant')";
				mysql_query($requeteAjouterPari) or die(mysql_error());
			}
			//Si oui, on ajoute au pari existant la valeur ajoutée
			else
			{
				$infoPari = mysql_fetch_assoc($resultatPari);
				$requeteModifierPari= "	UPDATE pari
												SET Pari_Montant = Pari_Montant + '$Pari_Montant'
												WHERE IdPari = '".$infoPari['IdPari']."'";
				mysql_query($requeteModifierPari) or die(mysql_error());
			}
//Transfert d'argent : Paiement du pari
			$requetePayerPari="	UPDATE manager
										SET Man_Solde = Man_Solde - $Pari_Montant
										WHERE IdManager = '$IdManager'";
			mysql_query($requetePayerPari)or die(mysql_error());

			header("Location:fiche.php?IdCourse=".$IdCourse);
			exit;
		}

//Gestion des erreurs de saisie si la vérification n'a pas été faite en JavaScript
		if($verificationJs == "false")
		{
			$codesErreur = "";


			if($codesErreur != "")
			{
				session_start();
				$_SESSION["Post"] = $_POST;
				$_SESSION["Erreur"] = 1;

				$codesErreur=explode(",",$codesErreur);
				$_SESSION["Codes"]=$codesErreur;
				$action=$_POST["action"];

				header("Location: gestion.php?IdCourse=".$IdCourse."&".session_name()."=".session_id()."&action=$action");
				exit();
			}
		}
		//requête permettant la modification d'un course
		if($_POST["action"]=="Terminer")
		{
/*			echo "<pre>Tronçons :<br>";
			print_r($troncons);
			echo "</pre>";*/
			for($i = count($troncons)-1; $i >= 0 ;$i--)
			{
				if($i == count($troncons)-1)
				{
					$requeteAjouterTroncon = "	INSERT INTO troncon(Tron_IdSecteur)
												VALUES('".$troncons[$i]."')";
				//	echo $requeteAjouterTroncon."<br>";
					mysql_query($requeteAjouterTroncon)or die ( mysql_error());

					if($i == 0)
					{
						$TronconDepart = mysql_fetch_row(mysql_query("SELECT MAX(IdTroncon) FROM troncon"));
						$IdTronconDepart = $TronconDepart[0];
					}
				}
				else
				{
					$TronconSuivant = mysql_fetch_row(mysql_query("SELECT MAX(IdTroncon) FROM troncon"));
					$IdTronconSuivant = $TronconSuivant[0];

					$requeteAjouterTroncon = "	INSERT INTO troncon(Tron_IdSecteur, Tron_IdTronconSuivant)
												VALUES('".$troncons[$i]."','$IdTronconSuivant')";
					mysql_query($requeteAjouterTroncon)or die ( mysql_error());

					if($i == 0)
					{
						$TronconDepart = mysql_fetch_row(mysql_query("SELECT MAX(IdTroncon) FROM troncon"));
						$IdTronconDepart = $TronconDepart[0];
					}
				}
			}

			if($Cou_NbCompetiteursMax < 2) $Cou_NbCompetiteursMax = 2;
			if($Cou_NbCompetiteursMax > 4) $Cou_NbCompetiteursMax = 4;

			$requeteAjouterCourse = "	INSERT INTO course(	Cou_Nom,
															Cou_Date,
															Cou_IdType,
															Cou_NbTours,
															Cou_PrixInscription,
															Cou_PrixEngagement,
															Cou_DensiteCirculation,
															Cou_NbCompetiteursMax,
															Cou_NiveauMin,
															Cou_NiveauMax,
															Cou_Commentaires,
															Cou_IdManager,
															Cou_IdTronconDepart)
												VALUES(	'$Cou_Nom',
														'$Cou_Date',
														'$Cou_IdType',
														'$Cou_NbTours',
														'$Cou_PrixInscription',
														'$Cou_PrixEngagement',
														'$Cou_DensiteCirculation',
														'$Cou_NbCompetiteursMax',
														'$Cou_NiveauMin',
														'$Cou_NiveauMax',
														'$Cou_Commentaires',
														'$IdManager',
														'".$IdTronconDepart."')";

			mysql_query($requeteAjouterCourse)or die(mysql_error());

			$IdCourse = mysql_fetch_row(mysql_query("SELECT MAX(IdCourse) FROM course"));
//exit;
			header("Location: fiche.php?IdCourse=".$IdCourse[0]);
			exit;
		}
		if($_POST["action"]=="Modifier")
		{
			$requeteModifierCourse = "	UPDATE course
										SET Cou_Nom = '$Cou_Nom',
											Cou_Date = '$Cou_Date',
											Cou_IdType = '$Cou_IdType',
											Cou_NbTours = '$Cou_NbTours',
											Cou_PrixInscription = '$Cou_PrixInscription',
											Cou_PrixEngagement = '$Cou_PrixEngagement',
											Cou_DensiteCirculation = '$Cou_DensiteCirculation',
											Cou_CompetiteursMax = '$Cou_CompetiteursMax',
											Cou_NiveauMin = '$Cou_NiveauMin',
											Cou_NiveauMax = '$Cou_NiveauMax',
											Cou_Commentaires = '$Cou_Commentaires',
										WHERE IdCourse='$IdCourse'";
			mysql_query($requeteModifierCourse)or die(mysql_error());

			header("Location: fiche.php?IdCourse=$IdCourse");
			exit;
		}
	}
?>
