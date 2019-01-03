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
/*		echo"<pre>";
		print_r($_POST);
		echo"</pre>";*/

		$IdGestionManager = $_POST['IdGestionManager'];
		$Man_Nom = $_POST['Man_Nom'];
		$Man_Sexe = $_POST['Man_Sexe'];
		$Man_Niveau = $_POST['Man_Niveau'];
		$Man_Solde = $_POST['Man_Solde'];
		$Man_Reputation = $_POST['Man_Reputation'];
		$Man_Chance = $_POST['Man_Chance'];
		$Man_IdJob = $_POST['Man_IdJob'];

		$verificationJs=$_POST['verificationJs'];

//Gestion des erreurs de saisie si la vérification n'a pas été faite en JavaScript
		if($verificationJs == "false")
		{
			$codesErreur = ",";
			$codesErreur .= is_NotNull($Man_Nom,"601").",";
			$codesErreur .= is_Number($Man_Solde,'',"602").",";
			$codesErreur .= is_Number($Man_Reputation,'',"603").",";
			$codesErreur .= is_Number($Man_Chance,'',"604").",";

			$codesErreur = ereg_replace(",{2,}",",",$codesErreur);
			$codesErreur = substr($codesErreur,1,-1);

			if($codesErreur != "")
			{
				$_SESSION["Post"] = $_POST;
				$_SESSION["Erreur"] = 1;

				$codesErreur=explode(",",$codesErreur);
				$_SESSION["Codes"]=$codesErreur;
				$action=$_POST["action"];

				header("Location: gestion.php?IdGestionManager=".$IdJoueur."&action=$action");
				exit();
			}
		}
		if($_POST["action"]=="Modifier")
		{
			$requeteModifierManager = "	UPDATE manager
													SET	Man_Nom = '$Man_Nom',
															Man_Sexe = '$Man_Sexe',";
			if(isset($Man_Niveau)) $requeteModifierManager .= "Man_Niveau = '$Man_Niveau',";
			$requeteModifierManager .= "			Man_Solde = '$Man_Solde',
															Man_Reputation = '$Man_Reputation',
															Man_Chance = '$Man_Chance',
															Man_IdJob = '$Man_IdJob'
													WHERE IdManager='$IdGestionManager'";
			mysql_query($requeteModifierManager) or die("Requete Modifier Manager : $requeteModifierManager<br>".mysql_error());

			header("Location: fiche.php?IdManager=$IdGestionManager");
			exit;
		}
	}
?>