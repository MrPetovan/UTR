<?php

define('TABLE_MANAGER', "manager");
define('TABLE_MAN_ID', "IdManager");
define('TABLE_MAN_NOM', "Man_Nom");

define('TABLE_MESSAGE', "message");
define('TABLE_MESS_ID', "IdMessage");
define('TABLE_MESS_PROPRIO', "Mess_Proprietaire");
define('TABLE_MESS_CONTENU', "Mess_Contenu");
define('TABLE_MESS_SUJET', "Mess_Sujet");
define('TABLE_MESS_EXP', "Mess_Expediteur");
define('TABLE_MESS_DEST', "Mess_Destinataire");
define('TABLE_MESS_LU', "Mess_Lu");
define('TABLE_MESS_POS', "Mess_Position");
define('TABLE_MESS_DATE', "Mess_Date");

define('POS_RECEPT', "1");
define('POS_ENVOI', "2");
define('POS_ARCHIVE', "3");

define('LU', "o");
define('NON_LU', "n");

function Nom_To_Id ( $nom ) {
/*
 *Permet de retrouver l'id d'un manager suivant son nom
 *$nom : le nom du manager
 *Retourne l'id du manager, -1 si l'on ne la retrouve pas
 *Version 1.0
 */
	$requete = "SELECT ".TABLE_MAN_ID." FROM ".TABLE_MANAGER." WHERE ".TABLE_MAN_NOM." = '$nom'";

	$result = mysql_query( $requete ) or die ( "Impossible d'exécuter la requête $requete" );

	$row =mysql_fetch_row( $result );

	if ( !$row ) {
		return -1;
	}
	else {
		return $row[0];
	}

}

function Id_To_Nom ( $id ) {
/*
 *Permet de retrouver le nom d'un manager suivant son id
 *$id : l'id du manager
 *Retourne le nom du manager, chaine vide si l'on ne le retrouve pas
 *Version 1.0
 */
	$requete = "SELECT ".TABLE_MAN_NOM." FROM ".TABLE_MANAGER." WHERE ".TABLE_MAN_ID." = $id";

	$result = mysql_query( $requete ) or die ( "Impossible d'exécuter la requête $requete" );

	$row = mysql_fetch_row( $result );

	if ( !$row ) {
		return '';
	}
	else {
		return $row[0];
	}
}

function Obtenir_Messages ( $idMan, $pos ) {
/*
 *Récupère les messages d'un manager
 *$idMan : le manager dont on veut les messages
 *$pos : le type de messages souhaité
 *Retourne la ressource de résultat du mysql_query contenant le résultat de la requête
 *Version 1.0
 */
 	$requete = "SELECT * FROM ".TABLE_MESSAGE." WHERE ".TABLE_MESS_PROPRIO." = $idMan AND ".TABLE_MESS_POS." = $pos ORDER BY ".TABLE_MESS_DATE." DESC";

	$result = mysql_query ( $requete ) or die ( "Impossible d'exécuter la requête $requete" );

	return $result;
}

function Obtenir_Message ( $idMess ) {
/*
 *Permet de récupérer un message
 *$idMess : l'id du message
 *Retourne le tableau contenant le résultat, false sinon
 *Version 1.0
 */
 	$requete = "SELECT * FROM ".TABLE_MESSAGE." WHERE ".TABLE_MESS_ID." = $idMess";

	$result = mysql_query ( $requete ) or die ( "Impossible d'exécuter la requête $requete" );

	if ( $tab = mysql_fetch_array( $result ) ) {
		return $tab;
	}
	else {
		return FALSE;
	}

}

function Envoyer_Message ( $idExp, $contenu, $idDest, $sujet, $copie = FALSE) {
/*
 *Permet d'ajouter un message en base de données
 *$idExp : l'expéditeur du message
 *$contenu : le contenu du message
 *$idDest : le destinataire du message
 *$sujet : le sujet du message
 *$copie : indique s'il faut faire une copie du message ou pas
 *Version 1.0
 */
	$requete = "INSERT INTO ".TABLE_MESSAGE." (".TABLE_MESS_PROPRIO.", ".
												TABLE_MESS_CONTENU.", ".
												TABLE_MESS_SUJET.", ".
												TABLE_MESS_EXP.", ".
												TABLE_MESS_DEST.", ".
												TABLE_MESS_LU.", ".
												TABLE_MESS_POS.", ".
												TABLE_MESS_DATE.")".
						"VALUES ($idDest,
								'$contenu',
								'$sujet',
								$idExp,
								$idDest,
								'".NON_LU."', ".
								POS_RECEPT.",
								now())";

	mysql_query ( $requete ) or die ( "Impossible d'exécuter la requête $requete" );

	if ( $copie ) {
		$requete = "INSERT INTO ".TABLE_MESSAGE." (".TABLE_MESS_PROPRIO.", ".
													TABLE_MESS_CONTENU.", ".
													TABLE_MESS_SUJET.", ".
													TABLE_MESS_EXP.", ".
													TABLE_MESS_DEST.", ".
													TABLE_MESS_LU.", ".
													TABLE_MESS_POS.", ".
													TABLE_MESS_DATE.")".
							"VALUES ($idExp,
									'$contenu',
									'$sujet',
									$idExp,
									$idDest,
									'".LU."', ".
									POS_ENVOI.",
									now())";

		mysql_query ( $requete ) or die ( "Impossible d'exécuter la requête $requete" );
	}
}

function Marquer_Lu ( $idMess ) {
/*
 *Permet d'indiquer un message comme étant lu
 *$idMess : le message
 *Version 1.0
 */
 	$requete = "UPDATE ".TABLE_MESSAGE." SET ".TABLE_MESS_LU." = '".LU."' WHERE ".TABLE_MESS_ID." = $idMess";

	mysql_query ( $requete ) or die ( "Impossible d'exécuter la requête $requete" );
}

function Archiver_Message ( $idMess ) {
/*
 *Permet d'archiver un message
 *$idMess : le message
 *Version 1.1
 */
	$requete = "UPDATE ".TABLE_MESSAGE." SET ".TABLE_MESS_POS." = ".POS_ARCHIVE.", ".TABLE_MESS_LU." = '".LU."' WHERE ".TABLE_MESS_ID." = $idMess";

	mysql_query ( $requete ) or die ( "Impossible d'exécuter la requête $requete" );
}

function Supprimer_Message ( $idMess ) {
/*
 *Permet de supprimer un message
 *$idMess : le message
 *Version 1.0
 */
	$requete = "DELETE FROM ".TABLE_MESSAGE." WHERE ".TABLE_MESS_ID." = $idMess";

	mysql_query ( $requete ) or die ( "Impossible d'exécuter la requête $requete" );
}

function Supprimer_Messages ( $idMan, $pos ) {
/*
 *Permet de supprimer tout les messages d'une position
 *$idMan : le manager à qui appartient les messages
 *$pos : la position à supprimer
 *Version 1.0
 */
	$requete = "DELETE FROM ".TABLE_MESSAGE." WHERE ".TABLE_MESS_PROPRIO." = $idMan AND ".TABLE_MESS_POS." = $pos";

 	mysql_query ( $requete ) or die ( "Impossible d'exécuter la requête $requete" );
}

function Recup_Managers () {
/*
 *Permet de récupérer les noms et id de tous les managers
 *Retourne la ressource de résultat du mysql_query contenant le résultat de la requête
 *Version 1.0
 */
	$requete = "SELECT ".TABLE_MAN_ID.", ".TABLE_MAN_NOM." FROM ".TABLE_MANAGER;

	$result = mysql_query ( $requete ) or die ( "Impossible d'exécuter la requête $requete" );

	return $result;
}




?>
