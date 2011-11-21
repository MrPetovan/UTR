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
  <title>Messagerie</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
<?php
	connexion ();

	if ( isset( $_GET["choix"] ) ) {
		$position = $_GET["choix"];
	}
	else {
		$position = POS_RECEPT;
	}

	if ( isset( $_POST["action"] ) ) {
		switch ( $_POST["action"] ) {
		case "post" :
			$mess = $_POST["message"];
			$idDest = $_POST["dest"];
			$sujet = $_POST["sujet"];
			$copie = isset ( $_POST["copie"] );


			if ( $mess == '' ) {
				$mess = "<I>pas de message</I>";
			}
			else {
				$mess = ereg_replace("<", "&lt;",$mess);
				$mess = ereg_replace(">", "&gt;",$mess);
				$mess = ereg_replace("\r", "<BR />",$mess);
				$mess = addslashes( $mess );
			}

			if ( isset ( $_POST["ajout_prec"] ) ) {
				$mess = $mess."<BR /><BR /><BR />".$_POST["aut_prec"]." a écrit : <BR /><BR />".addslashes ( $_POST["mess_prec"] );
			}


			$sujet = ereg_replace("<", "&lt;",$sujet);
			$sujet = ereg_replace(">", "&gt;",$sujet);
			$sujet = addslashes( $sujet );

			if ( $sujet == '' ) {
				$sujet = "<I>pas de sujet</I>";
			}

			if ( is_array ( $idDest ) ) {
				for ($i = 0; $i < count ( $idDest ); $i++) {
					Envoyer_Message ( $IdManager, $mess, $idDest[$i], $sujet, $copie );
				}
			}
			else {
				Envoyer_Message ( $IdManager, $mess, $idDest, $sujet, $copie );
			}



			$log = '<DIV align="center">Votre message a bien été envoyé</DIV><BR />';

			break;
		case "sup" :
			Supprimer_Message ( $_POST["id"] );

			$log = '<DIV align="center">Le message a bien été supprimé</DIV><BR />';

			break;
		case "supall" :
			Supprimer_Messages( $IdManager, $_POST["supall_pos"] );

			$log = '<DIV align="center">Les messages ont bien été supprimés</DIV><BR />';

			break;
		case "arc" :
			Archiver_Message ( $_POST["id"] );

			$log = '<DIV align="center">Votre message a bien été archivé</DIV><BR />';

			break;
		default :
			$log = '';
			break;
		}
	}
	$result = Obtenir_Messages ( $IdManager, $position );
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
<?php
	echo $log;
?>
				 <DIV align="center">
				 	<TABLE border = "1" width = "85%" cellspacing = "0">
						<TR>
<?php
	if ( $position == POS_RECEPT ) {
?>
							<TD align="center"><B><A HREF="messagerie.php?choix=<?php echo  POS_RECEPT ?>">Messagerie</A></B></TD>
<?php
	}
	else {
?>
							<TD align="center"><A HREF="messagerie.php?choix=<?php echo  POS_RECEPT ?>">Messagerie</A></TD>
<?php
	}
?>
<?php
	if ( $position == POS_ENVOI ) {
?>
							<TD align="center"><B><A HREF="messagerie.php?choix=<?php echo  POS_ENVOI ?>">Messages Envoyés</A></B></TD>
<?php
	}
	else {
?>
							<TD align="center"><A HREF="messagerie.php?choix=<?php echo  POS_ENVOI ?>">Messages Envoyés</A></TD>
<?php
	}
?>
<?php
	if ( $position == POS_ARCHIVE ) {
?>
							<TD align="center"><B><A HREF="messagerie.php?choix=<?php echo  POS_ARCHIVE ?>">Archives</A></B></TD>
<?php
	}
	else {
?>
							<TD align="center"><A HREF="messagerie.php?choix=<?php echo  POS_ARCHIVE ?>">Archives</A></TD>
<?php
	}
?>
							<TD align="center"><A HREF="ecriremess.php">Ecrire Message</A></TD>
						</TR>
					</TABLE>
				</DIV>
				<BR />

				<DIV align="center">
					<TABLE border = "1" width = "85%" cellspacing = "0">
						<TR>
							<TD align="center" width="45%">Sujet</TD>
<?php
	if ( $position == POS_ENVOI ) {
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
							<TD align="center" width="5%">Supr</TD>
<?php
	if ( $position != POS_ARCHIVE ) {
?>
							<TD align="center" width="5%">Archi</TD>
<?php
	}
?>
<?php
	if ( $position == POS_RECEPT ) {
?>
							<TD align="center" width="5%">Rép</TD>
<?php
	}
?>
						</TR>


<?php
	while ( $ligne = mysql_fetch_array( $result ) ) {
?>
						<TR>
<?php
		//si le message à été lu
		if ( $ligne[TABLE_MESS_LU] == LU ) {
?>
							<TD align="center" width="45%"><A HREF="liremess.php?id=<?php echo  $ligne[TABLE_MESS_ID] ?>"><?php echo  stripslashes ( $ligne[TABLE_MESS_SUJET] ) ?></A></TD>
<?php
			//si on est dans les messages envoyés
			if ( $position == POS_ENVOI ) {
?>
							<TD align="center" width="25%"><?php echo  Id_To_Nom( $ligne[TABLE_MESS_DEST] ) ?></TD>
<?php
			}
			else {
?>
							<TD align="center" width="25%"><?php echo  Id_To_Nom( $ligne[TABLE_MESS_EXP] ) ?></TD>
<?php
			}//end else message envoyé
?>
							<TD align="center" width="15%"><?php echo  $ligne[TABLE_MESS_DATE] ?></TD>
<?php
		}
		else {
?>
							<TD align="center" width="45%"><A HREF="liremess.php?id=<?php echo  $ligne[TABLE_MESS_ID] ?>"><B><?php echo  stripslashes ( $ligne[TABLE_MESS_SUJET] ) ?></B></A></TD>
<?php
			//si on est dans les messages envoyés
			if ( $position == POS_ENVOI ) {
?>
							<TD align="center" width="25%"><B><?php echo  Id_To_Nom( $ligne[TABLE_MESS_DEST] ) ?></B></TD>
<?php
			}
			else {
?>
							<TD align="center" width="25%"><B><?php echo  Id_To_Nom( $ligne[TABLE_MESS_EXP] ) ?></B></TD>
<?php
			}//end else message envoyé
?>
							<TD align="center" width="15%"><B><?php echo  $ligne[TABLE_MESS_DATE] ?></B></TD>
<?php
		}//end else message lu
?>
								<FORM method="post" action="messagerie.php?choix=<?php echo  $position ?>">
									<INPUT type="hidden" name="id" value="<?php echo  $ligne[TABLE_MESS_ID] ?>">
									<INPUT type="hidden" name="action" value="sup">
							<TD align="center" width="5%"><INPUT type="submit" value="X"></TD>
								</FORM>

<?php
	if ( $position != POS_ARCHIVE ) {
?>
								<FORM method="post" action="messagerie.php?choix=<?php echo  $position ?>">
									<INPUT type="hidden" name="id" value="<?php echo  $ligne[TABLE_MESS_ID] ?>">
									<INPUT type="hidden" name="action" value="arc">
							<TD align="center" width="5%"><INPUT type="submit" value="A"></TD>
								</FORM>
<?php
	}
?>
<?php
	if ( $position == POS_RECEPT ) {
?>
								<FORM method="post" action="ecriremess.php">
									<INPUT type="hidden" name="id" value="<?php echo  $ligne[TABLE_MESS_ID] ?>">
							<TD align="center" width="5%"><INPUT type="submit" value="->"></TD>
								</FORM>
<?php
	}
?>
						</TR>
<?php
	}//while messages
?>
					</TABLE>
				</DIV>
				<BR />
				<DIV align="center">
					<FORM method="post" action="messagerie.php?choix=<?php echo  $position ?>">
						<INPUT type="hidden" name="action" value="supall">
						<INPUT type="hidden" name="supall_pos" value="<?php echo  $position ?>">
						<INPUT type="submit" value="tout supprimer">
					</FORM>
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
