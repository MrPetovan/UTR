<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	include('../../include/verif.php');
	error_reporting(E_ALL ^E_NOTICE);

	$IdManager = $_SESSION['IdManager'];

	if(isset($_GET['action']))
	{
		if($_GET['action']=="Supprimer")
		{
			$IdSponsor = $_GET['IdSponsor'];
			$requeteSupprimerSponsor="	DELETE FROM sponsor
												WHERE IdSponsor = '$IdSponsor'";
			mysql_query($requeteSupprimerSponsor);

			header("Location: liste.php");
			exit;
		}
	}
	if (isset($_POST["action"]))
	{
		$IdSponsor = $_POST['IdSponsor'];
		$Spon_IdMarque = $_POST['Spon_IdMarque'];
		$Spon_Niveau = $_POST['Spon_Niveau'];
		$Spon_Salaire = $_POST['Spon_Salaire'];

		$Ajouter_Bis = $_POST['Ajouter_Bis'];

		$verificationJs=$_POST['verificationJs'];

//Gestion des erreurs de saisie si la vérification n'a pas été faite en JavaScript
		if($verificationJs == "false")
		{
			$codesErreur = ",";

			$codesErreur = preg_replace("/,{2,}/",",",$codesErreur);
			$codesErreur = substr($codesErreur,1,-1);

			if($codesErreur != "")
			{
				$_SESSION["Post"] = $_POST;
				$_SESSION["Erreur"] = 1;

				$codesErreur=explode(",",$codesErreur);
				$_SESSION["Codes"]=$codesErreur;
				$action=$_POST["action"];

				header("Location: gestion.php?IdSponsor=".$IdJoueur."&action=$action");
				exit();
			}
		}
		if($_POST["action"]=="Changer")
		{
			$requeteChangerSponsor = "	UPDATE manager
												SET Man_IdSponsor = '$IdSponsor'
												WHERE IdManager = '$IdManager'";
			mysql_query($requeteChangerSponsor);

			header("Location: ../joueur/stat.php");
			exit;
		}

		if($_POST["action"]=="Ajouter")
		{
			$requeteAjouterSponsor="	INSERT INTO sponsor(
												Spon_IdMarque,
												Spon_Niveau,
												Spon_Salaire)
											VALUES(
												'$Spon_IdMarque',
												'$Spon_Niveau',
												'$Spon_Salaire')";
			mysql_query($requeteAjouterSponsor);

			if($Ajouter_Bis=="false")
			{
				header("Location: liste.php");
				exit;
			}
			else
			{
				header("Location: gestion.php?action=Ajouter&bis=1");
				exit;
			}
		}

		if($_POST['action'] == "Modifier")
		{
			$requeteModifierSponsor = "	UPDATE sponsor
											SET	Spon_IdMarque = '$Spon_IdMarque',
													Spon_Niveau = '$Spon_Niveau',
													Spon_Salaire = '$Spon_Salaire'
											WHERE IdSponsor = '$IdSponsor'";
			mysql_query($requeteModifierSponsor)or die("Requete Modifier Sponsor : $requeteModifierSponsor<br>".mysql_error());

			header("Location: liste.php");
			exit;
		}
	}
?>