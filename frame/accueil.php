<?php
	session_name("Joueur");
	session_start();
	include('../include/connexion.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>UTR : Accueil</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
</head>

<body>
<div align="center"> </div>
<div align="center">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="3" align="left"> <div align="center">
                <?php
	include("titre.php");
?>
              </div></td>
          </tr>
          <tr>
            <td width="19%" valign="top"> <?php
	include("menu.php");
?> </td>
            <td width="81%"><table width="457" border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="61"><img src="/UTR/design/spacer.gif" width="61" height="64"></td>
                  <td width="329"><p>Bienvenue dans le jeu !</p>
                    <p>Le jeu est encore en pleine construction ... Ertaï se charge
                      du code et Moi (tomy) je me charge du design. Ces deux
                      op&eacute;rations &eacute;tant des parties importantes du jeu
                      il est possible que lorsque nous modifions des pages vous
                      vous trouviez dessus ou que des bugs apparaissent. Nous
                      en sommes d&eacute;sol&eacute;. Nous esssayons toutefois
                      de travailler le soir afin d'&eacute;viter ces probl&egrave;mes
                      pendant votre surf.</p>
                    <p>A bientôt </p>
                    <p>Tomy</p>
                    </td>
                </tr>
              </table></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td><div align="center">
          <p>&nbsp;</p><table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>
                <?php
	include("piedpage.php");
?>
              </td>
              <td><img src="/UTR/design/spacer.gif" width="56" height="64"></td>
            </tr>
          </table>

        </div>
</td>
    </tr>
  </table>
</div>
</body>
</html>
