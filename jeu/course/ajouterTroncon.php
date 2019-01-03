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

function msTOkmh($vitesse)
{
	return($vitesse*3.6);
}


/*	echo"<pre>";
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);*/

	$tab = $_SESSION;
	if(!empty($_GET)) unset($tab['troncons']);
	$_SESSION=$tab;
/*	print_r($_SESSION);*/
//Ajout du tronçon
	if(!empty($_POST))
	{
		$IdSecteur = $_POST['IdSecteur'];
		$troncons=$_SESSION['troncons'];
		$troncons[count($troncons)]=$IdSecteur;
		$_SESSION['troncons']=$troncons;
	}


	$i=0;
	$longueurTotale=0;
	$difficulteTotale=0;
	if(!empty($_POST))
	{
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
	}
	//echo"</pre>";
?>
<html>
<head>
	<title>UTR : Ajouter un tronçon à une course</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" language="JavaScript" src="../../include/verif.js"></script>
	<script type="text/javascript" language="JavaScript">
function Secteur(Id,Nom,Longueur,VitesseMax)
{
	this.Id = Id;
	this.Nom = Nom;
	this.Longueur = Longueur;
	this.VitesseMax = VitesseMax;
}
var SecteurSuivant = new Array();
//SecteurSuivant[0] = new Secteur("0","Terminer le circuit","0","0");
<?php
	$requeteSecteurs= "	SELECT * FROM secteur";
	$resultatSecteurs = mysql_query($requeteSecteurs)or die(mysql_error());
	$i=1;
	while($infoSecteur = mysql_fetch_assoc($resultatSecteurs))
	{
		echo "SecteurSuivant[$i] = new Secteur(".$infoSecteur['IdSecteur'].",\"".$infoSecteur['Sec_Nom']."\",".$infoSecteur['Sec_Longueur'].",".$infoSecteur['Sec_VitesseMaximum'].");\n";
		$i++;
	}
	echo "var longueurTotale = $longueurTotale;";
	echo "var difficulteTotale = $difficulteTotale;";
?>
function Ajouter(select,text,value)
{
	var o=new Option(text,value);
	select.options[select.options.length]=o;
}

function InitialiserSelect(FormulaireCourant)
{
//Remplissage des select
	for (var i=1; i < SecteurSuivant.length; i++)
	{
		Ajouter(FormulaireCourant.IdSecteur,SecteurSuivant[i].Nom,SecteurSuivant[i].Id);
	}
	changerSecteur(document.formulaire);
}
function changerSecteur(form)
{
	with(form)
	{
		var Longueur=eval("SecteurSuivant[" + (IdSecteur.selectedIndex+1)+"].Longueur");
		var VitesseMaximum=eval("SecteurSuivant[" + (IdSecteur.selectedIndex+1)+"].VitesseMax");
		Sec_Longueur.value=Longueur;
		Sec_VitesseMaximum.value=VitesseMaximum*3600/1000;
		Sec_LongueurTotale.value=longueurTotale + Longueur;
		Sec_DifficulteTotale.value = Math.round((difficulteTotale + (VitesseMaximum * Longueur) )/ (longueurTotale+Longueur));
	}
}
function annulModif(IdPilote)
{
	document.location="gestion.php?action=Modifier&IdPilote="+IdPilote;
}
	</script>
</head>
<?php
	if(isset($_GET['action']))
	{

	}
?>
<body onLoad="InitialiserSelect(document.formulaire)">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="3">
<?php
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td width="17%">
<?php
	include("../../frame/menu.php");
?>
		</td>

    <td width="83%"> <table width="90%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="2%"><img src="/UTR/design/spacer.gif" width="9" height="64"></td>
          <td width="98%">
            <div align="left"></div>
            <div align="center">
              <table cellspacing=0 cellpadding=4 width="100%" border=0>
                <tbody>
                  <tr>
                    <td width="536"> <table cellspacing=0 cellpadding=0 width="100%" border=0>
                        <tbody>
                          <tr>
                            <td width=457
                background="/UTR/design/nav.jpg"
                height=34><div align="left">&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp; &nbsp;<?php echo(isset($_GET['action']))?"Organiser une nouvelle course":"Ajouter un tronçon";?></div></td>
                            <td width="11" align=right
                background="/UTR/design/navtile.jpg">&nbsp;</td>
                            <td width="76" align=right
                background="/UTR/design/navtile.jpg"><img
                  height=34
                  src="/UTR/design/navdroite.gif"
                  width=2></td>
                          </tr>
                        </tbody>
                      </table>
                      <table width="100%" border=1 cellpadding=0 cellspacing=0 bordercolor="78000">
                        <tbody>
                          <tr>

                            <td width="524"> <table width="106%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td><table width="100%" border="1" align="center" cellspacing="1" bordercolor="780000">
                                      <form name="formulaire" action="ajouterTroncon.php" method="post">
                                        <tr>
                                          <th>Nom</th>
                                          <th>Longueur</th>
                                          <th>Vitesse Max</th>
                                        </tr>
                                        <?php
	if(!empty($_POST))
	{
		foreach($secteur as $infoSecteur)
		{
?>
                                        <tr>
                                          <td>
                                            <?php echo $infoSecteur['Sec_Nom'];?>
                                          </td>
                                          <td>
                                            <?php echo $infoSecteur['Sec_Longueur'];?>
                                            m</td>
                                          <td>
                                            <?php echo $infoSecteur['Sec_VitesseMaximum'];?>
                                            km/h</td>
                                        </tr>
                                        <?php
		}
	}
?>
                                        <tr>
                                          <td><select name="IdSecteur" onChange="changerSecteur(this.form)">
                                            </select></td>
                                          <td><input type="text" name="Sec_Longueur" value="0" readonly>
                                            m</td>
                                          <td><input type="text" name="Sec_VitesseMaximum" value="0" readonly>
                                            km/h</td>
                                        </tr>
                                        <tr>
                                          <td align="right">Longueur Totale</td>
                                          <td><input type="text" name="Sec_LongueurTotale" value="<?php echo $longueurTotale;?>" readonly>
                                            m</td>
                                          <td><input type="text" name="Sec_DifficulteTotale" value="<?php echo round($difficulteTotale/$longueurTotale,0);?>" readonly>
                                        </tr>
                                        <tr>
                                          <td><input type="submit" name="action2" value="Ajouter le secteur"></td>
                                      </form>
                                    </table></td>
                                </tr>
                                <tr>
                                  <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <form action="gestion.php" method="GET">
                                          <input type="hidden" name="action" value="Terminer">
                                          <td><div align="center">
                                              <input type="submit" value="Terminer le circuit">
                                            </div></td>
                                          <td><div align="center"><img src="/UTR/design/spacer.gif" width="56" height="64"></div></td>
                                          <td> <div align="center">
                                              <input type="checkbox" name="Sec_Boucle">
                                              Le circuit boucle</div></td>
                                        </form>
                                      </tr>
                                    </table></td>
                                </tr>
                              </table></td>
                          </tr>
                        </tbody>
                      </table>

                    </td>
                  </tr>
                </tbody>
              </table>

            </div></td>
        </tr>
      </table>

    </td>
	</tr>
	<tr>

    <td colspan="2"> <div align="center">
        <?php
	include("../../frame/piedpage.php");
?>
      </div></td>
	</tr>
</table>
</body>
</html>
