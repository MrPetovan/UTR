<?php
	session_name("Joueur");
	session_start();
	include('../include/connexion.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>Règles</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
</head>

<body>
<table width="100%">
	<tr>
		<td colspan="3">
<?php
	include("titre.php");
?>
		</td>
	</tr>
	<tr>
		<td width="22%">
<?php
	include("menu.php");
?>
		</td>

    <td width="78%"> <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="83"><img src="/UTR/design/spacer.gif" width="59" height="64"></td>
          <td>Règles du jeu :<br /><br />
          Lexique :<br /><br />
          <u>Shift</u> : passage de vitesse.<br />
          <p>Le but de tout pilote, c'est de réaliser le shift parfait, c'est-à-dire passer la vitesse suivante au moment précis où la voiture perdra le moins de vitesse.</p>
            </td>
        </tr>
      </table>

    </td>
	</tr>
	<tr>

    <td colspan="2"> <div align="center">
        <?php
	include("piedpage.php");
?>
      </div></td>
	</tr>
</table>
</body>
</html>
