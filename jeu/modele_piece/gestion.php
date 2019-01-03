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
			return true;
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
			$IdTypePiece = $_GET['IdTypePiece'];
		}
		else
		{
			$IdModelePiece = $_GET["IdModelePiece"];

			$requeteInfoModelePiece="	SELECT	IdModelePiece,
															ModPi_IdMarque,
															Marq_Libelle AS ModPi_LibelleMarque,
															ModPi_NomModele,
															ModPi_Niveau,
															ModPi_IdTypePiece,
															ModPi_Acceleration,
															ModPi_VitesseMax,
															ModPi_Freinage,
															ModPi_Turbo,
															ModPi_Adherence,
															ModPi_SoliditeMoteur,
															ModPi_AspectExterieur,
															ModPi_CapaciteMoteur,
															ModPi_CapaciteMax,
															ModPi_Poids,
															ModPi_DureeVieMax,
															ModPi_PrixNeuve
												FROM modele_piece, type_piece, marque
												WHERE IdTypePiece = ModPi_IdTypePiece
												AND IdMarque = ModPi_IdMarque
												AND IdModelePiece = '$IdModelePiece'";
			$resultatInfoModelePiece=mysql_query($requeteInfoModelePiece)or die(mysql_error());
			$infoModelePiece=mysql_fetch_assoc($resultatInfoModelePiece);

			$IdTypePiece = $infoModelePiece['ModPi_IdTypePiece'];
		}
		$requeteTypesPiece="	SELECT 	IdTypePiece,
												TypPi_Libelle,
												TypPi_Obligatoire,
												TypPi_Acceleration,
												TypPi_VitesseMax,
												TypPi_Freinage,
												TypPi_Turbo,
												TypPi_Adherence,
												TypPi_SoliditeMoteur,
												TypPi_AspectExterieur,
												TypPi_CapaciteMoteur,
												TypPi_CapaciteMax
									FROM type_piece
									WHERE IdTypePiece = '$IdTypePiece'
									ORDER BY TypPi_Libelle";
		$resultatTypesPiece = mysql_query($requeteTypesPiece) or die(mysql_error());
		$infoTypePiece = mysql_fetch_assoc($resultatTypesPiece);
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
<a href="liste.php?IdTypePiece=<?php echo $IdTypePiece?>"><< Revenir à la liste</a>
<br>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdModelePiece" value="<?php echo $infoModelePiece["IdModelePiece"]; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
	<table border="0">
		<tr>
			<th colspan="2"><?php echo ($_GET['action']=="Ajouter")?"Ajouter une pièce":"Modifier le modèle de la pièce ".$infoModelePiece['ModPi_NomModele']?></td>
		</tr>
		<tr>
			<th>Marque :</th>
			<td><select size="1" name="ModPi_IdMarque">
<?php
		$requeteMarque="SELECT IdMarque,Marq_Libelle FROM marque WHERE FIND_IN_SET('$IdTypePiece',Marq_IdTypePiece) > 0 OR FIND_IN_SET('0',Marq_IdTypePiece) > 0";
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
			<td><input type="text" size="30" name="ModPi_NomModele" value="<?php echo $infoModelePiece['ModPi_NomModele'];?>"></td>
		</tr>
		<tr>
			<th>Niveau<font color="#FF0000">*</font> :</th>
			<td><input type="text" size="3" name="ModPi_Niveau" value="<?php echo $infoModelePiece['ModPi_Niveau'];?>"></td>
		</tr>
		<tr>
			<th>Type :</th>
			<td ><input type="hidden" name="ModPi_IdTypePiece" value="<?php echo $IdTypePiece?>"><?php echo $infoTypePiece['TypPi_Libelle']?></td>
		</tr>
		<tr>
			<th><img alt="Durée de vie Max" title="Durée de vie Max" src="../../images/dureeviemax.gif"></th>
			<td><input type="text" name="ModPi_DureeVieMax" size="4" value="<?php echo $infoModelePiece['ModPi_DureeVieMax'];?>"></td>
		</tr>
		<tr>
			<th><img alt="Poids" title="Poids" src="../../images/poids.gif"></th>
			<td><input type="text" name="ModPi_Poids" size="4" value="<?php echo $infoModelePiece['ModPi_Poids'];?>"> kg</td>
		</tr>
		<tr>
			<th><img alt="Prix" title="Prix" src="../../images/prix.gif"></th>
			<td><input type="text" name="ModPi_PrixNeuve" size="6" value="<?php echo $infoModelePiece['ModPi_PrixNeuve'];?>"> §</td>
		</tr>
		<tr>
			<th colspan="2">Commentaires :</th>
		</tr>
		<tr>
			<td colspan="2"><textarea name="ModPi_Commentaires" cols="50" rows="5"><?php echo $infoModelePiece['ModPi_Commentaires']?></textarea></th>
		</tr>
	</table>
<br>
	<table border="0" id="Modifier">
		<tr>
			<th>Caractéristiques</th>
			<th><img alt="Accélération" title="Accélération" height="20" src="../../images/acc.gif"></th>
			<th><img alt="Vitesse Max" title="Vitesse Max" src="../../images/vmax.gif"></th>
			<th><img alt="Freinage" title="Freinage" src="../../images/frein.gif"></th>
			<th><img alt="Turbo" title="Turbo" src="../../images/turbo.gif"></th>
			<th><img alt="Adhérence" title="Adhérence" src="../../images/adh.gif"></th>
			<th><img alt="Solidité Moteur" title="Solidité Moteur" src="../../images/solmot.gif"></th>
			<th><img alt="Aspect Extérieur" title="Aspect Extérieur" src="../../images/aspect.gif"></th>
			<th><img alt="Capacité Moteur" title="Capacité Moteur" src="../../images/capa.gif"></th>
			<th><img alt="Capacité Max" title="Capacité Max" src="../../images/capamax.gif"></th>
		</tr>
		<tr>
			<th>Données standard</th>
			<td><?php if($infoTypePiece['TypPi_Acceleration']==1) {?><input type="text" name="ModPi_Acceleration" size="4" value="<?php echo $infoModelePiece['ModPi_Acceleration'];?>"><?php } ?></td>
			<td><?php if($infoTypePiece['TypPi_VitesseMax']==1) {?><input type="text" name="ModPi_VitesseMax" size="4" value="<?php echo $infoModelePiece['ModPi_VitesseMax'];?>"><?php } ?></td>
			<td><?php if($infoTypePiece['TypPi_Freinage']==1) {?><input type="text" name="ModPi_Freinage" size="4" value="<?php echo $infoModelePiece['ModPi_Freinage'];?>"><?php } ?></td>
			<td><?php if($infoTypePiece['TypPi_Turbo']==1) {?><input type="text" name="ModPi_Turbo" size="4" value="<?php echo $infoModelePiece['ModPi_Turbo'];?>"><?php } ?></td>
			<td><?php if($infoTypePiece['TypPi_Adherence']==1) {?><input type="text" name="ModPi_Adherence" size="4" value="<?php echo $infoModelePiece['ModPi_Adherence'];?>"><?php } ?></td>
			<td><?php if($infoTypePiece['TypPi_SoliditeMoteur']==1) {?><input type="text" name="ModPi_SoliditeMoteur" size="4" value="<?php echo $infoModelePiece['ModPi_SoliditeMoteur'];?>"><?php } ?></td>
			<td><?php if($infoTypePiece['TypPi_AspectExterieur']==1)	{?><input type="text" name="ModPi_AspectExterieur" size="3" value="<?php echo $infoModelePiece['ModPi_AspectExterieur'];?>"><?php } ?></td>
			<td><?php if($infoTypePiece['TypPi_CapaciteMoteur']==1) {?><input type="text" name="ModPi_CapaciteMoteur" size="2" value="<?php echo $infoModelePiece['ModPi_CapaciteMoteur'];?>"><?php } ?></td>
			<td><?php if($infoTypePiece['TypPi_CapaciteMax']==1) {?><input type="text" name="ModPi_CapaciteMax" size="2" value="<?php echo $infoModelePiece['ModPi_CapaciteMax'];?>"><?php } ?></td>
		</tr>
	</table>
	<table>
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
