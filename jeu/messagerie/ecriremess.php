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
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>Messagerie - Ecriture d'un message</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
<?php
	connexion ();

	if ( isset ( $_POST["id"] ) ) {
		$tab = Obtenir_Message ( $_POST["id"] );

		if ( $tab[TABLE_MESS_SUJET] == "<I>pas de sujet</I>" ) {
			$tab[TABLE_MESS_SUJET] = '';
		}

		if ( $tab[TABLE_MESS_CONTENU] == "<I>pas de message</I>" ) {
			$tab[TABLE_MESS_CONTENU] = '';
		}

		$rep = true;
	}
	else {
		$rep = false;
	}

	$admin = ( $Man_Niveau > 2 );
?>
  <script language="javascript">
function Confirm(obj) {
	alert('suis là');
	if ( obj.dest[].value == '' ) {
		alert('erreur pas de destinataire');
		return false;
	}
	else {
		alert('destinataire' + obj.dest[].value);
		return true;
	}

}
  </script>
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
							<TD align="center"><B><A HREF="ecriremess.php">Ecrire Message</A></B></TD>
						</TR>
					</TABLE>
				</DIV>
				<BR />
				<DIV align="center">
					<FORM method="post" action="messagerie.php">
						<INPUT type="hidden" name="action" value="post">
						<INPUT type="submit" value="Envoyer le message"> <!--onClick="return Confirm(this.form)">-->

				</DIV>
	 			<DIV align="center">
					<TABLE border = "1" width = "85%" cellspacing = "0">
						<TR>
							<TD align="center" width="60%">Sujet</TD>
							<TD align="center" width="25%">Destinataire</TD>
							<TD align="center" width="15%">Faire une copie</TD>
						</TR>
						<TR>
							<TD align="center" width="60%">
<?php
	if ( $rep ) {
		echo "<INPUT type=\"text\" name=\"sujet\" MAXLENGTH = \"100\" SIZE = \"66\" value=\"Re : ".stripslashes ( $tab[TABLE_MESS_SUJET] )."\">";
	}
	else {
		echo "<INPUT type=\"text\" name=\"sujet\" MAXLENGTH = \"100\" SIZE = \"66\">";
	}
?>
							</TD>
							<TD align="center" width="25%">
<?php
	if ( $rep ) {
		echo "<INPUT NAME=\"dest\" type=\"hidden\" value=\"".$tab[TABLE_MESS_EXP]."\">".Id_To_Nom( $tab[TABLE_MESS_EXP] );;
	}
	else {
		$result = Recup_Managers ();

		if ( $admin ) {
			echo "<SELECT NAME=\"dest[]\" MULTIPLE>", "\r";
		}
		else {
			echo "<SELECT NAME=\"dest\">", "\r";
		}

		$prems = true;

		while ( $ligne = mysql_fetch_array ( $result ) ) {
			if ( $prems ) {
				echo "<OPTION VALUE =\"".$ligne[TABLE_MAN_ID]."\" SELECTED>".$ligne[TABLE_MAN_NOM]."\r";
				$prems = false;
			}
			else {
				echo "<OPTION VALUE =\"".$ligne[TABLE_MAN_ID]."\">".$ligne[TABLE_MAN_NOM]."\r";
			}
		}
		echo "</SELECT>", "\r";

	}
?>
							</TD>
							<TD align="center" width="15%"><INPUT type="checkbox" name="copie"></TD>
						</TR>
						<TR>
							<TD colspan="3"><TEXTAREA name="message" ROWS="25" COLS="99"></TEXTAREA></TD>
						</TR>
<?php
	if ( $rep ) {
?>
						<TR>
							<TD colspan="3"><input name="ajout_prec" type="checkbox" checked>Ajouter le texte du message précédent</TD>
						</TR>
						<TR>
							<TD colspan="3">
							<TEXTAREA name="mess_prec" ROWS="10" COLS="99" readonly><?php echo  stripslashes ( $tab[TABLE_MESS_CONTENU] ) ?></TEXTAREA>
							</TD>
						</TR>
						<input type="hidden" name="aut_prec" value="<?php echo  Id_To_Nom ( $tab[TABLE_MESS_EXP] ) ?>">
<?php
	}
?>


					</TABLE>
				</DIV>
					</FORM>

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
