<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	include('../../include/Xp.php');
	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Jou_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<html>
<head>
	<title>UTR : Changer de job</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
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
	$requeteInfoManager = "	SELECT Man_IdJob, Man_Reputation, Man_Sexe
									FROM manager
									WHERE IdManager = '$IdManager'";
	$resultatInfoManager = mysql_query($requeteInfoManager) or die("Requete Info Manager :<br>$requeteInfoManager<br><br>".mysql_error());
	$infoManager = mysql_fetch_assoc($resultatInfoManager);

	$requeteIdJobs= "	SELECT IdJob
							FROM job
							WHERE IdJob != '".$infoManager['Man_IdJob']."'
							AND Job_Niveau = '".niveauDouble($infoManager['Man_reputation'],1000)."'";
	$resultatIdJobs = mysql_query($requeteIdJobs) or die("Requete Id Jobs :<br>$requeteIdJobs<br><br>".mysql_error());

	while($job = mysql_fetch_row($resultatIdJobs))
		$IdJobTemp[] = $job[0];

	$IdJobTemp = array_flip($IdJobTemp);

	$IdJob = array_rand($IdJobTemp,2);

	$IdJob[2] = $infoManager['Man_IdJob'];

	$requeteInfoJob = "	SELECT	IdJob,
											Job_NomMasculin,
											Job_NomFéminin,
											Job_Niveau,
											Job_Salaire
									FROM job
									WHERE (IdJob = '".$IdJob[0]."'
									OR IdJob = '".$IdJob[1]."'
									OR IdJob = '".$IdJob[2]."')";
	$resultatInfoJob=mysql_query($requeteInfoJob)or die(mysql_error());
	$infoJob[0]=mysql_fetch_assoc($resultatInfoJob);
	$infoJob[1]=mysql_fetch_assoc($resultatInfoJob);
	$infoJob[2]=mysql_fetch_assoc($resultatInfoJob);
?>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="action" value="Changer">
<input type="hidden" name="verificationJs" value="true">
	<table border="0" width="300">
		<tr>
			<th background="/UTR/design/nav.jpg" height=34 colspan="4"><div align="left">&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;
                                &nbsp; &nbsp;&nbsp;<strong>Choisir un nouveau job
                                : </strong></div></td>
		</tr>
		<tr>
			<th>Marque :</th>
			<td><div align="center"><?php echo $infoJob[0]["Job_Nom".$infoManager['Man_Sexe']]?></div></td>
			<td><div align="center"><?php echo $infoJob[1]["Job_Nom".$infoManager['Man_Sexe']]?></div></td>
			<td><div align="center"><?php echo $infoJob[2]["Job_Nom".$infoManager['Man_Sexe']]?></div></td>
		</tr>
		<tr>
			<th>Salaire :</th>
			<td><div align="center"><?php echo $infoJob[0]['Job_Salaire']?> &euro;</div></td>
			<td><div align="center"><?php echo $infoJob[1]['Job_Salaire']?> &euro;</div></td>
			<td><div align="center"><?php echo $infoJob[2]['Job_Salaire']?> &euro;</div></td>
		</tr>
		<tr>
			<th></th>
			<td><div align="center"><input type="radio" name="IdJob" value="<?php echo $infoJob[0]['IdJob']?>"<?php echo ($infoManager['Man_IdJob']==$infoJob[0]['IdJob'])?" checked":""?>></div></td>
			<td><div align="center"><input type="radio" name="IdJob" value="<?php echo $infoJob[1]['IdJob']?>"<?php echo ($infoManager['Man_IdJob']==$infoJob[1]['IdJob'])?" checked":""?>></div></td>
			<td><div align="center"><input type="radio" name="IdJob" value="<?php echo $infoJob[2]['IdJob']?>"<?php echo ($infoManager['Man_IdJob']==$infoJob[2]['IdJob'])?" checked":""?>></div></td>
		</tr>
		<tr>
			<th colspan="4"><br>
				<input type="submit" value="Changer de job"><br>
			</th>
		</tr>
	</table>
</div>
</form>
		</td>
	</tr>
	<tr>
		<td colspan="2"><br><div align="center">
<?php
	include("../../frame/piedpage.php");
?></div>
		</td>
	</tr>
</table>
</body>
</html>