<?php
	if(!isset($_GET['code']))
	{
		header("location:/UTR/index.php");
		exit;
	}
	include("../include/connexion.inc.php");

	$code = $_GET['code'];

	session_name("UTR");
	session_start();

	error_reporting(E_ALL ^ E_NOTICE);

	$requeteInfoJoueur = "	SELECT IdJoueur, Jou_Pseudo
							FROM joueur
							WHERE Jou_CodeInscription = '$code'";
	$resultatInfoJoueur = mysql_query($requeteInfoJoueur) or die("Info Joueur : $requeteInfoJoueur<br>".mysql_error());
	$infoJoueur = mysql_fetch_assoc($resultatInfoJoueur);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>UTR : Création du Profil</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type="text/javascript" language="JavaScript" src="../include/verif.js"></script>
	<script language="JavaScript">
	function verifForm(form)
	{
		with (form)
		{
			var chaineErreur = is_NotNull(Pil_Age.value,"L'âge");
			if(XPRestants.value != 0) chaineErreur += " - Il vous reste des points d'expérience à distribuer\n";

			if (chaineErreur != "")
			{
				alert("Il y a une ou plusieurs erreurs de saisie :\n"+chaineErreur);
				return false;
			}
			else
			{
				verificationJs.value = true;
				return true;
			}
		}
	}

	function ajouterPoints(bouton)
	{
		var formulaire=bouton.form;
		var nom = bouton.name;
		var PointsRestants = formulaire.XPRestants.value;
		var PointsCarac = parseInt(eval("formulaire.Pil_XP"+nom+".value"));

		if(PointsRestants >= 50)
		{
			PointsRestants -= 50;
			PointsCarac += 50;
		}
		else	if(PointsRestants > 0)
				{
					PointsCarac += PointsRestants;
					PointsRestants = 0;
				}
				else	if(PointsRestants == 0)
						{
							var pointsEnleves = 0;
							var Caracs;
							for(var i=1;i <= 4;i++)
							{
								if(Caracs = document.getElementById('Carac'+i))
									if(Caracs.value != 0 && Caracs.name != "Pil_XP"+nom)
									{
										Caracs.value--;
										pointsEnleves++;
									}
							}
							PointsCarac += pointsEnleves;
						}
		formulaire.XPRestants.value = PointsRestants;
		eval("formulaire.Pil_XP"+nom+".value="+PointsCarac);
	}


	function retirerPoints(bouton)
	{
		var formulaire=bouton.form;
		var nom = bouton.name;
		var PointsRestants = parseInt(formulaire.XPRestants.value);
		var PointsCarac = parseInt(eval("formulaire.Pil_XP"+nom+".value"));

		if(PointsCarac >= 50)
		{
			PointsRestants += 50;
			PointsCarac -= 50;
		}
		else
		{
			PointsRestants += PointsCarac;
			PointsCarac = 0;
		}
		formulaire.XPRestants.value = PointsRestants;
		eval("formulaire.Pil_XP"+nom+".value="+PointsCarac);
	}
	</script>
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="3">
<?php
	include("../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td width="164" valign="top">
<?php
	include("../frame/menu.php");
?>
		</td>
		<td width="693"><br>
			<table width="607" height="34" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="11"><img src="/UTR/design/spacer.gif" width="9" height="24"></td>
					<td width="579" height=34><div align="left">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td background="/UTR/design/nav.jpg" height="34" width="485"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Création du pilote et de la voiture</b></div></td>
								<td align="right" background="/UTR/design/navtile.jpg"><img height="34" src="/UTR/design/navdroite.gif" width="2"></td>
							</tr>
						</table></div>
					</td>
				</tr>
			</table>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2">
<?php
	if(!isset($_SESSION['Erreur']))
	{
		$erreur="";
		$erreur['XPRestants'] = 0;
		$erreur['Pil_XPShifts'] = 250;
		$erreur['Pil_XPVirage'] = 250;
		$erreur['Pil_XPFreinage'] = 250;
		$erreur['Pil_XPSpe'] = 250;
	}
	else
	{
		$erreur = $_SESSION['Post'];



		echo "<br>Il y a des erreurs dans le formulaire : <br>";
		foreach ($_SESSION["Codes"] as $codeErreur)
		{
			$requeteMessageErreur = "SELECT Err_Message
									FROM erreur
									WHERE IdErreur = $codeErreur";
			$resultatMessageErreur=mysql_query($requeteMessageErreur)or die(mysql_error());
			$messageErreur=mysql_fetch_assoc($resultatMessageErreur);

			echo "<br>".$messageErreur["Err_Message"];?>
<?php
 		}

		unset($_SESSION['Erreur']);
		unset($_SESSION['Post']);
	}
?>
					</td>
				</tr>
				<tr>
					<td width="2%"><img src="/UTR/design/spacer.gif" width="9" height="64"></td>
					<td width="98%" valign="top">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td valign="top">
									<table width="579" border="1" cellpadding="1" cellspacing="0" bordercolor="#780000">
<form name="inscription" action="traitementManager.php" method="POST" onSubmit="return verifForm(this)">
<input type="hidden" name="verificationJs" value="false">
<input type="hidden" name="code" value="<?php echo $code?>">
										<tr>
											<th colspan="4">Le pilote</th>
										</tr>
										<tr>
											<th width="150">Nom : </th>
											<td colspan="2"><?php echo $infoJoueur['Jou_Pseudo']?></td>
											<td rowspan="3">L'âge du pilote n'a aucune influence en terme de jeu, c'est uniquement un choix personnel : pilote jeune ou plus vieux ?</td>
										</tr>
										<tr>
											<th>Sexe :</th>
											<td colspan="2"><input type="radio" name="Man_Sexe" value="Masculin" checked>&nbsp;Masculin&nbsp;<input type="radio" name="Man_Sexe" value="Féminin">&nbsp;Feminin</td>
										</tr>
										<tr>
											<th>Age (18-25 ans) : </th>
											<td colspan="2"><input type="text" name="Pil_Age" size="2" value="<?php echo $erreur['Pil_Age']?>"></td>
										</tr>
									</table>
									<table width="579" border="1" cellpadding="1" cellspacing="0" bordercolor="#780000">
										<tr>
											<th colspan="5">Compétences</th>
										</tr>
										<tr>
											<th colspan="2">Points d'expérience restant :</th>
											<td colspan="1"><input type="text" name="XPRestants" size="4" value="<?php echo $erreur['XPRestants']?>" readonly></td>
											<td>&nbsp;</td>
											<td rowspan="5">Les points d'expérience servent à mesurer l'entraînement du pilote dans chacune des 4 grands domaines du pilotage.<br><br>
												Ces points servent ensuite à déterminer le niveau de maîtrise de chaque compétences. A chaque niveau correspond un seuil de points d'expérience.</td>
										</tr>
										<tr>
											<th width="180">Expérience des drifts : </th>
											<td><input name="Shifts" type="button" onClick="retirerPoints(this)" value=" - "></td>
											<td><input type="text" name="Pil_XPShifts" size="4" id="Carac1" value="<?php echo $erreur['Pil_XPShifts']?>" readonly></td>
											<td><input name="Shifts" type="button" onClick="ajouterPoints(this)" value=" + "></td>
										</tr>
										<tr>
											<th>Expérience du freinage : </th>
											<td><input name="Freinage" type="button" onClick="retirerPoints(this)" value=" - "></td>
											<td><input type="text" name="Pil_XPFreinage" size="4" id="Carac2" value="<?php echo $erreur['Pil_XPFreinage']?>" readonly></td>
											<td><input name="Freinage" type="button" onClick="ajouterPoints(this)" value=" + "></td>
										</tr>
										<tr>
											<th>Expérience des virages : </th>
											<td><input name="Virage" type="button" onClick="retirerPoints(this)" value=" - "></td>
											<td><input type="text" name="Pil_XPVirage" size="4" id="Carac3" value="<?php echo $erreur['Pil_XPVirage']?>" readonly>
											<td><input name="Virage" type="button" onClick="ajouterPoints(this)" value=" + "></td>
										</tr>
										<tr>
											<th>Expérience des "spécial" : </th>
											<td><input name="Spe" type="button" onClick="retirerPoints(this)" value=" - "></td>
											<td><input type="text" name="Pil_XPSpe" size="4" id="Carac4" value="<?php echo $erreur['Pil_XPSpe']?>" readonly></td>
											<td><input name="Spe" type="button" onClick="ajouterPoints(this)" value=" + "></td>
										</tr>
									</table>
									<table width="579" border="1" cellpadding="1" cellspacing="0" bordercolor="#780000">
										<tr>
											<th colspan="3">La voiture</th>
										</tr>
										<tr>
											<td>
												<select size="1" name="IdModeleVoiture" id="Niveau1">
<?php
		$requeteModelesNiv1 = "	SELECT IdModeleVoiture, ModVoi_IdMarque, Marq_Libelle, ModVoi_NomModele
										FROM modele_voiture, marque
										WHERE IdMarque = ModVoi_IdMarque
										AND ModVOi_Niveau = '1'
										ORDER BY Marq_Libelle, ModVoi_NomModele";
		$rechercheModelesNiv1 = mysql_query($requeteModelesNiv1);
		while( $modeleNiv1 = mysql_fetch_assoc($rechercheModelesNiv1) )
		{
?>
													<option value="<?php echo $modeleNiv1['IdModeleVoiture']?>"><?php echo $modeleNiv1['Marq_Libelle']." ".$modeleNiv1['ModVoi_NomModele']?></option>
<?php
		}
?>
												</select>
											</td>
										</tr>
										<tr>
											<td colspan="3"><input type="submit" value="Terminer l'inscription"></td>
										</tr>
</form>
								</table>
								</td>
							</tr>
							<tr>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td background="/UTR/design/bas2.gif" height=6></td>
											<td background="/UTR/design/bas_tile_droite.gif" height=6></td>
											<td align=right background="/UTR/design/bas_tile_droite.gif" height=6><img height=6 src="/UTR/design/bas_bord_droit.gif" width=3></td>
											</tr>
									</table>
								</td>
							</tr>
						</table>
<p align="center">&nbsp;</p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><div align="center">
<?php
	include("../frame/piedpage.php");
?>
		</div></td>
	</tr>
</table>
</body>
</html>
