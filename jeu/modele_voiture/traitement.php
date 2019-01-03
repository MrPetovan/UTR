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
		echo"<pre>";
		print_r($_POST);
		echo"</pre>";

		$IdModeleVoiture = $_POST['IdModeleVoiture'];
		$ModVoi_IdMarque = $_POST['ModVoi_IdMarque'];
		$ModVoi_NomModele = addslashes(trim($_POST['ModVoi_NomModele']));
		$ModVoi_Niveau = $_POST['ModVoi_Niveau'];
		$ModVoi_TypeCarburant = $_POST['ModVoi_TypeCarburant'];
		$ModVoi_PoidsCarrosserie = $_POST['ModVoi_Poids'];
		$ModVoi_PrixNeuve = $_POST['ModVoi_PrixNeuve'];

		$Ajouter_Bis = $_POST['Ajouter_Bis'];


//Gestion des id des pièces par défaut
		for($i = 1; $i <= 21; $i++)
		{
			//Ajout d'un modèle de pièce
			if($_POST['ajouterModele'][$i]=="true")
			{
				$ModPi_IdMarque[$i] = $_POST['ModPi_IdMarque'][$i];
				$ModPi_NomModele[$i] = addslashes(trim($_POST['ModPi_NomModele'][$i]));
				$ModPi_Acceleration[$i] = (isset($_POST['ModPi_Acceleration'][$i]))?"'".$_POST['ModPi_Acceleration'][$i]."'":"NULL";
				$ModPi_VitesseMax[$i] = (isset($_POST['ModPi_VitesseMax'][$i]))?"'".$_POST['ModPi_VitesseMax'][$i]."'":"NULL";
				$ModPi_Freinage[$i] = (isset($_POST['ModPi_Freinage'][$i]))?"'".$_POST['ModPi_Freinage'][$i]."'":"NULL";
				$ModPi_Turbo[$i] = (isset($_POST['ModPi_Turbo'][$i]))?"'".$_POST['ModPi_Turbo'][$i]."'":"NULL";
				$ModPi_Adherence[$i] = (isset($_POST['ModPi_Adherence'][$i]))?"'".$_POST['ModPi_Adherence'][$i]."'":"NULL";
				$ModPi_SoliditeMoteur[$i] = (isset($_POST['ModPi_SoliditeMoteur'][$i]))?"'".$_POST['ModPi_SoliditeMoteur'][$i]."'":"NULL";
				$ModPi_AspectExterieur[$i] = (isset($_POST['ModPi_AspectExterieur'][$i]))?"'".$_POST['ModPi_AspectExterieur'][$i]."'":"NULL";
				$ModPi_CapaciteMoteur[$i] = (isset($_POST['ModPi_CapaciteMoteur'][$i]))?"'".$_POST['ModPi_CapaciteMoteur'][$i]."'":"NULL";
				$ModPi_CapaciteMax[$i] = (isset($_POST['ModPi_CapaciteMax'][$i]))?"'".$_POST['ModPi_CapaciteMax'][$i]."'":"NULL";
				$ModPi_Poids[$i] = $_POST['ModPi_Poids'][$i];
				$ModPi_DureeVieMax[$i] = $_POST['ModPi_DureeVieMax'][$i];
				$ModPi_PrixNeuve[$i] = $_POST['ModPi_PrixNeuve'][$i];

				$requeteAjouterModelePiece ="	INSERT INTO modele_piece(
															ModPi_IdMarque,
															ModPi_NomModele,
															ModPi_IdTypePiece,
															ModPi_Niveau,
															ModPi_Acceleration,
															ModPi_VitesseMax,
															ModPi_Freinage,
															ModPi_Turbo,
															ModPi_Adherence,
															ModPi_SoliditeMoteur,
															ModPi_AspectExterieur,
															ModPi_CapaciteMoteur,
															ModPi_CapaciteMax,
															ModPi_TypeCarburant,
															ModPi_Poids,
															ModPi_DureeVieMax,
															ModPi_PrixNeuve)
														VALUES(
															'".$ModPi_IdMarque[$i]."',
															'".$ModPi_NomModele[$i]."',
															'$i',
															'$ModVoi_Niveau',
															".$ModPi_Acceleration[$i].",
															".$ModPi_VitesseMax[$i].",
															".$ModPi_Freinage[$i].",
															".$ModPi_Turbo[$i].",
															".$ModPi_Adherence[$i].",
															".$ModPi_SoliditeMoteur[$i].",
															".$ModPi_AspectExterieur[$i].",
															".$ModPi_CapaciteMoteur[$i].",
															".$ModPi_CapaciteMax[$i].",
															'$ModVoi_TypeCarburant',
															'".$ModPi_Poids[$i]."',
															'".$ModPi_DureeVieMax[$i]."',
															'".$ModPi_PrixNeuve[$i]."')";
				mysql_query($requeteAjouterModelePiece)or die("modele_voiture/Ajouter Modele Piece : <br>$requeteAjouterModelePiece<br><br>".mysql_error());


				$IdModelePiece = mysql_fetch_row(mysql_query("SELECT MAX(IdModelePiece) FROM modele_piece"));
				$ModVoi_IdModelePiece[$i] = "'".$IdModelePiece[0]."'";

				echo " Pièce ajoutée : ".$IdModelePiece[0]."<br>";
			}
			else $ModVoi_IdModelePiece[$i] = ($_POST['ModVoi_IdModelePiece'][$i]!="")?"'".$_POST['ModVoi_IdModelePiece'][$i]."'":"NULL";
		}

		echo"<pre>";
		print_r($ModVoi_IdModelePiece);
		echo"</pre>";

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

				header("Location: gestion.php?IdModeleVoiture=".$IdJoueur."&action=$action");
				exit();
			}
		}
		if($_POST["action"]=="Ajouter")
		{
			$requeteAjouterModeleVoiture = "	INSERT INTO modele_voiture(
															ModVoi_IdMarque,
															ModVoi_NomModele,
															ModVoi_Niveau,
															ModVoi_PrixNeuve,
															ModVoi_PoidsCarrosserie,
															ModVoi_TypeCarburant,
															ModVoi_IdInjection,
															ModVoi_IdRefroidissement,
															ModVoi_IdBlocMoteur,
															ModVoi_IdTransmission,
															ModVoi_IdJantes,
															ModVoi_IdPneus,
															ModVoi_IdFreins,
															ModVoi_IdAmortisseurs,
														   ModVoi_IdSpoiler,
														   ModVoi_IdOptiques,
														   ModVoi_IdAileron,
														   ModVoi_IdChassis,
														   ModVoi_IdPucedeContrôle,
														   ModVoi_IdNOS,
														   ModVoi_IdNéons,
														   ModVoi_IdSono,
														   ModVoi_IdEchappement,
														   ModVoi_IdTurbo,
														   ModVoi_IdCarrosserie)
														VALUES(
															'$ModVoi_IdMarque',
															'$ModVoi_NomModele',
															'$ModVoi_Niveau',
														'$ModVoi_PrixNeuve',
															'$ModVoi_PoidsCarrosserie',
															'$ModVoi_TypeCarburant',
															$ModVoi_IdModelePiece[1],
															$ModVoi_IdModelePiece[2],
															$ModVoi_IdModelePiece[3],
															$ModVoi_IdModelePiece[4],
															$ModVoi_IdModelePiece[5],
															$ModVoi_IdModelePiece[6],
															$ModVoi_IdModelePiece[7],
															$ModVoi_IdModelePiece[8],
															$ModVoi_IdModelePiece[9],
															$ModVoi_IdModelePiece[10],
															$ModVoi_IdModelePiece[11],
															$ModVoi_IdModelePiece[13],
															$ModVoi_IdModelePiece[14],
															$ModVoi_IdModelePiece[15],
															$ModVoi_IdModelePiece[16],
															$ModVoi_IdModelePiece[17],
															$ModVoi_IdModelePiece[19],
															$ModVoi_IdModelePiece[20],
															$ModVoi_IdModelePiece[21])";
			//echo $requeteAjouterModeleVoiture;
			mysql_query($requeteAjouterModeleVoiture)or die("Requete Ajouter Modele voiture<br>$requeteAjouterModeleVoiture<br><br>".mysql_error());

			if($Ajouter_Bis=="false")
			{
				$IdModeleVoiture = mysql_fetch_row(mysql_query("SELECT MAX(IdModeleVoiture) FROM modele_voiture"));

				header("Location: fiche.php?IdModeleVoiture=".$IdModeleVoiture[0]);
				exit;
			}
			else
			{
				header("Location: gestion.php?action=Ajouter&bis=1");
				exit;
			}
		}

		if($_POST["action"]=="Modifier")
		{
			$requeteModifierModeleVoiture= "	UPDATE modele_voiture
														SET	ModVoi_IdMarque = '$ModVoi_IdMarque',
																ModVoi_NomModele = '$ModVoi_NomModele',
																ModVoi_Niveau = '$ModVoi_Niveau',
																ModVoi_PrixNeuve = '$ModVoi_PrixNeuve',
																ModVoi_PoidsCarrosserie = '$ModVoi_PoidsCarrosserie',
																ModVoi_TypeCarburant = '$ModVoi_TypeCarburant',
																ModVoi_IdInjection = $ModVoi_IdModelePiece[1],
																ModVoi_IdRefroidissement = $ModVoi_IdModelePiece[2],
																ModVoi_IdBlocMoteur = $ModVoi_IdModelePiece[3],
																ModVoi_IdTransmission = $ModVoi_IdModelePiece[4],
																ModVoi_IdJantes = $ModVoi_IdModelePiece[5],
																ModVoi_IdPneus = $ModVoi_IdModelePiece[6],
																ModVoi_IdFreins = $ModVoi_IdModelePiece[7],
																ModVoi_IdAmortisseurs = $ModVoi_IdModelePiece[8],
															   ModVoi_IdSpoiler = $ModVoi_IdModelePiece[9],
															   ModVoi_IdOptiques = $ModVoi_IdModelePiece[10],
															   ModVoi_IdAileron = $ModVoi_IdModelePiece[11],
															   ModVoi_IdChassis = $ModVoi_IdModelePiece[13],
															   ModVoi_IdPucedeContrôle = $ModVoi_IdModelePiece[14],
															   ModVoi_IdNOS = $ModVoi_IdModelePiece[15],
															   ModVoi_IdNéons = $ModVoi_IdModelePiece[16],
															   ModVoi_IdSono = $ModVoi_IdModelePiece[17],
															   ModVoi_IdEchappement = $ModVoi_IdModelePiece[19],
															   ModVoi_IdTurbo = $ModVoi_IdModelePiece[20],
																ModVoi_IdCarrosserie = $ModVoi_IdModelePiece[21]
														WHERE IdModeleVoiture = '$IdModeleVoiture'";
			//echo $requeteModifierModeleVoiture;
			mysql_query($requeteModifierModeleVoiture)or die("Requete Modifier Modele Voiture :<br>$requeteModifierModeleVoiture<br><br>".mysql_error());

			header("Location: fiche.php?IdModeleVoiture=$IdModeleVoiture");
			exit;
		}
	}
?>