<?php
	session_name("Joueur");
	session_start();
	if($_SESSION['Man_Niveau'] < 3)
	{
		header("location:../frame/news.php");
	}
	include('../include/connexion.inc.php');
?>
<html>
<head>
	<title>UTR : Fiche de news</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />

	<script language="JavaScript">
		function confirmSuppr(form)
		{
			var confirmation;
			var action=form.action.value;
			if(action=="Supprimer") confirmation = "Etes-vous sûr de vouloir supprimer cette news ?";
			else confirmation = (action+" cette news ?");
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
	if(isset($_GET['IdNews']))
	{
		$IdNews = $_GET['IdNews'];

		include('../include/connexion.inc.php');

		$requeteInfoNews= "	SELECT IdNews, Nws_Titre, Nws_Texte, Nws_IdPosteur, Jou_Pseudo, Nws_NomPosteur, Nws_Date, Nws_Acceptee
							FROM news
							LEFT JOIN joueur ON IdJoueur = Nws_IdPosteur
							WHERE IdNews = '$IdNews'";
		$resultatInfoNews=mysql_query($requeteInfoNews);
	echo mysql_error();
		$news=mysql_fetch_assoc($resultatInfoNews);
		$annee=substr($news['Nws_Date'],0,2);
		$mois=substr($news['Nws_Date'],2,2);
		$jour=substr($news['Nws_Date'],4,2);
		$heure=substr($news['Nws_Date'],6,2);
		$minute=substr($news['Nws_Date'],8,2);
?>
<br>
<br>
<br>
<div align="center">
	<table border="1" width="90%">
		<tr>
			<td colspan="2"><?php echo $news['Nws_Titre'];?></td>
		</tr>
		<tr>
			<td align="left">par <?php echo ($news['Jou_Pseudo']!='')?$news['Jou_Pseudo']:$news['Nws_NomPosteur'];?></td>
			<td align="right">le <?php echo "$jour/$mois/$annee à $heure:$minute";?></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><?php echo nl2br($news['Nws_Texte'])?>
			</td>
		</tr>
	</table>
	<table>
<form action="gestion.php" method="get">
<input type="hidden" name="IdNews" value="<?php echo $news['IdNews']; ?>">
		<tr>
			<td><br><br>
<input name="action" type="submit" value="Modifier">
</form>
			</td>
			<td>
<form action="traitement.php" method="get" onSubmit="return confirmSuppr(this)">
<input type="hidden" name="reponse" value="Oui">
<input type="hidden" name="IdNews" value="<?php echo $news['IdNews']; ?>"><br><br>
<input type="submit" name="action" value="Supprimer">
<input type="submit" name="action" value="<?php echo ($news['Nws_Acceptee']=='0')?"Accepter":"Retirer";?>">
</form>
			</td>
		</tr>
	</table>
<?php
	}
?>
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

