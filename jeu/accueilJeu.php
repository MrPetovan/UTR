<?php
	session_name("Joueur");
	session_start();
	error_reporting(E_ALL ^ E_NOTICE);
	include('../include/connexion.inc.php');
	if($_SESSION['IdJoueur']!="")
	{
		header("location:joueur/stat.php");
		exit;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>UTR : Jouer !</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="3">
<?php
	include("../frame/titre.php");
?>
    </td>
	</tr>
	<tr>
		<td width="162" valign="top">
<?php
	include("../frame/menu.php");
?>
		</td>
		<td width="586">
<p>&nbsp;</p>

<table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="/UTR/design/spacer.gif" width="27" height="64"></td>
          <td><table cellspacing=0 cellpadding=4 width="502" border=0>
              <tbody>
                <tr>
                  <td width="549"> <table cellspacing=0 cellpadding=0 width="100%" border=0>
                      <tbody>
                        <tr>
                          <td width=482
                background="/UTR/design/nav.jpg"
                height=34><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>
                              Bienvenue dans le jeu !</b></div></td>
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
                          <td width=6
                background="/UTR/design/tile_gauche.gif">&nbsp;</td>
                          <td> <table cellspacing=0 cellpadding=10 width="100%" align=center
                  border=0>
                              <tbody>
                                <tr>
                                  <td valign=top>
                                    <table cellspacing=0 cellpadding=0 width="100%"
                        align=center border=0>
                                      <tbody>
                                        <tr>
                                          <td width="100%" height=1 bgcolor=#0a0a0a></td>
                                        </tr>
                                        <tr>
                                          <td> <p>
                                              <?php
	if(isset($_GET['erreur']))
	{
		switch($_GET['erreur'])
		{
			case 1 :
				echo "Mauvais login/mot de passe";
				break;
			case 2 :
				echo "Vous devez saisir un login et un mot de passe";
				break;
			case 3 :
				echo "Le jeu n'est accessible qu'aux joueurs enregistrés.";
				break;
			case 4 :
				echo "Votre compte n'est pas encore activé !<br>Normalement, un mail vous a été envoyé, il contient un lien permettant d'activer votre compte. Si vous n'avez pas reçu ce mail avant un jour, contactez le Pacha.";
				break;
		}
	}
?>
                                            </p>
                                            <p>Identifiez-vous : </p>
                                            <table>
                                              <form action="login.php" method="POST">
                                                <tr>
                                                  <td>Pseudo : </td>
                                                  <td><input type="text" name="Jou_Login"></td>
                                                </tr>
                                                <tr>
                                                  <td>PassWord : </td>
                                                  <td><input type="password" name="Jou_PassWord"></td>
                                                </tr>
                                                <tr>
                                                  <td colspan="2"><input name="submit" type="submit" value="Entrer dans le jeu"></td>
                                                </tr>
                                              </form>
                                            </table><br>
                                            <a href="inscription.php">S'incrire à UTR</a> </td>
                                        </tr>
                                        <tr>
                                          <td bgcolor=#0a0a0a height=1></td>
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
                </tr>
              </tbody>
            </table>

          </td>
        </tr>
      </table>
      <p>&nbsp; </p></td>
	</tr>
	<tr>

    <td colspan="2"> <div align="center">
        <?php
	include("../frame/piedpage.php");
?>
      </div></td>
	</tr>
</table>
</body>
</html>
