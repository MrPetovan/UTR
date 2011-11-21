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
	<title>UTR : Modifier un modèle de pièce détachée</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
  <script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdPieceDetachee)
		{
			document.location="gestion.php?action=Modifier&IdPieceDetachee="+IdPieceDetachee;
		}
		function verifForm(form)
		{
			with(form)
			{
				var chaineErreur = "";

				chaineErreur +=	is_NotNull(ModPi_NomModele.value,"Modele de la piece detachee");

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

		if($_GET["action"]=="Ajouter")
		{
			$infoModelePiece="";
		}
		else
		{
			$IdModelePiece = $_GET["IdModelePiece"];

			$requeteInfoModelePiece="	SELECT 	IdModelePiece, ModPi_IdMarque, Marq_Libelle AS ModPi_LibelleMarque, ModPi_NomModele, ModPi_Niveau, ModPi_IdTypePiece, TypPi_Libelle,
												ModPi_Acceleration, ModPi_VitesseMax, ModPi_Freinage,ModPi_Turbo, ModPi_Adherence,
												ModPi_SoliditeMoteur, ModPi_AspectExterieur, ModPi_CapaciteMoteur, ModPi_CapaciteMax,
												ModPi_Poids, ModPi_DureeVieMax, ModPi_PrixNeuve
										FROM modele_piece, type_piece, marque
										WHERE IdTypePiece = ModPi_IdTypePiece
										AND IdMarque = ModPi_IdMarque
										AND IdModelePiece = '$IdModelePiece'";
			$resultatInfoModelePiece=mysql_query($requeteInfoModelePiece)or die(mysql_error());
			$infoModelePiece=mysql_fetch_assoc($resultatInfoModelePiece);
		}
	}
	else
	{
?>
<table align="center"><tr><td><img src="../images/warningpetit.gif"></td><td>Il y a une ou plusieurs erreurs dans le formulaire :</td></tr></table>
<div align="center" class="erreur"><BR>
  <?php
		$infoModelePiece = $_SESSION["Post"];

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
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdModelePiece" value="<?php echo $infoModelePiece["IdModelePiece"]; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
	<table border="1">
		<tr>
			<th colspan="3"><?php echo ($_GET['action']=="Ajouter")?"Ajouter une pièce":"Modifier le modèle de la pièce ".$infoModelePiece['ModPi_NomModele']?></td>
		</tr>
		<tr>
			<th>Marque :</th>
			<td colspan="2"><select size="1" name="ModPi_IdMarque">
<?php
		$requeteMarque="SELECT IdMarque,Marq_Libelle FROM marque WHERE Marq_IdTypePiece = '".$infoModelePiece['ModPi_IdTypePiece']."' OR Marq_IdTypePiece = '0'";
		$rechercheMarque = mysql_query($requeteMarque." ORDER BY Marq_Libelle");
	while( $marque = mysql_fetch_assoc($rechercheMarque) )
		{
?>
			<option value="<?php echo $marque['IdMarque']; ?>"<?php echo ($marque['IdMarque'] == $infoModelePiece['ModPi_IdMarque'])? " selected" : ""; ?>><?php echo $marque["Marq_Libelle"]; ?></option>
<?php
		}
?>
			</select></td>
		</tr>
		<tr>
			<th>Modèle<font color="#FF0000">*</font> :</th>
			<td colspan="2"><input type="text" size="30" name="ModPi_NomModele" value="<?php echo $infoModelePiece['ModPi_NomModele'];?>"></td>
		</tr>
		<tr>
			<th>Niveau<font color="#FF0000">*</font> :</th>
			<td colspan="2"><input type="text" size="30" name="ModPi_Niveau" value="<?php echo $infoModelePiece['ModPi_Niveau'];?>"></td>
		</tr>
		<tr>
			<th>Type :</th>
			<td colspan="2">
				<select size="1" name="ModPi_IdTypePiece">
<?php echo $infoModelePiece['TypPi_Libelle'];?>
				</select>
			</td>
		</tr>
		<tr>
			<th><img alt="Aspect Extérieur" src="../../images/aspect.gif"></th>
			<td colspan="2"><input type="text" name="ModPi_AspectExterieur" value="<?php echo $infoModelePiece['ModPi_AspectExterieur'];?>"></td>
		</tr>
		<tr>
			<th><img alt="Poids" src="../../images/poids.gif"></th>
			<td colspan="2"><input type="text" name="ModPi_Poids" size="4" value="<?php echo $infoModelePiece['ModPi_Poids'];?>"> kg</td>
		</tr>
		<tr>
			<th><img alt="Age" src="../../images/age.gif"></th>
			<td colspan="2"><input type="text" name="ModPi_DateFabrication" size="4" value="<?php echo $infoModelePiece['ModPi_DateFabrication'];?>"><?php echo ($infoModelePiece['ModPi_DateFabrication']==1)?" an":" ans";?></td>
		</tr>
		<tr>
			<th><img alt="Prix" src="../../images/prix.gif"></th>
			<td colspan="2"><input type="text" name="ModPi_PrixNeuve" size="6" value="<?php echo $infoModelePiece['ModPi_PrixNeuve'];?>"> &euro;</td>
		</tr>
	</table>
<br>
	<table border="1" id="Modifier">
		<tr>
			<th>Caractéristiques</th>
			<th><img alt="Accélération" height="20" src="../../images/acc.gif"></th>
			<th><img alt="Vitesse Max" src="../../images/vmax.gif"></th>
			<th><img alt="Freinage" src="../../images/frein.gif"></th>
			<th><img alt="Turbo" src="../../images/turbo.gif"></th>
			<th><img alt="Adhérence" src="../../images/adh.gif"></th>
			<th><img alt="Solidité Moteur" src="../../images/solmot.gif"></th>
			<th><img alt="Capacité Moteur" src="../../images/capa.gif"></th>
			<th><img alt="Capacité Max" src="../../images/capamax.gif"></th>
			<th><img alt="Durée de vie Max" src="../../images/dureeviemax.gif"></th>
		</tr>
		<tr>
			<th>Données standard</th>
			<td><input type="text" name="ModPi_Acceleration" size="4" value="<?php echo $infoModelePiece['ModPi_Acceleration'];?>"></td>
			<td><input type="text" name="ModPi_VitesseMax" size="4" value="<?php echo $infoModelePiece['ModPi_VitesseMax'];?>"></td>
			<td><input type="text" name="ModPi_Freinage" size="4" value="<?php echo $infoModelePiece['ModPi_Freinage'];?>"></td>
			<td><input type="text" name="ModPi_Turbo" size="4" value="<?php echo $infoModelePiece['ModPi_Turbo'];?>"></td>
			<td><input type="text" name="ModPi_Adherence" size="4" value="<?php echo $infoModelePiece['ModPi_Adherence'];?>"></td>
			<td><input type="text" name="ModPi_SoliditeMoteur" size="4" value="<?php echo $infoModelePiece['ModPi_SoliditeMoteur'];?>"></td>
			<td rowspan="2"><input type="text" name="ModPi_CapaciteMoteur" size="3" value="<?php echo $infoModelePiece['ModPi_CapaciteMoteur'];?>"></td>
			<td rowspan="2"><input type="text" name="ModPi_CapaciteMax" size="3" value="<?php echo $infoModelePiece['ModPi_CapaciteMax'];?>"></td>
			<td rowspan="2"><input type="text" name="ModPi_DureeVieMax" size="4" value="<?php echo $infoModelePiece['ModPi_DureeVieMax'];?>"></td>
		</tr>
	<tr>
			<th>Données mesurées</th>
			<td><?php if($infoModelePiece['ModPi_Acceleration']!="") echo (empty($infoModelePiece['ModPi_AccelerationMesuree']))?"?":$infoModelePiece['ModPi_AccelerationMesuree'];?></td>
			<td><?php if($infoModelePiece['ModPi_VitesseMax']!="") echo (empty($infoModelePiece['ModPi_VitesseMaxMesuree']))?"?":$infoModelePiece['ModPi_VitesseMaxMesuree'];?></td>
			<td><?php if($infoModelePiece['ModPi_Freinage']!="") echo (empty($infoModelePiece['ModPi_FreinageMesure']))?"?":$infoModelePiece['ModPi_FreinageMesure'];?></td>
			<td><?php if($infoModelePiece['ModPi_Turbo']!="") echo (empty($infoModelePiece['ModPi_TurboMesure']))?"?":$infoModelePiece['ModPi_TurboMesure'];?></td>
			<td><?php if($infoModelePiece['ModPi_Adherence']!="") echo (empty($infoModelePiece['ModPi_AdherenceMesuree']))?"?":$infoModelePiece['ModPi_AdherenceMesuree'];?></td>
			<td><?php if($infoModelePiece['ModPi_SoliditeMoteur']!="")echo (empty($infoModelePiece['ModPi_SoliditeMoteurMesuree']))?"?":$infoModelePiece['ModPi_SoliditeMoteurMesuree'];?></td>
			<td><?php echo (empty($infoModelePiece['ModPi_UsureMesuree']))?"?":$infoModelePiece['ModPi_UsureMesuree'];?></td>
			<td><?php echo (empty($infoModelePiece['ModPi_QualiteMesuree']))?"?":$infoModelePiece['ModPi_QualiteMesuree'];?></td>
		</tr>
	</table>
<?php
	}
?>
	<table>
		<tr>
			<td align="center" colspan="3"><br>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]; ?>"><br>
			</td>
			<td align="center" colspan="3"><br>
				<input type="reset" value="<?php echo($_GET['action']=="Ajouter")?"Effacer saisie":"Annuler les modifications";?>"><br>
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
