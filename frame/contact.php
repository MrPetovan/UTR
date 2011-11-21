<?php
	session_name("Joueur");
	session_start();
	include('../include/connexion.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Contact</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
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

    <td width="78%"> <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="/UTR/design/spacer.gif" width="55" height="64"></td>
          <td><p>Programmeurs :<br>
            </p>
            <blockquote><a href="mailto:ertaii@aeriesguard.com">Ertaï</a><br>
              Aka Guymelef</blockquote>
            <br>
            Designer :
            <blockquote> <a href="http://tomydesign.free.fr">TomyDesign</a><br>
              R&eacute;alisation de design gratuit</blockquote>
            <br>
            Testeurs :
            <p>&nbsp;</p>
            </td>
        </tr>
      </table>
      <blockquote>
</blockquote>
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
