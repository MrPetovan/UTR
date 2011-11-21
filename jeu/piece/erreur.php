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
	$Man_Niveau = $_SESSION['Jou_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>UTR : Pièce introuvable !</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="3">
<?php
	include("../../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td width="22%" valign="top">
<?php
	include("../../frame/menu.php");
?>
		</td>
    	<td width="78%">
Cette pièce n'existe plus !
		</td>
	</tr>
	<tr>

    <td colspan="2"><div align="center">
        <?php
	include("../../frame/piedpage.php");
?>
      </div></td>
	</tr>
</table>
</body>
</html>
