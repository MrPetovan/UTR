<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	include('../../include/fonctions.php');

	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];

	$IdTypePiece = $_GET['IdType'];

	$resultatTypePiece = mysql_query("	SELECT	IdTypePiece,
																TypPi_Libelle,
																TypPi_Obligatoire,
																TypPi_PrixDemontage,
																TypPi_PrixMontage,
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
													WHERE IdTypePiece = '$IdTypePiece'");
	$typePiece = mysql_fetch_assoc($resultatTypePiece)or die('Requete type pieces :' . mysql_error());

	$requetePiecesDetachees= "	SELECT 	IdPieceDetachee,
													ModPi_NomModele,
													ModPi_IdTypePiece,
													TypPi_Libelle,
													Marq_Libelle,
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
													PiDet_Usure,
													PiDet_UsureMesuree,
													PiDet_Qualite,
													PiDet_QualiteMesuree,
													UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(PiDet_DateFabrication) AS PiDet_Age,
													ModPi_PrixNeuve
										FROM piece_detachee
										JOIN modele_piece ON IdModelePiece = PiDet_IdModele
										JOIN marque ON IdMarque = ModPi_IdMarque
										JOIN type_piece ON IdTypePiece = ModPi_IdTypePiece
										WHERE PiDet_IdManager = '$IdManager'
										AND IdTypePiece = '$IdTypePiece'";
	$resultatPiecesDetachees = mysql_query($requetePiecesDetachees)or die('Requete pieces détachées :' . mysql_error());
?>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">

function PieceDetachee(Id,Modele,IdTypePiece,Acceleration,VitesseMax,Freinage,Turbo,Adherence,SoliditeMoteur,AspectExterieur,CapaciteMoteur,CapaciteMax,Poids,DureeVieMax,Casse,Usure,UsureMesuree,Qualite,QualiteMesuree,Age,PrixNeuve,Installee)
{
	this.Id = Id;
	this.Modele = Modele;
	this.IdTypePiece = IdTypePiece;
	if(QualiteMesuree == "")
	{
		if(Acceleration != '') this.AccelerationMesuree = '?'; else this.Acceleration = '';
		if(VitesseMax != '') this.VitesseMaxMesuree = '?'; else this.VitesseMax = '';
		if(Freinage != '') this.FreinageMesure = '?'; else this.Freinage = '';
		if(Turbo != '') this.TurboMesure = '?'; else this.Turbo = '';
		if(Adherence != '') this.AdherenceMesuree = '?'; else this.Adherence = '';
		if(SoliditeMoteur != '') this.SoliditeMoteurMesuree = '?'; else this.SoliditeMoteur = '';
		this.DureeVieMax = DureeVieMax;
		this.Qualite = Qualite;
		this.Usure = Usure;
	}
	else
	{
		if(Acceleration != '') this.AccelerationMesuree = Math.round(Acceleration*QualiteMesuree/10)/10;
		else this.Acceleration = '';
		if(VitesseMax != '') this.VitesseMaxMesuree = Math.round(VitesseMax*QualiteMesuree/10)/10;
		else this.VitesseMax = '';
		if(Freinage != '') this.FreinageMesure = Math.round(Freinage*QualiteMesuree/10)/10;
		else this.Freinage = '';
		if(Turbo != '') this.TurboMesure = Math.round(Turbo*QualiteMesuree/10)/10;
		else this.Turbo = '';
		if(Adherence != '') this.AdherenceMesuree = Math.round(Adherence*QualiteMesuree/10)/10;
		else this.Adherence = '';
		if(SoliditeMoteur != '') this.SoliditeMoteurMesuree = Math.round(SoliditeMoteur*QualiteMesuree/10)/10;
		else this.SoliditeMoteur = '';
		this.DureeVieMax = Math.round((DureeVieMax*QualiteMesuree/100)*(1-UsureMesuree/100)*10)/10;
		this.Qualite = QualiteMesuree;
		this.Usure = UsureMesuree;
	}
	this.Casse = Casse;
	this.Acceleration = Acceleration;
	this.VitesseMax = VitesseMax;
	this.Freinage = Freinage;
	this.Turbo = Turbo;
	this.Adherence = Adherence;
	this.SoliditeMoteur = SoliditeMoteur;
	this.AspectExterieur = AspectExterieur;
	this.CapaciteMoteur = CapaciteMoteur;
	this.CapaciteMax = CapaciteMax;
	this.Poids = Poids;
	this.Age = Age;
	this.PrixNeuve = PrixNeuve;
	this.Installee = Installee;
}

var piece = new Array();
<?php
	echo "piece[0] = new PieceDetachee('','','','','','','','','','','','','','','','','','','','');\n";
	while($infoPieceDetachee = mysql_fetch_assoc($resultatPiecesDetachees))
	{
		$requeteInfoVoiture = "	SELECT IdVoiture FROM voiture
										WHERE Voit_Id".str_replace(" ","",$infoPieceDetachee['TypPi_Libelle'])."='".$infoPieceDetachee['IdPieceDetachee']."'";
		$resultatInfoVoiture = mysql_query($requeteInfoVoiture)or die("Requete Info Voiture : ".mysql_error());
		$infoVoiture = mysql_fetch_assoc($resultatInfoVoiture);
		$infoPieceDetachee['Installee'] = ($infoVoiture['IdVoiture']=="")?"0":"1";

		$dureeVieActuelle = $infoPieceDetachee['ModPi_DureeVieMax']*($infoPieceDetachee['PiDet_Qualite']/100) * sqrt(100 - $infoPieceDetachee['PiDet_Usure']) / sqrt(100);
		$dureeVieActuelle *= 365*24*60*60;

		if($infoPieceDetachee['PiDet_Age'] > $dureeVieActuelle)
		{
			$PiDet_Casse = 1;
			$label = $infoPieceDetachee['ModPi_NomModele']." ".$infoPieceDetachee['Marq_Libelle'] . " (pièce cassée)";
		}
		else
		{
			$PiDet_Casse = 0;
			$label = "Remplacer par : ".$infoPieceDetachee['ModPi_NomModele']." ".$infoPieceDetachee['Marq_Libelle'];
		}

		echo "piece[".$infoPieceDetachee['IdPieceDetachee']."] = new PieceDetachee(".$infoPieceDetachee['IdPieceDetachee'].",
							\"".$label."\",
							'".$infoPieceDetachee['ModPi_IdTypePiece']."',
							'".$infoPieceDetachee['ModPi_Acceleration']."',
							'".$infoPieceDetachee['ModPi_VitesseMax']."',
							'".$infoPieceDetachee['ModPi_Freinage']."',
							'".$infoPieceDetachee['ModPi_Turbo']."',
							'".$infoPieceDetachee['ModPi_Adherence']."',
							'".$infoPieceDetachee['ModPi_SoliditeMoteur']."',
							'".$infoPieceDetachee['ModPi_AspectExterieur']."',
							'".$infoPieceDetachee['ModPi_CapaciteMoteur']."',
							'".$infoPieceDetachee['ModPi_CapaciteMax']."',
							".$infoPieceDetachee['ModPi_Poids'].",
							".$infoPieceDetachee['ModPi_DureeVieMax'].",
							".$PiDet_Casse.",
							".$infoPieceDetachee['PiDet_Usure'].",
							'".$infoPieceDetachee['PiDet_UsureMesuree']."',
							".$infoPieceDetachee['PiDet_Qualite'].",
							'".$infoPieceDetachee['PiDet_QualiteMesuree']."',
							".round($infoPieceDetachee['PiDet_Age']/(24*3600),0).",
							".$infoPieceDetachee['ModPi_PrixNeuve'].",
							".$infoPieceDetachee['Installee'].");\n";
	}

	if(isset($_GET['IdVoiture']))
	{
		$IdVoiture = $_GET['IdVoiture'];

		$pieceInstallee = array();
		$infoVoiture = array();

		infoVoiture($IdVoiture,$pieceInstallee,$caracVoiture);

		echo "var Voit_Acceleration = '".$caracVoiture['Voit_Acceleration']."';\n";
		echo "var Voit_VitesseMax = '".$caracVoiture['Voit_VitesseMax']."';\n";
		echo "var Voit_Freinage = '".$caracVoiture['Voit_Freinage']."';\n";
		echo "var Voit_Turbo = '".$caracVoiture['Voit_Turbo']."';\n";
		echo "var Voit_Adherence = '".$caracVoiture['Voit_Adherence']."';\n";
		echo "var Voit_SoliditeMoteur = '".$caracVoiture['Voit_SoliditeMoteur']."';\n";
		echo "var Voit_AspectExterieur = '".$caracVoiture['Voit_AspectExterieur']."';\n\n";
	}

	$requetePieceStock = "	SELECT 	IdPieceDetachee,
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
												PiDet_UsureMesuree,
												PiDet_QualiteMesuree,
												UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(PiDet_DateFabrication) AS PiDet_Age,
												ModPi_PrixNeuve
									FROM piece_detachee
									JOIN modele_piece ON IdModelePiece = Pidet_IdModele
									JOIN marque ON IdMarque = ModPi_IdMarque
									JOIN type_piece ON IdTypePiece = ModPi_IdTypePiece
									JOIN voiture ON Voit_Id".str_replace(" ","",$typePiece['TypPi_Libelle'])." = IdPieceDetachee
									WHERE PiDet_IdManager = '$IdManager'
									AND ModPi_IdTypePiece ='$IdTypePiece'
									AND IdVoiture = '$IdVoiture'";
	$resultatPieceStock = mysql_query($requetePieceStock) or die("requete Piece Stock : ".mysql_error());
	$pieceStock = mysql_fetch_assoc($resultatPieceStock);

	$IdPieceInstallee = $infoVoiture["Voit_Id".str_replace(' ','',$typePiece['TypPi_Libelle'])];

	if(!empty($pieceStock))
	{
		echo "var PiInst_Acceleration = '".$pieceStock['ModPi_Acceleration']."';\n";
		echo "var PiInst_VitesseMax = '".$pieceStock['ModPi_VitesseMax']."';\n";
		echo "var PiInst_Freinage = '".$pieceStock['ModPi_Freinage']."';\n";
		echo "var PiInst_Turbo = '".$pieceStock['ModPi_Turbo']."';\n";
		echo "var PiInst_Adherence = '".$pieceStock['ModPi_Adherence']."';\n";
		echo "var PiInst_SoliditeMoteur = '".$pieceStock['ModPi_SoliditeMoteur']."';\n";
		echo "var PiInst_AspectExterieur = '".$pieceStock['ModPi_AspectExterieur']."';\n";
		echo "var PieceInstallee = '1';\n";
	}
	else
	{
		echo "var PiInst_Acceleration = 0;\n";
		echo "var PiInst_VitesseMax = 0;\n";
		echo "var PiInst_Freinage = 0;\n";
		echo "var PiInst_Turbo = 0;\n";
		echo "var PiInst_Adherence = 0;\n";
		echo "var PiInst_SoliditeMoteur = 0;\n";
		echo "var PiInst_AspectExterieur = 0;\n";
		echo "var PieceInstallee = '0';\n";
	}

	echo "var prixMontage = '".$typePiece['TypPi_PrixMontage']."';\n";
	echo "var prixDemontage = '".$typePiece['TypPi_PrixDemontage']."';\n";
	echo "var prixChangement = '0';\n";
?>

function InitialiserBarres()
{
	if(!document.all)
	{
		var acceleration_verte = document.getElementById("acceleration_verte");
		var acceleration_rouge = document.getElementById("acceleration_rouge");
		var acceleration_bleue = document.getElementById("acceleration_bleue");
		var vitesse_max_verte = document.getElementById("vitesse_max_verte");
		var vitesse_max_rouge = document.getElementById("vitesse_max_rouge");
		var vitesse_max_bleue = document.getElementById("vitesse_max_bleue");
		var freinage_verte = document.getElementById("freinage_verte");
		var freinage_rouge = document.getElementById("freinage_rouge");
		var freinage_bleue = document.getElementById("freinage_bleue");
		var turbo_verte = document.getElementById("turbo_verte");
		var turbo_rouge = document.getElementById("turbo_rouge");
		var turbo_bleue = document.getElementById("turbo_bleue");
		var adherence_verte = document.getElementById("adherence_verte");
		var adherence_rouge = document.getElementById("adherence_rouge");
		var adherence_bleue = document.getElementById("adherence_bleue");
		var solidite_moteur_verte = document.getElementById("solidite_moteur_verte");
		var solidite_moteur_rouge = document.getElementById("solidite_moteur_rouge");
		var solidite_moteur_bleue = document.getElementById("solidite_moteur_bleue");
		var aspect_exterieur_verte = document.getElementById("aspect_exterieur_verte");
		var aspect_exterieur_rouge = document.getElementById("aspect_exterieur_rouge");
		var aspect_exterieur_bleue = document.getElementById("aspect_exterieur_bleue");

		acceleration_verte.width = 100;
		acceleration_rouge.width = 0;
		acceleration_bleue.width = 0;
		vitesse_max_verte.width = 100;
		vitesse_max_rouge.width = 0;
		vitesse_max_bleue.width = 0;
		freinage_verte.width = 100;
		freinage_rouge.width = 0;
		freinage_bleue.width = 0;
		turbo_verte.width = 100;
		turbo_rouge.width = 0;
		turbo_bleue.width = 0;
		adherence_verte.width = 100;
		adherence_rouge.width = 0;
		adherence_bleue.width = 0;
		solidite_moteur_verte.width = 100;
		solidite_moteur_rouge.width = 0;
		solidite_moteur_bleue.width = 0;
		aspect_exterieur_verte.width = 100;
		aspect_exterieur_rouge.width = 0;
		aspect_exterieur_bleue.width = 0;
	}
}

function Ajouter(select,text,value,disabled = false)
{
	var o=new Option(text,value);
	o.disabled = disabled;
	select.options[select.options.length]=o;
}

function InitialiserSelects(FormulaireCourant)
{
//Remplissage des select
	for (var i=1; i < piece.length; i++)
	{
		if(piece[i]!=null && piece[i].Installee == "0")
		{
			Ajouter(FormulaireCourant.IdPieceChangement,piece[i].Modele,piece[i].Id,piece[i].Casse === 1);
		}
	}
	if(!document.all)
	{
		InitialiserBarres();
	}
	else
	{
			ChangeMessage("text","Si vous ne voyez pas les barres vertes, c'est que vous ne disposez pas d'un navigateur respectant les normes Internet.<br /><a href='http://www.getfirefox.com' target='_blank'>Essayez plutôt Firefox</a> !");
			document.all.text.width = 100;
	}
}

function ChangeMessage(cp,msg)
{
	if(document.getElementById)
		document.getElementById(cp).innerHTML = msg;
}
function GetMessage(cp)
{
	if(document.getElementById)
		return document.getElementById(cp).innerHTML;
}

function afficherCarac(radio)
{
	var idPiece = radio.value;

	if(!document.all)
	{
		InitialiserBarres();

		var acceleration_verte = document.getElementById("acceleration_verte");
		var acceleration_rouge = document.getElementById("acceleration_rouge");
		var acceleration_bleue = document.getElementById("acceleration_bleue");
		var vitesse_max_verte = document.getElementById("vitesse_max_verte");
		var vitesse_max_rouge = document.getElementById("vitesse_max_rouge");
		var vitesse_max_bleue = document.getElementById("vitesse_max_bleue");
		var freinage_verte = document.getElementById("freinage_verte");
		var freinage_rouge = document.getElementById("freinage_rouge");
		var freinage_bleue = document.getElementById("freinage_bleue");
		var turbo_verte = document.getElementById("turbo_verte");
		var turbo_rouge = document.getElementById("turbo_rouge");
		var turbo_bleue = document.getElementById("turbo_bleue");
		var adherence_verte = document.getElementById("adherence_verte");
		var adherence_rouge = document.getElementById("adherence_rouge");
		var adherence_bleue = document.getElementById("adherence_bleue");
		var solidite_moteur_verte = document.getElementById("solidite_moteur_verte");
		var solidite_moteur_rouge = document.getElementById("solidite_moteur_rouge");
		var solidite_moteur_bleue = document.getElementById("solidite_moteur_bleue");
		var aspect_exterieur_verte = document.getElementById("aspect_exterieur_verte");
		var aspect_exterieur_rouge = document.getElementById("aspect_exterieur_rouge");
		var aspect_exterieur_bleue = document.getElementById("aspect_exterieur_bleue");

		if(idPiece != '')
		{
			if(piece[idPiece].Acceleration != '')
			{
				var Acceleration = Voit_Acceleration - PiInst_Acceleration + piece[idPiece].Acceleration;
				var Pourcentage = Math.floor(Acceleration * 100 / Voit_Acceleration);

				if(Pourcentage >= 100)
				{
					acceleration_verte.width = 100;
					acceleration_rouge.width = 0;
					acceleration_bleue.width = Pourcentage-100;
				}
				else
				{
					acceleration_verte.width = Pourcentage;
					acceleration_rouge.width = 100 - Pourcentage;
					acceleration_bleue.width = 0;
				}
			}
			if(piece[idPiece].VitesseMax != '')
			{
				eval("VitesseMax = "+Voit_VitesseMax+" - "+PiInst_VitesseMax+" + "+piece[idPiece].VitesseMax);
				var Pourcentage = Math.floor(VitesseMax * 100 / Voit_VitesseMax);

				if(Pourcentage >= 100)
				{
					vitesse_max_verte.width = 100;
					vitesse_max_rouge.width = 0;
					vitesse_max_bleue.width = Pourcentage-100;
				}
				else
				{
					vitesse_max_verte.width = Pourcentage;
					vitesse_max_rouge.width = 100 - Pourcentage;
					vitesse_max_bleue.width = 0;
				}
			}
			if(piece[idPiece].Freinage != '')
			{
				eval("Freinage = "+Voit_Freinage+" - "+PiInst_Freinage+" + "+piece[idPiece].Freinage);
				Pourcentage = Math.floor(Freinage * 100 / Voit_Freinage);

				if(Pourcentage >= 100)
				{
					freinage_verte.width = 100;
					freinage_rouge.width = 0;
					freinage_bleue.width = Pourcentage-100;
				}
				else
				{
					freinage_verte.width = Pourcentage;
					freinage_rouge.width = 100 - Pourcentage;
					freinage_bleue.width = 0;
				}
			}
			if(piece[idPiece].Turbo != '')
			{
				eval("Turbo = "+Voit_Turbo+" - "+PiInst_Turbo+" + "+piece[idPiece].Turbo);
				Pourcentage = Math.floor(Turbo * 100 / Voit_Turbo);

				if(Pourcentage >= 100)
				{
					turbo_verte.width = 100;
					turbo_rouge.width = 0;
					turbo_bleue.width = Pourcentage-100;
				}
				else
				{
					turbo_verte.width = Pourcentage;
					turbo_rouge.width = 100 - Pourcentage;
					turbo_bleue.width = 0;
				}
			}
			if(piece[idPiece].Adherence != '')
			{
				eval("Adherence = "+Voit_Adherence+" - "+PiInst_Adherence+" + "+piece[idPiece].Adherence);
				Pourcentage = Math.floor(Adherence * 100 / Voit_Adherence);

				if(Pourcentage >= 100)
				{
					adherence_verte.width = 100;
					adherence_rouge.width = 0;
					adherence_bleue.width = Pourcentage-100;
				}
				else
				{
					adherence_verte.width = Pourcentage;
					adherence_rouge.width = 100 - Pourcentage;
					adherence_bleue.width = 0;
				}
			}
			if(piece[idPiece].SoliditeMoteur != '')
			{
				eval("SoliditeMoteur = "+Voit_SoliditeMoteur+" - "+PiInst_SoliditeMoteur+" + "+piece[idPiece].SoliditeMoteur);
				Pourcentage = Math.floor(SoliditeMoteur * 100 / Voit_SoliditeMoteur);

				if(Pourcentage >= 100)
				{
					solidite_moteur_verte.width = 100;
					solidite_moteur_rouge.width = 0;
					solidite_moteur_bleue.width = Pourcentage-100;
				}
				else
				{
					solidite_moteur_verte.width = Pourcentage;
					solidite_moteur_rouge.width = 100 - Pourcentage;
					solidite_moteur_bleue.width = 0;
				}
			}
			if(piece[idPiece].AspectExterieur != '')
			{
				eval("AspectExt = "+Voit_AspectExterieur+" - "+PiInst_AspectExterieur+" + "+piece[idPiece].AspectExterieur);
				Pourcentage = Math.floor(AspectExt * 100 / Voit_AspectExterieur);

				if(Pourcentage >= 100)
				{
					aspect_exterieur_verte.width = 100;
					aspect_exterieur_rouge.width = 0;
					aspect_exterieur_bleue.width = Pourcentage-100;
				}
				else
				{
					aspect_exterieur_verte.width = Pourcentage;
					aspect_exterieur_rouge.width = 100 - Pourcentage;
					aspect_exterieur_bleue.width = 0;
				}
			}
		}
	}

	if(PieceInstallee)
		switch(idPiece)
		{
			case "":
				prixChangement = 0;
				break;
			case "0" :
				prixChangement = prixDemontage;
				break;
			default :
				eval("prixChangement = "+prixMontage+" + "+prixDemontage);
		}
	else
		if(IdPiece == 0)
			prixChangement = 0;
		else
			prixChangement = prixMontage;

	document.formulaire.prix.value = prixChangement;
}

function ChangeMessage(cp,msg)
{
	if(document.getElementById)
		document.getElementById(cp).innerHTML = msg;
	else
		eval("document.all."+cp+".innerHTML = "+msg);
}


function annulModif(IdVoiture)
{
	document.location="fiche.php?IdVoiture="+IdVoiture+"&page=pieces";
}

function confirmationChangement()
{
	return confirm("Les changements coûteront "+document.getElementById("prix").value+" &euro;\nVoulez-vous continuer ?");
}
	</script>
</head>
<body onLoad="InitialiserSelects(document.formulaire);">
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
		<td width="800" valign="top" align="center">

<form action="traitement.php" name="formulaire" method="post" onSubmit="return confirmationChangement()">
<input type="hidden" name="IdVoiture" value="<?php echo $caracVoiture['IdVoiture']?>" />
<input type="hidden" name="IdTypePiece" value="<?php echo $typePiece['IdTypePiece']?>" />
<input type="hidden" name="action" value="Changer" />
<input type="hidden" name="verificationJs" value="false" />
	<table class="liste">
		<tr>
			<th colspan="2" class="titre">Ajouter/Changer/Retirer : <?php echo $typePiece['TypPi_Libelle'];?></th>
		</tr>
		<tr class="piece">
			<th class="titre">Pièce actuelle</th>
			<td>
<?php
	echo ($pieceStock!="")?	"<a href=\"../piece/fiche.php?IdPieceDetachee=".$pieceStock['IdPieceDetachee']."\">".$pieceStock['ModPi_NomModele']." ".$pieceStock['Marq_Libelle']."</a>":
									"Pas de pièce installée";
?>
			</td>
		</tr>
		<tr class='piece'>
			<th class="titre">Action</th>
			<td><select id="<?php echo $typePiece['IdTypePiece']?>" name="IdPieceChangement" onChange="afficherCarac(this)">
<?php
	if($pieceStock!="")
	{?>
					<option value="<?php echo $IdPieceInstallee?>">Laisser la pièce</option>
					<option value="0">Retirer</option>
<?php }
	else
	{?>
					<option value="0">Ne rien faire</option>
<?php }
?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Coût total :</th>
			<td><input type="text" size="6" id="prix" name="PrixTotal" value="0" readonly /> &euro;</td>
		</tr>
	</table>
<br />
	<table class="liste">
		<tr>
			<th colspan="4" class='titre'>Changements sur la voiture</th>
		</tr>
		<tr>
			<th>Accélération</th>
			<td>
				<table cellspacing="0" cellpadding="0">
					<tr height="10">
						<td bgcolor="green" id="acceleration_verte" width="0"></td>
						<td bgcolor="#8B0000" id="acceleration_rouge" width="0"></td>
						<td bgcolor="navy" id="acceleration_bleue" width="0"></td>
					</tr>
				</table>
			</td>
			<td rowspan="7" id="text"></td>
		</tr>
		<tr>
			<th>Vitesse Max</th>
			<td>
				<table cellspacing="0" cellpadding="0">
					<tr height="10">
						<td bgcolor="green" id="vitesse_max_verte" width="0"></td>
						<td bgcolor="#8B0000" id="vitesse_max_rouge" width="0"></td>
						<td bgcolor="navy" id="vitesse_max_bleue" width="0"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>Freinage</th>
			<td>
				<table cellspacing="0" cellpadding="0">
					<tr height="10">
						<td bgcolor="green" id="freinage_verte" width="0"></td>
						<td bgcolor="#8B0000" id="freinage_rouge" width="0"></td>
						<td bgcolor="navy" id="freinage_bleue" width="0"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>Turbo</th>
			<td>
				<table cellspacing="0" cellpadding="0">
					<tr height="10">
						<td bgcolor="green" id="turbo_verte" width="0"></td>
						<td bgcolor="#8B0000" id="turbo_rouge" width="0"></td>
						<td bgcolor="navy" id="turbo_bleue" width="0"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>Adhérence</th>
			<td>
				<table cellspacing="0" cellpadding="0">
					<tr height="10">
						<td bgcolor="green" id="adherence_verte" width="0"></td>
						<td bgcolor="#8B0000" id="adherence_rouge" width="0"></td>
						<td bgcolor="navy" id="adherence_bleue" width="0"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>Solidité Moteur</th>
			<td>
				<table cellspacing="0" cellpadding="0">
					<tr height="10">
						<td bgcolor="green" id="solidite_moteur_verte" width="0"></td>
						<td bgcolor="#8B0000" id="solidite_moteur_rouge" width="0"></td>
						<td bgcolor="navy" id="solidite_moteur_bleue" width="0"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>Aspect Extérieur</th>
			<td>
				<table cellspacing="0" cellpadding="0">
					<tr height="10">
						<td bgcolor="green" id="aspect_exterieur_verte" width="0"></td>
						<td bgcolor="#8B0000" id="aspect_exterieur_rouge" width="0"></td>
						<td bgcolor="navy" id="aspect_exterieur_bleue" width="0"></td>
					</tr>
				</table>
			</td>

		</tr>
	</table>
<br />
	<table>
		<tr>
			<td align="center" colspan="3"><br>
				<input type="submit" value="Appliquer les changements">
			</td>
			<td align="center" colspan="3"><br><input type="button" onclick="annulModif('<?php echo $IdVoiture?>')" value="Revenir à la fiche"><br>
			</td>
		</tr>
	</table>
</form>
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
