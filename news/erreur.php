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
</head>

<body>
<table width="100%">
	<tr>
		<td colspan="3">
<?php
	include("../frame/titre.php");
?>
		</td>
	</tr>
	<tr>
		<td>
<?php
	include("../frame/menu.php");
?>
		</td>
		<td>
Sorry,
Vous devez être logué pour pouvoir poster une news...
		</td>
	</tr>
	<tr>
		<td colspan="2">
<?php
	include("../frame/piedpage.php");
?>
		</td>
	</tr>
</table>
</body>
</html>
