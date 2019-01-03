<?php
	session_name("Joueur");
	session_start();
	$Man_Niveau = $_SESSION['Man_Niveau'];
	if($Man_Niveau < 3)
	{
		header("location:../frame/news.php");
	}
	include('../include/verif.php');
	include('../include/connexion.inc.php');
	if (isset($_GET["action"]))
	{
		if($_GET["action"]=="Supprimer")
		{
?>
<!--
Nom : nemws\traitement.php
Fonction : Fichier de traitement des news
Version : 1.0
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Confirmation de suppression</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--	<link rel=stylesheet type="text/css" href="../style/style.css">-->
</head>
<body>
table width="100%">
	<tr>
		<td colspan="3">
<?php
	include("../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td>
<?php
	include("../frame/menu.php");
?>
		</td>
	</tr>
	<tr>
		<td>
		<form method="POST" action="traitement.php">
			<input type="hidden" name="IdNews" value="<?php echo $_GET["IdNews"]; ?>">
			<input type="hidden" name="action" value="Supprimer">
			<table width="75%" border="1" align="center">
				<tr>
					<td><img src="../images/warningpetit.gif">
					</td>
					<td></td>
					<td align="center">
<?php
				$codeErreur="101";
				$requeteMessageErreur="	SELECT MsgEr_Message
										FROM message_erreur
										WHERE MsgEr_Code = $codeErreur";
				$resultatMessageErreur=mysql_query($requeteMessageErreur)or die(mysql_error());
				$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
				//echo ($messageErreur["MsgEr_Message"]." \n");
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
	<tr>
		<td colspan="2">
<?php
	include("../frame/piedpage.php");
?>
		</td>
	</tr>
</table>
</body>
</html>

<?php
		}
	}

	if (isset($_POST["action"]))
	{
		$IdNews = $_POST["IdNews"];
		$Nws_NomPosteur = addslashes(trim($_POST['Nws_NomPosteur']));
		$Nws_Titre = addslashes(trim($_POST['Nws_Titre']));
		$Nws_Texte = addslashes(trim($_POST['Nws_Texte']));

		$verificationJs=$_POST['verificationJs'];

	//	print_r($_POST);
//requête supprimant une news
		if($_POST["action"]=="Supprimer")
		{
			if($_POST["reponse"]=="Oui")
			{
				$requeteSupprimerNews="	DELETE FROM news
										WHERE IdNews=".$IdNews;
				mysql_query($requeteSupprimerNews);

				header("Location: liste.php");
				exit;
			}
			else
			{
				header("Location: fiche.php?IdNews=".$IdNews);
				exit;
			}
		}
		if($_POST["action"]=="Accepter" || $_POST["action"]=="Retirer")
		{
			$IdNews = $_POST['IdNews'];
			$choix = ($_POST['action']=="Accepter")?"1":"0";
			$requeteAcceptNews = "	UPDATE news
										SET Nws_Acceptee ='$choix'
									WHERE IdNews= '$IdNews'";
			mysql_query($requeteAcceptNews);
			header("location:liste.php");
			exit;
		}
		$codesErreur=",";
//Gestion des erreurs de saisie si la vérification n'a pas été faite en JavaScript
/*		if($verificationJs == "false");
		{
			$codesErreur .=	is_NotNull($Bat_Numero,"102").",";
			if(!is_Null($Bat_CoordX1)) $codesErreur .= is_Number($Bat_CoordX1,"","103").",";

			$codesErreur = preg_replace("/,{2,}/",",",$codesErreur);
			$codesErreur = substr($codesErreur,1,-1);
		}
		if($codesErreur != "")
		{
			//session_name("SESS_Erreur");
			session_start();
			$_SESSION["Post"] = $_POST;
			$_SESSION["Erreur"] = 1;

			$codesErreur=explode(",",$codesErreur);
			$_SESSION["Codes"]=$codesErreur;
			$action=$_POST["action"];

			//print_r($_POST);

			header("Location: gestion.php?IdNews=".$IdNews."&".session_name()."=".session_id()."&action=$action");
			exit();
		}*/

		//requète permettant la modification d'un bêtiment
		if($_POST["action"]=="Modifier")
		{
			$requeteModifierNews = "UPDATE news
									SET Nws_NomPosteur = '$Nws_NomPosteur',
										Nws_Titre = '$Nws_Titre',
										Nws_Texte = '$Nws_Texte'
									WHERE IdNews='".$IdNews."'";
			mysql_query($requeteModifierNews);
		//Redirection vers la fiche
			header("Location: fiche.php?IdNews=".$IdNews);
		exit;
		}

//pour Ajouter un bâtiment
		if($_POST["action"]=="Ajouter")
		{
			$requeteAjouterNews = "	INSERT INTO news(	Nws_Titre,
														Nws_Texte,
														Nws_Date,
														Nws_Acceptee,
														Nws_NomPosteur)
									VALUES(	'$Nws_Titre',
											'$Nws_Texte',
											NOW(),";
			$requeteAjouterNews .= ($Man_Niveau >= 3)?"'1',":"'0',";
			$requeteAjouterNews .= "'$Nws_NomPosteur')";

			mysql_query($requeteAjouterNews)or die("Ajouter News : $requeteAjouterNews\n".mysql_error());
		//Redirection vers les news
//			exit;

			if($Man_Niveau >= 3)

			header("Location: ../frame/news.php");
			exit;
		}
	}
?>