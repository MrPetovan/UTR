<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	include('../../include/fonctions.php');
	include('../bin/fonctionMath.php');

	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];

/*
	echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";
*/
?>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdVoiture)
		{
			document.location="gestion.php?action=Modifier&IdVoiture="+IdVoiture;
		}
		function verifForm(form)
		{
			return true;
			with(form)
			{
				var chaineErreur = "";

				chaineErreur +=	is_NotNull(ModVoi_NomModele.value,"Modele de la voiture");

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
		<td colspan="2">
<?php
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td valign="top" width="14%">
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td valign="top" align="center">
<?php
	if(isset($_GET["action"])&& !isset($_SESSION["Erreur"]))
	{

		if($_GET["action"]=="Ajouter")
		{
			$IdModeleVoiture = $_GET['IdModeleVoiture'];
			$infoModeleVoiture="";

			$requeteInfoModeleVoiture = "	SELECT	IdModeleVoiture,
																ModVoi_IdMarque,
																Marq_Libelle,
																ModVoi_NomModele,
																ModVoi_PrixNeuve,
																ModVoi_PoidsCarrosserie,
																ModVoi_TypeCarburant
													FROM modele_voiture, marque
													WHERE IdMarque = ModVoi_IdMarque
													AND IdModeleVoiture = '$IdModeleVoiture'";
			$resultatInfoModeleVoiture=mysql_query($requeteInfoModeleVoiture) or die("Requete Info Modele Voiture : ".mysql_error());
			$infoModeleVoiture=mysql_fetch_assoc($resultatInfoModeleVoiture);

			$requeteTypesPiece = "SELECT IdTypePiece, TypPi_Libelle, TypPi_Obligatoire FROM type_piece ORDER BY TypPi_Libelle";
			$resultatTypesPiece = mysql_query($requeteTypesPiece) or die("Requete Types Piece : ".mysql_error());

			while($typePiece=mysql_fetch_assoc($resultatTypesPiece))
			{
				$IdTypePiece=$typePiece['IdTypePiece'];
				$TypPi_Libelle = $typePiece['TypPi_Libelle'];
				$TypPi_Obligatoire = $typePiece['TypPi_Obligatoire'];

				$requeteInfoPieceSerie= "	SELECT	IdModelePiece,
																ModPi_IdMarque,
																Marq_Libelle,
																ModPi_NomModele,
																ModPi_IdTypePiece,
																TypPi_Libelle,
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
													FROM modele_piece, modele_voiture, marque, type_piece
													WHERE IdMarque = ModPi_IdMarque
													AND ModVoi_Id".ereg_replace(" ","",$TypPi_Libelle)." = IdModelePiece
													AND IdTypePiece = ModPi_IdTypePiece
													AND IdTypePiece = '$IdTypePiece'
													AND IdModeleVoiture = '$IdModeleVoiture'";
				$resultatPieceSerie = mysql_query($requeteInfoPieceSerie)or die("Requete Info Piece Serie : $requeteInfoPieceSerie<br>".mysql_error());
				$infoPieceSerie = mysql_fetch_assoc($resultatPieceSerie);

				$pieceSerie[$IdTypePiece]=$infoPieceSerie;
				$pieceSerie[$IdTypePiece]['TypPi_Libelle'] = $TypPi_Libelle;
				$pieceSerie[$IdTypePiece]['TypPi_Obligatoire'] = $TypPi_Obligatoire;
/*				echo"<pre>infoPieceSerie[$TypPi_Libelle] Qualite :".$infoPieceSerie['PiDet_Qualite']." :<br>";
				print_r($infoPieceSerie);*/
				$infoModeleVoiture['ModVoi_Acceleration'] += $infoPieceSerie['ModPi_Acceleration'];
				$infoModeleVoiture['ModVoi_VitesseMax'] += $infoPieceSerie['ModPi_VitesseMax'];
				$infoModeleVoiture['ModVoi_Freinage'] += $infoPieceSerie['ModPi_Freinage'];
				$infoModeleVoiture['ModVoi_Turbo'] += $infoPieceSerie['ModPi_Turbo'];
				$infoModeleVoiture['ModVoi_Adherence'] += $infoPieceSerie['ModPi_Adherence'];
				$infoModeleVoiture['ModVoi_SoliditeMoteur'] += $infoPieceSerie['ModPi_SoliditeMoteur'];
				$infoModeleVoiture['ModVoi_AspectExterieur'] += $infoPieceSerie['ModPi_AspectExterieur'];
				$infoModeleVoiture['ModVoi_CapaciteMoteur'] += $infoPieceSerie['ModPi_CapaciteMoteur'];
				$infoModeleVoiture['ModVoi_CapaciteMax'] += $infoPieceSerie['ModPi_CapaciteMax'];
				$infoModeleVoiture['ModVoi_Poids'] += $infoPieceSerie['ModPi_Poids'];
			}
			/*echo"<pre><br>infoModeleVoiture<br>";
			print_r($infoModeleVoiture);
			echo"</pre>";*/
		}
		else
		{
			$IdVoiture = $_GET["IdVoiture"];

			$requeteInfoVoiture = "	SELECT 	IdVoiture, ModVoi_IdModele, ModVoi_IdMarque, Marq_Libelle, ModVoi_NomModele
											FROM voiture, modele_voiture, marque
											WHERE IdModeleVoiture = ModVoi_IdModele
											AND IdMarque = ModVoi_IdMarque
											AND IdVoiture = '$IdVoiture'";
			$resultatInfoVoiture=mysql_query($requeteInfoVoiture)or die(mysql_error());
			$infoModeleVoiture=mysql_fetch_assoc($resultatInfoVoiture);
		}
	}
?>
</div>
<form action="traitement.php" name="formulaire" method="post" >
<input type="hidden" name="IdVoiture" value="<?php echo $infoModeleVoiture["IdVoiture"]; ?>">
<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
<input type="hidden" name="verificationJs" value="false">
<input type="hidden" name="Voit_IdModele" value="<?php echo $_GET['IdModeleVoiture']?>">

<table border="0" cellspacing="5"><tr><td valign="top">

	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="2" class="titre">Créer une <?php echo $infoModeleVoiture['Marq_Libelle']." ".$infoModeleVoiture['ModVoi_NomModele'];?></td>
		</tr>
		<tr class="piece">
			<th>Marque :</th>
			<td><?php echo $infoModeleVoiture['Marq_Libelle'];?></td>
		</tr>
		<tr class="piece">
			<th>Modèle :</th>
			<td><?php echo $infoModeleVoiture['ModVoi_NomModele'];?></td>
		</tr>
		<tr class="piece">
			<th>Type Carburant :</th>
			<td><?php echo $infoModeleVoiture['ModVoi_TypeCarburant'];?></td>
		</tr>
		<tr class="piece">
			<th>Victoires/Courses :</th>
			<td><?php echo $infoModeleVoiture['ModVoi_NbVictoires']."/$nbCoursesCourues"?></td>
		</tr>
		<tr class="piece">
			<th><img alt="Aspect Extérieur" src="../../images/aspect.gif"></th>
			<td><?php echo $infoModeleVoiture['ModVoi_AspectExterieur'];?></td>
		</tr>
		<tr class="piece">
			<th><img alt="Poids" src="../../images/poids.gif"></th>
			<td><?php echo $infoModeleVoiture['ModVoi_Poids']+$infoModeleVoiture['ModVoi_PoidsCarrosserie'];?> kg</td>
		</tr>
		<tr class="piece">
			<th><img alt="Prix" src="../../images/prix.gif"></th>
			<td><?php echo $infoModeleVoiture['ModVoi_PrixNeuve'];?> &euro;</td>
		</tr>
		<tr class="piece">
			<th>Manager :</th>
			<td><select name="Voit_IdManager">
<?php
	$requeteManagers = "	SELECT IdManager, Man_Nom, Man_Niveau FROM manager ORDER BY Man_Niveau, Man_Nom";
	$resultatManagers = mysql_query($requeteManagers);
	while($infoManager = mysql_fetch_assoc($resultatManagers))
	{
?>
				<option value="<?php echo $infoManager['IdManager']?>"<?php echo ($infoManager['IdManager']==$infoPieceDetachee['PiDet_IdManager'])?" selected":"";?>>(Niv <?php echo $infoManager['Man_Niveau']?>) <?php echo $infoManager['Man_Nom']?></option>
<?php
	}
?>
			</select></td>		</tr>
	</table>

</td><td>

	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="7" class="titre">Caractéristiques techniques</th>
		</tr>
		<tr class="piece">
			<th class="titre"><img alt="Accélération" height="20" src="../../images/acc.gif"></th>
			<th class="titre"><img alt="Vitesse Max" src="../../images/vmax.gif"></th>
			<th class="titre"><img alt="Freinage" src="../../images/frein.gif"></th>
			<th class="titre"><img alt="Turbo" src="../../images/turbo.gif"></th>
			<th class="titre"><img alt="Adhérence" src="../../images/adh.gif"></th>
			<th class="titre"><img alt="Solidité Moteur" src="../../images/solmot.gif"></th>
			<th class="titre"><img alt="Capacité Moteur" src="../../images/capa.gif"></th>
		</tr>
		<tr class="piece">
			<td><?php echo round($infoModeleVoiture['ModVoi_Acceleration'],2);?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_VitesseMax'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_Freinage'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_Turbo'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_Adherence'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_SoliditeMoteur'];?></td>
			<td><?php echo $infoModeleVoiture['ModVoi_CapaciteMoteur']."/".$infoModeleVoiture['ModVoi_CapaciteMax'];?></td>
		</tr>
	</table>
<br>
	<table border="0" class="liste">
		<tr class="piece">
			<th colspan="2" class="titre">Performances estimées</th>
		</tr>
		<tr class="piece">
			<th>Vitesse maximum</th>
			<td><?php echo msTOKmh($infoModeleVoiture['ModVoi_VitesseMax']);?> km/h</td>
		</tr>
		<tr class="piece">
			<th>de 0 à 100 km/h</th>
			<td><?php echo TempsAcc($infoModeleVoiture['ModVoi_Acceleration'], $infoModeleVoiture['ModVoi_VitesseMax']);?> s</td>
		</tr>
		<tr class="piece">
			<th>1000 m départ arrêté</th>
			<td><?php echo MilleMetreArrete($infoModeleVoiture['ModVoi_Acceleration'], $infoModeleVoiture['ModVoi_VitesseMax']);?>&nbsp;s</td>
		</tr>
		<tr class="piece">
			<th colspan="2" class="titre">Distances Freinage</th>
		</tr>
		<tr class="piece">
			<th>à 50 km/h</th>
			<td><?php echo DistanceFreinage($infoModeleVoiture['ModVoi_Freinage'],50);?> m</td>
		</tr>
		<tr class="piece">
			<th>à 100 km/h</th>
			<td><?php echo DistanceFreinage($infoModeleVoiture['ModVoi_Freinage'],100);?> m</td>
		</tr>
		<tr class="piece">
			<th>à 130 km/h</th>
			<td><?php echo DistanceFreinage($infoModeleVoiture['ModVoi_Freinage'],130);?> m</td>
		</tr>
		<tr class="piece">
			<th>à 150 km/h</th>
			<td><?php echo DistanceFreinage($infoModeleVoiture['ModVoi_Freinage'],150);?> m</td>
		</tr>
	</table>

</td></tr><tr><td colspan="2">

	<table border="0" class="liste" width="100%">
		<tr class="piece">
			<th colspan="5" class="titre">Pièces de sûrie</th>
		</tr>
		<tr class="piece">
			<th class="titre">Type</th>
			<th class="titre">Modèle</th>
			<th class="titre">Marque</th>
			<th class="titre">Duree de vie</th>
			<th class="titre">Poids</th>
		</tr>
<?php
	foreach($pieceSerie as $IdTypePiece => $infoPiece)
	{
?>
		<tr class="piece">
			<th><?php echo $infoPiece['TypPi_Libelle']; echo($infoPiece['TypPi_Obligatoire']==1)?"*":"";?></td>
<?php
		if(!empty($infoPiece['IdModelePiece']))
		{
?>
			<td><a href="../modele_piece/fiche.php?IdModelePiece=<?php echo $infoPiece['IdModelePiece']; ?>"><?php echo $infoPiece['ModPi_NomModele'];?></a></td>
			<td><?php echo $infoPiece['Marq_Libelle'];?></td>
			<td><?php echo $infoPiece['ModPi_DureeVieMax'];?> ans</td>
			<td><?php echo $infoPiece['ModPi_Poids'];?> kg</td>
<?php
		}
		else
		{
?>
			<td colspan="5" class="<?php echo $classe?>"><font color="#C0C0C0">N/A</font></td>
		</tr>
<?php
		}
	}
?>
	</table>

</td></tr></table>

	<table>
		<tr>
			<td align="center" colspan="3"><br>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $_GET["action"]; ?>"><br>
			</td>
			<td align="center" colspan="3"><br>
		<?php echo ($_GET["action"]=="Ajouter")? "<input type=\"reset\" value=\"Effacer saisie\">":
			"<input type=\"button\" onclick=\"annulModif(".$infoModeleVoiture['IdVoiture'].")\" value=\"Annuler les modifications\">";?><br>
			</td>
		</tr>
	</table>
</form>
<font color="#FF0000">*</font> : Champ obligatoire
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
