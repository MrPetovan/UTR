<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^E_NOTICE);

	/*echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";*/
	//exit;

	include('../../include/verif.php');

	if(isset($_GET['action']))
	{
		if($_GET['action']=="Supprimer")
		{
			$IdMarque = $_GET['IdMarque'];
			$requeteSupprimerMarque ="	DELETE FROM marque
												WHERE IdMarque = '$IdMarque'";
			mysql_query($requeteSupprimerMarque);

			header("Location: liste.php");
			exit;
		}
	}
	if (isset($_POST["action"]))
	{
		$IdMarque = $_POST['IdMarque'];
		$Marq_Libelle = addslashes(trim($_POST['Marq_Libelle']));
		$Marq_IdTypePiece = $_POST['Marq_IdTypePiece'];

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

				header("Location: gestion.php?IdMarque=".$IdJoueur."&action=$action");
				exit();
			}
		}
		if($_POST["action"]=="Ajouter")
		{
			$SetIdMarques = "";
			foreach($Marq_IdTypePiece as $IdTypePiece)
				$SetIdMarques .= "$IdTypePiece,";

		$SetIdMarques = substr($SetIdMarques,0,-1);

			$requeteAjouterMarque="	INSERT INTO marque(
												Marq_Libelle,
												Marq_IdTypePiece)
											VALUES(
												'$Marq_Libelle',
												'$SetIdMarques')";
			//echo $requeteAjouterMarque;
			//exit;
			mysql_query($requeteAjouterMarque);

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
			$SetIdMarques = "";
			foreach($Marq_IdTypePiece as $IdTypePiece)
				$SetIdMarques .= "$IdTypePiece,";

			$SetIdMarques = substr($SetIdMarques,0,-1);

			$requeteModifierMarque="	UPDATE marque
												SET	Marq_Libelle = '$Marq_Libelle',
														Marq_IdTypePiece = '$SetIdMarques'
												WHERE IdMarque = '$IdMarque'";
			mysql_query($requeteModifierMarque)or die("Requete Modifier Marque : $requeteModifierMarque<br>".mysql_error());

			header("Location: liste.php");
			exit;
		}
	}
?>