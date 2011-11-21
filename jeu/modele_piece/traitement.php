<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^E_NOTICE);

	include('../../include/verif.php');

	if (isset($_POST["action"]))
	{
		$IdModelePiece = $_POST['IdModelePiece'];
		$ModPi_IdMarque = $_POST['ModPi_IdMarque'];
		$ModPi_NomModele = addslashes(trim($_POST['ModPi_NomModele']));
		$ModPi_Niveau = $_POST['ModPi_Niveau'];
		$ModPi_Commentaires = addslashes(trim($_POST['ModPi_Commentaires']));
		$ModPi_IdTypePiece = $_POST['ModPi_IdTypePiece'];
		$ModPi_Poids = $_POST['ModPi_Poids'];
		$ModPi_PrixNeuve = $_POST['ModPi_PrixNeuve'];
		$ModPi_DureeVieMax = $_POST['ModPi_DureeVieMax'];

		$ModPi_AspectExterieur = ($_POST['ModPi_AspectExterieur']=="")?"NULL":$_POST['ModPi_AspectExterieur'];
		$ModPi_Acceleration = ($_POST['ModPi_Acceleration']=="")?"NULL":$_POST['ModPi_Acceleration'];
		$ModPi_VitesseMax = ($_POST['ModPi_VitesseMax']=="")?"NULL":$_POST['ModPi_VitesseMax'];
		$ModPi_Freinage = ($_POST['ModPi_Freinage']=="")?"NULL":$_POST['ModPi_Freinage'];
		$ModPi_Turbo = ($_POST['ModPi_Turbo']=="")?"NULL":$_POST['ModPi_Turbo'];
		$ModPi_Adherence = ($_POST['ModPi_Adherence']=="")?"NULL":$_POST['ModPi_Adherence'];
		$ModPi_SoliditeMoteur = ($_POST['ModPi_SoliditeMoteur']=="")?"NULL":$_POST['ModPi_SoliditeMoteur'];
		$ModPi_CapaciteMoteur = ($_POST['ModPi_CapaciteMoteur']=="")?"NULL":$_POST['ModPi_CapaciteMoteur'];
		$ModPi_CapaciteMax = ($_POST['ModPi_CapaciteMax']=="")?"NULL":$_POST['ModPi_CapaciteMax'];


		$verificationJs=$_POST['verificationJs'];

//Gestion des erreurs de saisie si la vérification n'a pas été faite en JavaScript
		if($verificationJs == "false")
		{
			$codesErreur = ",";

			$codesErreur = ereg_replace(",{2,}",",",$codesErreur);
			$codesErreur = substr($codesErreur,1,-1);

			if($codesErreur != "")
			{
				$_SESSION["Post"] = $_POST;
				$_SESSION["Erreur"] = 1;

				$codesErreur=explode(",",$codesErreur);
				$_SESSION["Codes"]=$codesErreur;
				$action=$_POST["action"];

				header("Location: gestion.php?IdModelePiece=".$IdModelePiece."&action=$action");
				exit();
			}
		}
		if($_POST["action"]=="Ajouter")
		{
			$requeteAjouterModèle="	INSERT INTO modele_piece(
												ModPi_IdMarque,
												ModPi_NomModele,
												ModPi_Commentaires,
												ModPi_IdTypePiece,
												ModPi_Niveau,
												ModPi_AspectExterieur,
												ModPi_Poids,
												ModPi_PrixNeuve,
												ModPi_Acceleration,
												ModPi_VitesseMax,
												ModPi_Freinage,
												ModPi_Turbo,
												ModPi_Adherence,
												ModPi_SoliditeMoteur,
												ModPi_CapaciteMoteur,
												ModPi_CapaciteMax,
											   ModPi_DureeVieMax)
											VALUES(
												'$ModPi_IdMarque',
												'$ModPi_NomModele',
												'$ModPi_Commentaires',
												'$ModPi_IdTypePiece',
												'$ModPi_Niveau',
												$ModPi_AspectExterieur,
												'$ModPi_Poids',
												'$ModPi_PrixNeuve',
												$ModPi_Acceleration,
												$ModPi_VitesseMax,
												$ModPi_Freinage,
												$ModPi_Turbo,
												$ModPi_Adherence,
												$ModPi_SoliditeMoteur,
												$ModPi_CapaciteMoteur,
												$ModPi_CapaciteMax,
												'$ModPi_DureeVieMax')";
			mysql_query($requeteAjouterModèle);

			$IdModelePiece = mysql_fetch_row(mysql_query("SELECT MAX(IdModelePiece) FROM modele_piece"));

			header("Location: fiche.php?IdModelePiece=".$IdModelePiece[0]);
			exit;
		}

		if($_POST["action"]=="Modifier")
		{
			$requeteModifierModèle = "	UPDATE modele_piece
												SET	ModPi_IdMarque = '$ModPi_IdMarque',
														ModPi_NomModele = '$ModPi_NomModele',
														ModPi_Commentaires = '$ModPi_Commentaires',
														ModPi_IdTypePiece = '$ModPi_IdTypePiece',
														ModPi_Niveau = '$ModPi_Niveau',
														ModPi_AspectExterieur = $ModPi_AspectExterieur,
														ModPi_Poids = '$ModPi_Poids',
														ModPi_PrixNeuve = '$ModPi_PrixNeuve',
														ModPi_Acceleration = $ModPi_Acceleration,
														ModPi_VitesseMax = $ModPi_VitesseMax,
														ModPi_Freinage = $ModPi_Freinage,
														ModPi_Turbo = $ModPi_Turbo,
														ModPi_Adherence = $ModPi_Adherence,
														ModPi_SoliditeMoteur = $ModPi_SoliditeMoteur,
														ModPi_CapaciteMoteur = $ModPi_CapaciteMoteur,
														ModPi_CapaciteMax = $ModPi_CapaciteMax,
													   ModPi_DureeVieMax = '$ModPi_DureeVieMax'
												WHERE IdModelePiece = '$IdModelePiece'";
			mysql_query($requeteModifierModèle)or die("Requete Modifier Modele Piece : $requeteModifierModèle<br>".mysql_error());

			header("Location: fiche.php?IdModelePiece=$IdModelePiece");
			exit;
		}
	}
?>