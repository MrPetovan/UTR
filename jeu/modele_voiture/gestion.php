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
	<title>UTR : Modifier un modèle de voiture</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript" src="../../include/formulaire.js"></script>
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
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdModeleVoiture)
		{
			document.location="gestion.php?action=Modifier&IdModeleVoiture="+IdModeleVoiture;
		}
		function verifForm(form)
		{
			return true;
			with(form)
			{
				var chaineErreur = "";

				chaineErreur +=	is_NotNull(ModVoi_NomModele.value,"Modele de la piece detachee");

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

		function choixAjouter(idType,ajouter,form)
		{
			if(ajouter == "false" )
			{
				ajouterExist = "";
				ajouter="true";
			}
			else
			{
				ajouterExist = "true";
				ajouter = "";
			}


			var input=document.getElementsByTagName("input");
			for(var i=0; i<input.length; i++)
			if(input[i].id == "ModPi"+idType)
			{
				input[i].disabled=ajouter;
			}


			var select=document.getElementsByTagName("select");
			for(var i=0; i<select.length; i++)
			if(select[i].id == "ModPi"+idType)
			{
				select[i].disabled=ajouter;
			}
			else if(select[i].id == "ModPiExist"+idType)
				{
					select[i].disabled = ajouterExist;
				}

		}
	</script>
</head>
<body onLoad="changerFormulaire(1)">
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
			$infoModeleVoiture="";
		}
		else
		{
			$IdModeleVoiture = $_GET["IdModeleVoiture"];

			$requeteInfoModeleVoiture = "	SELECT	IdModeleVoiture,
																ModVoi_IdMarque,
																Marq_Libelle,
																ModVoi_NomModele,
																ModVoi_Niveau,
																ModVoi_PrixNeuve,
																ModVoi_PoidsCarrosserie,
																ModVoi_TypeCarburant,
																ModVoi_IdInjection,
																ModVoi_IdTurbo,
																ModVoi_IdRefroidissement,
																ModVoi_IdBlocMoteur,
																ModVoi_IdTransmission,
																ModVoi_IdEchappement,
																ModVoi_IdJantes,
																ModVoi_IdPneus,
																ModVoi_IdFreins,
																ModVoi_IdAmortisseurs,
																ModVoi_IdCarrosserie,
																ModVoi_IdSpoiler,
																ModVoi_IdOptiques,
																ModVoi_IdAileron,
																ModVoi_IdChassis,
																ModVoi_IdPucedeContrôle,
																ModVoi_IdNOS,
																ModVoi_IdNéons,
																ModVoi_IdSono
												FROM modele_voiture, marque
												WHERE IdMarque = ModVoi_IdMarque
												AND IdModeleVoiture = '$IdModeleVoiture'";
			$resultatInfoModeleVoiture=mysql_query($requeteInfoModeleVoiture)or die(mysql_error());
			$infoModeleVoiture=mysql_fetch_assoc($resultatInfoModeleVoiture);
		}
	}
	else
	{
?>
<table align="center"><tr><td><img src="../images/warningpetit.gif"></td><td>Il y a une ou plusieurs erreurs dans le formulaire :</td></tr></table>
<div align="center" class="erreur"><BR>
  <?php
		$infoModeleVoiture = $_SESSION["Post"];

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
<form action="traitement.php" name="formulaire" method="post">
<input type="hidden" name="IdModeleVoiture" value="<?php echo $infoModeleVoiture["IdModeleVoiture"]; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
	<table border="1">
		<tr>
			<th colspan="3"><?php echo ($_GET['action']=="Ajouter")?"Ajouter un modèle":"Modifier le modèle de la voiture ".$infoModeleVoiture['Marq_Libelle']." ".$infoModeleVoiture['ModVoi_NomModele']?></td>
		</tr>
		<tr>
			<th>Marque :</th>
			<td colspan="2"><select size="1" name="ModVoi_IdMarque">
<?php
		$requeteMarque="SELECT IdMarque,Marq_Libelle FROM marque WHERE Marq_IdTypePiece LIKE '%-1%'";
		$rechercheMarque = mysql_query($requeteMarque." ORDER BY Marq_Libelle");
		while( $marque = mysql_fetch_assoc($rechercheMarque) )
		{
?>
			<option value="<?php echo $marque['IdMarque']; ?>"<?php echo ($marque['IdMarque'] == $infoModeleVoiture['ModVoi_IdMarque'])? " selected" : ""; ?>><?php echo $marque["Marq_Libelle"]; ?></option>
<?php
		}
?>
			</select></td>
		</tr>
		<tr>
			<th>Modèle<font color="#FF0000">*</font> :</th>
			<td colspan="2"><input type="text" size="30" name="ModVoi_NomModele" value="<?php echo $infoModeleVoiture['ModVoi_NomModele'];?>"></td>
		</tr>
		<tr>
			<th>Type Carburant :</th>
			<td colspan="2"><select size="1" name="ModVoi_TypeCarburant">
					<option value="Essence">Essence</option>
					<option value="Diesel">Diesel</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Niveau<font color="#FF0000">*</font> :</th>
			<td colspan="2"><input type="text" size="3" name="ModVoi_Niveau" value="<?php echo $infoModeleVoiture['ModVoi_Niveau'];?>"></td>
		</tr>
		<tr>
			<th><img alt="Poids Carrosserie" src="../../images/poids.gif"></th>
			<td colspan="2"><input type="text" name="ModVoi_PoidsCarrosserie" size="4" value="<?php echo $infoModeleVoiture['ModVoi_PoidsCarrosserie'];?>"> kg</td>
		</tr>
		<tr>
			<th><img alt="Prix" src="../../images/prix.gif"></th>
			<td colspan="2"><input type="text" name="ModVoi_PrixNeuve" size="6" value="<?php echo $infoModeleVoiture['ModVoi_PrixNeuve'];?>"> &euro;</td>
		</tr>
	</table>
<br>

<table border="0">
	<tr>
		<th colspan="18">Pièces par défaut</th>
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
								ORDER BY TypPi_Libelle";
	$resultatTypesPiece = mysql_query($requeteTypesPiece) or die(mysql_error());

	while($typePiece=mysql_fetch_assoc($resultatTypesPiece))
	{
		$IdTypePiece = $typePiece['IdTypePiece'];
		$TypPi_Libelle = $typePiece['TypPi_Libelle'];

		$requeteModelesPiece ="	SELECT 	IdModelePiece,
													Marq_Libelle,
													ModPi_NomModele,
													ModPi_Niveau
										FROM modele_piece, marque
										WHERE IdMarque = ModPi_IdMarque
										AND ModPi_IdTypePiece = '$IdTypePiece'
										ORDER BY ModPi_Niveau, Marq_Libelle, ModPi_NomModele";
		$resultatModelesPiece = mysql_query($requeteModelesPiece) or die("Requete Modeles Piece :<br> $requeteModelesPiece<br><br>".mysql_error());
?>
	<table border="1" id="remove<?php echo $IdTypePiece?>">
		<tr>
			<th colspan="13"><?php echo $TypPi_Libelle?></th>
		</tr>
		<tr>
			<th>Modèle existant : <input type="radio" name="ajouterModele[<?php echo $IdTypePiece?>]" onClick="choixAjouter(<?php echo $IdTypePiece?>,this.value,this.form)" value="false" checked></th>
			<td colspan="12">
				<select size="1" id="ModPiExist<?php echo $IdTypePiece?>" name="ModVoi_IdModelePiece[<?php echo $IdTypePiece?>]">
					<option value="">N/A</option>
<?php
		while($infoModelePiece = mysql_fetch_assoc($resultatModelesPiece))
		{
?>
					<option value="<?php echo $infoModelePiece['IdModelePiece']?>"<?php echo ($infoModelePiece['IdModelePiece'] == $infoModeleVoiture['ModVoi_Id'.str_replace(" ","",$TypPi_Libelle)])?" selected":""?>>
						<?php echo "(Niv ".$infoModelePiece['ModPi_Niveau'].") ".$infoModelePiece['ModPi_NomModele']." ".$infoModelePiece['Marq_Libelle']?>
					</option>
<?php
		}
?>
				</select>
			</td>
		</tr>
		<tr>
			<th rowspan="2">Ajouter Modèle : <input type="radio" name="ajouterModele[<?php echo $IdTypePiece?>]" onClick="choixAjouter(<?php echo $IdTypePiece?>,this.value,this.form)" value="true"></th>
			<th colspan="6">Marque</th>
			<th colspan="6">Modèle</th>
		<tr>

			<td colspan="6"><select size="1" id="ModPi<?php echo $IdTypePiece?>" name="ModPi_IdMarque[<?php echo $IdTypePiece?>]" disabled>
<?php
		$requeteMarque="SELECT IdMarque,Marq_Libelle FROM marque WHERE Marq_IdTypePiece LIKE '%$IdTypePiece%' OR FIND_IN_SET('0',Marq_IdTypePiece) > 0";
		$rechercheMarque = mysql_query($requeteMarque." ORDER BY Marq_Libelle");
	while( $marque = mysql_fetch_assoc($rechercheMarque) )
		{
?>
			<option value="<?php echo $marque['IdMarque']; ?>"<?php echo ($marque['IdMarque'] == $infoModelePiece['ModPi_IdMarque'])? " selected" : ""; ?>><?php echo $marque["Marq_Libelle"]; ?></option>
<?php
		}
?>
				</select>
			</td>
			<td colspan="6"><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="30" name="ModPi_NomModele[<?php echo $IdTypePiece?>]" value="<?php echo $infoModelePiece['ModPi_NomModele'];?>" disabled></td>
		</tr>
		<tr>
			<th rowspan="2">Caractéristiques</th>
			<th><img alt="Accélération" height="20" src="../../images/acc.gif"></th>
			<th><img alt="Vitesse Max" src="../../images/vmax.gif"></th>
			<th><img alt="Freinage" src="../../images/frein.gif"></th>
			<th><img alt="Turbo" src="../../images/turbo.gif"></th>
			<th><img alt="Adhérence" src="../../images/adh.gif"></th>
			<th><img alt="Solidité Moteur" src="../../images/solmot.gif"></th>
			<th><img alt="Aspect Extérieur" src="../../images/aspect.gif"></th>
			<th><img alt="Capacité Moteur" src="../../images/capa.gif"></th>
			<th><img alt="Capacité Max" src="../../images/capamax.gif"></th>
			<th><img alt="Poids" src="../../images/poids.gif"></th>
			<th><img alt="Durée de vie Max" src="../../images/dureeviemax.gif"></th>
			<th><img alt="Prix Neuve" src="../../images/prix.gif"></th>
		</tr>
		<tr>
			<td><?php if($typePiece['TypPi_Acceleration']!=""){?><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="2" name="ModPi_Acceleration[<?php echo $IdTypePiece?>]" disabled><?php } ?></td>
			<td><?php if($typePiece['TypPi_VitesseMax']!=""){?><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="2" name="ModPi_VitesseMax[<?php echo $IdTypePiece?>]" disabled><?php } ?></td>
			<td><?php if($typePiece['TypPi_Freinage']!=""){?><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="2" name="ModPi_Freinage[<?php echo $IdTypePiece?>]" disabled><?php } ?></td>
			<td><?php if($typePiece['TypPi_Turbo']!=""){?><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="2" name="ModPi_Turbo[<?php echo $IdTypePiece?>]" disabled><?php } ?></td>
			<td><?php if($typePiece['TypPi_Adherence']!=""){?><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="2" name="ModPi_Adherence[<?php echo $IdTypePiece?>]" disabled><?php } ?></td>
			<td><?php if($typePiece['TypPi_SoliditeMoteur']!=""){?><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="2" name="ModPi_SoliditeMoteur[<?php echo $IdTypePiece?>]" disabled><?php } ?></td>
			<td><?php if($typePiece['TypPi_AspectExterieur']!=""){?><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="2" name="ModPi_AspectExterieur[<?php echo $IdTypePiece?>]" disabled><?php } ?></td>
			<td><?php if($typePiece['TypPi_CapaciteMoteur']!=""){?><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="2" name="ModPi_CapaciteMoteur[<?php echo $IdTypePiece?>]" disabled><?php } ?></td>
			<td><?php if($typePiece['TypPi_CapaciteMax']!=""){?><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="2" name="ModPi_CapaciteMax[<?php echo $IdTypePiece?>]" disabled><?php } ?></td>
			<td><input type="text" size="2" id="ModPi<?php echo $IdTypePiece?>" name="ModPi_Poids[<?php echo $IdTypePiece?>]" disabled> kg</td>
			<td><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="2" name="ModPi_DureeVieMax[<?php echo $IdTypePiece?>]" disabled></td>
			<td><input type="text" id="ModPi<?php echo $IdTypePiece?>" size="5" name="ModPi_PrixNeuve[<?php echo $IdTypePiece?>]" disabled> &euro;</td>
		</tr>
	</table>
<?php
	}
?>
<br>
	<table>
<?php
	if($_GET['action']=="Ajouter")
	{
?>
		<tr>
			<td><input type="radio" name="Ajouter_Bis" value="false" checked> Retourner à la fiche
			</td>
			<td><input type="radio" name="Ajouter_Bis" value="true"> Ajouter un autre modèle
			</td>
		</tr>
<?php
	}
?>
		<tr>
			<td>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]; ?>">
			</td>
			<td>
				<input type="reset" value="<?php echo ($_GET['action']=="Ajouter")?"Effacer saisie":"Annuler les modifications";?>">
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
