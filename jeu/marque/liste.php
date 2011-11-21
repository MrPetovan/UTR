<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
		exit;
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdModelePiece = $_SESSION['IdModelePiece'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Liste Marque</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../../include/formulaire.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script language="JavaScript">

function cacherElements(indice)
{
	eval("var remove_el=document.getElementById(\"remove"+indice+"\")");
	if(remove_el!=null)
	{
		if (remove_el!=''&&remove_el.length==null)
		{
			remove_el.style.display='none';
		}
		else
		{
			for (i=0;i<remove_el.length;i++)	remove_el[i].style.display='none';
		}

		var td=document.getElementsByTagName("td");
		for(var i=0; i<td.length; i++)
			if(td[i].id != '' && td[i].id == indice)
			{
				td[i].className='normal';
				td[i].onmouseout=function (){this.className='normal'};
				td[i].onmouseover=function (){this.className='over'};
			}
	}
}
function montrerElement(indice)
{
	eval("var remove_el=document.getElementById(\"remove"+indice+"\")");

	if (remove_el!=''&&remove_el.length==null)remove_el.style.display='';
	else
	{
		for (i=0;i<remove_el.length;i++)	remove_el[i].style.display='';
	}
	var td=document.getElementsByTagName("td");
	for(var i=0; i<td.length; i++)
		if(td[i].id != '' && td[i].id == indice)
		{
			td[i].className='select';
			td[i].onmouseout="";
			td[i].onmouseover="";
		}
}
function changerFormulaire(indice)
{
	for(var i=-1; i<= 21 ; i++)
		if(i != indice) cacherElements(i);
		else montrerElement(i);
}
	</script>
</head>
<body onLoad="changerFormulaire(0)">
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
		<td valign="top" align="center">
<table border="0" class="liste">
	<tr class="piece">
		<th colspan="4">Liste des marques</th>
	</tr>
	<tr>
		<td id="0" onClick="changerFormulaire(0)" colspan="4" align="center">Marques de pièces</td>
	</tr>
	<tr>
		<td id="-1" onClick="changerFormulaire(-1)" colspan="4" align="center">Marques de voitures</td>
	</tr>
	<tr>
<?php
	$resultatTypesPiece = mysql_query("SELECT IdTypePiece, TypPi_Libelle FROM type_piece ORDER BY TypPi_Libelle");
	$i=0;
	while($typePiece = mysql_fetch_assoc($resultatTypesPiece))
	{
		if($i==4)
		{
			echo "</tr><tr>";
			$i=0;
		}
?>
		<td id="<?php echo $typePiece['IdTypePiece']?>" onClick="changerFormulaire(<?php echo $typePiece['IdTypePiece']?>)"><?php echo $typePiece['TypPi_Libelle']?></td>
<?php
		$i++;
	}
?>
	</tr>
</table>
<br>
<table border="0" id="remove-1" class="liste">
<tr class="piece">
	<th>Marque de voiture</th>
	<th colspan="2"><a href="gestion.php?action=Ajouter&IdTypePiece=0">Ajouter une marque</a></th>
</tr>
<?php
		$requeteInfoMarquesVoiture = "	SELECT	IdMarque,
																Marq_IdTypePiece,
																Marq_Libelle
													FROM marque
													WHERE Marq_IdTypePiece LIKE '%-1%'
													ORDER BY Marq_Libelle";
		$resultatInfoMarquesVoiture = mysql_query($requeteInfoMarquesVoiture)or die("Requete Info Marque Voiture :<br>$requeteInfoMarquesVoiture<br><br>".mysql_error());

		while($infoMarqueVoiture = mysql_fetch_assoc($resultatInfoMarquesVoiture))
		{
?>
<tr class="piece">
	<td><?php echo $infoMarqueVoiture['Marq_Libelle']?></td>
	<td><a href="gestion.php?action=Modifier&IdMarque=<?php echo $infoMarqueVoiture['IdMarque']?>">Modifier</a></td>
	<td><a href="traitement.php?action=Supprimer&IdMarque=<?php echo $infoMarqueVoiture['IdMarque']?>">Supprimer</a></td>
</tr>
<?php
		}
?>
</table>
<br>
<table border="0" id="remove0" class="liste">
<tr class="piece">
	<th>Marque de pièces</th>
	<th colspan="2"><a href="gestion.php?action=Ajouter&IdTypePiece=0">Ajouter une marque</a></th>
</tr>
<?php
		$requeteInfoMarquesVoiture = "	SELECT	IdMarque,
																Marq_IdTypePiece,
																Marq_Libelle
													FROM marque
													WHERE FIND_IN_SET('0',Marq_IdTypePiece) > 0
													ORDER BY Marq_Libelle";
		$resultatInfoMarquesVoiture = mysql_query($requeteInfoMarquesVoiture)or die("Requete Info Marque Voiture :<br>$requeteInfoMarquesVoiture<br><br>".mysql_error());

		while($infoMarqueVoiture = mysql_fetch_assoc($resultatInfoMarquesVoiture))
		{
?>
<tr class="piece">
	<td><?php echo $infoMarqueVoiture['Marq_Libelle']?></td>
	<td><a href="gestion.php?action=Modifier&IdMarque=<?php echo $infoMarqueVoiture['IdMarque']?>">Modifier</a></td>
	<td><a href="traitement.php?action=Supprimer&IdMarque=<?php echo $infoMarqueVoiture['IdMarque']?>">Supprimer</a></td>
</tr>
<?php
		}
?>
</table>
<?php
	$resultatTypesPiece = mysql_query("SELECT IdTypePiece, TypPi_Libelle FROM type_piece");
	while($typePiece = mysql_fetch_assoc($resultatTypesPiece))
	{
?>
<table border="0" id="remove<?php echo $typePiece['IdTypePiece']?>" class="liste">
<tr class="piece">
	<th>Marques pour le type : <?php echo $typePiece['TypPi_Libelle']?></th>
	<th colspan="2"><a href="gestion.php?action=Ajouter&IdTypePiece=<?php echo $typePiece['IdTypePiece']?>">Ajouter une marque</a></th>
</tr>
<?php
		$requeteInfoMarquesPiece = "	SELECT	IdMarque,
															Marq_Libelle
												FROM marque
												WHERE FIND_IN_SET('".$typePiece['IdTypePiece']."',Marq_IdTypePiece) > 0
												ORDER BY Marq_Libelle";
		$resultatInfoMarquesPiece = mysql_query($requeteInfoMarquesPiece)or die("Requete Info MarquesPiece : $requeteInfoMarquesPiece<br>".mysql_error());

//		print_r($infoMarquePiece);
		while($infoMarquePiece = mysql_fetch_assoc($resultatInfoMarquesPiece))
		{
?>
<tr class="piece">
	<td><?php echo $infoMarquePiece['Marq_Libelle']?></td>
	<td><a href="gestion.php?action=Modifier&IdMarque=<?php echo $infoMarquePiece['IdMarque']?>">Modifier</a></td>
	<td><a href="traitement.php?action=Supprimer&IdMarque=<?php echo $infoMarquePiece['IdMarque']?>">Supprimer</a></td>
</tr>
<?php
		}
?>
</table>
<?php
	}
?>
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
