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
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];

	/*echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	echo"</pre>";*/

	$action = $_POST['action'];

	if($action != '')
	{
		if($action=="Vendre")
		{
			$infoVente="";
			if(isset($_POST['IdVoiture']))
			{
				$infoVente['Ven_IdTypeVente']=1;
				$infoVente['Ven_IdItem'] = $_POST['IdVoiture'];
				$infoVente['ModVoi_PrixNeuve'] = $_POST['ModVoi_PrixNeuve'];
			}
			if(isset($_POST['IdPieceDetachee']))
			{
				$infoVente['Ven_IdTypeVente']=2;
				$infoVente['Ven_IdItem']=$_POST['IdPieceDetachee'];
			}
		}
		else
		{
			$IdVente = $_POST["IdVente"];

			$requeteInfoVente="	SELECT 	IdVente, Ven_IdItem, Ven_IdTypeVente, Ven_Prix, Ven_Usure, Ven_Qualite
										FROM vente
										WHERE IdVente = '$IdVente'";
			$resultatInfoVente=mysql_query($requeteInfoVente)or die("Requete Info Vente : ".mysql_error());
			$infoVente=mysql_fetch_assoc($resultatInfoVente);
		}
	}
?>
<html>
<head>
	<title>UTR : Gestion d'une vente</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
		function annulModif(IdVente)
		{
			document.location="gestion.php?action=Modifier&IdVente="+IdVente;
		}
		function verifForm(form)
		{
			return true;
			with(form)
			{
				var chaineErreur = "";

				chaineErreur += is_Number(Ven_Prix.value,'',"Le prix de la vente");
<?php
	if($infoVente['Ven_IdTypeVente']==2)
	{
?>
				chaineErreur += is_Number(Ven_Usure.value,'',"L'usure annoncée");
				chaineErreur += is_Number(Ven_Qualite.value,'',"La qualite annoncée");
<?php
	}
?>
				if (chaineErreur != "")
				{
					alert("Le(s) champ(s) suivant est(sont) incorrect(s) :\n"+chaineErreur);
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
<table width="100%" border="0">
	<tr>
		<td colspan="3">
<?php
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td width="110">
<?php
	include("../../frame/menu.php");
?>
		</td>
		<td>
<?php

	if($_SESSION['Erreur']==1)
	{
?>
<table align="center"><tr><td>Il y a une ou plusieurs erreurs dans le formulaire :</td></tr></table>
<div align="center" class="erreur"><BR>
  <?php
		$infoVente = $_SESSION["Post"];
		$IdVente = $infoVente['IdVente'];
		$action = $infoVente['action'];

		foreach ($_SESSION["Codes"] as $codeErreur)
		{
			$requeteMessageErreur="	SELECT Err_Message
											FROM erreur
											WHERE IdErreur = $codeErreur";
			$resultatMessageErreur=mysql_query($requeteMessageErreur)or die(mysql_error());
			$messageErreur=mysql_fetch_assoc($resultatMessageErreur);
			echo $messageErreur["Err_Message"]."<br>";
 		}

 		unset($_SESSION['Erreur']);
 		unset($_SESSION['Post']);
 		unset($_SESSION['Codes']);
	}

	if($action != '')
	{
		switch($infoVente['Ven_IdTypeVente'])
		{
			case '1' : //Voiture entière
				$requeteInfoItem = "	SELECT	IdVoiture AS IdItem,
														Marq_Libelle,
														ModVoi_NomModele AS Item_Modele
											FROM voiture, modele_voiture, marque
											WHERE IdModeleVoiture = Voit_IdModele
											AND IdMarque = ModVoi_IdMarque
											AND IdVoiture = '".$infoVente['Ven_IdItem']."'";
				break;
			case '2' : //Pièce détachée seule
				$requeteInfoItem = "	SELECT 	IdPieceDetachee AS IdItem,
														Marq_Libelle,
														ModPi_NomModele AS Item_Modele,
														IFNULL(PiDet_UsureMesuree,'?') AS PiDet_Usure,
														IFNULL(PiDet_QualiteMesuree,'?') AS PiDet_Qualite,
														ModPi_PrixNeuve AS Item_PrixNeuf
											FROM piece_detachee, modele_piece, marque
											WHERE IdModelePiece = PiDet_IdModele
											AND IdMarque = ModPi_IdMarque
											AND IdPieceDetachee = '".$infoVente['Ven_IdItem']."'";
		}
		$resultatInfoItem = mysql_query($requeteInfoItem) or die("Requete Info Item : ".mysql_error());
		$infoItem = mysql_fetch_assoc($resultatInfoItem);

		if($infoVente['Ven_IdTypeVente'] == "1") $infoItem['Item_PrixNeuf'] = $infoVente['ModVoi_PrixNeuve'];
	}
?>
</div>
<form action="traitement.php" name="formulaire" method="post">
<input type="hidden" name="IdVente" value="<?php echo $infoVente['IdVente']?>">
<input type="hidden" name="Ven_IdItem" value="<?php echo $infoVente['Ven_IdItem']?>">
<input type="hidden" name="Ven_IdTypeVente" value="<?php echo $infoVente['Ven_IdTypeVente']?>">
<input type="hidden" name="ModVoi_PrixNeuve" value="<?php echo $infoVente['ModVoi_PrixNeuve']?>">
<input type="hidden" name="action" value="<?php echo $_GET['action']?>">
<input type="hidden" name="verificationJs" value="false">
<div align="center">
	<table border="1">
		<tr>
			<th colspan="2">
<?php
	if($IdVente!='')
	{
		echo "Modifier la vente de votre ";
	}
	else
	{
		echo "Vendre votre ";

	}
	echo ($infoVente['Ven_IdTypeVente']==1)?$infoItem['Marq_Libelle']." ".$infoItem['Item_Modele']:$infoItem['Item_Modele']." ".$infoItem['Marq_Libelle'];
?>
			</th>
		</tr>
		<tr>
			<th>Prix neuve :</th>
			<td colspan="1"><?php echo $infoItem['Item_PrixNeuf']?> &euro;</td>
		</tr>
		<tr>
			<th>Prix demandé<font color="#FF0000">*</font> :</th>
			<td colspan="1"><input type="text" size="7" name="Ven_Prix" value="<?php echo (isset($infoVente['Ven_Prix']))?$infoVente['Ven_Prix']:$infoItem['Item_PrixNeuf']?>"> &euro;</td>
		</tr>
<?php
	if($infoVente['Ven_IdTypeVente']==2)
	{
?>
		<tr>
			<th>Usure :</th>
			<td colspan="1"><?php echo $infoItem['PiDet_Usure'];?> %</td>
		</tr>
		<tr>
			<th>Usure annoncée<font color="#FF0000">*</font> :</th>
			<td colspan="1"><input type="text" size="7" name="Ven_Usure" value="<?php echo (isset($infoVente['Ven_Usure']))?$infoVente['Ven_Usure']:$infoItem['PiDet_Usure'];?>"> %</td>
		</tr>
		<tr>
			<th>Qualité :</th>
			<td colspan="1"><?php echo $infoItem['PiDet_Qualite'];?> %</td>
		</tr>
		<tr>
			<th>Qualité annoncée<font color="#FF0000">*</font> :</th>
			<td colspan="1"><input type="text" size="7" name="Ven_Qualite" value="<?php echo (isset($infoVente['Ven_Qualite']))?$infoVente['Ven_Qualite']:$infoItem['PiDet_Qualite'];?>"> %</td>
		</tr>
<?php
	}
?>
	</table>
<br>
	<table>
		<tr>
			<td align="center" colspan="3"><br>
				<input name="action" type="submit" onclick="return verifForm(this.form)" value="<?php echo $action?>"><br>
			</td>
			<td align="center" colspan="3"><br>
		<?php echo ($_GET["action"]=="Vendre")? "<input type=\"reset\" value=\"Effacer saisie\">":
			"<input type=\"button\" onclick=\"annulModif(".$infoVente['IdVente'].")\" value=\"Annuler les modifications\">";?><br>
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
