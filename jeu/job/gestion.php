<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Jou_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<html>
<head>
	<title>UTR : Modifier un job</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdJob)
		{
			document.location="gestion.php?action=Modifier&IdJob="+IdJob;
		}
		function verifForm(form)
		{
			return true;
			with(form)
			{
				var chaineErreur = "";

				chaineErreur +=	is_NotNull(Job_NomMasculin.value,"Le nom masculin du job");
				chaineErreur +=	is_NotNull(Job_NomFéminin.value,"Le nom féminin du job");
				chaineErreur +=	is_Number(Job_Niveau.value,'',"Le niveau du job");
				chaineErreur +=	is_Number(Job_Salaire.value,'',"Le salaire du job");

				if (chaineErreur != "")
				{
					alert("Le(s) champ(s) suivant est(sont) incorrect(s) :\n"+chaineErreur);
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
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td>
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td>
<?php
	if(isset($_GET["action"])&& !isset($_SESSION["Erreur"]))
	{

		if($_GET["action"]=="Ajouter")
		{
			$infoJob="";
		}
		else
		{
			$IdJob = $_GET["IdJob"];

			$requeteInfoJob="	SELECT	IdJob,
												Job_NomMasculin,
												Job_NomFéminin,
												Job_Niveau,
												Job_Salaire
									FROM job
									WHERE IdJob = '$IdJob'";
			$resultatInfoJob=mysql_query($requeteInfoJob)or die(mysql_error());
			$infoJob=mysql_fetch_assoc($resultatInfoJob);
		}
	}
	else
	{
?>
<table align="center"><tr><td><img src="../images/warningpetit.gif"></td><td>Il y a une ou plusieurs erreurs dans le formulaire :</td></tr></table>
<div align="center" class="erreur"><BR>
  <?php
		$infoJob = $_SESSION["Post"];

		foreach ($_SESSION["Codes"] as $codeErreur)
		{
			$requeteMessageErreur="	SELECT MsgEr_Message
											FROM message_erreur
											WHERE MsgEr_Code = $codeErreur";
			$resultatMessageErreur=mysql_query($requeteMessageErreur)or die(mysql_error());
			$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
			echo ($messageErreur["MsgEr_Message"]);?>
  <BR>
  <?php
 		}
 		unset($_SESSION['Erreur']);
 		unset($_SESSION['Codes']);
 		unset($_SESSION['Post']);
	}
?>
</div>
<?php
	if($_GET['bis']==1)
	{
?>
<br>
Insertion effectuée avec succès !
<br>
<?php
	}
?>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdJob" value="<?php echo $infoJob["IdJob"]; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
	<table border="1">
		<tr>
			<th colspan="3"><?php echo ($_GET['action']=="Ajouter")?"Ajouter un job":"Modifier un job"?></td>
		</tr>
		<tr>
			<th colspan="2">Niveau<font color="#FF0000">*</font> :</th>
			<td colspan="1"><input type="text" size="3" name="Job_Niveau" value="<?php echo $infoJob['Job_Niveau']?>"></td>
		</tr>
		<tr>
			<th rowspan="2">Nom</th>
			<th>Masculin<font color="#FF0000">*</font> :</th>
			<td colspan="1"><input type="text" size="30" name="Job_NomMasculin" value="<?php echo $infoJob['Job_NomMasculin']?>"></td>
		</tr>
		<tr>
			<th>Féminin<font color="#FF0000">*</font> :</th>
			<td colspan="1"><input type="text" size="30" name="Job_NomFéminin" value="<?php echo $infoJob['Job_NomFéminin']?>"></td>
		</tr>
		<tr>
			<th colspan="2">Salaire<font color="#FF0000">*</font> :</th>
			<td colspan="1"><input type="text" size="5" name="Job_Salaire" value="<?php echo $infoJob['Job_Salaire']?>"> &euro;</td>
		</tr>
<table>
<?php
	if($_GET['action'] == "Ajouter")
	{
?>
		<tr>
			<td><input type="radio" name="Ajouter_Bis" value="false" checked> Retourner à la liste
			</td>
			<td><input type="radio" name="Ajouter_Bis" value="true"> Ajouter un autre job
			</td>
		</tr>
<?php
	}
?>
		<tr>
			<td align="center" colspan="3"><br>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]; ?>"><br>
			</td>
			<td align="center" colspan="3"><br>
				<input type="reset" value="<?php echo ($_GET['action']=="Ajouter")?"Effacer saisie":"Annuler les modifications";?>"><br>
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
	include("../../frame/piedpage.php");
?>
		</td>
	</tr>
</table>
</body>
</html>