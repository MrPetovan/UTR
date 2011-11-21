<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdManager']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<html>
<head>
	<title>UTR : Fiche de manager</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script language="JavaScript">
		function confirmSuppr(form)
		{
			var action=form.action.value;
			//if(action=="Supprimer") confirmation = "Etes-vous sûr de vouloir supprimer ce manager ?";
			var confirmation = "Etes-vous sûr de vouloir "+action+" ce manager ?";
			if(confirm(confirmation))
			{
				form.method="POST";
				return true;
			}
			else
			{
				return false;
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
	if(isset($_GET['IdManager']))
	{
		$IdGestionManager = $_GET['IdManager'];

		$requeteInfoManager = "	SELECT	IdManager AS IdGestionManager,
													Man_Nom,
													Man_Sexe,
													Man_Niveau,
													Man_Solde,
													Man_Reputation,
													Man_IdJob,
													Job_NomMasculin,
													Job_NomFéminin,
													Job_Salaire,
													Man_IdJoueur,
													Jou_Pseudo
										FROM manager, job, joueur
										WHERE IdJob = Man_IdJob
										AND IdJoueur = Man_IdJoueur
										AND IdManager ='$IdGestionManager'";
		$resultatInfoManager=mysql_query($requeteInfoManager) or die(mysql_error());
		$infoManager=mysql_fetch_assoc($resultatInfoManager);
?>
<div align="center">
<table border="0" width="80%">
<tr><td>

	<table border="1">
		<tr>
			<th colspan="3">Fiche du manager <?php echo $infoManager['Man_Nom']?></th>
		</tr>
		<tr>
			<th colspan="1">Sexe :</th>
			<td colspan="2"><?php echo $infoManager['Man_Sexe']?></td>
		</tr>
		<tr>
			<th colspan="1">Niveau :</th>
			<td colspan="2"><?php echo $infoManager['Man_Niveau']?></td>
		</tr>
		<tr>
			<th colspan="1">Solde :</th>
			<td colspan="2"><?php echo $infoManager['Man_Solde']?> &euro;</td>
		</tr>
		<tr>
			<th colspan="1">Reputation :</th>
			<td colspan="2"><?php echo $infoManager['Man_Reputation']?></td>
		</tr>
<?php
		if($infoManager['Man_Niveau']==1)
		{
?>
		<tr>
			<th colspan="1">Job :</th>
			<td colspan="2"><?php echo $infoManager['Job_Nom'.$infoManager['Man_Sexe']]?></td>
		</tr>
		<tr>
			<th colspan="1">Salaire :</th>
			<td colspan="2"><?php echo $infoManager['Job_Salaire']?></td>
		</tr>
<?php
		}
?>
<?php
	if($Man_Niveau >= 3)
	{
?>
		<tr>
			<th colspan="1">Joueur :</th>
			<td colspan="2"><a href="../joueur/fiche.php?IdJoueur=<?php echo $infoManager['Man_IdJoueur']?>"><?php echo $infoManager['Jou_Pseudo']?></a></td>
		</tr>
	</table>
</td>
<td>
	<table border="1">
		<tr><th>Actions possibles</th></tr>
		<tr>
			<td>
<form action="gestion.php" method="get">
	<input type="hidden" name="IdGestionManager" value="<?php echo $infoManager['IdGestionManager'];?>">
	<input type="submit" name="action" value="Modifier">
</form>
			</td>
		</tr>
<?php
	}
?>
	</table>
</td>
</tr></table>
<?php
	}
?>
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
