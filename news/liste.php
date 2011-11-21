<?php
	session_name("Joueur");
	session_start();
	if($_SESSION['Man_Niveau'] < 3)
	{
		header("location:../frame/news.php");
	}
	include('../include/connexion.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Gestion des news</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
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
<a href="gestion.php?action=Ajouter">Ajouter une news</a>
<br>
<br>
<hr>
<blockquote>
	News publiées :<br><br>

<table border="0">
<?php
	$requeteNewsPubliees="SELECT IdNews, Nws_Titre, Nws_Date FROM news WHERE Nws_Acceptee = '1' ORDER BY Nws_Date";
	$resultatNewsPubliees = mysql_query($requeteNewsPubliees);
	echo mysql_error();

	while($ligneNewsPubliees=mysql_fetch_assoc($resultatNewsPubliees))
	{
		$annee=substr($ligneNewsPubliees['Nws_Date'],0,2);
		$mois=substr($ligneNewsPubliees['Nws_Date'],2,2);
		$jour=substr($ligneNewsPubliees['Nws_Date'],4,2);
		$heure=substr($ligneNewsPubliees['Nws_Date'],6,2);
		$minute=substr($ligneNewsPubliees['Nws_Date'],8,2);
?>
	<tr>
		<td><a href="fiche.php?IdNews=<?php echo $ligneNewsPubliees["IdNews"]; ?>"><?php echo $ligneNewsPubliees["Nws_Titre"];?></td>
		<td></a> du <?php echo "$jour/$mois/$annee à $heure:$minute";?></td>
	</tr>
<?php
	}
?>
</table>
</blockquote>
<br>
<hr>
<br>
<blockquote>
	News en attente :<br><br>
<table border="0">
<?php
	$requeteNewsAttente="SELECT IdNews, Nws_Titre, Nws_Date FROM news WHERE Nws_Acceptee = '0' ORDER BY Nws_Date";
	$resultatNewsAttente = mysql_query($requeteNewsAttente);
	echo mysql_error();

	while($ligneNewsAttente=mysql_fetch_assoc($resultatNewsAttente))
	{
		$annee=substr($ligneNewsAttente['Nws_Date'],0,2);
		$mois=substr($ligneNewsAttente['Nws_Date'],2,2);
		$jour=substr($ligneNewsAttente['Nws_Date'],4,2);
		$heure=substr($ligneNewsAttente['Nws_Date'],6,2);
		$minute=substr($ligneNewsAttente['Nws_Date'],8,2);
?>
	<tr>
		<td><a href="fiche.php?IdNews=<?php echo $ligneNewsAttente["IdNews"]; ?>"><?php echo $ligneNewsAttente["Nws_Titre"];?></a></td>
		<td> du <?php echo "$jour/$mois/$annee à $heure:$minute";?><br></td>
	</tr>
<?php
	}
?>
</table>
</blockquote>
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

