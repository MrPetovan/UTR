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
	<title>UTR : Changer de sponsor</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
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
	$requeteInfoManager = "	SELECT Man_IdSponsor, Man_Reputation
									FROM manager
									WHERE IdManager = '$IdManager'";
	$resultatInfoManager = mysql_query($requeteInfoManager) or die("Requete Info Manager :<br>$requeteInfoManager<br><br>".mysql_error());
	$infoManager = mysql_fetch_assoc($resultatInfoManager);

	$requeteIdSponsors="	SELECT IdSponsor
								FROM sponsor
								WHERE IdSponsor != '".$infoManager['Man_IdSponsor']."'
								AND Spon_Niveau = '".niveauDouble($infoManager['Man_reputation'],1000)."'";
	$resultatIdSponsors = mysql_query($requeteIdSponsors) or die("Requete Id Sponsors :<br>$requeteIdSponsors<br><br>".mysql_error());

	while($sponsor = mysql_fetch_row($resultatIdSponsors))
		$IdSponsorTemp[] = $sponsor[0];

	$IdSponsorTemp = array_flip($IdSponsorTemp);

	$IdSponsor = array_rand($IdSponsorTemp,2);

	$IdSponsor[2] = $infoManager['Man_IdSponsor'];

	$requeteInfoSponsor = "	SELECT	IdSponsor,
												Spon_IdMarque,
												Marq_Libelle,
												Spon_Niveau,
												Spon_Salaire
									FROM sponsor, marque
									WHERE IdMarque = Spon_IdMarque
									AND (IdSponsor = '".$IdSponsor[0]."'
									OR IdSponsor = '".$IdSponsor[1]."'
									OR IdSponsor = '".$IdSponsor[2]."')";
	$resultatInfoSponsor=mysql_query($requeteInfoSponsor)or die(mysql_error());
	$infoSponsor[0]=mysql_fetch_assoc($resultatInfoSponsor);
	$infoSponsor[1]=mysql_fetch_assoc($resultatInfoSponsor);
	$infoSponsor[2]=mysql_fetch_assoc($resultatInfoSponsor);
?>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="action" value="Changer">
<input type="hidden" name="verificationJs" value="true">
	<table border="1">
		<tr>
			<th colspan="4">Choisir un nouveau sponsor</td>
		</tr>
		<tr>
			<th>Marque :</th>
			<td><?php echo $infoSponsor[0]['Marq_Libelle']?></td>
			<td><?php echo $infoSponsor[1]['Marq_Libelle']?></td>
			<td><?php echo $infoSponsor[2]['Marq_Libelle']?></td>
		</tr>
		<tr>
			<th>Salaire :</th>
			<td><?php echo $infoSponsor[0]['Spon_Salaire']?> &euro;</td>
			<td><?php echo $infoSponsor[1]['Spon_Salaire']?> &euro;</td>
			<td><?php echo $infoSponsor[2]['Spon_Salaire']?> &euro;</td>
		</tr>
		<tr>
			<th></th>
			<td><input type="radio" name="IdSponsor" value="<?php echo $infoSponsor[0]['IdSponsor']?>"<?php echo ($infoManager['Man_IdSponsor']==$infoSponsor[0]['IdSponsor'])?" checked":""?>></td>
			<td><input type="radio" name="IdSponsor" value="<?php echo $infoSponsor[1]['IdSponsor']?>"<?php echo ($infoManager['Man_IdSponsor']==$infoSponsor[1]['IdSponsor'])?" checked":""?>></td>
			<td><input type="radio" name="IdSponsor" value="<?php echo $infoSponsor[2]['IdSponsor']?>"<?php echo ($infoManager['Man_IdSponsor']==$infoSponsor[2]['IdSponsor'])?" checked":""?>></td>
		</tr>
		<tr>
			<th colspan="4"><br>
				<input type="submit" value="Changer de sponsor"><br>
			</th>
		</tr>
	</table>
</div>
</form>
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