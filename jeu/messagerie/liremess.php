<?php
	session_name("Joueur");
	session_start();

	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('connexion.inc.php');
	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Jou_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>Messagerie - Lecture d'un message</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
<?php
	connexion ();

	$tab = Obtenir_Message ( $_GET["id"] );

	if ( $tab[TABLE_MESS_LU] == NON_LU ) {
		Marquer_Lu ( $_GET["id"] );
	}
?>
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
				<DIV align="center">
				 	<TABLE border = "1" width = "85%" cellspacing = "0">
						<TR>
							<TD align="center"><A HREF="messagerie.php?choix=<?php echo  POS_RECEPT ?>">Messagerie</A></TD>
							<TD align="center"><A HREF="messagerie.php?choix=<?php echo  POS_ENVOI ?>">Messages Envoyés</A></TD>
							<TD align="center"><A HREF="messagerie.php?choix=<?php echo  POS_ARCHIVE ?>">Archives</A></TD>
							<TD align="center"><A HREF="ecriremess.php">Ecrire Message</A></TD>
						</TR>
					</TABLE>
				</DIV>
				<BR />
				<DIV align="center">
					<TABLE border="0" width="85%">
						<TR>
						<TD align="center">
							<FORM method="post" action="messagerie.php">
								<INPUT type="hidden" name="id" value="<?php echo  $_GET["id"] ?>">
								<INPUT type="hidden" name="action" value="sup">
								<INPUT type="submit" value="Supprimer">
							</FORM>
						</TD>
<?php
	if ( $tab[TABLE_MESS_POS] != POS_ARCHIVE ) {
?>
						<TD align="center">
							<FORM method="post" action="messagerie.php">
								<INPUT type="hidden" name="id" value="<?php echo  $_GET["id"] ?>">
								<INPUT type="hidden" name="action" value="arc">
								<INPUT type="submit" value="Archiver">
							</FORM>
						</TD>
<?php
	}

	if ( $tab[TABLE_MESS_POS] == POS_RECEPT ) {
?>
						<TD align="center">
							<FORM method="post" action="ecriremess.php">
								<INPUT type="hidden" name="id" value="<?php echo  $_GET["id"] ?>">
								<INPUT type="submit" value="Répondre">
							</FORM>
						</TD>
<?php
	}
?>
					</TR>
					</TABLE>
				</DIV>

				<DIV align="center">
					<TABLE border = "1" width = "85%" cellspacing = "0">
						<TR>
							<TD align="center" width="60%">Sujet</TD>
<?php
	if ( $tab[TABLE_MESS_POS] == POS_ENVOI ) {
?>
							<TD align="center" width="25%">Destinataire</TD>
<?php
	}
	else {
?>
	 						<TD align="center" width="25%">Expéditeur</TD>
<?php
	}
?>
	 						<TD align="center" width="15%">Date</TD>
						</TR>
						<TR>
							<TD align="center" width="60%"><?php echo  stripslashes ( $tab[TABLE_MESS_SUJET] ) ?></TD>
<?php
	if ( $tab[TABLE_MESS_POS] == POS_ENVOI ) {
?>
							<TD align="center" width="25%"><?php echo  Id_To_Nom ( $tab[TABLE_MESS_DEST] ) ?></TD>
<?php
	}
	else {
?>
							<TD align="center" width="25%"><?php echo  Id_To_Nom ( $tab[TABLE_MESS_EXP] ) ?></TD>
<?php
	}
?>
							<TD align="center" width="15%"><?php echo  $tab[TABLE_MESS_DATE] ?></TD>
						</TR>

						<TR>
							<TD colspan="3"><?php echo  stripslashes ( $tab[TABLE_MESS_CONTENU] ) ?></TD>
						</TR>
	 				</TABLE>
				</DIV>
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
