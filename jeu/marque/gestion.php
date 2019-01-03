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
	$Man_Niveau = $_SESSION['Jou_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<html>
<head>
	<title>UTR : Modifier une marque</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdMarque)
		{
			document.location="gestion.php?action=Modifier&IdMarque="+IdMarque;
		}
		function verifForm(form)
		{
			return true;
			with(form)
			{
				var chaineErreur = "";

				chaineErreur +=	is_NotNull(Marq_Libelle.value,"Nom de la marque");

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
	</script>
</head>
<body onLoad="focus(document.formulaire.Marq_Libelle)">
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
			$infoMarque="";
			$infoMarque['Marq_IdTypePiece'] = array($_GET['IdTypePiece']);
		}
	else
		{
			$IdMarque = $_GET["IdMarque"];

			$requeteInfoMarque="	SELECT	IdMarque,
													Marq_Libelle,
													Marq_IdTypePiece,
													TypPi_Libelle
										FROM marque
										LEFT JOIN type_piece ON IdTypePiece = Marq_IdTypePiece
										WHERE IdMarque = '$IdMarque'";
			$resultatInfoMarque=mysql_query($requeteInfoMarque)or die(mysql_error());
			$infoMarque=mysql_fetch_assoc($resultatInfoMarque);
			$infoMarque['Marq_IdTypePiece'] = explode(',',$infoMarque['Marq_IdTypePiece']);
		}
	}
	else
	{
?>
<table align="center"><tr><td><img src="../images/warningpetit.gif"></td><td>Il y a une ou plusieurs erreurs dans le formulaire :</td></tr></table>
<div align="center" class="erreur"><BR>
  <?php
		$infoMarque = $_SESSION["Post"];

		foreach ($_SESSION["Codes"] as $codeErreur)
		{
			$requeteMessageErreur="	SELECT MsgEr_Message
											FROM message_erreur
											WHERE MsgEr_Code = $codeErreur";
			$resultatMessageErreur=mysql_query($requeteMessageErreur)or die(mysql_error());
			$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
			echo ($messageErreur["MsgEr_Message"]);?>
  <BR>
  <?php
 		}
	}
?>
</div>
<?php
	if($_GET['bis']==1)
	{
?>
<br>
Insertion effectuée avec succès !
<br>
<?php
	}
?>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdMarque" value="<?php echo $infoMarque["IdMarque"]; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
	<table border="1">
		<tr>
			<th colspan="3"><?php echo ($_GET['action']=="Ajouter")?"Ajouter une marque":"Modifier la marque ".$infoMarque['Marq_Libelle']?></td>
		</tr>
		<tr>
			<th>Nom<font color="#FF0000">*</font> :</th>
			<td colspan="2"><input type="text" size="30" name="Marq_Libelle" value="<?php echo $infoMarque['Marq_Libelle'];?>"></td>
		</tr>
		<tr>
			<th>Type de pièces :</th>
			<td colspan="2"><select size="21" name="Marq_IdTypePiece[]" multiple="multiple">
				<option value="-1"<?php echo (in_array("-1",$infoMarque['Marq_IdTypePiece']))?" selected":"";?>>Voiture</option>
				<option value="0"<?php echo (in_array("0",$infoMarque['Marq_IdTypePiece']))?" selected":"";?>>Tout type</option>
<?php
	$requeteTypesPiece="	SELECT IdTypePiece,TypPi_Libelle FROM type_piece ORDER BY TypPi_Libelle";
	$rechercheTypesPiece = mysql_query($requeteTypesPiece);
	while( $typePiece = mysql_fetch_assoc($rechercheTypesPiece) )
	{
?>
				<option value="<?php echo $typePiece['IdTypePiece']; ?>"<?php echo (in_array($typePiece['IdTypePiece'],$infoMarque['Marq_IdTypePiece']))? " selected" : ""; ?>><?php echo $typePiece["TypPi_Libelle"]; ?></option>
<?php
	}
?>
			</select></td>
		</tr>
<table>
<?php
	if($_GET['action']=="Ajouter")
	{
?>
		<tr>
			<td><input type="radio" name="Ajouter_Bis" value="false" checked> Retourner à la liste
			</td>
			<td><input type="radio" name="Ajouter_Bis" value="true"> Ajouter une autre marque
			</td>
		</tr>
<?php
	}
?>
		<tr>
			<td align="center" colspan="3"><br>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]; ?>"><br>
			</td>
			<td align="center" colspan="3"><br>
				<input type="reset" value="<?php echo ($_GET['action']=="Ajouter")?"Effacer saisie":"Annuler les modifications";?>"><br>
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
