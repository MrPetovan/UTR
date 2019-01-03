<?php
	function creationVoiture()
	{
		//echo"creationVoiture<br>";
		//Gestion des niveaux
		$requeteNiveauMaximum="	SELECT MAX(Man_Reputation) FROM manager WHERE Man_Niveau < 4";
		$resultatNiveauMax = mysql_query($requeteNiveauMaximum) or die ("Requete Niveau Max<br />$requeteNiveauMaximum<br /><br />".mysql_error());
		$niveauMax = mysql_fetch_row($resultatNiveauMax);
		$niveauMax = niveauDouble($niveauMax[0],1000);

		$requeteDateDerniereCreation = "	SELECT UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(UTR_DateCreationVoiture) FROM utr";
		$resultatDateDerniereCreation = mysql_query($requeteDateDerniereCreation)or die("Requete Date Dernière Creation :<br />$requeteDateDerniereCreation<br /><br />".mysql_error());
		$tempsCreation = mysql_fetch_row($resultatDateDerniereCreation);
		//echo $tempsCreation[0];
		$dureeJour = 24 * 3600;
$dureeJour = 0;
		$requeteModelesVoiture= "	SELECT IdModeleVoiture, ModVoi_PrixNeuve FROM modele_voiture WHERE ModVoi_Niveau <= '$niveauMax'";
		$resultatModelesVoiture = mysql_query($requeteModelesVoiture);
		while($infoModele = mysql_fetch_assoc($resultatModelesVoiture))
		{
			$IdModele[] = $infoModele;
		}

		if($tempsCreation[0] >= $dureeJour)
		{
//On supprime tout le stock
			$requeteVoituresNeuves ="	SELECT 	IdVoiture,
															IdVente
												FROM voiture, vente
												WHERE Ven_IdItem = IdVoiture
												AND Voit_IdManager = '-1'
												AND Ven_IdTypeVente = '1'";
			$resultatVoituresNeuves = mysql_query($requeteVoituresNeuves);
			while($infoVoitureNeuve = mysql_fetch_assoc($resultatVoituresNeuves))
			{
				$requeteSupprimerVente = "	DELETE FROM vente
													WHERE IdVente = '".$infoVoitureNeuve['IdVente']."'";
				mysql_query($requeteSupprimerVente)or die("Requete Supprimer Vente Neuve : ".mysql_error());
				$requeteSupprimerVoiture = "	DELETE FROM voiture
													WHERE IdVoiture = '".$infoVoitureNeuve['IdVoiture']."'";
				mysql_query($requeteSupprimerVoiture)or die("Requete Supprimer Voiture Neuve : ".mysql_error());
			}

//On réapprovisionne le stock avec tout ce qui se fait.
			foreach($IdModele as $infoModele)
			{
				creerVoiture($infoModele['IdModeleVoiture'],'-1');

				$IdVoiture = mysql_fetch_row(mysql_query("SELECT MAX(IdVoiture) FROM voiture"));

				$requeteCreationVente = "	INSERT INTO vente(
														Ven_IdItem,
														Ven_IdTypeVente,
														Ven_Prix)
													VALUES (
														'".$IdVoiture[0]."',
														'1',
														'".$infoModele['ModVoi_PrixNeuve']."')";
				mysql_query($requeteCreationVente);
			}
			$requeteMAJDate="	UPDATE utr
									SET UTR_DateCreationVoiture = NOW()";
			mysql_query($requeteMAJDate);
		}
/*
//On retire tous les invendus de plus d'une semaine.
		$requeteVoituresOccaz ="	SELECT 	IdVoiture,
													IdVente
										FROM voiture, vente
										WHERE Ven_IdItem = IdVoiture
										AND Voit_IdManager = '-2'
										AND Ven_IdTypeVente = '1'
										AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Voit_DateFabrication) > '".($dureeJour*2)."'";
		$resultatVoituresOccaz = mysql_query($requeteVoituresOccaz)or die("Requete Voitures Occaz : ".mysql_query());
		while($infoVoitureOccaz = mysql_fetch_assoc($resultatVoituresOccaz))
		{
			$requeteSupprimerVente = "	DELETE FROM vente
												WHERE IdVente = '".$infoVoitureOccaz['IdVente']."'";
			mysql_query($requeteSupprimerVente)or die("Requete Supprimer Vente Occaz : ".mysql_error());
			$requeteSupprimerVoiture = "	DELETE FROM voiture
												WHERE IdVoiture = '".$infoVoitureOccaz['IdVoiture']."'";
			mysql_query($requeteSupprimerVoiture)or die("Requete Supprimer Voiture Occaz : ".mysql_error());
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

			$requeteCreationVoitureOccaz="	INSERT INTO voiture(
															Voit_IdModele,
															Voit_Usure,
															Voit_UsureMesuree,
															Voit_Qualite,
															Voit_QualiteMesuree,
															Voit_DateFabrication,
															Voit_IdManager)
													VALUES (
															'".$IdModele[$IdHasard]['IdModeleVoiture']."',
															'$Usure',
															NULL,
															'$Qualite',
															NULL,
															NOW(),
															'-2')";
			mysql_query($requeteCreationVoitureOccaz)or die("Requete Creation Voiture Occaz : ".mysql_query());

			$IdVoiture = mysql_fetch_row(mysql_query("SELECT MAX(IdVoiture) FROM voiture"));

			$requeteCreationVente = "	INSERT INTO vente(
													Ven_IdItem,
													Ven_IdTypeVente,
													Ven_Prix,
													Ven_Qualite,
													Ven_Usure)
												VALUES (
													'".$IdVoiture[0]."',
													'2',
													'$PrixVente',
													'$QualiteAnnoncee',
													'$UsureAnnoncee')";
			mysql_query($requeteCreationVente)or die("Requete Creation Vente Occaz : ".mysql_query());
		}
*/
	}
?>