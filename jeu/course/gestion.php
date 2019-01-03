<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	error_reporting(E_ALL ^ E_NOTICE);
	$IdManager = $_SESSION['IdManager'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdJoueur = $_SESSION['IdJoueur'];

	/*echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";*/

	function msTOkmh($vitesse)
	{
	return($vitesse*3.6);
	}
?>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdCourse)
		{
			document.location="gestion.php?action=Modifier&IdCourse="+IdCourse;
		}
		function verifForm(form)
		{
			with(form)
			{
				var chaineErreur = "";
				chaineErreur += is_NotNull(Cou_Nom.value,"Le nom de la course");
				chaineErreur += is_Date(Cou_Date.value,"La date de la course",1);
				chaineErreur += is_Number(Cou_NbTours.value,'',"Le nombre de tours");
				chaineErreur += is_Number(Cou_PrixInscription.value,'',"Le prix de l'inscription");
				chaineErreur += is_Number(Cou_PrixEngagement.value,'',"Le prix d'engagement");
				chaineErreur += is_Number(Cou_DensiteCirculation.value,'',"La densité de circulation");
				chaineErreur += is_Number(Cou_NbCompetiteursMax.value,'1',"Le nombre maximum de compétiteurs");
				chaineErreur += is_Number(Cou_NiveauMin.value,'',"Le niveau minimum");
				chaineErreur += is_Number(Cou_NiveauMax.value,'',"Le niveau maximum");

				if (chaineErreur != "")
				{
					alert("Le champ suivant est incorrect :\n"+chaineErreur);
					return false;
				}
				else
				{
					form.verificationJs.value = true;
					return true;
				}
			}
		}
		function prixTotal(form)
		{
			with(form)
			{
				Cou_PrixTotal.value=Cou_NbCompetiteursMax.value*Cou_PrixEngagement.value;
			}
		}
		function risquePolice(form)
		{
			with(form)
			{
				Cou_RisquePolice.value=(Cou_NbCompetiteursMax.value/12)*100;
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
		$troncons=$_SESSION['troncons'];
		//unset($_SESSION['troncons']);
		if($_GET["action"]=="Terminer")
		{
			$infoCourse="";
		}
		else
		{
			$IdCourse = $_GET["IdCourse"];

			$requeteInfoCourse = "	SELECT 	IdCourse, Cou_Nom, Cou_Date, Cou_IdType, Cou_NbTours, Cou_PrixEngagement,
									Cou_PrixInscription, Cou_DensiteCirculation, Cou_NbCompetiteursMax, Cou_NiveauMin, Cou_NiveauMax,
									Cou_Commentaires
									FROM course
									WHERE IdCourse = '$IdCourse'";
			$resultatInfoCourse=mysql_query($requeteInfoCourse)or die(mysql_error());
			$infoCourse=mysql_fetch_assoc($resultatInfoCourse);
		}
	}
	else
	{
?>
<table align="center"><tr><td><img src="../images/warningpetit.gif"></td><td>Il y a une ou plusieurs erreurs dans le formulaire :</td></tr></table>
<div align="center" class="erreur"><BR>
  <?php
		$infoCourse = $_SESSION["Post"];

		foreach ($_SESSION["Codes"] as $codeErreur)
		{
			$requeteMessageErreur="	SELECT MsgEr_Message
									FROM message_erreur
									WHERE MsgEr_Code = $codeErreur";
			$resultatMessageErreur=mysql_query($requeteMessageErreur);
	echo mysql_error();
			$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
			echo ($messageErreur["MsgEr_Message"]);?>
  <BR>
  <?php
 		}
	}
?>
</div>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdManager" value="<?php echo $IdManager;?>">
<input type="hidden" name="IdCourse" value="<?php echo $infoCourse["IdCourse"]; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
<div align="center">
<table border="1">
	<tr>
		<th colspan="3"><?php echo(isset($_GET['action']))?"Organiser une nouvelle course":"Ajouter un tronçon";?></th>
	</tr>
	<tr>
		<th>Nom</th>
		<th>Longueur</th>
		<th>Vitesse Max</th>
	</tr>
<?php
	foreach($troncons as $IdSecteur)
	{
		$requeteInfoSecteur = "	SELECT Sec_Nom, Sec_Longueur, Sec_VitesseMaximum
								FROM secteur
								WHERE IdSecteur = '$IdSecteur'";
		$resultatInfoSecteur=mysql_query($requeteInfoSecteur)or die(mysql_error());
		$infoSecteur=mysql_fetch_assoc($resultatInfoSecteur);
		$secteur[$i]=$infoSecteur;
		$secteur[$i++]['Sec_VitesseMaximum'] = msTOkmh($infoSecteur['Sec_VitesseMaximum']);
		$longueurTotale += $infoSecteur['Sec_Longueur'];
		$difficulteTotale += $infoSecteur['Sec_VitesseMaximum']*$infoSecteur['Sec_Longueur'];
	}

	foreach($secteur as $infoSecteur)
	{
?>
	<tr>
		<td><?php echo $infoSecteur['Sec_Nom'];?></td>
		<td><?php echo $infoSecteur['Sec_Longueur'];?> m</td>
		<td><?php echo $infoSecteur['Sec_VitesseMaximum'];?> km/h</td>
	</tr>
<?php
	}
?>
	<tr>
		<td align="right">Longueur Totale</td>
		<td><?php echo $longueurTotale;?> m</td>
		<td><?php echo $difficulteTotale/$longueurTotale;?></td>
	</tr>
</table>
<table>
<tr>
	<td>
	<table border="1">
		<tr>
			<td>Nom de la course<font color="#FF0000">*</font> :</td>
			<td><input type="text" name="Cou_Nom" size="50" value="<?php echo  $infoCourse['Cou_Nom']?>"></td>
		</tr>
		<tr>
			<td>Date de la course<font color="#FF0000">*</font> :</td>
			<td><input type="text" name="Cou_Date" size="10" maxlength="10" value="<?php echo(isset($infoCourse['Cou_Date']))?$infoCourse['Cou_Date']:"JJ/MM/AAAA";?>"></td>
		</tr>
		<tr>
			<td>Type de course :</td>
			<td><select name="Cou_IdType">
<?php
		$requeteTypeCourse = "	SELECT IdTypeCourse, TypeCou_Libelle
							FROM type_course";
		$rechercheTypeCourse = mysql_query($requeteTypeCourse)or die(mysql_error());
	while($typeCourse = mysql_fetch_assoc($rechercheTypeCourse))
	{
?>
			<option value="<?php echo $typeCourse['IdTypeCourse']; ?>"<?php echo ($typeCourse['IdTypeCourse'] == $infoCourse['Cou_IdType'])? " selected" : ""; ?>><?php echo $typeCourse["TypeCou_Libelle"];?></option>
<?php
	}
?>
		</tr>
		<tr>
			<td>Nombre de tours :</tr>
			<td><?php 	if(!isset($_GET['Sec_Boucle']))
						{
?>
				<input type="hidden" name="Cou_NbTours" value="1">1
<?php					}
						else
						{
?>
				<input type="text" name="Cou_NbTours" size="2" value="<?php echo(isset($infoCourse['Cou_NbTours']))?$infoCourse['Cou_NbTours']:"1";?>">
<?php					}
?>
			</td>
		</tr>
		<tr>
			<td>Prix Inscription<font color="#FF0000">*</font> :</td>
			<td><input type="text" name="Cou_PrixInscription" value="<?php echo  $infoCourse['Cou_PrixInscription'];?>"> &euro;</td>
		</tr>
		<tr>
			<td>Prix Engagement<font color="#FF0000">*</font> :</td>
			<td><input type="text" name="Cou_PrixEngagement" onChange="prixTotal(this.form)" value="<?php echo  $infoCourse['Cou_PrixEngagement'];?>"> &euro;</td>
		</tr>
		<tr>
			<td>Densité de circulation :</td>
			<td><input type="text" name="Cou_DensiteCirculation" size="2" value="<?php echo(isset($infoCourse['Cou_DensiteCirculation']))?$infoCourse['Cou_DensiteCirculation']:"0";?>"> %</td>
		</tr>
		<tr>
			<td>Nombre maximum de compétiteurs (2-4) :</td>
			<td><input type="text" name="Cou_NbCompetiteursMax" size="2" onChange="prixTotal(this.form);risquePolice(this.form);" value="<?php echo(isset($infoCourse['Cou_NbCompetiteursMax']))?$infoCourse['Cou_NbCompetiteursMax']:"4";?>"></td>
		</tr>
		<tr>
			<td>Niveau minimum :</td>
			<td><input type="text" name="Cou_NiveauMin" size="2" value="<?php echo(isset($infoCourse['Cou_NiveauMin']))?$infoCourse['Cou_NiveauMin']:"1";?>"></td>
		</tr>
		<tr>
			<td>Niveau maximum :</td>
			<td><input type="text" name="Cou_NiveauMax" size="2" value="<?php echo(isset($infoCourse['Cou_NiveauMax']))?$infoCourse['Cou_NiveauMax']:"10";?>"></td>
		</tr>
		<tr>
			<th colspan="2">Commentaires</th>
		</tr>
		<tr>
			<td colspan="2"><textarea name="Cou_Commentaires" cols="80" rows="7"><?php echo stripslashes($infoCourse['Cou_Commentaires'])?></textarea></td>
		</tr>
	</table>
	</td>
	<td>
		<table>
			<tr>
				<th colspan="2">Informations</th>
			</tr>
			<tr>
				<th>Prix au vainqueur :</th>
				<td><input type="text" name="Cou_PrixTotal" size="6" value="0" readonly > &euro;</td>
			</tr>
			<tr>
				<th>Intérêt de la police :</th>
				<td><input type="text" name="Cou_RisquePolice" size="1" value="33.3" readonly > % de chance</td>
			</tr>
		</table>
	</td>
<br>
	<table>
		<tr>
			<td align="center" colspan="3"><br>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]; ?>"><br>
			</td>
			<td align="center" colspan="3"><br>
		<?php echo ($_GET["action"]=="Terminer le circuit")? "<input type=\"reset\" value=\"Effacer saisie\">":
			"<input type=\"button\" onclick=\"annulModif(".$infoCourse['IdCourse'].")\" value=\"Annuler les modifications\">";?><br>
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
