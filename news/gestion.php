<?php
	session_name("Joueur");
	session_start();
	error_reporting(E_ALL ^ E_NOTICE);
	include('../include/connexion.inc.php');

	if($_SESSION['IdJoueur']=="")
	{
		header("location:erreur.php");
		exit;
	}
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<html>
<head>
	<title>UTR : Ajouter une news</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

	<link href="../style/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="JavaScript" src="../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdNews)
		{
			document.location="gestion.php?action=Modifier&IdNews="+IdNews;
		}
		function verifForm(form)
		{
			with(form)
			{
				var chaineErreur = "";

				if(is_Null(Nws_NomPosteur.value)) chaineErreur += is_NotNull(Nws_NomPosteur.value,"Nom de l'auteur");

				chaineErreur +=	is_NotNull(Nws_Titre.value,"Titre de la news")+
								is_NotNull(Nws_Texte.value,"Texte de la news");

				if (chaineErreur != "")
				{
					alert("Le(s) champ(s) suivant(s) est(sont) incorrect(s) :\n"+chaineErreur);
					return false;
				}
				else
				{
					form.verificationJs.value = true;
					return true;
				}
			}
		}
	</script>
</head>
<body>
<table width="100%">
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
		<td>
<?php
	if(isset($_GET["action"])&& !isset($_GET["SESS_Erreur"]))
	{
		if($_GET["action"]=="Ajouter")
		{
			$infoNews="";
			$NomPosteur = mysql_fetch_row(mysql_query("SELECT Man_Nom FROM manager WHERE IdManager='$IdManager'"));
			$NomPosteur = $NomPosteur[0];
		}
		else
		{
			$IdNews = $_GET["IdNews"];

			$requeteInfoNews= "	SELECT IdNews, Nws_Titre, Nws_Texte, Nws_IdPosteur, Jou_Pseudo, Nws_NomPosteur, Nws_Date, Nws_Acceptee
										FROM news
										LEFT JOIN joueur ON IdJoueur = Nws_IdPosteur
										WHERE IdNews = '$IdNews'";
			$resultatInfoNews=mysql_query($requeteInfoNews) or die(mysql_error());
			$infoNews=mysql_fetch_assoc($resultatInfoNews);
			$NomPosteur = $infoNews['Nws_NomPosteur'];
		}
	}
	else
	{
?>
<table align="center"><tr><td><img src="../images/warningpetit.gif"></td><td>Il y a une ou plusieurs erreurs dans le formulaire :</td></tr></table>
<div align="center" class="erreur"><BR>
  <?php
		$infoNews = $_SESSION["Post"];

		foreach ($_SESSION["Codes"] as $codeErreur) {
			$requeteMessageErreur="	SELECT MsgEr_Message
									FROM message_erreur
									WHERE MsgEr_Code = $codeErreur";
			$resultatMessageErreur=mysql_query($requeteMessageErreur);
	echo mysql_error();
			$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
			echo ($messageErreur["MsgEr_Message"]);?>
  <BR>
  <?php
 		}


	}
?>
</div>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdNews" value="<?php echo $infoNews["IdNews"]; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
<div align="center">
	<table width="75%" border="1">
		<tr class="titre">
			<td colspan="3"><center>
			<div class="font-titre"><?php echo ($_GET["action"]=="Ajouter")? "Ajouter une news":"Modifier les informations d'une news";?></div>
			</center></td>
		</tr>
		<tr>
			<td>Auteur<font color="#FF0000">*</font> :</td>
			<td colspan="2">
				<input type="hidden" name="Nws_NomPosteur" value="<?php echo $NomPosteur?>">
				<?php echo $NomPosteur?>
			</td>
		</tr>
		<tr>
			<td>Titre<font color="#FF0000">*</font> :</td>
			<td colspan="2"><input type="text" name="Nws_Titre" maxlength="50" size="50" value="<?php echo $infoNews["Nws_Titre"];?>"></td>
		</tr>
		<tr>
			<td>Texte<font color="#FF0000">*</font> :</td>
			<td colspan="2"><textarea name="Nws_Texte" cols="75" rows="10"><?php echo $infoNews['Nws_Texte'];?></textarea></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><br>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]; ?>"><br>
			</td>
			<td align="center"><br>
		<?php echo ($_GET["action"]=="Ajouter")? "<input type=\"reset\" value=\"Effacer saisie\">":
			"<input type=\"button\" onclick=\"annulModif(".$infoNews['IdNews'].")\" value=\"Annuler les modifications\">";?><br>
			</td>
		</tr>
	</table>
</div>
</form>
<div align="center"><font color="#FF0000">*</font> : Champ obligatoire
</div>
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
