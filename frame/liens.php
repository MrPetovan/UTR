<?php
	session_name("Joueur");
	session_start();
	include('../include/connexion.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>Liens</title>
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
		<td width="22%" valign="top">
<?php
	include("menu.php");
?>
		</td>

    <td width="78%"> <table width="394" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="28"><img src="/UTR/design/spacer.gif" width="75" height="64"></td>
          <td width="360"><p>Voici quelques liens : </p>
            <p>**<a href="http://www.google.fr">Google</a> : l'un des meilleurs
              moteurs de recherche</p>
            <p>**<a href="http://tomydesign.free.fr">Tomydesign</a> : Site pour
              contacter Tomy</p></td>
        </tr>
      </table></td>
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
