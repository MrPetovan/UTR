<?php
	if(isset($_POST))
	{
		include('../include/connexion.inc.php');
		include('../include/verif.php');
		include('../include/fonctions.php');
		error_reporting(E_ALL ^ E_NOTICE);

		/*echo"<pre>";
		print_r($_POST);
		echo"</pre>";*/

		$Pil_Sexe = $Man_Sexe = $_POST['Man_Sexe'];
		$Pil_Age = $_POST['Pil_Age'];
		$XPRestants = $_POST['XPRestants'];
		$Pil_XPShifts = $_POST['Pil_XPShifts'];
		$Pil_XPFreinage = $_POST['Pil_XPFreinage'];
		$Pil_XPVirage = $_POST['Pil_XPVirage'];
		$Pil_XPSpe = $_POST['Pil_XPSpe'];
		$ModVoi_IdMarque = $_POST['ModVoi_IdMarque'];
		$IdModeleVoiture = $_POST['IdModeleVoiture'];

		$verificationJs=$_POST['verificationJs'];
		$code = $_POST['code'];

		if($verificationJs == "false");
		{
			$codesErreur = ",";

			$codesErreur .= is_NotNull($Pil_Age,"301").",";
			if($XPRestants != 0) $codesErreur .= "2001,";


			$codesErreur = ereg_replace(",{2,}",",",$codesErreur);
			$codesErreur = substr($codesErreur,1,-1);
			if($codesErreur != "")
			{
				session_name("UTR");
				session_start();
				$_SESSION["Post"] = $_POST;
				$_SESSION["Erreur"] = 1;

				$codesErreur=explode(",",$codesErreur);
				$_SESSION["Codes"]=$codesErreur;

				header("Location: creationManager.php?code=$code&".session_name()."=".session_id());
				exit();
			}
		}

		$requeteInfoJoueur = "	SELECT IdJoueur, Jou_Pseudo
										FROM joueur
										WHERE Jou_CodeInscription = '$code'";
		$resultatInfoJoueur = mysql_query($requeteInfoJoueur) or die("Info Joueur : $requeteInfoJoueur<br>".mysql_error());
		$infoJoueur = mysql_fetch_assoc($resultatInfoJoueur);

//Récupération de l'id du joueur créé pour la suite
		$IdJoueur = $infoJoueur['IdJoueur'];
		$Man_Nom = $infoJoueur['Jou_Pseudo'];

//Détermination du job
		$requeteChoixJob = " SELECT IdJob FROM job WHERE Job_Niveau = '1'";

		$resultatChoixJob = mysql_query($requeteChoixJob) or die("Requete Choix Job : $requeteChoixJob<br>".mysql_error());
		while($infoJob = mysql_fetch_row($resultatChoixJob))
			$IdJob[] = $infoJob[0];

//Création du manager
		$requeteAjouterManager = "	INSERT INTO manager(Man_Nom,
														Man_Sexe,
														Man_Niveau,
														Man_Solde,
														Man_Reputation,
														Man_Chance,
														Man_IdJob,
														Man_IdJoueur)
											VALUES(	'$Man_Nom',
														'$Man_Sexe',
														'1',
														'100',
														'0',
														'0',
														'".$IdJob[array_rand($IdJob,1)]."',
														'$IdJoueur')";
		mysql_query($requeteAjouterManager)or die(mysql_error());

//Récupération de l'id du manager pour la suite
		$IdManager = mysql_fetch_row(mysql_query("SELECT MAX(IdManager) FROM manager"));
		$IdManager = $IdManager[0];

		creerVoiture($IdModeleVoiture, $IdManager);

		$requeteAjouterPilote="	INSERT INTO pilote(	Pil_Nom,
																	Pil_Sexe,
																	Pil_Age,
																	Pil_Reputation,
																	Pil_Solde,
																	Pil_XPShifts,
																	Pil_XPVirage,
																	Pil_XPFreinage,
																	Pil_XPSpe,
																	Pil_Chance,
																	Pil_IdManager)
								VALUES(	'$Man_Nom',
											'$Pil_Sexe',
											'$Pil_Age',
											'0',
											'0',
											'$Pil_XPShifts',
											'$Pil_XPVirage',
											'$Pil_XPFreinage',
											'$Pil_XPSpe',
											'0',
											'$IdManager')";
		mysql_query($requeteAjouterPilote)or die(mysql_error());

		$requeteFinalisationInscription = "	UPDATE joueur
														SET Jou_CodeInscription = NULL
														WHERE IdJoueur = '$IdJoueur'";
		mysql_query($requeteFinalisationInscription);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>UTR : Confirmation Inscription</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
</head>

<body>
<div align="center"> </div>
<div align="center">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="3" align="left"> <div align="left">
<?php
	include("../frame/titre.php");
?>
					</div></td>
				</tr>
				<tr>
					<td width="19%" valign="top">
<?php
	include("../frame/menu.php");
?>
					</td>
					<td width="81%">
						<table width="457" border="0" align="left" cellpadding="0" cellspacing="0">
						<tr>
							<td width="61"><img src="/UTR/design/spacer.gif" width="61" height="64"></td>
							<td width="329">
								<p>Votre voiture et votre pilote ont été créés !</p>
								<p>Vous pouvez maintenant commencer à jouer <a href="accueilJeu.php">en vous loguant</a>.</p>
								<p>Bon jeu !</p>
							</td>
                </tr>
              </table></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td><div align="center">
          <table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>
<?php
	include("../frame/piedpage.php");
?>
              </td>
              <td><img src="/UTR/design/spacer.gif" width="56" height="64"></td>
            </tr>
          </table>

        </div>
</td>
    </tr>
  </table>
</div>
</body>
</html>