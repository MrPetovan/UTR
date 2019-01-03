<?php
	session_name("Joueur");
	session_start();
	include("../include/connexion.inc.php");
	error_reporting(E_ALL ^ E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>UTR : Les News</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
</head>
<body bgcolor="#000000">
<table width="99%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="3"> <?php
	include("titre.php");
?> </td>
        </tr>
        <tr>
          <td width="17%" height="555" valign="top"> <?php
	include("menu.php");
?> </td>
          <td width="83%" valign="top" background="/UTR/design/bg.jpg"> <div align="left"></div>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="2%"><img src="/UTR/design/spacer.gif" width="46" height="64"></td>
                <td width="98%"><p align="center">
                    <?php
	$requeteNews ="	SELECT IdNews, Nws_Titre, Nws_Texte, Nws_IdPosteur, Jou_Pseudo, Nws_NomPosteur, DATE_FORMAT( Nws_Date, '%d/%m/%Y à %H:%i') AS Nws_DateFormat
							FROM news
							LEFT JOIN joueur ON IdJoueur = Nws_IdPosteur
							WHERE Nws_Acceptee = '1'
							ORDER BY Nws_Date DESC";

	$resultatNews = mysql_query($requeteNews)or die("Erreur dans la requête de news : ".mysql_error());

	if(mysql_num_rows($resultatNews)!=0)
	{
		while($news = mysql_fetch_assoc($resultatNews))
		{
?>
                  </p><br>
                    <table cellspacing=0 cellpadding=4 width="618" border=0>
                      <tbody>
                        <tr>
                          <td width="540"> <table cellspacing=0 cellpadding=0 width="100%" border=0>
                              <tbody>
                                <tr>
                                  <td width=482
                background="/UTR/design/nav.jpg"
                height=34><div align="left">&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                                      &nbsp; &nbsp;<b><?php echo $news['Nws_Titre'];?></b></div></td>
                                  <td align=right
                background="/UTR/design/navtile.jpg">&nbsp;</td>
                                  <td align=right
                background="/UTR/design/navtile.jpg"><img
                  height=34
                  src="/UTR/design/navdroite.gif"
                  width=2></td>
                                </tr>
                              </tbody>
                            </table>
                            <table cellspacing=0 cellpadding=0 width="100%" border=0>
                              <tbody>
                                <tr>
                                  <td width=5
                background="/UTR/design/tile_gauche.gif">&nbsp;</td>
                                  <td> <table cellspacing=0 cellpadding=10 width="100%" align=center
                  border=0>
                                      <tbody>
                                        <tr>
                                          <td valign=top> <table cellspacing=0 cellpadding=0 width="100%"
border=0>
                                              <tbody>
                                              </tbody>
                                            </table>
                                            <table cellspacing=0 cellpadding=0 width="100%"
                        align=center border=0>
                                              <tbody>
                                                <tr>
                                                  <td  colspan=3 height=1></td>
                                                </tr>
                                                <tr>
                                                  <td valign=top width="2%" bgcolor=#000000>&nbsp;</td>
                                                  <td class=news valign=top width="53%"><div align="left"><b>
                                                    Date :
                                                    <?php echo $news['Nws_DateFormat'];?>
                                                    </b></div></td>
                                                  <td valign=top align=right width="45%">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                  <td bgcolor=#000000 colspan=3 height=1></td>
                                                </tr>
                                                <tr>
                                                  <td bgcolor=#780000 colspan=3 height=1></td>
                                                </tr>
                                                <tr>
                                                  <td bgcolor=#000000 colspan=3 height=1></td>
                                                </tr>
                                                <tr>
                                                  <td valign=top width="2%"
bgcolor=#000000>&nbsp;</td>
                                                  <td valign=top width="53%"><div align="left">Posté
                                                      par :<?php echo ($news['Jou_Pseudo']!='')?$news['Jou_Pseudo']:$news['Nws_NomPosteur'];?></div></td>
                                                  <td valign=top align=right width="45%">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                  <td bgcolor=#000000 colspan=3 height=1></td>
                                                </tr>
                                                <tr>
                                                  <td colspan=3>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                  <td colspan=3><?php echo nl2br(stripslashes($news['Nws_Texte']))?></td>
                                                </tr>
                                                <tr>
                                                  <td colspan=3>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                  <td colspan=3 height=1></td>
                                                </tr>
                                              </tbody>
                                            </table></td>
                                        </tr>
                                      </tbody>
                                    </table></td>
                                  <td width=2
                background="/UTR/design/tile_droit.gif">&nbsp;</td>
                                </tr>
                              </tbody>
                            </table>
                            <table cellspacing=0 cellpadding=0 width="100%" border=0>
                              <tbody>
                                <tr>
                                  <td width=482
                background="/UTR/design/bas2.gif"
                height=6></td>
                                  <td
                background="/UTR/design/bas_tile_droite.gif"
                height=6></td>
                                  <td align=right
                background="/UTR/design/bas_tile_droite.gif"
                height=6><img height=6
                  src="/UTR/design/bas_bord_droit.gif"
                  width=3></td>
                                </tr>
                              </tbody>
                            </table></td>
                          <td width="62"><img src="/UTR/design/spacer.gif" width="62" height="64"></td>
                        </tr>
                      </tbody>
                    </table>
                    <br>
                    <?php
		}
	}
	else
	{
		echo "Aucune news n'a été postée ou aucune news postée n'a été acceptée...";
	}
	?>
                    <?php
	if(isset($_SESSION['IdJoueur']))
	{
?>
                    <br>
                    <br>
                    <a href="../news/gestion.php?action=Ajouter">Poster votre
                    propre news</a><br>
                    <br>
                    <?php
	}
	if($Man_Niveau >= 3)
	{
		$requeteNewsAttente = "SELECT COUNT(*) FROM news WHERE Nws_Acceptee = '0'";
		$nbNewsAttente = mysql_fetch_row(mysql_query($requeteNewsAttente));

		{
?>
                    <a href="../news/liste.php">Lire les <?php echo $nbNewsAttente[0];?>
                    news en attente</a>
                    <?php
		}
	}
?>
                  </td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td>
      <div align="center">
        <table border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td>
              <?php
	include("piedpage.php");
?>
            </td>
            <td><img src="/UTR/design/spacer.gif" width="56" height="64"></td>
          </tr>
        </table>
      </div></td>
  </tr>
</table>
</body>
</html>
