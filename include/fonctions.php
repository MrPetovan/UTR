<?php
/* Cette fonction d�termine si une pi�ce est cass�e ou pas*/
	function dispoPiece($IdPiece)
	{
		$requeteUsurePiece = "	SELECT	IdPieceDetachee,
													ModPi_DureeVieMax,
													PiDet_Usure,
													PiDet_Qualite,
													(UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(PiDet_DateFabrication)) AS PiDet_Age
										FROM piece_detachee, modele_piece
										WHERE IdModelePiece = PiDet_IdModele
										AND IdPieceDetachee = '$IdPiece'";
		$resultatUsurePiece = mysql_query($requeteUsurePiece)or die("Requete Usure Piece :<br>$requeteUsurePiece<br><br>".mysql_query());
		$usurePiece = mysql_fetch_assoc($resultatUsurePiece);
		//echo "Usure : ".$usurePiece['PiDet_Usure'];
		$dureeVieActuelle = $usurePiece['ModPi_DureeVieMax']*($usurePiece['PiDet_Qualite']/100) * sqrt(100 - $usurePiece['PiDet_Usure']) / 10;
		//Passage aux secondes
		$dureeVieActuelle *= 365*24*60*60;
		//echo "dispoPiece($IdPiece) : Piece Detachee n�".$usurePiece['IdPieceDetachee']." : DVA = $dureeVieActuelle <=> ".$usurePiece['PiDet_Age']." = Age<br>";
		//Pi�ce cass�e
		return($usurePiece['PiDet_Age'] < $dureeVieActuelle);
	}

	function dispoVoiture($IdVoiture)
	{
		$requeteTypesObligatoires="	SELECT IdTypePiece, TypPi_Libelle
												FROM type_piece
												WHERE TypPi_Obligatoire = '1'";
		//echo $requeteTypesObligatoires."<br>";
		$resultatTypesObligatoires = mysql_query($requeteTypesObligatoires)or die(mysql_error());
		while($typeObligatoire = mysql_fetch_assoc($resultatTypesObligatoires))
		{
			$requeteIdPiece="	SELECT Voit_Id".ereg_replace(" ","",$typeObligatoire['TypPi_Libelle'])."
									FROM voiture
									WHERE IdVoiture = '$IdVoiture'";
			$IdPiece = mysql_fetch_row(mysql_query($requeteIdPiece));
			//Pi�ce manque
			if(empty($IdPiece[0])) return 0;
			else
			{
				if(!dispoPiece($IdPiece[0])) return -1;
			}
		}
		//Tout est ok
		return 1;
	}

	function affichageTemps($temps)
	{
		$heures = intval($temps / 3600000);
		$temps -= $heures * 3600000;
		$minutes = intval($temps / 60000);
		$temps -= $minutes * 60000;
		$secondes = intval($temps / 1000);
		$temps -= $secondes *1000;
		$chaine ="";
		if(!empty($heures)) $chaine .= "$heuresh ";
		$chaine .= "$minutes' $secondes\" $temps";

		return($chaine);
	}

	function msTOkmh($vitesse)
	{
		return(round($vitesse*3.6,0));
	}

	function longueurCourse($IdTroncon)
	{
		$requeteTroncon = "	SELECT Tron_IdTronconSuivant, Sec_Longueur
							FROM troncon
							INNER JOIN secteur ON IdSecteur = Tron_IdSecteur
							WHERE IdTroncon = '$IdTroncon'";
		$resultatTroncon=mysql_query($requeteTroncon)or die(mysql_error());
		$infoTroncon=mysql_fetch_assoc($resultatTroncon);

		$IdTronconSuivant=$infoTroncon['Tron_IdTronconSuivant'];
		$longueurTroncon=$infoTroncon['Sec_Longueur'];

		if(empty($IdTronconSuivant))
			return $longueurTroncon;
		else
			return($longueurTroncon + longueurCourse($IdTronconSuivant));
	}

	function difficulteCourse($IdTroncon)
	{
		$requeteTroncon = "	SELECT Tron_IdTronconSuivant, Sec_Longueur, Sec_VitesseMaximum
							FROM troncon,secteur
							WHERE IdSecteur = Tron_IdSecteur
							AND IdTroncon = '$IdTroncon'";
		$resultatTroncon=mysql_query($requeteTroncon)or die(mysql_error());
		$infoTroncon=mysql_fetch_assoc($resultatTroncon);

		$IdTronconSuivant=$infoTroncon['Tron_IdTronconSuivant'];
		$longueurTroncon=$infoTroncon['Sec_Longueur'];
		$vitesseTroncon=$infoTroncon['Sec_VitesseMaximum'];

		if(empty($IdTronconSuivant))
			return $longueurTroncon*$vitesseTroncon;
		else
			return($longueurTroncon*$vitesseTroncon + difficulteCourse($IdTronconSuivant));
	}

	//Fonction permettant de calculer le temps de 0 � 100 km/h
	function TempsAcc($acceleration, $vitesseMax )
	{
		$a = (float)-pow($acceleration,2)/(4 * $vitesseMax);
		$b = $acceleration;
		$c = 100/3.6;
		return round((((-$b+(sqrt(pow($a,2)-(4*$a*$c))))/(2*$a))),2);
	}
	//Fonction permettant de calculer le temps du 1000 m d�part arr�t�
	function MilleMetreArrete($acceleration, $vitesseMax)
	{
		$vInit = 0;
		$vitesseMax = $vitesseMax;
		$distance = 1000;
		$Acceleration = $acceleration;

		$tempsVitesseMax = 2 * $Acceleration * $vitesseMax /($Acceleration*$Acceleration);
		$distanceVitesseMax = 	((-$Acceleration * $Acceleration) / (12 * $vitesseMax)) * $tempsVitesseMax * $tempsVitesseMax * $tempsVitesseMax +
								($Acceleration / 2) * $tempsVitesseMax * $tempsVitesseMax;
		if($distanceVitesseMax > $distance)
		{
			$tempsTotalTroncon = calcTemps($Acceleration,$Acceleration,$vitesseMax,$vInit,$distance);
		}
		else
		{
			$tempsTotalTroncon = $tempsVitesseMax + ($distance - $distanceVitesseMax)/ $vitesseMax;
		}

		return round($tempsTotalTroncon,2);
	}

	function DistanceFreinage($freinage, $vitesse)
	{
		$vitesse = ($vitesse*1000)/3600;
		$temps=$vitesse/$freinage;
		$distance=-$freinage*pow($temps,2)/2+$vitesse*$temps;
		return round($distance,2);
	}

	function creerVoiture($IdModeleVoiture, $IdManager)
	{
		//Cr�ation des pi�ces
		$requetePiecesDefaut= "	SELECT	ModVoi_IdInjection,
													ModVoi_IdTurbo,
													ModVoi_IdRefroidissement,
													ModVoi_IdBlocMoteur,
													ModVoi_IdTransmission,
													ModVoi_IdEchappement,
													ModVoi_IdJantes,
													ModVoi_IdPneus,
													ModVoi_IdFreins,
													ModVoi_IdAmortisseurs,
													ModVoi_IdCarrosserie,
													ModVoi_IdSpoiler,
													ModVoi_IdOptiques,
													ModVoi_IdAileron,
													ModVoi_IdChassis,
													ModVoi_IdPucedeContr�le,
													ModVoi_IdNOS,
													ModVoi_IdN�ons,
													ModVoi_IdSono
										FROM modele_voiture
										WHERE IdModeleVoiture = '$IdModeleVoiture'";
		$resultatPiecesDefaut = mysql_query($requetePiecesDefaut);
		$IdPiecesDefaut = mysql_fetch_assoc($resultatPiecesDefaut);

		$indexChamp = 0;
		foreach($IdPiecesDefaut as $IdModelePiece)
		{
			if($IdModelePiece == "")
				$Voit_IdPiece[substr(mysql_field_name($resultatPiecesDefaut,$indexChamp),9)] = '';
			else
			{
				$Qualite = rand(95,99);

				$requeteAjouterPiece ="	INSERT INTO piece_detachee (	PiDet_IdModele,
																						PiDet_Usure,
																						PiDet_Qualite,
																						PiDet_DateFabrication,
																						PiDet_IdManager)
												VALUES(	'$IdModelePiece',
															'0',
															'$Qualite',
															NOW(),
															'$IdManager')";
				mysql_query($requeteAjouterPiece)or die("Requete Ajouter Piece :<br>$requeteAjouterPiece<br><br>".mysql_error());
				$IdPiece = mysql_fetch_row(mysql_query("SELECT MAX(IdPieceDetachee) FROM piece_detachee"));
				$Voit_IdPiece[substr(mysql_field_name($resultatPiecesDefaut,$indexChamp),9)] = $IdPiece[0];
			}
			$indexChamp++;
		}

//Cr�ation de la voiture
		$requeteAjouterVoiture = "	INSERT INTO voiture(	Voit_IdModele,
																		Voit_IdInjection,
																		Voit_IdTurbo,
																		Voit_IdRefroidissement,
																		Voit_IdBlocMoteur,
																		Voit_IdTransmission,
																		Voit_IdEchappement,
																		Voit_IdJantes,
																		Voit_IdPneus,
																		Voit_IdFreins,
																		Voit_IdAmortisseurs,
																		Voit_IdCarrosserie,
																		Voit_IdSpoiler,
																		Voit_IdOptiques,
																		Voit_IdAileron,
																		Voit_IdChassis,
																		Voit_IdPuceDeContr�le,
																		Voit_IdNOS,
																		Voit_IdN�ons,
																		Voit_IdSono,
																		Voit_IdManager)
									VALUES(	'".$IdModeleVoiture."',
												'".$Voit_IdPiece['Injection']."',
												'".$Voit_IdPiece['Turbo']."',
												'".$Voit_IdPiece['Refroidissement']."',
												'".$Voit_IdPiece['BlocMoteur']."',
												'".$Voit_IdPiece['Transmission']."',
												'".$Voit_IdPiece['Echappement']."',
												'".$Voit_IdPiece['Jantes']."',
												'".$Voit_IdPiece['Pneus']."',
												'".$Voit_IdPiece['Freins']."',
												'".$Voit_IdPiece['Amortisseurs']."',
												'".$Voit_IdPiece['Carrosserie']."',
												'".$Voit_IdPiece['Spoiler']."',
												'".$Voit_IdPiece['Optiques']."',
												'".$Voit_IdPiece['Aileron']."',
												'".$Voit_IdPiece['Chassis']."',
												'".$Voit_IdPiece['PuceDeContr�le']."',
												'".$Voit_IdPiece['NOS']."',
												'".$Voit_IdPiece['N�ons']."',
												'".$Voit_IdPiece['Sono']."',
												'$IdManager')";
		mysql_query($requeteAjouterVoiture)or die("Requ�te Ajouter Voiture :<br>$requeteAjouterVoiture<br><br>".mysql_error());
	}

	function infoVoiture($IdVoiture,&$pieceInstallee,&$infoVoiture)
	{
		$requeteInfoVoiture = "	SELECT 	IdVoiture,
													Voit_IdModele,
													ModVoi_IdMarque,
													Marq_Libelle,
													ModVoi_NomModele,
													ModVoi_PrixNeuve,
													ModVoi_PoidsCarrosserie,
													ModVoi_TypeCarburant,
													Voit_IdManager,
													Man_Nom,
													IdVente,
													Ven_Prix
										FROM voiture
										INNER JOIN modele_voiture ON IdModeleVoiture = Voit_IdModele
										INNER JOIN marque ON IdMarque = ModVoi_IdMarque
										INNER JOIN manager ON IdManager = Voit_IdManager
										LEFT JOIN vente ON Ven_IdItem = IdVoiture AND Ven_IdTypeVente = '1'
										WHERE IdVoiture = '$IdVoiture'
										GROUP BY IdVoiture";
		$resultatInfoVoiture=mysql_query($requeteInfoVoiture) or die("Requete Info Voiture : ".mysql_error());
		$infoVoiture=mysql_fetch_assoc($resultatInfoVoiture);

		$requeteTypesPiece = "SELECT IdTypePiece, TypPi_Libelle, TypPi_Obligatoire FROM type_piece ORDER BY TypPi_Libelle";
		$resultatTypesPiece = mysql_query($requeteTypesPiece) or die("Requete Types Piece : ".mysql_error());

		while($typePiece=mysql_fetch_assoc($resultatTypesPiece))
		{
			$IdTypePiece=$typePiece['IdTypePiece'];
			$TypPi_Libelle = $typePiece['TypPi_Libelle'];
			$TypPi_Obligatoire = $typePiece['TypPi_Obligatoire'];

			$requeteInfoPieceInstallee= "	SELECT	IdPieceDetachee,
																ModPi_IdMarque,
																Marq_Libelle,
																ModPi_NomModele,
																ModPi_IdTypePiece,
																TypPi_Libelle,
																TypPi_PrixDemontage,
																TypPi_PrixEstimation,
																ModPi_Acceleration,
																ModPi_VitesseMax,
																ModPi_Freinage,
																ModPi_Turbo,
																ModPi_Adherence,
																ModPi_SoliditeMoteur,
																ModPi_AspectExterieur,
																ModPi_CapaciteMoteur,
																ModPi_CapaciteMax,
																ModPi_Poids,
																ModPi_DureeVieMax,
																PiDet_Usure,
																PiDet_UsureMesuree,
																PiDet_Qualite,
																PiDet_QualiteMesuree,
																UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(PiDet_DateFabrication) AS PiDet_Age,
																ModPi_PrixNeuve,
																PiDet_IdManager,
																IdVente
													FROM piece_detachee
													INNER JOIN modele_piece ON IdModelePiece = PiDet_IdModele
													INNER JOIN voiture ON Voit_Id".ereg_replace(" ","",$TypPi_Libelle)." = IdPieceDetachee
													INNER JOIN marque ON IdMarque = ModPi_IdMarque
													INNER JOIN type_piece ON IdTypePiece = ModPi_IdTypePiece
													LEFT JOIN vente ON Ven_IdItem = IdVoiture AND Ven_IdTypeVente = '1'
													WHERE ModPi_IdTypePiece = '$IdTypePiece'
													AND IdVoiture = '$IdVoiture'";
			$resultatPieceInstallee = mysql_query($requeteInfoPieceInstallee)or die("Requete Info Piece Installee : $requeteInfoPieceInstallee<br>".mysql_error());
			$infoPieceInstallee = mysql_fetch_assoc($resultatPieceInstallee);

			$pieceInstallee[$IdTypePiece]=$infoPieceInstallee;
			$pieceInstallee[$IdTypePiece]['TypPi_Libelle'] = $TypPi_Libelle;
			$pieceInstallee[$IdTypePiece]['TypPi_Obligatoire'] = $TypPi_Obligatoire;

			/*echo"<pre>infoPieceInstallee[$TypPi_Libelle] Qualite :".$infoPieceInstallee['PiDet_Qualite']." :<br>";
			print_r($infoPieceInstallee);*/

			$infoVoiture['Voit_Acceleration'] += $infoPieceInstallee['ModPi_Acceleration']*$infoPieceInstallee['PiDet_Qualite']/100;
			$infoVoiture['Voit_VitesseMax'] += $infoPieceInstallee['ModPi_VitesseMax']*$infoPieceInstallee['PiDet_Qualite']/100;
			$infoVoiture['Voit_Freinage'] += $infoPieceInstallee['ModPi_Freinage']*$infoPieceInstallee['PiDet_Qualite']/100;
			$infoVoiture['Voit_Turbo'] += $infoPieceInstallee['ModPi_Turbo']*$infoPieceInstallee['PiDet_Qualite']/100;
			$infoVoiture['Voit_Adherence'] += $infoPieceInstallee['ModPi_Adherence']*$infoPieceInstallee['PiDet_Qualite']/100;
			$infoVoiture['Voit_SoliditeMoteur'] += $infoPieceInstallee['ModPi_SoliditeMoteur']*$infoPieceInstallee['PiDet_Qualite']/100;
			$infoVoiture['Voit_AspectExterieur'] += $infoPieceInstallee['ModPi_AspectExterieur'];
			$infoVoiture['Voit_CapaciteMoteur'] += $infoPieceInstallee['ModPi_CapaciteMoteur'];
			$infoVoiture['Voit_CapaciteMax'] += $infoPieceInstallee['ModPi_CapaciteMax'];
			$infoVoiture['Voit_Poids'] += $infoPieceInstallee['ModPi_Poids'];
			$infoVoiture['Voit_Prix'] += $infoPieceInstallee['ModPi_PrixNeuve'];

			/*echo"<br>infoVoiture<br>";
			print_r($infoVoiture);
			echo"</pre>";*/
		}
	}
?>