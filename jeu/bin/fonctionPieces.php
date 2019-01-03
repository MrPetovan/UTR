<?php
	function creationPiece()
	{
		//echo"creationPiece<br>";
		//Gestion des niveaux
		$requeteNiveauMaximum="	SELECT MAX(Man_Reputation) FROM manager WHERE Man_Niveau < 4";
		$resultatNiveauMax = mysql_query($requeteNiveauMaximum) or die ("Requete Niveau Max<br />$requeteNiveauMaximum<br /><br />".mysql_error());
		$niveauMax = mysql_fetch_row($resultatNiveauMax);
		$niveauMax = niveauDouble($niveauMax[0],1000);

		$requeteDateDerniereCreation = "	SELECT UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(UTR_DateCreationPiece) FROM utr";
		$resultatDateDerniereCreation = mysql_query($requeteDateDerniereCreation)or die("Requete Date Dernière Creation :<br />$requeteDateDerniereCreation<br /><br />".mysql_error());
		$tempsCreation = mysql_fetch_row($resultatDateDerniereCreation);
		//echo $tempsCreation[0];
		$dureeJour = 24 * 3600;

		$requeteModelesPiece= "	SELECT IdModelePiece, ModPi_PrixNeuve FROM modele_piece WHERE ModPi_Niveau <= '$niveauMax'";
		$resultatModelesPiece = mysql_query($requeteModelesPiece);
		while($infoModele = mysql_fetch_assoc($resultatModelesPiece))
		{
			$IdModele[] = $infoModele;
		}

		if($tempsCreation[0] > $dureeJour)
		{
//On supprime tout le stock
			$requetePiecesNeuves ="	SELECT 	IdPieceDetachee,
														IdVente
											FROM piece_detachee, vente
											WHERE Ven_IdItem = IdPieceDetachee
											AND PiDet_IdManager = '-1'
											AND Ven_IdTypeVente = '2'";
			$resultatPiecesNeuves = mysql_query($requetePiecesNeuves);
			while($infoPieceNeuve = mysql_fetch_assoc($resultatPiecesNeuves))
			{
				$requeteSupprimerVente = "	DELETE FROM vente
													WHERE IdVente = '".$infoPieceNeuve['IdVente']."'";
				mysql_query($requeteSupprimerVente)or die("Requete Supprimer Vente Neuve : ".mysql_error());
				$requeteSupprimerPiece = "	DELETE FROM piece_detachee
													WHERE IdPieceDetachee = '".$infoPieceNeuve['IdPieceDetachee']."'";
				mysql_query($requeteSupprimerPiece)or die("Requete Supprimer Piece Neuve : ".mysql_error());
			}

//On réapprovisionne le stock avec tout ce qui se fait.
			foreach($IdModele as $infoModele)
			{
				$Qualite = rand(95,99);

				$requeteCreationPieceNeuve= "	INSERT INTO piece_detachee(
															PiDet_IdModele,
															PiDet_Usure,
															PiDet_UsureMesuree,
															PiDet_Qualite,
															PiDet_QualiteMesuree,
															PiDet_DateFabrication,
															PiDet_IdManager)
														VALUES (
															'".$infoModele['IdModelePiece']."',
															'0',
															NULL,
															'$Qualite',
															NULL,
															NOW(),
															'-1')";
				mysql_query($requeteCreationPieceNeuve);

				$IdPiece = mysql_fetch_row(mysql_query("SELECT MAX(IdPieceDetachee) FROM piece_detachee"));

				$requeteCreationVente = "	INSERT INTO vente(
														Ven_IdItem,
														Ven_IdTypeVente,
														Ven_Prix,
														Ven_Qualite,
														Ven_Usure)
													VALUES (
														'".$IdPiece[0]."',
														'2',
														'".$infoModele['ModPi_PrixNeuve']."',
														'100',
														'0')";
				mysql_query($requeteCreationVente);
			}
			$requeteMAJDate="	UPDATE utr
									SET UTR_DateCreationPiece = NOW()";
			mysql_query($requeteMAJDate);
		}

//On retire tous les invendus de plus d'une semaine.
		$requetePiecesOccaz ="	SELECT 	IdPieceDetachee,
													IdVente
										FROM piece_detachee, vente
										WHERE Ven_IdItem = IdPieceDetachee
										AND PiDet_IdManager = '-2'
										AND Ven_IdTypeVente = '2'
										AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(PiDet_DateFabrication) > '".($dureeJour*2)."'";
		$resultatPiecesOccaz = mysql_query($requetePiecesOccaz)or die("Requete Pieces Occaz : ".mysql_query());
		while($infoPieceOccaz = mysql_fetch_assoc($resultatPiecesOccaz))
		{
			$requeteSupprimerVente = "	DELETE FROM vente
												WHERE IdVente = '".$infoPieceOccaz['IdVente']."'";
			mysql_query($requeteSupprimerVente)or die("Requete Supprimer Vente Occaz : ".mysql_error());
			$requeteSupprimerPiece = "	DELETE FROM piece_detachee
												WHERE IdPieceDetachee = '".$infoPieceOccaz['IdPieceDetachee']."'";
			mysql_query($requeteSupprimerPiece)or die("Requete Supprimer Piece Occaz : ".mysql_error());
		}
//On rajoute autant de pièces d'occasion que de managers
		$nbManager = mysql_fetch_row(mysql_query("SELECT COUNT(IdManager) FROM manager"));
		for($i = 0; $i < $nbManager[0]; $i ++)
		{
			$IdHasard = array_rand($IdModele,1);

			$Usure = rand(25,75);
			$UsureAnnoncee = rand(10,50);
			$PrixVente = round((200 / 3 / sqrt($UsureAnnoncee) + 100/3)* $IdModele[$IdHasard]['ModPi_PrixNeuve'] / 100,0);
			$Qualite = rand(89,95);
			$QualiteAnnoncee = rand(95,99);

			$requeteCreationPieceOccaz="	INSERT INTO piece_detachee(
															PiDet_IdModele,
															PiDet_Usure,
															PiDet_UsureMesuree,
															PiDet_Qualite,
															PiDet_QualiteMesuree,
															PiDet_DateFabrication,
															PiDet_IdManager)
													VALUES (
															'".$IdModele[$IdHasard]['IdModelePiece']."',
															'$Usure',
															NULL,
															'$Qualite',
															NULL,
															NOW(),
															'-2')";
			mysql_query($requeteCreationPieceOccaz)or die("Requete Creation Piece Occaz : ".mysql_query());

			$IdPiece = mysql_fetch_row(mysql_query("SELECT MAX(IdPieceDetachee) FROM piece_detachee"));

			$requeteCreationVente = "	INSERT INTO vente(
													Ven_IdItem,
													Ven_IdTypeVente,
													Ven_Prix,
													Ven_Qualite,
													Ven_Usure)
												VALUES (
													'".$IdPiece[0]."',
													'2',
													'$PrixVente',
													'$QualiteAnnoncee',
													'$UsureAnnoncee')";
			mysql_query($requeteCreationVente)or die("Requete Creation Vente Occaz : ".mysql_query());
		}
	}
?>