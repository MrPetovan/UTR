<?php
  function traitementCourse()
  {
    //echo"traitementCourse<br>";

    $debug = 0;
    function compare($a, $b)
    {
      if(is_string($a)) return 1;
      if(is_string($b)) return -1;
      if ($a == $b) return 0;    return ($a > $b) ? 1 : -1;
    }

    $requeteNiveauxCritiques="  SELECT Com_NiveauCritiqueShifts AS Shifts, Com_NiveauCritiqueVirage AS Virage, Com_NiveauCritiqueFreinage AS Freinage, Com_NiveauCritiqueSpe AS Spe
                      FROM competence";
    $niveauCritique = mysql_fetch_assoc(mysql_query($requeteNiveauxCritiques));

    $requeteCoursesJour = "  SELECT   IdCourse,
                          Cou_Nom,
                          Cou_NbTours,
                          Cou_PrixEngagement,
                          Cou_DensiteCirculation,
                          Cou_IdTronconDepart,
                          Cou_IdManager,
                          Man_Nom,
                          IC_Position
                    FROM course
                    INNER JOIN manager ON IdManager = Cou_IdManager
                    LEFT JOIN inscription_course ON IC_IdCourse = IdCourse
                    WHERE Cou_Date <= CURRENT_DATE()
                    AND IC_Position IS NULL
                    GROUP BY IdCourse";
    $resultatCoursesJour = mysql_query($requeteCoursesJour)or die ("Requete Courses Jour : ".mysql_error());

    $NbCourse = mysql_num_rows($resultatCoursesJour);
    echo ($debug)?"Nombre de courses = $NbCourse <br>":"";

//While Courses
    while($infoCourse = mysql_fetch_assoc($resultatCoursesJour))
    {
  //////////////////////////////////////////
  //Initialisation de toutes les variables//
  //////////////////////////////////////////
      $IdCourse = $infoCourse['IdCourse'];
      $infoConcurrent = [];
      $mess = "";
      $position = 1;

      $requeteInscriptionCourse="  SELECT   IdInscriptionCourse,
                                IC_Position,
                                IC_Temps,
                                IdPilote,
                                Pil_Nom,
                                Pil_Reputation,
                                Pil_Solde,
                                Pil_XPShifts,
                                Pil_XPVirage,
                                Pil_XPFreinage,
                                Pil_XPSpe,
                                Pil_Style,
                                Pil_Chance,
                                Pil_PourcentageGains,
                                IdManager,
                                Man_Nom,
                                Man_Reputation,
                                Man_Solde,
                                IdVoiture,
                                Marq_Libelle,
                                ModVoi_NomModele,
                                SUM(Pari_Montant) AS Pari_Montant
                          FROM inscription_course
                          JOIN pilote ON IdPilote = IC_IdPilote
                          JOIN manager ON IdManager = Pil_IdManager
                          JOIN voiture ON IdVoiture = IC_IdVoiture
                          JOIN modele_voiture ON IdModeleVoiture = Voit_IdModele
                          JOIN marque ON IdMarque = ModVoi_IdMarque
                          LEFT JOIN pari ON Pari_IdInscriptionCourse = IdInscriptionCourse
                          WHERE IC_IdCourse = '$IdCourse'
                          GROUP BY IdInscriptionCourse";
      if($debug) echo $requeteInscriptionCourse;
      $resultatInscriptionCourse = mysql_query($requeteInscriptionCourse) or die("Requete Inscription Course :<br>$requeteInscriptionCourse<br><br>".mysql_error());
      $Cou_NbCompetiteurs = mysql_num_rows($resultatInscriptionCourse);
      $j = $Cou_NbCompetiteurs;
      echo ($debug)?"Course : ".$infoCourse['Cou_Nom']."<br>Nombre de concurrents = $Cou_NbCompetiteurs<br>":"";
      if($debug)
        {
          echo "<pre>";
          print_r($infoCourse);
          echo "</pre>";
        }
  //Récupération de la liste des pièces pour chaque concurrent pour l'initialisation
      for($i = 0; $i < $j; $i++)
      {
        $infoConcurrent[$i] = mysql_fetch_assoc($resultatInscriptionCourse);

        if($debug)
        {
          echo "<pre>";
          //print_r($infoConcurrent[$i]);
          echo "</pre>";
        }

        //foreach($infoConcurrent as $i => $infoConcurrentTour)
        //{
          echo ($debug)?"dispoVoiture(".$infoConcurrent[$i]['IdVoiture'].") = ".dispoVoiture($infoConcurrent[$i]['IdVoiture'])."<br />":"";
      //Récupération de la liste des pièces détachées
          if(dispoVoiture($infoConcurrent[$i]['IdVoiture'])==1)
          {
            $infoConcurrent[$i]['IC_TempsParcours'] = 0;
            $infoConcurrent[$i]['IC_VitesseInitiale'] = 0;
            $infoConcurrent[$i]['IC_AccelerationInitiale'] = 0;
            $infoConcurrent[$i]['Cou_SoldeCourseManager'] = -$infoCourse['Cou_PrixEngagement'];
            $infoConcurrent[$i]['Cou_SoldeCoursePilote'] = 0;
            $infoConcurrent[$i]['IC_VitesseMaximale'] = 0;
            $infoConcurrent[$i]['IC_Reputation'] = 0;
            $infoConcurrent[$i]['IC_XPShifts'] = 0;
            $infoConcurrent[$i]['IC_XPVirage'] = 0;
            $infoConcurrent[$i]['IC_XPFreinage'] = 0;
            $infoConcurrent[$i]['IC_XPSpe'] = 0;


            $requetePieces= "  SELECT  Voit_IdInjection,
                              Voit_IdTurbo,
                              Voit_IdBlocMoteur,
                              Voit_IdTransmission,
                              Voit_IdEchappement,
                              Voit_IdPneus,
                              Voit_IdPucedeContrôle
                        FROM voiture
                        WHERE IdVoiture = '".$infoConcurrent[$i]['IdVoiture']."'";
            $infoPieces[$i] = mysql_fetch_assoc(mysql_query($requetePieces));

            foreach($infoPieces[$i] as $typePiece => $IdPiece)
            {
              if(empty($IdPiece)) unset($infoPieces[$i][$typePiece]);
              else
              {
                $requeteInfoPiece="  SELECT   ModPi_Acceleration AS Acceleration
                              FROM piece_detachee, modele_piece
                              WHERE IdModelePiece = PiDet_IdModele
                              AND IdPieceDetachee = '$IdPiece'";
                $resultatInfoPiece = mysql_query($requeteInfoPiece) or die("Requete Info Piece : $requeteInfoPiece<br>".mysql_error());
                $infoPieces[$i][$typePiece] = mysql_fetch_assoc($resultatInfoPiece);

                $infoConcurrent[$i]['IC_AccelerationInitiale'] += $infoPieces[$i][$typePiece]['Acceleration'] * $infoPieces[$i][$typePiece]['Qualite'] / 100;
              }
            }
            $infoConcurrent[$i]['IC_AccelerationInitiale'] *= (1+(niveauCarre($infoConcurrentTour['Pil_XPShifts']) - $niveauCritique['Shifts'])/100);
          }
      //Voiture non opérationnelle
          else
          {
            $infoConcurrent[$i]['IC_TempsParcours'] = "Non couru";
            $infoConcurrent[$i]['IC_Reputation'] = 0;
            $infoConcurrent[$i]['Cou_SoldeCourse'] = 0;
            unset($infoConcurrent[$i]['IdVoiture']);
            $Cou_NbCompetiteurs--;
          }
        //}
      }

      //$Cou_NbCompetiteurs = count($infoConcurrent);

      if($debug)
      {
        echo"<pre>";
        print_r($infoConcurrent);
        echo"</pre>";
      }

  //Suppression course si pas assez de participants
      if($Cou_NbCompetiteurs <= 1 )
      {
  //Suppression des tronçons
        if($debug == 0)
        {
          $infoTroncon['Tron_IdTronconSuivant'] = $infoCourse['Cou_IdTronconDepart'];
          do
          {
            $IdTroncon = $infoTroncon['Tron_IdTronconSuivant'];
            $requeteInfoTroncon = "  SELECT Tron_IdTronconSuivant
                            FROM troncon
                            WHERE IdTroncon = '$IdTroncon'";
            $resultatInfoTroncon = mysql_query($requeteInfoTroncon) or die("Requete Info Troncon : <br>$requeteInfoTroncon<br><br>".mysql_error());
            $infoTroncon = mysql_fetch_assoc($resultatInfoTroncon);

            $requeteSupprimerTroncon = "  DELETE FROM troncon
                                WHERE IdTroncon = '$IdTroncon'";
            mysql_query($requeteSupprimerTroncon);

          }while(!empty($infoTroncon['Tron_IdTronconSuivant']));

    //Remboursement des paris éventuels et suppression de l'inscription
          if($Cou_NbCompetiteurs)
          {
            $infoConcurrent = mysql_fetch_assoc($resultatInscriptionCourse);
            $requeteInfoParis= "  SELECT IdPari, Pari_Montant, Pari_IdManager
                          FROM pari
                          WHERE Pari_IdInscriptionCourse = '".$infoConcurrent['IdInscriptionCourse']."'";
            $resultatInfoParis = mysql_query($requeteInfoParis) or die("Requete Info Paris : $requeteInfoParis<br>".mysql_error());
            while($infoPari = mysql_fetch_assoc($resultatInfoParis))
            {
              $requeteRembourserPari = "  UPDATE manager
                                SET Man_Solde = Man_Solde + '".$infoPari['Pari_Montant']."'
                                WHERE IdManager = '".$infoPari['Pari_IdManager']."'";
              mysql_query($requeteRembourserPari);
            }
            $requeteSupprimerParis = "  DELETE FROM pari
                              WHERE Pari_IdInscriptionCourse = '".$infoConcurrent['IdInscriptionCourse']."'";
            mysql_query($requeteSupprimerParis);

            $requeteSupprimerInscriptionCourse = "  DELETE FROM inscription_course
                                      WHERE IC_IdCourse = '$IdCourse'";
            mysql_query($requeteSupprimerInscriptionCourse);
          }
          $requeteSupprimerCourse = "  DELETE FROM course
                              WHERE IdCourse = '$IdCourse'";
          mysql_query($requeteSupprimerCourse);
        }
        $sujet = "Suppression de course";

        $mess .= "La course ".$infoCourse['Cou_Nom']." a été annulée faute de concurrent";

        Envoyer_Message ( $infoCourse['Cou_IdManager'], $mess, 1, $sujet);

        if($debug){ echo "Envoyer_Message (".$infoCourse['IdManager'].",".$mess.",".Nom_To_Id("Le Pacha").",".$sujet.")"; exit;}
        return 0;
      }

      $Cou_NbCompetiteurCourant = $Cou_NbCompetiteurs;
      if($debug)
      {
        echo "InfoConcurrent( ";
        foreach($infoConcurrent as $Concurrent)
          echo $Concurrent['IdInscriptionCourse']." , ";
        echo " )<br>";
      }
  //Pour chaque tour
      for($indiceTour = 0; $indiceTour < $infoCourse['Cou_NbTours']; $indiceTour ++)
      {
        echo ($debug)?$infoCourse['Cou_Nom']." : Tour ".($indiceTour+1)."<br><hr>":"";
        $IdTronconCourant = $infoCourse['Cou_IdTronconDepart'];

        if($debug)
        {
          echo "Début tour : InfoConcurrent(";
          foreach($infoConcurrent as $Concurrent)
            echo $Concurrent['IdInscriptionCourse']." , ";
          echo ")<br>";
        }

    //Pour chaque tronçon
        do
        {
          $requeteTronconCourant = "  SELECT IdTroncon, Tron_IdTronconSuivant, Sec_Nom, Sec_Longueur, Sec_TurboRequis, Sec_TypeXP, Sec_UsurePneus, Sec_UsureAmortisseurs, Sec_VitesseMaximum
                            FROM troncon, secteur
                            WHERE IdSecteur = Tron_IdSecteur
                            AND IdTroncon = '$IdTronconCourant'";
          $infoTroncon = mysql_fetch_assoc(mysql_query($requeteTronconCourant));
          echo ($debug)?"<hr>Troncon : ".$infoTroncon['Sec_Nom']."<br>":"";

          if($debug)
          {
            echo "Début tronçon : InfoConcurrent(";
            foreach($infoConcurrent as $Concurrent)
              echo $Concurrent['IdInscriptionCourse']." , ";
            echo ")<br>";
          }
      //Pour chaque concurrent
          foreach($infoConcurrent as $i => $infoConcurrentTour)
          {
        //Récupération de la liste des pièces détachées
            if(isset($infoConcurrentTour['IdVoiture']))
            {
              if(dispoVoiture($infoConcurrentTour['IdVoiture'])!=1)
              {
            //Voiture non opérationnelle
                $infoConcurrent[$i]['IC_TempsParcours'] = "Voiture HS";
                unset($infoConcurrent[$i]['IdVoiture']);
                $infoConcurrent[$i]['IC_Reputation'] = 0;
                $Cou_NbCompetiteurCourant--;
              }
              else
              {
                $requetePieces= "  SELECT  Voit_IdInjection,
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
                                  Voit_IdPucedeContrôle,
                                  Voit_IdNOS,
                                  Voit_IdNéons,
                                  Voit_IdSono
                            FROM voiture
                            WHERE IdVoiture = '".$infoConcurrent[$i]['IdVoiture']."'";
                $resultatPieces = mysql_query($requetePieces) or ("Requete Pieces : ".mysql_error());
                $infoPieces[$i] = mysql_fetch_assoc($resultatPieces);

                foreach($infoPieces[$i] as $typePiece => $IdPiece)
                {
                  if(empty($IdPiece)) unset($infoPieces[$i][$typePiece]);
                  else
                  {
                    $requeteInfoPiece="  SELECT   IdPieceDetachee,
                                        ModPi_Acceleration AS Acceleration,
                                        ModPi_VitesseMax AS VitesseMax,
                                        ModPi_Freinage AS Freinage,
                                        ModPi_Turbo AS Turbo,
                                        ModPi_Adherence AS Adherence,
                                        ModPi_SoliditeMoteur AS SoliditeMoteur,
                                        ModPi_AspectExterieur AS AspectExterieur,
                                        ModPi_Poids AS Poids,
                                        ModPi_DureeVieMax,
                                        PiDet_Usure,
                                        PiDet_Qualite AS Qualite
                                  FROM piece_detachee, modele_piece
                                  WHERE IdModelePiece = PiDet_IdModele
                                  AND IdPieceDetachee = '$IdPiece'";
                    $resultatInfoPiece = mysql_query($requeteInfoPiece) or die("Requete Info Piece : $requeteInfoPiece<br>".mysql_error());
                    $infoPieces[$i][$typePiece] = mysql_fetch_assoc($resultatInfoPiece);
                  }
                }

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
                  //$infoConcurrent[$i]['Voit_AspectExterieur'] += $infoPiece['AspectExterieur']* (100-$infoPiece['Usure'])/100;
                  $AspectExterieur += $infoPiece['AspectExterieur']* (100-$infoPiece['Usure'])/100;
                  $Poids += $infoPiece['Poids'];
                }
                $Acceleration = $Acceleration * (1+(niveauCarre($infoConcurrentTour['Pil_XPShifts']) - $niveauCritique['Shifts'])/100);
                $Freinage = $Freinage * (1+(niveauCarre($infoConcurrentTour['Pil_XPFreinage']) - $niveauCritique['Freinage'])/100);
                $Turbo = $Turbo * (1+(niveauCarre($infoConcurrentTour['Pil_XPSpe']) - $niveauCritique['Spe'])/100);
                $Adherence = $Adherence * (1+(niveauCarre($infoConcurrentTour['Pil_XPVirage']) - $niveauCritique['Virage'])/100);

                if($debug>1)
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
    <td>?</td>
    <td><?php echo $infoConcurrentTour['IC_VitesseInitiale']?></td>
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

                echo ($debug>1)?"Vitesse Max Troncon : $vitesseMax m/s<br>":"";

                if($infoConcurrentTour['IC_VitesseInitiale'] <= $vitesseMax)
                {
              //Acceleration
                  echo ($debug>1)?"Acceleration : ":"";
                  $infoConcurrentTour['IC_AccelerationInitiale'] = $Acceleration * sqrt( 1 - $infoConcurrentTour['IC_VitesseInitiale'] / $vitesseMax);

                  $tempsVitesseMax = 2 * $infoConcurrentTour['IC_AccelerationInitiale'] * $vitesseMax /($Acceleration*$Acceleration);
                  $distanceVitesseMax =   ((-$Acceleration * $Acceleration) / (12 * $vitesseMax)) * $tempsVitesseMax * $tempsVitesseMax * $tempsVitesseMax +
                              ($infoConcurrentTour['IC_AccelerationInitiale'] / 2) * $tempsVitesseMax * $tempsVitesseMax + $infoConcurrentTour['IC_VitesseInitiale'] * $tempsVitesseMax;
                  if($distanceVitesseMax > $infoTroncon['Sec_Longueur'])
                  {
                    echo ($debug>1)?"Pas Vitesse max : calcTemps($Acceleration,".$infoConcurrentTour['IC_AccelerationInitiale'].", $vitesseMax,".$infoConcurrentTour['IC_VitesseInitiale'].", ".$infoTroncon['Sec_Longueur'].")<br>":"";
                    $tempsTotalTroncon = calcTemps($Acceleration,$infoConcurrentTour['IC_AccelerationInitiale'], $vitesseMax,$infoConcurrent[$i]['IC_VitesseInitiale'], $infoTroncon['Sec_Longueur']);

                    echo ($debug>1)?"Vitesse Finale : ".$infoConcurrent[$i]['IC_VitesseInitiale']."<br>":"";
                  }
                  else
                  {
                    echo ($debug>1)?"Vitesse max<br>":"";
                    $tempsTotalTroncon = $tempsVitesseMax + ($infoTroncon['Sec_Longueur'] - $distanceVitesseMax)/ $vitesseMax;

                    $infoConcurrent[$i]['IC_VitesseInitiale'] = $vitesseMax;
                  }
                  $VitesseInitiale = $infoConcurrent[$i]['IC_VitesseInitiale'];
                }
                else
                {
              //Freinage
                  $VitesseInitiale = $infoConcurrentTour['IC_VitesseInitiale'];
                  $tempsVitesseMax = ($infoConcurrentTour['IC_VitesseInitiale'] - $vitesseMax) / $Freinage;
                  $distanceVitesseMax = -$Freinage * $tempsVitesseMax * $tempsVitesseMax / 2 + $infoConcurrentTour['IC_VitesseInitiale'] * $tempsVitesseMax;
                  echo ($debug>1)?"Freinage<br>":"";
                  if($distanceVitesseMax > $infoTroncon['Sec_Longueur'])
                  {
                    echo ($debug>1)?"Pas Vitesse max (accident)<br>":"";
                    $tempsTotalTroncon = -( sqrt($infoConcurrentTour['IC_VitesseInitiale']*$infoConcurrentTour['IC_VitesseInitiale'] - 2 * $Freinage / $infoTroncon['Sec_Longueur'])-$infoConcurrentTour['IC_VitesseInitiale']) / $Freinage;

                    $infoConcurrent[$i]['IC_VitesseInitiale'] = - $Freinage * $tempsTotalTroncon + $infoConcurrentTour['IC_VitesseInitiale'];
              //Traitement accident
                    $infoConcurrent[$i]['IC_VitesseInitiale'] = 0;


                    echo ($debug>1)?"Vitesse finale : ".$infoConcurrent[$i]['IC_VitesseInitiale']."<br>":"";
                  }
                  else
                  {
                    echo ($debug>1)?"Vitesse max :":"";
                    if($distanceVitesseMax > $infoTroncon['Sec_Longueur'] / 2)
                    {
                      echo ($debug>1)?"Dérapage":"";
                      $tempsTotalTroncon = $tempsVitesseMax + ($infoTroncon['Sec_Longueur'] - $distanceVitesseMax)/ $vitesseMax;
              //Traitement dérapage
                      $infoConcurrent[$i]['IC_VitesseInitiale'] = $vitesseMax * (1-(($distanceVitesseMax*(1 - niveauCarre($infoConcurrentTour['Pil_XPSpe'])/100) - $infoTroncon['Sec_Longueur'] / 2)/($infoTroncon['Sec_Longueur'] / 2))/2);

                      if($debug>1)
                      {
                        echo "<br>Temps troncon : $tempsTotalTroncon = $tempsVitesseMax + (".$infoTroncon['Sec_Longueur']." - $distanceVitesseMax)/ $vitesseMax<br>";
                        echo "Vitesse Finale : ".$infoConcurrent[$i]['IC_VitesseInitiale']." = $vitesseMax * 1-((($distanceVitesseMax*".(1 - niveauCarre($infoConcurrentTour['Pil_XPSpe'])/100).") - ".($infoTroncon['Sec_Longueur'] / 2).") / ".$infoTroncon['Sec_Longueur'].")<br>";
                        echo "Vitesse Finale : ".$infoConcurrent[$i]['IC_VitesseInitiale']." = $vitesseMax * ".(1-(($distanceVitesseMax*(1 - niveauCarre($infoConcurrentTour['Pil_XPSpe'])/100) - $infoTroncon['Sec_Longueur'] / 2)/($infoTroncon['Sec_Longueur'] / 2))/2)."<br>";
                      }
                    }
                    else
                    {
                      echo ($debug>1)?"Pas dérapage":"";
                      $tempsTotalTroncon = $tempsVitesseMax + (($infoTroncon['Sec_Longueur'] / 2 - $distanceVitesseMax)/ $infoConcurrent[$i]['IC_VitesseInitiale']) + $infoTroncon['Sec_Longueur'] / $vitesseMax / 2 ;

                      echo ($debug>1)?"Temps Tronçon : $tempsTotalTroncon = $tempsVitesseMax + ((".$infoTroncon['Sec_Longueur']." / 2 - $distanceVitesseMax)/ ".$infoConcurrent[$i]['IC_VitesseInitiale'].") + ".$infoTroncon['Sec_Longueur']." / $vitesseMax / 2<br>":"";
                      $infoConcurrent[$i]['IC_VitesseInitiale'] = $vitesseMax;
                    }
                  }
                }

    //Traitement temps tour
                $malus = rand(0,10);

                $infoConcurrent[$i]['IC_TempsTour'][$indiceTour] += $tempsTotalTroncon*(1 - $malus / 100);
                $infoConcurrent[$i]['IC_TempsParcours'] += $tempsTotalTroncon*(1 - $malus / 100);
                echo ($debug>1)?"Temps tronçon : $tempsTotalTroncon s <br>":"";

    //Traitement expérience
                $typeXP = explode(",",$infoTroncon['Sec_TypeXP']);
                $XPgagnes = $infoTroncon['Sec_Longueur'] / 10 / $infoCourse['Cou_NbTours'] / count($typeXP);
                foreach($typeXP as $NomDomaine)
                {
                  $infoConcurrent[$i]['Pil_XP'.$NomDomaine] += $XPgagnes;
                  $infoConcurrent[$i]['IC_XP'.$NomDomaine] += $XPgagnes;
                }

    //Traitement vitesse maximum course
                if($infoConcurrent[$i]['IC_VitesseMaximale'] < $infoConcurrent[$i]['IC_VitesseInitiale'])
                  $infoConcurrent[$i]['IC_VitesseMaximale'] = $infoConcurrent[$i]['IC_VitesseInitiale'];

    //Traitement réputation
                echo ($debug>1)?"Reputation tronçon : $AspectExterieur * ".$infoTroncon['Sec_Longueur']." m / 10<br>":"";
                $reputationTroncon = $AspectExterieur * $infoTroncon['Sec_Longueur'] / 10;
                echo ($debug>1)?"Réputation : ".$infoConcurrent[$i]['IC_Reputation']." += ".$reputationTroncon."points<br>":"";
                $infoConcurrent[$i]['IC_Reputation'] += $reputationTroncon;

    //Traitement usure
                foreach($infoPieces[$i] as $typePiece => $caracPiece)
                {
                  if($typePiece == "Voit_IdInjection" || $typePiece == "Voit_IdRefroidissement" || $typePiece == "Voit_IdChassis")
                    $usure = $infoTroncon['Sec_Longueur']/ 10 / (2000 * $caracPiece['ModPi_DureeVieMax']);
                  elseif($typePiece == "Voit_IdBlocMoteur")
                    $usure = ($infoTroncon['Sec_Longueur']/ 10 / (1000 * $caracPiece['ModPi_DureeVieMax'])) * (100-$SoliditeMoteur) / 100;
                  elseif($typePiece == "Voit_IdTransmission")
                    $usure = ($infoTroncon['Sec_Longueur']/ 10 / (1000 * $caracPiece['ModPi_DureeVieMax'])) * (100-$SoliditeMoteur) / 100;
                  elseif($typePiece == "Voit_IdPneus")
                    $usure = $infoTroncon['Sec_Longueur']/ 10 / (2000 * $caracPiece['ModPi_DureeVieMax']) + $infoTroncon['Sec_UsurePneus'];
                  elseif($typePiece == "Voit_IdAmortisseurs")
                    $usure = $infoTroncon['Sec_Longueur']/ 10 / (2000 * $caracPiece['ModPi_DureeVieMax']) + $infoTroncon['Sec_UsureAmortisseurs'];
                  elseif($typePiece == "Voit_IdFreins" && $VitesseInitiale - $infoConcurrent[$i]['IC_VitesseInitiale']!=0)
                    $usure = $infoTroncon['Sec_Longueur']/(($VitesseInitiale - $infoConcurrent[$i]['IC_VitesseInitiale']) / $VitesseInitiale ) / 1000 * $caracPiece['ModPi_DureeVieMax'];
                  elseif($typePiece == "Voit_IdNOS")
                    $usure = 0; // A changer
                  else $usure = 0;



                  if(($usure + $caracPiece['PiDet_Usure']) > 100) $usure = 100 - $caracPiece['PiDet_Usure'];
                  echo ($debug>1)?"Usure initiale de $typePiece : ".$caracPiece['PiDet_Usure']."<br>Usure finale de $typePiece : $usure<br>":"";

                  $requeteAjouterUsure ="  UPDATE piece_detachee
                                  SET PiDet_Usure = PiDet_Usure + '$usure'
                                  WHERE IdPieceDetachee = '".$caracPiece['IdPieceDetachee']."'";

                  if($debug==0)  mysql_query($requeteAjouterUsure) or die ("Requete Ajouter Usure :<br>$requeteAjouterUsure<br><br>".mysql_error());
                  //else echo $requeteAjouterusure."<br>";
                }
                if($debug)
                {
                  echo"<pre>";
                  print_r($infoConcurrent[$i]);
                  //print_r($infoPieces[$i]);
                  echo"</pre>";
                }
              }
            }
          }
          $IdTronconCourant = $infoTroncon['Tron_IdTronconSuivant'];

          if($debug)
          {
            echo "Fin tronçon : InfoConcurrent(";
            foreach($infoConcurrent as $Concurrent)
              echo $Concurrent['IdInscriptionCourse']." , ";
            echo ")<br>";
          }

        }while(!empty($IdTronconCourant));
        if($debug)
        {
          echo "Fin tour : InfoConcurrent(";
          foreach($infoConcurrent as $Concurrent)
            echo $Concurrent['IdInscriptionCourse']." , ";
          echo ")<br>";
        }
      }
  //Traitement des résultats

      if($debug)
      {
        echo "InfoConcurrent(";
        foreach($infoConcurrent as $Concurrent)
          echo $Concurrent['IdInscriptionCourse']." , ";
        echo ")<br>";
      }

      foreach($infoConcurrent as $id => $info)
        $tempsCourse[$id] = $info['IC_TempsParcours'];

      uasort($tempsCourse,"compare");

      if($debug) print_r($tempsCourse);

      $position = 1;
      foreach($tempsCourse as $id => $temps)
      {
        echo ($debug)?"Id : $id Temps : $temps<br>":"";
        $temps = intval($temps * 1000);

        if($position == 1)
        {
          $infoConcurrent[$id]['IC_Reputation'] *= 1.1;
          $infoConcurrent[$id]['IC_XPShifts'] *= 1.1;
          $infoConcurrent[$id]['IC_XPVirage'] *= 1.1;
          $infoConcurrent[$id]['IC_XPFreinage'] *= 1.1;
          $infoConcurrent[$id]['IC_XPSpe'] *= 1.1;
          $infoConcurrent[$id]['Cou_SoldeCourseManager'] += $Cou_NbCompetiteurs * $infoCourse['Cou_PrixEngagement'] * (100 - $infoConcurrent[$id]['Pil_PourcentageGains'])/100;
          $infoConcurrent[$id]['Cou_SoldeCoursePilote'] += $Cou_NbCompetiteurs * $infoCourse['Cou_PrixEngagement'] * ($infoConcurrent[$id]['Pil_PourcentageGains'])/100;

          $mess = "Félicitations, ".$infoConcurrent[$id]['Pil_Nom']." a remporté la course en arrivant 1er.\nIl empoche donc ".$infoConcurrent[$id]['Cou_SoldeCoursePilote']." &euro; et vous ".$infoConcurrent[$id]['Cou_SoldeCourseManager']." &euro; !\n";
         }
        else
        {
          $mess = "Dommage, ".$infoConcurrent[$id]['Pil_Nom']." est arrivé $position ème de la course.\n";
        }

        if($debug)
        {
          echo "Position : $position<br>Temps : $temps ms<br>";
//          print_r($infoConcurrent[$id]);
        }


        $requeteMAJInscriptionCourse = "  UPDATE inscription_course
                              SET  IC_Position = '$position',
                                  IC_Temps = '$temps'
                              WHERE IdInscriptionCourse = '".$infoConcurrent[$id]['IdInscriptionCourse']."'";
        mysql_query($requeteMAJInscriptionCourse)or die("Requete MAJ IC :".mysql_error());
        if($debug) echo "Requête MAJ Inscription Course :<br>".$requeteMAJInscriptionCourse."<br>";

        $requeteMAJPilote= "  UPDATE pilote
                      SET  Pil_Reputation = Pil_Reputation + '".$infoConcurrent[$id]['IC_Reputation']."',
                          Pil_XPShifts = '".$infoConcurrent[$id]['Pil_XPShifts']."',
                          Pil_XPVirage = '".$infoConcurrent[$id]['Pil_XPVirage']."',
                          Pil_XPFreinage = '".$infoConcurrent[$id]['Pil_XPFreinage']."',
                          Pil_XPSpe = '".$infoConcurrent[$id]['Pil_XPSpe']."',
                          Pil_Solde = Pil_Solde + '".$infoConcurrent[$id]['Cou_SoldeCoursePilote']."'
                      WHERE IdPilote = '".$infoConcurrent[$id]['IdPilote']."'";
        mysql_query($requeteMAJPilote)or die("Requete MAJ Pilote :".mysql_error());
        if($debug) echo "Requête MAJ Inscription Course :<br>".$requeteMAJPilote."<br>";

        $requeteMAJManager="  UPDATE manager
                      SET   Man_Solde = Man_Solde + '".$infoConcurrent[$id]['Cou_SoldeCourseManager']."',
                          Man_Reputation = Man_Reputation + '".$infoConcurrent[$id]['IC_Reputation']."'
                      WHERE IdManager = '".$infoConcurrent[$id]['IdManager']."'";
        mysql_query($requeteMAJManager)or die("Requete MAJ Manager :".mysql_error());
        if($debug) echo "Requête MAJ Inscription Course :<br>".$requeteMAJManager."<br>";

        $position++;

        $sujet = "Résultat de la course ".$infoCourse['Cou_Nom'];


        if($temps == "Non couru")
        {
          $mess .= "Votre voiture n\'était pas prête pour la course !";
        }
        else
        {
          foreach($infoConcurrent[$id]['IC_TempsTour'] as $numTour => $tempsTour)
          {
            $mess .= "Tour ".($numTour+1)." : ".addslashes(affichageTemps($tempsTour*1000))."\n";
          }
          if($temps == "Voiture HS")
          {
            $mess .= "Votre voiture est tombée en rade pendant la course !";
          }
          else
          {
            $mess .= "Vitesse maximum : ".round(($infoConcurrent[$id]['IC_VitesseMaximale']*3.6),2)." km/h\n";

            $vitesseMoyenne = round((longueurCourse($infoCourse['Cou_IdTronconDepart'])/ ($temps / 1000))*3.6,2);

            $mess .= "Vitesse moyenne : ".$vitesseMoyenne." km/h\n";
            $mess .= "Vous avez gagné ".$infoConcurrent[$id]['IC_Reputation']." points de réputation.\n";
          }
        }
if($debug) echo"</pre>";
        if($debug) echo "Envoyer_Message (".$infoConcurrent[$id]['Pil_IdManager'].", ".$mess.", ".$infoCourse['Cou_IdManager'].",".$sujet.")<br><br>";

        Envoyer_Message ( $infoCourse['Cou_IdManager'], $mess,$infoConcurrent[$id]['IdManager'], $sujet);
      }


      $requeteParisTotaux = "  SELECT SUM(Pari_Montant)
                      FROM pari, inscription_course
                      WHERE IdInscriptionCourse = Pari_IdInscriptionCourse
                      AND IC_IdCourse = '$IdCourse'";
      $montantParis = mysql_fetch_row(mysql_query($requeteParisTotaux));
      $montantParis = $montantParis[0];

      $requeteNbGagnants = "  SELECT COUNT(*)
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

      $requeteGainsParis = "  UPDATE manager, pari, inscription_course
                      SET Man_Solde = Man_Solde + '$gainsPari'
                      WHERE IdManager = Pari_IdManager
                      AND Pari_IdInscriptionCourse = IdInscriptionCourse
                      AND IC_IdCourse = '$IdCourse'
                      AND IC_Position = '1'";
      mysql_query($requeteGainsParis)or die(mysql_error());

      $requeteRAZ= "  UPDATE inscription_course
                SET  IC_Position = NULL,
                    IC_Temps = NULL
                WHERE IC_IdCourse = $IdCourse";
      if($debug > 0) mysql_query($requeteRAZ);

      $tempsCourse = "";
    }

    $requeteMAJDate = "   UPDATE utr
                  SET UTR_DateTraitementCourse = NOW()";
    mysql_query($requeteMAJDate);

    if($debug) exit;
  }
?>