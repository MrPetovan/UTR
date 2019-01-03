<?php
	if(isset($_POST))
	{
		include('../include/connexion.inc.php');
		include('../include/verif.php');

		error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

		$Man_Nom = $_POST['Man_Nom'];
		$Jou_Pseudo = $Man_Nom;
		$Jou_Login = $_POST['Jou_Login'];
		$Jou_MotDePasse = $_POST['Jou_MotDePasse'];
		$Jou_MotDePasse2 = $_POST['Jou_MotDePasse2'];
		$Jou_Email = $_POST['Jou_Email'];

		$verificationJs = $_POST['verificationJs'];

		//Vérification de la non-présence du login
		$requetePresenceLogin = "	SELECT IdJoueur FROM joueur, manager WHERE Jou_Login = '$Jou_Login' OR Man_Nom = '$Jou_Login'";
		$resultatPresenceLogin = mysql_query($requetePresenceLogin) or die("Requête Présence Login : $requetePresenceLogin\n".mysql_error());
		$nbResultat = mysql_num_rows($resultatPresenceLogin);

		if($verificationJs == "false")
		{
			$codesErreur=",";

			$codesErreur .= is_NotNull($Jou_Login,"101").",";
			$codesErreur .= is_NotNull($Jou_MotDePasse,"102").",";
			$codesErreur .= is_NotNull($Jou_MotDePasse,"103").",";
			$codesErreur .= is_NotNull($Jou_Email,"105").",";

			if(!is_Null($Jou_MotDePasse) && !is_Null($Jou_MotDePasse2))
				if($Jou_MotDePasse != $Jou_MotDePasse2)
					$codesErreur .= "104,";
			$codesErreur .= is_NotNull($Man_Nom,"201").",";

			$codesErreur = ereg_replace(",{2,}",",",$codesErreur);
			$codesErreur = substr($codesErreur,1,-1);

			if($codesErreur != "")
			{
				session_name("UTR");
				session_start();
				$_SESSION["Post"] = $_POST;
				$_SESSION["Erreur_Inscription"] = 2;

				$codesErreur=explode(",",$codesErreur);
				$_SESSION["Codes"]=$codesErreur;
				$action=$_POST["action"];

				header("Location: inscription.php?".session_name()."=".session_id());
				exit();
			}
		}

		if($nbResultat != 0)
		{
			session_name("UTR");
			session_start();
			$_SESSION["Post"] = $_POST;
			$_SESSION["Erreur_Inscription"] = 1;

			header("Location: inscription.php?".session_name()."=".session_id());
			exit();
		}

		$Jou_CodeInscription = substr(md5($Jou_Login.$Jou_MotDePasse),0,8);

		$requeteAjouterJoueur="	INSERT INTO joueur(	Jou_Pseudo,
													Jou_Login,
													Jou_MotDePasse,
													Jou_Email,
													Jou_DateInscription,
													Jou_CodeInscription)
								VALUES(	'$Jou_Pseudo',
										'$Jou_Login',
										'".crypt($Jou_MotDePasse)."',
										'$Jou_Email',
										NOW(),
										'$Jou_CodeInscription')";
		mysql_query($requeteAjouterJoueur)or die(mysql_error());

		/* destinataire */
		$to  = "$Man_Nom <$Jou_Email>";
		/* sujet */
		$subject = "Confirmation d'inscription à UTR";

		/* message */
		$message = "
		<html>
		<head>
		<title>Confirmation d'inscription à UTR</title>
		</head>
		<body>
		<p>Merci de vous être inscrit à UTR !</p>
		<p>Voici un rappel de vos coordonnées :<p>
		<p>- login : $Jou_Login<br>
		<p>- Mot de passe : $Jou_MotDePasse</p>
		<p>Pour valider votre inscription, cliquez sur <a href=\"/UTR/jeu/creationManager.php?code=$Jou_CodeInscription\">ce lien</a>, ou recopiez cette adresse dans votre navigateur :<br>
		/UTR/jeu/creationManager.php?code=$Jou_CodeInscription</p>
		</body>
		</html>
		";

		/* Pour envoyer un mail au format HTML, vous pouvez configurer le type Content-type. */
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

		/* D'autres en-têtes */
		$headers .= "From: Le Pacha <ben.lort@oreka.com>\r\n";

		/* et hop, à la poste */
		$mailOk = mail($to, $subject, $message, $headers);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>UTR : Confirmation Inscription</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
<?php
	if($mailOk)
	{
?>
								<p>Votre inscription est enregistrée !</p>
								<p>Un mail vous a été envoyé à votre adresse. Il contient un lien permettant d'activer votre compte.</p>
<?php
	}
	else
	{
?>
								<p>Une erreur est survenue !</p>
								<p>Le mail n'a pas pu étre envoyé. Contactez l'administrateur du site qui pourra peut-être faire quelquechose pour vous.</p>
<?php
	}
?>
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