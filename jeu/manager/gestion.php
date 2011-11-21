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
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];

	if($Man_Niveau < 3)
	{
		header("location:../../index.php");
		exit;
	}
?>
<html>
<head>
	<title>UTR : Modifier un manager</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdGestionManager)
		{
			document.location="gestion.php?action=Modifier&IdGestionManager="+IdGestionManager;
		}
function verifForm(form)
		{
			return true;
			with(form)
			{
				var chaineErreur = is_NotNull(Man_Nom.value,"Le nom");
				chaineErreur += is_Number(Man_Solde.value,'',"Le solde");
				chaineErreur += is_Number(Man_Reputation.value,'',"La réputation");
				chaineErreur += is_Number(Man_Chance.value,'',"La chance");

				if (chaineErreur != "")
				{
					alert("Il y a une ou plusieurs erreurs de saisie :\n"+chaineErreur);
					return false;
				}
				else
				{
					verificationJs.value = true;
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
			$infoManager="";
		}
		else
		{
			$IdGestionManager = $_GET["IdGestionManager"];

			$requeteInfoManager="	SELECT	IdManager AS IdGestionManager,
													Man_Nom,
													Man_Sexe,
													Man_Niveau,
													Man_Solde,
													Man_Reputation,
													Man_Chance,
													Man_IdJob,
													Job_NomMasculin,
													Job_NomFéminin,
													Job_Salaire
										FROM manager, job
										WHERE IdJob = Man_IdJob
										AND IdManager ='$IdGestionManager'";
			$resultatInfoManager=mysql_query($requeteInfoManager) or die(mysql_error());
			$infoManager=mysql_fetch_assoc($resultatInfoManager);
		}
	}
	else
	{
?>
Il y a une ou plusieurs erreurs dans le formulaire :<BR>
  <?php
		$infoManager = $_SESSION["Post"];

		foreach ($_SESSION["Codes"] as $codeErreur)
		{
			$requeteMessageErreur="	SELECT Err_Message
											FROM erreur
											WHERE IdErreur = $codeErreur";
			$resultatMessageErreur=mysql_query($requeteMessageErreur)or die("Requete Message Erreur : $requeteMessageErreur<br>".mysql_error());
			$messageErreur=mysql_fetch_assoc($resultatMessageErreur);

			echo "<br>".$messageErreur["Err_Message"];
 		}
		unset($_SESSION['Erreur']);
		unset($_SESSION['Post']);
	}
?>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdGestionManager" value="<?php echo $infoManager['IdGestionManager']; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
<div align="center">
	<table border="1">
		<tr>
			<th colspan="2">Modifier les informations du manager n°<?php echo $infoManager['IdGestionManager'];?></td>
		</tr>
		<tr>
			<th>Nom<font color="#FF0000">*</font> :</th>
			<td><input type="text" name="Man_Nom" value="<?php echo $infoManager['Man_Nom'];?>"></td>
		</tr>
		<tr>
			<th>Sexe :</th>
			<td><input type="radio" name="Man_Sexe" value="Masculin"<?php echo ($infoManager['Man_Sexe']=='Masculin')?" checked":""?>>Masculin<input type="radio" name="Man_Sexe" value="Féminin"<?php echo ($infoManager['Man_Sexe']=='Féminin')?" checked":""?>>Féminin</td></td>
		</tr>
		<tr>
			<th>Niveau :</th>
			<td>
<?php
	if($Man_Niveau >= 3 && $infoManager['Man_Niveau'] < 3)
	{
?>
				<select name="Man_Niveau">
					<option value="1"<?php echo ($infoManager['Man_Niveau']==1)?" checked":""?>>Pilote</option>
					<option value="2"<?php echo ($infoManager['Man_Niveau']==2)?" checked":""?>>Manager</option>
<?php
		if($Man_Niveau > 3)
		{
?>
					<option value="3"<?php echo ($infoManager['Man_Niveau']==3)?" checked":""?>>Modérateur</option>
<?php
		}
?>
				</select>
<?php
	}
	else echo($infoManager['Man_Niveau'] == 3)?"Modérateur":"Administrateur";
?>
			</td>
		</tr>
		<tr>
			<th>Solde :</th>
			<td><input type="text" name="Man_Solde" value="<?php echo $infoManager['Man_Solde']?>"> &euro;</td>
		</tr>
		<tr>
			<th>Reputation :</th>
			<td><input type="text" name="Man_Reputation" value="<?php echo $infoManager['Man_Reputation'];?>"></td>
		</tr>
		<tr>
			<th>Chance :</th>
			<td><input type="text" name="Man_Chance" value="<?php echo $infoManager['Man_Chance'];?>"></td>
		</tr>
		<tr>
			<th>Job :</th>
			<td>
				<select name="Man_IdJob">
<?php
	$requeteJobs = "	SELECT IdJob, Job_NomMasculin, Job_NomFéminin, Job_Salaire
							FROM job
							ORDER BY Job_Niveau";
	$resultatJobs = mysql_query($requeteJobs);
	while($infoJob = mysql_fetch_assoc($resultatJobs))
	{
?>
					<option value="<?php echo $infoJob['IdJob']?>"<?php echo ($infoManager['Man_IdJob']==$infoJob['IdJob'])?" checked":""?>><?php echo $infoJob['Job_Nom'.$infoManager['Man_Sexe']]." ( ".$infoJob['Job_Salaire']." à )"?></option>
<?php
	}
?>
				</select>
			</td>
		</tr>
	</table>
<br>
	<table>
		<tr>
			<td align="center" colspan="3"><br>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]; ?>"><br>
			</td>
			<td align="center" colspan="3"><br>
				<input type="button" onclick="annulModif(<?php echo $infoManager['IdGestionManager']?>)" value="Annuler les modifications"><br>
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
