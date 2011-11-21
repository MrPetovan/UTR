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
	<title>UTR : Ajouter/Modifier un sponsor</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdSponsor)
		{
			document.location="gestion.php?action=Modifier&IdSponsor="+IdSponsor;
		}
		function verifForm(form)
		{
			return true;
			with(form)
			{
				var chaineErreur = "";

				chaineErreur +=	is_Number(Spon_Niveau.value,'',"Le niveau du sponsor");
				chaineErreur +=	is_Number(Spon_Salaire.value,'',"Le salaire du sponsor");

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
			$infoSponsor="";
		}
		else
		{
			$IdSponsor = $_GET["IdSponsor"];

			$requeteInfoSponsor = "	SELECT	IdSponsor,
														Spon_IdMarque,
														Marq_Libelle,
														Spon_Niveau,
														Spon_Salaire
											FROM sponsor, marque
											WHERE IdMarque = Spon_IdMarque
											AND IdSponsor = '$IdSponsor'";
			$resultatInfoSponsor=mysql_query($requeteInfoSponsor)or die(mysql_error());
			$infoSponsor=mysql_fetch_assoc($resultatInfoSponsor);
		}
	}
	else
	{
?>
<table align="center"><tr><td><img src="../images/warningpetit.gif"></td><td>Il y a une ou plusieurs erreurs dans le formulaire :</td></tr></table>
<div align="center" class="erreur"><BR>
  <?php
		$infoSponsor = $_SESSION["Post"];

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
<input type="hidden" name="IdSponsor" value="<?php echo $infoSponsor['IdSponsor']?>">
<input type="hidden" name="action" value="<?php echo $_GET['action']?>">
<input type="hidden" name="verificationJs" value="false">
	<table border="1">
		<tr>
			<th colspan="2"><?php echo ($_GET['action']=="Ajouter")?"Ajouter un sponsor":"Modifier un sponsor"?></td>
		</tr>
		<tr>
			<th>Niveau<font color="#FF0000">*</font> :</th>
			<td><input type="text" size="3" name="Spon_Niveau" value="<?php echo $infoSponsor['Spon_Niveau']?>"></td>
		</tr>
		<tr>
			<th>Marque</th>
			<td><select size="1" name="Spon_IdMarque">
<?php
	$requeteMarques = "	SELECT IdMarque, Marq_Libelle FROM marque ORDER BY Marq_Libelle";
	$resultatMarques = mysql_query($requeteMarques)or die(mysql_error());
	while($infoMarque = mysql_fetch_assoc($resultatMarques))
	{
?>
				<option value="<?php echo $infoMarque['IdMarque']?>"<?php echo ($infoMarque['IdMarque']==$infoSponsor['Spon_IdMarque'])?" disabled":""?>><?php echo $infoMarque['Marq_Libelle']?></option>
<?php
	}
?>
			</td>
		</tr>
		<tr>
			<th>Salaire<font color="#FF0000">*</font> :</th>
			<td><input type="text" size="5" name="Spon_Salaire" value="<?php echo $infoSponsor['Spon_Salaire']?>"> &euro;</td>
		</tr>
<table>
<?php
	if($_GET['action'] == "Ajouter")
	{
?>
		<tr>
			<td><input type="radio" name="Ajouter_Bis" value="false" checked> Retourner à la liste
			</td>
		<td><input type="radio" name="Ajouter_Bis" value="true"> Ajouter un autre sponsor
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