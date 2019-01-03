<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^E_NOTICE);

	$IdManager = $_SESSION['IdManager'];

	include('../../include/verif.php');

	if(isset($_GET['action']))
	{
		if($_GET['action']=="Supprimer")
		{
			$IdJob = $_GET['IdJob'];
			$requeteSupprimerJob ="	DELETE FROM job
											WHERE IdJob = '$IdJob'";
			mysql_query($requeteSupprimerJob);

			header("Location: liste.php");
			exit;
		}
	}
	if (isset($_POST["action"]))
	{
		$IdJob = $_POST['IdJob'];
		$Job_NomMasculin = addslashes(trim($_POST['Job_NomMasculin']));
		$Job_NomFéminin = addslashes(trim($_POST['Job_NomFéminin']));
		$Job_Niveau = $_POST['Job_Niveau'];
		$Job_Salaire = $_POST['Job_Salaire'];

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

				header("Location: gestion.php?IdJob=".$IdJoueur."&action=$action");
				exit();
			}
		}
		if($_POST["action"]=="Ajouter")
		{
			$requeteAjouterJob="	INSERT INTO job(
												Job_NomMasculin,
												Job_NomFéminin,
												Job_Niveau,
												Job_Salaire)
											VALUES(
												'$Job_NomMasculin',
												'$Job_NomFéminin',
												'$Job_Niveau',
												'$Job_Salaire')";
			mysql_query($requeteAjouterJob);

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
		if($_POST["action"]=="Changer")
		{
			$requeteChangerJob="	UPDATE manager
										SET Man_IdJob = '$IdJob'
										WHERE IdManager = '$IdManager'";
			mysql_query($requeteChangerJob);

			header("Location: ../joueur/stat.php");
			exit;
		}

		if($_POST['action'] == "Modifier")
		{
			$requeteModifierJob = "	UPDATE job
											SET	Job_NomMasculin = '$Job_NomMasculin',
													Job_NomFéminin = '$Job_NomFéminin',
													Job_Niveau = '$Job_Niveau',
													Job_Salaire = '$Job_Salaire'
											WHERE IdJob = '$IdJob'";
			mysql_query($requeteModifierJob)or die("Requete Modifier Job : $requeteModifierJob<br>".mysql_error());

			header("Location: liste.php");
			exit;
		}
	}
?>