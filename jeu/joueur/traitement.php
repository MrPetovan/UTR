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
			<input type="hidden" name="IdJoueur" value="<?php echo $_GET['IdJoueur'];?>">
			<input type="hidden" name="action" value="<?php echo $_GET['action'];?>">
			<table width="75%" border="1" align="center">
				<tr>
					<td align="center">
						Pour supprimer un joueur, vous devez indiquer les raisons dans la boîte de dialogue ci-dessus.
					</td>
				</tr>
				<tr>
					<td>
						<textarea name="Raison_Suppression" cols="50" rows="5"></textarea>
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
		/*echo"<pre>";
		print_r($_POST);
		echo"</pre>";*/
		$IdGestionJoueur = $_POST['IdGestionJoueur'];
		$Jou_Pseudo = $_POST['Jou_Pseudo'];
		$Jou_Login = $_POST['Jou_Login'];
		$Jou_MotDePasse = $_POST['Jou_MotDePasse'];
		$Jou_MotDePasse2 = $_POST['Jou_MotDePasse2'];
		$Jou_Email = $_POST['Jou_Email'];

		$Mail_Sujet = $_POST['Mail_Sujet'];
		$Mail_Texte = $_POST['Mail_Texte'];

		$verificationJs=$_POST['verificationJs'];

//requête supprimant une voiture
		if($_POST['action']=="Envoyer")
		{
			$requeteEmailJoueurs= "	SELECT IdJoueur, Jou_Pseudo, Jou_Email
											FROM joueur";
			$resultatEmailJoueurs = mysql_query($requeteEmailJoueurs);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Compte-rendu d'envoi de mail</title>
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
			<table border="1">
				<tr>
					<th colspan="">Compte-rendu mail collectif</td>
				</tr>
				<tr>
					<th>Id</th>
					<th>Pseudo</th>
					<th>E-mail</th>
					<th>Mail envoyé</th>
				</tr>
<?php
			while($infoJoueur = mysql_fetch_assoc($resultatEmailJoueurs))
			{
				$to  = $infonJoueur['Jou_Pseudo']." <".$infoJoueur['Jou_Email'].">";
				$subject = $Mail_Sujet;
				$message = "
				<html>
				<head>
				</head>
				<body>
				<p>Bonjour ".$infonJoueur['Jou_Pseudo'].",<br>ceci est un mail commun envoyé à tous les joueurs d'UTR.</p>
				<p>$Mail_Texte</p>
				<hr>
				Si vous ne souhaitez plus jouer à UTR, faites-le savoir au Webmaster.<br>-----<br>Le Pacha
				</body>
				</html>
				";

				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
				$headers .= "From: Le Pacha <ben.lort@oreka.com>\r\n";

				$mailOk = mail($to, $subject, $message, $headers);
?>
				<tr>
					<td><?php echo $infoJoueur['IdJoueur']?></td>
					<td><?php echo $infoJoueur['Jou_Pseudo']?></td>
					<td><?php echo $infoJoueur['Jou_Email']?></td>
					<td><?php echo ($mailOk)?"Oui":"Non"?></td>
				</tr>
<?php
			}
?>
			</table>
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

		if($_POST["action"]=="Supprimer")
		{
			if($_POST["reponse"]=="Oui")
			{
				if($infoJoueur['IdGestionManager'] != "")
				{
					$Raison_Suppression = $_POST['Raison_Suppression'];

					$requeteInfoJoueur = "	SELECT Jou_Pseudo, Jou_Email, IdManager AS IdGestionManager
													FROM joueur
													LEFT JOIN manager ON IdJoueur = Man_IdJoueur
													WHERE IdJoueur = '$IdGestionJoueur'";
					$resultatInfoJoueur = mysql_query($requeteInfoJoueur)or die(mysql_error());
					$infoJoueur = mysql_fetch_assoc($resultatInfoJoueur);

					$to  = $infonJoueur['Jou_Pseudo']." <".$infoJoueur['Jou_Email'].">";
					$subject = "UTR : Suppression du profil";
					$message = "
					<html>
					<head>
					<title>Suppression du profil</title>
					</head>
					<body>
					<p>Votre profil a été supprimé, en voici les raisons :</p>
					<p>$Raison_Suppression</p>
					<hr>
					Merci d'avoir joué !
					</body>
					</html>
					";

					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
					$headers .= "From: Le Pacha <ben.lort@oreka.com>\r\n";

					$mailOk = mail($to, $subject, $message, $headers);

					do
					{
						$IdGestionManager = $infoJoueur['IdGestionManager'];

						$requeteEnleverPieces="	UPDATE piece_detachee
														SET PiDet_IdManager = '0'
														WHERE PiDet_IdManager = '$IdGestionManager'";
						mysql_query($requeteEnleverPieces)or die($requeteEnleverPieces."<br>".mysql_error());

						$requeteRemboursementParis ="	UPDATE manager, pari, inscription_course, voiture, pilote
																SET Man_Solde = Man_Solde + Pari_Montant
																WHERE Voit_IdManager =  '$IdGestionManager' AND IC_IdVoiture = IdVoiture
																AND Pil_IdManager =  '$IdGestionManager' AND IC_IdPilote = IdPilote
																AND Pari_IdManager = IdManager
																AND Pari_IdInscriptionCourse = IdInscriptionCourse
																AND IC_Position IS NULL";
						mysql_query($requeteRemboursementParis)or die("Requete Remboursement Paris : $requeteRemboursementParis<br>".mysql_error());

	//Suppression de paris
						$requeteIdParis = "	SELECT IdPari
													FROM pari, inscription_course, pilote, voiture
													WHERE IC_IdVoiture = IdVoiture
													AND IC_IdPilote = IdPilote
													AND Pari_IdInscriptionCourse = IdInscriptionCourse
													AND IC_Position IS NULL
													AND((	Voit_IdManager =  '$IdGestionManager'
															AND Pil_IdManager =  '$IdGestionManager')
															OR ( Pari_IdManager =  '$IdGestionManager'))";
						$resultatIdParis = mysql_query($requeteIdParis) or die("Requête Id Paris : $requeteIdParis<br>".mysql_error());

						while($idPari = mysql_fetch_row($resultatIdParis))
						{
							$requeteSupprimerPari = " 	DELETE FROM pari
																WHERE IdPari = '".$idPari[0]."'";
							mysql_query($requeteSupprimerPari);
						}

	//Suppression Engagements
						$requeteIdInscriptionCourse = "	SELECT IdInscriptionCourse
																	FROM inscription_course, pilote, voiture
																	WHERE IC_IdVoiture = IdVoiture
																	AND IC_IdPilote = IdPilote
																	AND IC_Position IS  NULL
																	AND Voit_IdManager =  '$IdGestionManager'
																	AND Pil_IdManager =  '$IdGestionManager'";
						$resultatIdInscriptionCourse = mysql_query($requeteIdInscriptionCourse) or die($requeteIdInscriptionCourse."<br>".mysql_error());
						while($idInscriptionCourse = mysql_fetch_row($resultatIdInscriptionCourse))
						{
							$requeteSupprimerInscriptionCourse = "	DELETE FROM inscription_course
																				WHERE IdInscriptionCourse = '".$idInscriptionCourse[0]."'";
							mysql_query($requeteSupprimerInscriptionCourse);
						}

						$requeteLibererVoitures ="	UPDATE voiture
															SET Voit_IdManager = NULL
															WHERE Voit_IdManager = '$IdGestionManager'";
						mysql_query($requeteLibererVoitures)or die($requeteLibererVoitures."<br>".mysql_error());

						$requeteLibererPilote = "	UPDATE pilote
															SET Pil_IdManager = '0'
															WHERE Pil_IdManager = '$IdGestionManager'";
						mysql_query($requeteLibererPilote)or die($requeteLibererPilote."<br>".mysql_error());

					}while($infoJoueur = mysql_fetch_assoc($resultatInfoJoueur));
				}
			/*	$requeteSupprimerManagers = "	DELETE FROM manager
														WHERE Man_IdJoueur = '$IdJoueur'";
				mysql_query($requeteSupprimerManagers);*/

				$requeteSupprimerJoueur= "	DELETE FROM joueur
													WHERE IdJoueur='$IdGestionJoueur'";
				mysql_query($requeteSupprimerJoueur);

				exit;
				header("Location: liste.php");
			}
			else
			{
				header("Location: fiche.php?IdGestionJoueur=".$IdGestionJoueur);
				exit;
			}
		}
//Gestion des erreurs de saisie si la vérification n'a pas été faite en JavaScript
		if($verificationJs == "false")
		{
			$codesErreur = ",";
			$codesErreur .= is_NotNull($Jou_Login,"101").",";
			$codesErreur .= is_NotNull($Jou_Email,"105").",";
			if(!is_Nul($Jou_MotDePasse) && !is_Nul($Jou_MotDePasse2))
				if($Jou_MotDePasse != $Jou_MotDePasse2)
					$codesErreur .= "104,";

			$codesErreur = ereg_replace(",{2,}",",",$codesErreur);
			$codesErreur = substr($codesErreur,1,-1);

			if($codesErreur != "")
			{
				$_SESSION["Post"] = $_POST;
				$_SESSION["Erreur"] = 1;

				$codesErreur=explode(",",$codesErreur);
				$_SESSION["Codes"]=$codesErreur;
				$action=$_POST["action"];

				header("Location: gestion.php?IdJoueur=".$IdGestionJoueur."&action=$action");
				exit();
			}
		}
		if($_POST["action"]=="Modifier")
		{
			$requeteModifierJoueur = "	UPDATE joueur
												SET	Jou_Pseudo = '$Jou_Pseudo',
														Jou_Login = '$Jou_Login',";
			if(!empty($Jou_MotDePasse)) $requeteModifierJoueur .= "Jou_MotDePasse = '".crypt($Jou_MotDePasse)."',";
			$requeteModifierJoueur .= "		Jou_Email = '$Jou_Email'
												WHERE IdJoueur='$IdGestionJoueur'";
			mysql_query($requeteModifierJoueur) or die("Requete Modifier Joueur : $requeteModifierJoueur<br>".mysql_error());

			$to  = "$Jou_Pseudo <$Jou_Email>";
			$subject = "UTR : Modifications du profil";
			$message = "
			<html>
			<head>
			<title>Modification du profil</title>
			</head>
			<body>
			<p>Votre profil a été modifié.</p>
			<p>Voici un rappel de vos coordonnées :
			- Login : $Jou_Login<br>
			- Mot de passe : $Jou_MotDePasse</p>
			<hr>
			Bon jeu !
			</body>
			</html>
			";

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "From: Le Pacha <ben.lort@oreka.com>\r\n";

			$mailOk = mail($to, $subject, $message, $headers);

			header("Location: fiche.php?IdGestionJoueur=$IdGestionJoueur");
			exit;
		}
	}
?>