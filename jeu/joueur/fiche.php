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

	function msTOkmh($vitesse)
	{
	return($vitesse*3.6);
	}
?>
<html>
<head>
	<title>UTR : Fiche de joueur</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script language="JavaScript">
		function confirmSuppr(form)
		{
			var action=form.action.value;
			//if(action=="Supprimer") confirmation = "Etes-vous sûr de vouloir supprimer ce pilote ?";
			var confirmation = "Etes-vous sûr de vouloir "+action+" ce pilote ?";
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
	if(isset($_GET['IdJoueur']))
	{
		$IdGestionJoueur = $_GET['IdJoueur'];

		$requeteInfoJoueur="	SELECT	IdJoueur AS IdGestionJoueur,
												Jou_Pseudo,
												Jou_Login,
												Jou_Email,
												DATE_FORMAT(Jou_DateInscription,'%d/%m/%Y %H:%i:%s') AS Jou_DateInscription,
												DATE_FORMAT(Jou_DernierLogin,'%d/%m/%Y %H:%i:%s') AS Jou_DernierLogin,
												Jou_CodeInscription
									FROM joueur
									WHERE IdJoueur ='$IdGestionJoueur'";
		$resultatInfoJoueur=mysql_query($requeteInfoJoueur) or die(mysql_error());
		$infoJoueur=mysql_fetch_assoc($resultatInfoJoueur);
?>
<div align="center">
<table border="0" width="80%">
<tr><td>

	<table border="1">
		<tr>
			<th colspan="3">Fiche du joueur n°<?php echo $infoJoueur['IdGestionJoueur'];?></th>
		</tr>
		<tr>
			<th colspan="1">Pseudo :</th>
			<td colspan="2"><?php echo $infoJoueur['Jou_Pseudo'];?></td>
		</tr>
		<tr>
			<th colspan="1">Email :</th>
			<td colspan="2"><a href="mailto:<?php echo $infoJoueur['Jou_Pseudo'];?><<?php echo $infoJoueur['Jou_Email'];?>>"><?php echo $infoJoueur['Jou_Email'];?></a></td>
		</tr>
		<tr>
			<th colspan="1">Date Inscription :</th>
			<td colspan="2"><?php echo $infoJoueur['Jou_DateInscription'] ?></td>
		</tr>
		<tr>
			<th colspan="1">Dernier Login :</th>
			<td colspan="2"><?php echo $infoJoueur['Jou_DernierLogin'] ?></td>
		</tr>
		<tr>
			<th colspan="1">Inscription validée :</th>
			<td colspan="2"><?php echo (empty($infoJoueur['Jou_CodeInscription']))?"Oui":"Non";?></td>
		</tr>
	</table>
	<br>
<?php
		$requeteManagers = "	SELECT 	IdManager AS IdGestionManager,
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
									WHERE Man_IdJoueur = '$IdGestionJoueur'
									AND IdJob = Man_IdJob";
		$resultatManagers = mysql_query($requeteManagers) or die(mysql_error());
?>
	<table border="1">
		<tr>
			<th colspan="32">Managers</th>
		</tr>
		<tr>
			<th>Id</th>
			<th>Nom</th>
			<th>Niveau</th>
			<th>Solde</th>
			<th>Réputation</th>
			<th>Chance</th>
			<th>Job</th>
			<th>Salaire</th>
		</tr>
<?php
	while($infoManager = mysql_fetch_assoc($resultatManagers))
	{
?>
		<tr>
			<td><a href="../manager/fiche.php?IdManager=<?php echo $infoManager['IdGestionManager']?>"><?php echo $infoManager['IdGestionManager']?></a></td>
			<td><a href="../manager/fiche.php?IdManager=<?php echo $infoManager['IdGestionManager']?>"><?php echo $infoManager['Man_Nom']?></a></td>
			<td><?php echo $infoManager['Man_Niveau']?></td>
			<td><?php echo $infoManager['Man_Solde']?> &euro;</td>
			<td><?php echo $infoManager['Man_Reputation']?></td>
			<td><?php echo $infoManager['Man_Chance']?></td>
			<td><?php echo $infoManager['Job_Nom'.$infoManager['Man_Sexe']]?></td>
			<td><?php echo $infoManager['Job_Salaire']?> &euro;</td>
		</tr>
<?php
	}
?>
	</table>
</td><td>

	<table border="1">
		<tr><th>Actions possibles</th></tr>

		<tr>
			<td>
<form action="gestion.php" method="get">
	<input type="hidden" name="IdGestionJoueur" value="<?php echo $infoJoueur['IdGestionJoueur'];?>">
	<input type="submit" name="action" value="Modifier">
</form>
<form action="traitement.php" method="get" onSubmit="return confirmSuppr(this)">
	<input type="hidden" name="reponse" value="Oui">
	<input type="hidden" name="IdGestionJoueur" value="<?php echo $infoJoueur['IdGestionJoueur'];?>">
	<input type="submit" name="action" value="Supprimer">
			</td>
		</tr>
</form>
	</table>

</td></tr></table>

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
