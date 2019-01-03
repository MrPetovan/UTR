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
	$IdManager = $_SESSION['IdManager'];
	if($Man_Niveau < 3)
	{
		header("location:../../index.php");
		exit;
	}
	$IdTypePiece = (isset($_GET['IdTypePiece']))?$_GET['IdTypePiece']:"1";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Liste Modèles Pièce</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<style>
	.caseInfo
	{
		background-color: #FFFFFF;
		border-style: none;
	}
	td.normal
	{
		background-color : #0080FF;
		color : #000000;
	}
	td.over
	{
		background-color : #FF0000;
		color : #000000;
	}
	td.select
	{
		background-color : #00FF00;
		color : #000000;
	}
	</style>
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
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
			if(td[i].id==indice)
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
		if(td[i].id==indice)
		{
			td[i].className='select';
			td[i].onmouseout="";
			td[i].onmouseover="";
		}

}
function changerFormulaire(indice)
{
	for(var i=1; i<= 21 ; i++)
		if(i != indice) cacherElements(i);
		else montrerElement(i);
}
	</script>
</head>
<body onLoad="changerFormulaire(<?php echo $IdTypePiece?>)">
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
		<td valign="top">
<div align="center">
<table border="0" width="90%">
	<tr>
		<th colspan="18">Liste des modèles de pièce</th>
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
<?php
	$resultatTypesPiece = mysql_query("SELECT IdTypePiece, TypPi_Libelle FROM type_piece");
	while($typePiece = mysql_fetch_assoc($resultatTypesPiece))
	{
?>
<table border="1" id="remove<?php echo $typePiece['IdTypePiece']?>" class="liste">
<tr>
	<th colspan="5">Modèle pour le type : <?php echo $typePiece['TypPi_Libelle']?> | <a href="gestion.php?action=Ajouter&IdTypePiece=<?php echo $typePiece['IdTypePiece']?>">Ajouter un modèle</a></th>
</tr>
<tr>
	<th class="titre">Niveau</th>
	<th class="titre">Modèle</th>
	<th class="titre">Marque</th>
	<th class="titre">Prix</th>
	<th class="titre">Action</th>
</tr>
<?php
		$requeteInfoModelesPiece = "	SELECT	IdModelePiece,
															ModPi_IdMarque,
															Marq_Libelle,
															ModPi_NomModele,
															ModPi_IdTypePiece,
															TypPi_Libelle,
															ModPi_Niveau,
															ModPi_PrixNeuve
												FROM modele_piece, marque, type_piece
												WHERE IdMarque = ModPi_IdMarque
												AND IdTypePiece = ModPi_IdTypePiece
												AND ModPi_IdTypePiece = '".$typePiece['IdTypePiece']."'
												ORDER BY ModPi_IdTypePiece, ModPi_Niveau";
		$resultatInfoModelesPiece = mysql_query($requeteInfoModelesPiece)or die("Requete Info ModelesPiece : $requeteInfoModelesPiece<br>".mysql_error());

//		print_r($infoModelePiece);
		while($infoModelePiece = mysql_fetch_assoc($resultatInfoModelesPiece))
		{
?>
<tr class="piece">
	<td><a href="fiche.php?IdModelePiece=<?php echo $infoModelePiece['IdModelePiece']?>"><?php echo $infoModelePiece['ModPi_Niveau']?></a></td>
	<td><a href="fiche.php?IdModelePiece=<?php echo $infoModelePiece['IdModelePiece']?>"><?php echo $infoModelePiece['ModPi_NomModele']?></a></td>
	<td><?php echo $infoModelePiece['Marq_Libelle']?></td>
	<td><?php echo $infoModelePiece['ModPi_PrixNeuve']?></td>
	<td><a href="../piece/gestion.php?action=Ajouter&IdModelePiece=<?php echo $infoModelePiece['IdModelePiece']?>">Ajouter une pièce</a></td>
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
