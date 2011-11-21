<?php
	session_name("Joueur");
	session_start();

	$debug = 0;

	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	include('../../include/connexion.inc.php');
	include('fonctionMath.php');
	include('../../include/fonctions.php');


	error_reporting(E_ALL ^E_NOTICE);
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];
	if($Man_Niveau < 3)
	{
		header("location:../../index.php");
	}

	function traitementCourse()
	{
		function compare($a, $b)
		{
			if(is_string($a)) return 1;
			if(is_string($b)) return -1;
			if ($a == $b) return 0;    return ($a > $b) ? 1 : -1;
		}

		$requeteNiveauxCritiques= "	SELECT Com_NiveauCritiqueShifts AS Shifts, Com_NiveauCritiqueVirage AS Virage, Com_NiveauCritiqueFreinage AS Freinage, Com_NiveauCritiqueSpe AS Spe
									FROM competence";
		$niveauCritique = mysql_fetch_assoc(mysql_query($requeteNiveauxCritiques));

		$requeteCoursesJour = "	SELECT 	IdCourse,
										Cou_Nom,
										Cou_NbTours,
										Cou_PrixEngagement,
										Cou_DensiteCirculation,
										Cou_IdTronconDepart,
										Cou_IdManager,
										IC_Position
								FROM course
								LEFT JOIN inscription_course ON IC_IdCourse = IdCourse
								WHERE Cou_Date <= CURRENT_DATE()
								AND IC_Position IS NULL
								GROUP BY IdCourse";
		$resultatCoursesJour = mysql_query($requeteCoursesJour)or die (mysql_error());
		while($infoCourse = mysql_fetch_assoc($resultatCoursesJour))
		{
	//////////////////////////////////////////
	//Initialisation de toutes les variables//
	//////////////////////////////////////////
			$IdCourse = $infoCourse['IdCourse'];

			$requeteInscriptionCourse="	SELECT 	IdInscriptionCourse,
																IdPilote,
																Pil_Nom,
																Pil_NiveauShifts,
																Pil_NiveauVirage,
																Pil_NiveauFreinage,
																Pil_NiveauSpe,
																Pil_Style,
																Pil_Chance,
																Pil_PourcentageGains,
																Pil_IdManager,
																IdVoiture,
																Marq_Libelle,
																ModVoi_NomModele,
																SUM(Pari_Montant) AS Pari_Montant
													FROM inscription_course, pilote, marque, voiture, modele_voiture
													LEFT JOIN pari ON Pari_IdInscriptionCourse = IdInscriptionCourse
													WHERE IdPilote = IC_IdPilote
													AND IdVoiture = IC_IdVoiture
													AND IdMarque = ModVoi_IdMarque
													AND IdModeleVoiture = Voit_IdModele
													AND IC_IdCourse = '$IdCourse'
													GROUP BY IdInscriptionCourse";
			$resultatInscriptionCourse = mysql_query($requeteInscriptionCourse) or die(mysql_error());
			$Cou_NbCompetiteurs = mysql_num_rows($resultatInscriptionCourse);

	//Suppression course si pas assez de participants
			if($Cou_NbCompetiteurs <= 1 )
			{
	//Suppression des tronçons
				$infoTroncon['Tron_IdTronconSuivant'] = $infoCourse['Cou_IdTronconDepart'];
				do
				{
					$IdTroncon = $infoTroncon['Tron_IdTronconSuivant'];
					$requeteInfoTroncon = "	SELECT Tron_IdTronconSuivant
													FROM troncon
													WHERE IdTroncon = '$IdTroncon'";
					$resultatInfoTroncon = mysql_query($requeteInfoTroncon);
					$infoTroncon = mysql_fetch_assoc($resultatInfoTroncon);

					$requeteSupprimerTroncon = "	DELETE FROM troncon
															WHERE IdTroncon = '$IdTroncon'";
					mysql_query($requeteSupprimerTroncon);

				}while(!empty($infoTroncon['Tron_IdTronconSuivant']));

	//Remboursement des paris éventuels et suppression de l'inscription
				if($Cou_NbCompetiteurs)
				{
					$infoConcurrent = mysql_fetch_assoc($resultatInscriptionCourse);
					$requeteInfoParis= "	SELECT IdPari, Pari_Montant, Pari_IdManager
												FROM pari
												WHERE Pari_IdInscriptionCourse = '".$infoConcurrent['IdInscriptionCourse']."'";
					$resultatInfoParis = mysql_query($requeteInfoParis) or die("Requete Info Paris : $requeteInfoParis<br>".mysql_error());
					while($infoPari = mysql_fetch_assoc($resultatInfoParis))
					{
						$requeteRembourserPari = "	UPDATE manager
															SET Man_Solde = Man_Solde + '".$infoPari['Pari_Montant']."'
															WHERE IdManager = '".$infoPari['Pari_IdManager']."'";
						mysql_query($requeteRembourserPari);
					}
					$requeteSupprimerParis = "	DELETE FROM pari
														WHERE Pari_IdInscriptionCourse = '".$infoConcurrent['IdInscriptionCourse']."'";
					mysql_query($requeteSupprimerParis);

					$requeteSupprimerInscriptionCourse = "	DELETE FROM inscription_course
																		WHERE IC_IdCourse = '$IdCourse'";
					mysql_query($requeteSupprimerInscriptionCourse);
				}
				$requeteSupprimerCourse = "	DELETE FROM course
														WHERE IdCourse = '$IdCourse'";
				mysql_query($requeteSupprimerCourse);
			}

			for($i = 0; $i < $Cou_NbCompetiteurs; $i ++)
			{
				$infoConcurrent[$i] = mysql_fetch_assoc($resultatInscriptionCourse);
				if(dispoVoiture($infoConcurrent[$i]['IdVoiture']))
				{
					$infoConcurrent[$i]['Cou_TempsParcours'] = 0;
					$infoConcurrent[$i]['Cou_VitesseInitiale'] = 0;
					$infoConcurrent[$i]['Cou_AccelerationInitiale'] = 0;

					$requetePieces= "	SELECT	Voit_IdInjection,
														Voit_IdRefroidissement,
														Voit_IdBlocMoteur,
														Voit_IdTransmission,
														Voit_IdJantes,
														Voit_IdPneus,
														Voit_IdFreins,
														Voit_IdAmortisseurs,
														Voit_IdSpoiler,
														Voit_IdOptiques,
														Voit_IdAileron,
														Voit_IdChassis,
														Voit_IdPucedeContrôle,
														Voit_IdNOS,
														Voit_IdNéons,
														Voit_IdSono
											FROM voiture
											WHERE IdVoiture = '".$infoConcurrent[$i]['IdVoiture']."'";
					$infoPieces[$i] = mysql_fetch_assoc(mysql_query($requetePieces));

					foreach($infoPieces[$i] as $typePiece => $IdPiece)
					{
						if(empty($IdPiece)) unset($infoPieces[$i][$typePiece]);
						else
						{
							$requeteInfoPiece="	SELECT 	ModPi_Acceleration AS Acceleration,
																	ModPi_VitesseMax AS VitesseMax,
																	ModPi_Freinage AS Freinage,
																	ModPi_Turbo AS Turbo,
																	ModPi_Adherence AS Adherence,
																	ModPi_SoliditeMoteur AS SoliditeMoteur,
																	ModPi_AspectExterieur AS AspectExterieur,
																	ModPi_Poids AS Poids,
																	ModPi_DureeVieMax,
																	PiDet_Usure AS Usure,
																	PiDet_Qualite AS Qualite
														FROM piece_detachee, modele_piece
														WHERE IdModelePiece = PiDet_IdModele
														AND IdPieceDetachee = '$IdPiece'";
							$resultatInfoPiece = mysql_query($requeteInfoPiece) or die("Requete Info Piece : $requeteInfoPiece<br>".mysql_error());
							$infoPieces[$i][$typePiece] = mysql_fetch_assoc($resultatInfoPiece);

							$infoConcurrent[$i]['Cou_AccelerationInitiale'] += $infoPieces[$i][$typePiece]['Acceleration'] * $infoPieces[$i][$typePiece]['Qualite'] / 100;
						}
					}
					$infoConcurrent[$i]['Cou_AccelerationInitiale'] *= (1+($infoConcurrentTour['Pil_NiveauShifts'] - $niveauCritique['Shifts'])/100);
				}
				else
				{
					$tempsCourse[$infoConcurrent[$i]['IdInscriptionCourse']] = "Non couru";
					unset($infoConcurrent[$i]);
				}
				/*echo"<pre>";
				print_r($infoConcurrent);
				echo"</pre>";*/
			}

	//Pour chaque tour//
			for($indiceTour = 0; $indiceTour < $infoCourse['Cou_NbTours']; $indiceTour ++)
			{
				echo ($debug)?$infoCourse['Cou_Nom']." : Tour ".($indiceTour+1)."<br><hr>":"";
				$IdTronconCourant = $infoCourse['Cou_IdTronconDepart'];
		//Pour chaque tronçon
				do
				{
					$requeteTronconCourant = "	SELECT IdTroncon, Tron_IdTronconSuivant, Sec_Nom, Sec_Longueur, Sec_TurboRequis, Sec_UsurePneus, Sec_UsureAmortisseurs, Sec_VitesseMaximum
														FROM troncon, secteur
														WHERE IdSecteur = Tron_IdSecteur
														AND IdTroncon = '$IdTronconCourant'";
					$infoTroncon = mysql_fetch_assoc(mysql_query($requeteTronconCourant));
					echo "<hr>Troncon : ".$infoTroncon['Sec_Nom'];
			//Pour chaque concurrent
					foreach($infoConcurrent as $i => $infoConcurrentTour)
					{
				//Calcul des caractéristiques voiture+pilote
						$Acceleration = $VitesseMaxVoiture = $Freinage = $Turbo = $Adherence = $SoliditeMoteur = $AspectExterieur = $Poids = 0;

						foreach($infoPieces[$i] as $typePiece => $infoPiece)
						{
							$Acceleration += $infoPiece['Acceleration']* $infoPiece['Qualite']/100;
							$VitesseMaxVoiture += $infoPiece['VitesseMax']* $infoPiece['Qualite']/100;
							$Freinage += $infoPiece['Freinage']* $infoPiece['Qualite']/100;
							$Turbo += $infoPiece['Turbo']* $infoPiece['Qualite']/100;
							$Adherence += $infoPiece['Adherence']* $infoPiece['Qualite']/100;
							$SoliditeMoteur += $infoPiece['SoliditeMoteur']* $infoPiece['Qualite']/100;
							$AspectExterieur  += $infoPiece['AspectExterieur']* (100-$infoPiece['Usure'])/100;
							$Poids += $infoPiece['Poids'];
						}
						$Acceleration = $Acceleration * (1+($infoConcurrentTour['Pil_NiveauShifts'] - $niveauCritique['Shifts'])/100);
						$Freinage = $Freinage * (1+($infoConcurrentTour['Pil_NiveauFreinage'] - $niveauCritique['Freinage'])/100);
						$Turbo = $Turbo * (1+($infoConcurrentTour['Pil_NiveauSpe'] - $niveauCritique['Spe'])/100);
						$Adherence = $Adherence * (1+($infoConcurrentTour['Pil_NiveauVirage'] - $niveauCritique['Virage'])/100);

						if($debug)
						{
	?>
	<table border="1">
	<tr>
		<th colspan="10">Caractéristiques du concurrent <?php echo $infoConcurrentTour['Pil_Nom']?></th>
	</tr>
	<tr>
		<th>Acc</th><th>VitMax</th><th>Frein</th><th>Turbo</th><th>Adh</th><th>SolMot</th><th>Aspect</th><th>Poids</th>
		<th>Accélération Initiale</th>
		<th>Vitesse Initiale</th>
	</tr>
	<tr>
		<td><?php echo $Acceleration?></td>
		<td><?php echo $VitesseMaxVoiture?></td>
		<td><?php echo $Freinage?></td>
		<td><?php echo $Turbo?></td>
		<td><?php echo $Adherence?></td>
		<td><?php echo $SoliditeMoteur?></td>
		<td><?php echo $AspectExterieur?></td>
		<td><?php echo $Poids?></td>
		<td><?php echo $infoConcurrentTour['Cou_AccelerationInitiale']?></td>
		<td><?php echo $infoConcurrentTour['Cou_VitesseInitiale']?></td>
	</tr>
	</table>
	<?php
						}
				///////////////////////////////
				//Calcul du temps de parcours//
				///////////////////////////////
						if($infoTroncon['Sec_VitesseMaximum'] < $VitesseMaxVoiture)
							$vitesseMax = $infoTroncon['Sec_VitesseMaximum'];
						else
							$vitesseMax = $VitesseMaxVoiture;

						if($infoConcurrentTour['Cou_VitesseInitiale'] <= $vitesseMax)
						{
					//Acceleration
							echo ($debug)?"Acceleration : ":"";
							$tempsVitesseMax = 2 * $infoConcurrentTour['Cou_AccelerationInitiale'] * $vitesseMax /($Acceleration*$Acceleration);
							$distanceVitesseMax = 	((-$Acceleration * $Acceleration) / (12 * $vitesseMax)) * $tempsVitesseMax * $tempsVitesseMax * $tempsVitesseMax +
													($infoConcurrentTour['Cou_AccelerationInitiale'] / 2) * $tempsVitesseMax * $tempsVitesseMax + $infoConcurrentTour['Cou_VitesseInitiale'] * $tempsVitesseMax;
							if($distanceVitesseMax > $infoTroncon['Sec_Longueur'])
							{
								echo ($debug)?"Pas Vitesse max : calcTemps($Acceleration,".$infoConcurrentTour['Cou_AccelerationInitiale'].", $vitesseMax,".$infoConcurrentTour['Cou_VitesseInitiale'].", ".$infoTroncon['Sec_Longueur'].")<br>":"";
								$tempsTotalTroncon = calcTemps($Acceleration,$infoConcurrent[$i]['Cou_AccelerationInitiale'], $vitesseMax,$infoConcurrent[$i]['Cou_VitesseInitiale'], $infoTroncon['Sec_Longueur']);

								echo "<br><br>Acceleration Finale : ".$infoConcurrent[$i]['Cou_AccelerationInitiale']."<br>";
								echo "Vitesse Finale : ".$infoConcurrent[$i]['Cou_VitesseInitiale']."<br>";
							}
							else
							{
								echo ($debug)?"Vitesse max<br>":"";
								$tempsTotalTroncon = $tempsVitesseMax + ($infoTroncon['Sec_Longueur'] - $distanceVitesseMax)/ $vitesseMax;

								$infoConcurrent[$i]['Cou_VitesseInitiale'] = $vitesseMax;
								$infoConcurrent[$i]['Cou_AccelerationInitiale'] = 0;
							}
						}
						else
						{
					//Freinage

							$tempsVitesseMax = ($infoConcurrentTour['Cou_VitesseInitiale'] - $vitesseMax) / $Freinage;
							$distanceVitesseMax = -$Freinage * $tempsVitesseMax * $tempsVitesseMax / 2 + $infoConcurrentTour['Cou_VitesseInitiale'] * $tempsVitesseMax;
							echo ($debug)?"Freinage<br>":"";
							if($distanceVitesseMax > $infoTroncon['Sec_Longueur'])
							{
								echo ($debug)?"Pas Vitesse max<br>":"";
								$tempsTotalTroncon = -( sqrt($infoConcurrentTour['Cou_VitesseInitiale']*$infoConcurrentTour['Cou_VitesseInitiale'] - 2 * $Freinage / $infoTroncon['Sec_Longueur'])-$infoConcurrentTour['Cou_VitesseInitiale']) / $Freinage;

								$infoConcurrent[$i]['Cou_VitesseInitiale'] = - $Freinage * $tempsTotalTroncon + $infoConcurrentTour['Cou_VitesseInitiale'];
								$infoConcurrent[$i]['Cou_AccelerationInitiale'] = $Acceleration * sqrt( 1 - $infoConcurrentTour['Cou_VitesseInitiale'] / $VitesseMaxVoiture );
								echo ($debug)?"Vitesse finale : ".$infoConcurrentTour['Cou_VitesseInitiale']."<br>":"";
								echo ($debug)?"Acceleration finale : $Acceleration * sqrt( 1 - ".$infoConcurrentTour['Cou_VitesseInitiale']." / $VitesseMaxVoiture ) = ".$infoConcurrentTour['Cou_AccelerationInitiale']."<br>":"";
							}
							else
							{
								echo ($debug)?"Vitesse max":"";
								$tempsTotalTroncon = $tempsVitesseMax + ($infoTroncon['Sec_Longueur'] - $distanceVitesseMax)/ $vitesseMax;

								$infoConcurrent[$i]['Cou_VitesseInitiale'] = $vitesseMax;
								$infoConcurrent[$i]['Cou_AccelerationInitiale'] = 0;
							}
						}
						$infoConcurrent[$i]['Cou_TempsParcours'] += $tempsTotalTroncon;
						echo "Temps tronçon : $tempsTotalTroncon s <br>";
					}
					$IdTronconCourant = $infoTroncon['Tron_IdTronconSuivant'];
				}while(!empty($IdTronconCourant));
			}
	//Traitement des résultats

			if($debug)
			{
				echo "<pre>";
				print_r($infoConcurrent);
				echo"</pre>";
			}

			foreach($infoConcurrent as $info)
				$tempsCourse[$info['IdInscriptionCourse']] = $info['Cou_TempsParcours'];

			uasort($tempsCourse,"compare");
		//	print_r($tempsCourse);
			if(!$debug)
			{
				$position = 1;
				foreach($tempsCourse as $IdInscriptionCourse => $temps)
				{
					$temps = intval($temps * 1000);
					$requetePositionPilote = "	UPDATE inscription_course, pilote, manager
														SET	IC_Position = '$position',
																IC_Temps = '$temps',
																Man_Solde = Man_Solde - ".$infoCourse['Cou_PrixEngagement']."
														WHERE IdInscriptionCourse = '$IdInscriptionCourse'
														AND IC_IdPilote = IdPilote
														AND Pil_IdManager = IdManager";
					mysql_query($requetePositionPilote)or die(mysql_error());
					$position++;
				}
			}
			$requeteGainsCourse = "	UPDATE manager, pilote, inscription_course
											SET Man_Solde = Man_Solde + ".$Cou_NbCompetiteurs * $infoCourse['Cou_PrixEngagement']."
											WHERE IdManager = Pil_IdManager
											AND IdPilote = IC_IdPilote
											AND IC_IdCourse = '$IdCourse'
											AND IC_Position = '1'";
			mysql_query($requeteGainsCourse)or die(mysql_error());

			$requeteParisTotaux = "	SELECT SUM(Pari_Montant)
											FROM pari, inscription_course
											WHERE IdInscriptionCourse = Pari_IdInscriptionCourse
											AND IC_IdCourse = '$IdCourse'";
			$montantParis = mysql_fetch_row(mysql_query($requeteParisTotaux));
			$montantParis = $montantParis[0];

			$requeteNbGagnants = "	SELECT COUNT(*)
											FROM pari, inscription_course
											WHERE IdInscriptionCourse = Pari_IdInscriptionCourse
											AND IC_IdCourse = '$IdCourse'
											AND IC_Position = '1'";
			$nbGagnants = mysql_fetch_row(mysql_query($requeteNbGagnants));
			$nbGagnants = $nbGagnants[0];

			if($nbGagnants == 0)
				$gainsPari = 0;
			else
				$gainsPari = intval($montantParis / $nbGagnants);

			$requeteGainsParis = "	UPDATE manager, pari, inscription_course
											SET Man_Solde = Man_Solde + '$gainsPari'
											WHERE IdManager = Pari_IdManager
											AND Pari_IdInscriptionCourse = IdInscriptionCourse
											AND IC_IdCourse = '$IdCourse'
											AND IC_Position = '1'";
			mysql_query($requeteGainsParis)or die(mysql_error());

			/*$requeteRAZ = " UPDATE inscription_course
							SET IC_Position = NULL,
								IC_Temps = NULL
							WHERE IC_IdCourse = '$IdCourse'";
			mysql_query($requeteRAZ);*/

			$tempsCourse = "";
		}

		$requeteMAJDate = " 	UPDATE utr
								SET UTR_DateTraitementCourse = NOW()";
		mysql_query($requeteMAJDate);
	}

	function traitementSalaire()
	{
		$requeteSalaire = "	UPDATE manager, job
									SET Man_Solde = Man_Solde + Job_Salaire
									WHERE Man_IdJob = IdJob
									AND Man_Niveau = '1'";
		mysql_query($requeteSalaire);

		$requeteMAJDate="	UPDATE utr
								SET UTR_DateDernierSalaire = NOW()";
		mysql_query($requeteMAJDate);
	}

	function creationPièce()
	{
		//Gestion des niveaux
		$requeteModelesPiece= "	SELECT IdModelePiece, ModVoi_PrixNeuve FROM modele_piece";
		$resultatModelesPiece = mysql_query($requeteModelesPiece);
		while($infoModele = mysql_fetch_assoc($resultatModelesPiece))
			$IdModele[] = $infoModele;

		$Qualite = rand(95,99);
		$IdHasard = array_rand($IdModele,1)

		$requeteCreationPiece="	INSERT INTO piece_detachee(
											PiDet_IdModele,
											PiDet_Usure,
											PiDet_UsureMesuree,
											PiDet_Qualite,
											PiDet_QualiteMesuree,
											PiDet_DateFabrication,
											PiDet_IdManager)
										VALUES (
											'".$IdModele[$IdHasard]['IdModelePiece']."',
											'0',
											NULL,
											'$Qualite',
											NULL,
											NOW(),
											'0')";
		mysql_query($requeteCreationPiece);

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
												'".$IdModele[$IdHasard]['ModPi_PrixNeuve']."',
												'100',
												'0')";
		mysql_query($requeteCreationVente);
	}


?>
</body>
</html>