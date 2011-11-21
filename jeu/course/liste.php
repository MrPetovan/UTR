<?php
	session_name("Joueur");
	session_start();
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
}
	error_reporting(E_ALL ^ E_NOTICE);
	include('../../include/connexion.inc.php');
	include('../../include/fonctions.php');

	$IdManager = $_SESSION['IdManager'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>UTR : Liste Course</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../../include/style.css" rel="stylesheet" type="text/css" />
	<link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
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
		<td width="17%" valign="top">
<?php
	include("../../frame/menu.php");
?>
		</td>

    <td width="83%"> <table width="100%" height="341" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="2%"><img src="/UTR/design/spacer.gif" width="9" height="64"></td>
          <td width="98%">
            <div align="left"></div>
            <div align="center">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
	if($Man_Niveau >= 1)
	{
?>
                      <tr>
                        <td><br>
                          <a href="ajouterTroncon.php?action=Ajouter">Ajouter
                          une course</a><br><br>
                          </td>
                      </tr>
<?php
	}
?>
                      <tr>
                        <td><table cellspacing=0 cellpadding=0 border=0 width="100%">
                            <tbody>
                              <tr>
                                <td> <table cellspacing=0 cellpadding=0 width="100%" border=0>
                                    <tbody>
                                      <tr>
                                        <td width=482
                background="/UTR/design/nav.jpg"
                height=34><div align="left">&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp; &nbsp;Courses proposées</div></td>
                                        <td align=right
                background="/UTR/design/navtile.jpg">&nbsp;</td>
                                        <td align=right
                background="/UTR/design/navtile.jpg"><img
                  height=34
                  src="/UTR/design/navdroite.gif"
                  width=2></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  <table cellspacing=0 cellpadding=0 width="100%" border=0>
                                    <tbody>
                                      <tr>
                                        <td width=6
                background="/UTR/design/tile_gauche.gif">&nbsp;</td>
                                        <td><table width="100%" border="0" class="liste">
                                            <tr>
                                              <th class="titre">Nom</th>
                                              <th class="titre">Type</th>
                                              <th class="titre">Date</th>
                                              <th class="titre">Niveau Min/Max</th>
                                              <th class="titre">Longueur</th>
                                              <th class="titre">Difficulte</th>
                                              <th class="titre">Disponibilité</th>
                                              <th class="titre">Organisateur</th>
                                            </tr>
                                            <?php
	$requeteCoursesProposees= "	SELECT 	IdCourse, Cou_Nom, Cou_IdType, Cou_Date, TypeCou_Libelle, Cou_NiveauMin, Cou_NiveauMax,
														Cou_NbCompetiteursMax, Cou_IdTronconDepart, COUNT(IdInscriptionCourse) AS Cou_NbCompetiteurs, Cou_IdManager, Man_Nom
											FROM course
											INNER JOIN type_course ON Cou_IdType = IdTypeCourse
											INNER JOIN manager ON IdManager = Cou_IdManager
											LEFT JOIN inscription_course ON IC_IdCourse = IdCourse
											WHERE IC_Position IS NULL
											GROUP BY IdCourse
											HAVING Cou_NbCompetiteursMax > Cou_NbCompetiteurs
											ORDER BY Cou_Date DESC";
	$resultatCoursesProposees = mysql_query($requeteCoursesProposees) or die (mysql_error());

	while($courseProposee=mysql_fetch_assoc($resultatCoursesProposees))
	{
		$difficulte = difficulteCourse($courseProposee['Cou_IdTronconDepart'])/longueurCourse($courseProposee['Cou_IdTronconDepart']);
?>
                                            <tr class="piece">
                                              <td><a href="fiche.php?IdCourse=<?php echo $courseProposee['IdCourse'];?>"><?php echo $courseProposee['Cou_Nom'];?></a></td>
                                              <td>
                                                <?php echo  $courseProposee['TypeCou_Libelle'];?>
                                              </td>
                                              <td>
                                                <?php echo implode(" / ",array_reverse(explode("-",$courseProposee['Cou_Date'])))?>
                                              </td>
                                              <td>
                                                <?php echo  $courseProposee['Cou_NiveauMin']." => ".$courseProposee['Cou_NiveauMax'];?>
                                              </td>
                                              <td><?php echo longueurCourse($courseProposee['Cou_IdTronconDepart']);?>
                                                m</td>
                                              <td>
                                                <?php
	//echo $difficulte."<br>";
	if($difficulte > 110)
		echo "Facile";
	else if($difficulte > 100)
		echo "Moyen";
	else if($difficulte > 90)
		echo "Difficile";
	else echo "Très difficile";
?>
                                              </td>
                                              <td>
                                                <?php echo $courseProposee['Cou_NbCompetiteurs']." / ".$courseProposee['Cou_NbCompetiteursMax']?>
                                              </td>
                                              <td>
                                                <a href="../manager/fiche.php?IdManager=<?php echo $courseProposee['Cou_IdManager']?>"><?php echo $courseProposee['Man_Nom']?></a>
                                              </td>
                                            </tr>
<?php
	}
?>
                                          </table></td>
                                        <td width=2 background="/UTR/design/tile_droit.gif">&nbsp;</td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  <table cellspacing=0 cellpadding=0 width="100%" border=0>
                                    <tbody>
                                      <tr>
                                        <td width=482 background="/UTR/design/bas2.gif" height=6></td>
                                        <td background="/UTR/design/bas_tile_droite.gif" height=6></td>
                                        <td align=right background="/UTR/design/bas_tile_droite.gif" height=6>
                                          <img height=6 src="/UTR/design/bas_bord_droit.gif" width=3>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table></td>
                              </tr>
                            </tbody>
                          </table>

                        </td>
                      </tr>
                    </table></td>
                </tr>
                <tr>
                  <td><img src="/UTR/design/spacer.gif" width="28" height="19"></td>
                </tr>
                <tr>
                  <td><table cellspacing=0 cellpadding=0 width="100%" border="0">
                      <tbody>
                        <tr>
                          <td width=482
                background="/UTR/design/nav.jpg"
                height=34><div align="left">&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                              &nbsp; &nbsp;Courses complètes</div></td>
                          <td align=right
                background="/UTR/design/navtile.jpg">&nbsp;</td>
                          <td align=right
                background="/UTR/design/navtile.jpg"><img
                  height=34
                  src="/UTR/design/navdroite.gif"
                  width=2></td>
                        </tr>
                      </tbody>
                    </table>
							<table width="100%" border="0" class="liste">
								<tr>
									<th class="titre">Nom</th>
									<th class="titre">Type</th>
									<th class="titre">Date</th>
									<th class="titre">Niveau Min/Max</th>
									<th class="titre">Longueur</th>
									<th class="titre">Difficulté</th>
									<th class="titre">Participants</th>
									<th class="titre">Organisateur</th>
								</tr>
<?php
	$requeteCoursesCompletes= "	SELECT 	IdCourse, Cou_Nom, Cou_IdType, Cou_Date, TypeCou_Libelle, Cou_NiveauMin, Cou_NiveauMax,
										Cou_NbCompetiteursMax, Cou_IdTronconDepart, COUNT(IdInscriptionCourse) AS Cou_NbCompetiteurs, Cou_IdManager, Man_Nom
								FROM course
								INNER JOIN type_course ON Cou_IdType = IdTypeCourse
								INNER JOIN manager ON IdManager = Cou_IdManager
								LEFT JOIN inscription_course ON IC_IdCourse = IdCourse
								WHERE IC_Position IS NULL
								GROUP BY IdCourse
								HAVING Cou_NbCompetiteursMax = Cou_NbCompetiteurs
								ORDER BY Cou_Date DESC";
	$resultatCoursesCompletes = mysql_query($requeteCoursesCompletes) or die (mysql_error());

	while($courseComplete=mysql_fetch_assoc($resultatCoursesCompletes))
	{
		$difficulte = difficulteCourse($courseComplete['Cou_IdTronconDepart'])/longueurCourse($courseComplete['Cou_IdTronconDepart']);
?>
								<tr class="piece">
									<td><a href="fiche.php?IdCourse=<?php echo $courseComplete['IdCourse'];?>"><?php echo $courseComplete['Cou_Nom'];?></a></td>
									<td><?php echo  $courseComplete['TypeCou_Libelle'];?></td>
									<td><?php echo implode(" / ",array_reverse(explode("-",$courseComplete['Cou_Date'])))?></td>
									<td><?php echo  $courseComplete['Cou_NiveauMin']." => ".$courseComplete['Cou_NiveauMax'];?></td>
									<td><?php echo longueurCourse($courseComplete['Cou_IdTronconDepart']);?> m</td>
									<td>
                                  <?php
	//echo $difficulte."<br>";
	if($difficulte > 110)
		echo "Facile";
	else if($difficulte > 100)
		echo "Moyen";
	else if($difficulte > 90)
		echo "Difficile";
	else echo "Très difficile";
?>
									</td>
									<td><?php echo $courseComplete['Cou_NbCompetiteursMax']?></td>
									<td>
										<a href="../manager/fiche.php?IdManager=<?php echo $courseComplete['Cou_IdManager']?>"><?php echo $courseComplete['Man_Nom']?></a>
									</td>
								</tr>
                              <?php
	}
?>
							</table>

                    <table cellspacing=0 cellpadding=0 width="100%" border=0>
                      <tbody>
                        <tr>
                          <td width=482
                background="/UTR/design/bas2.gif"
                height=6></td>
                          <td
                background="/UTR/design/bas_tile_droite.gif"
                height=6></td>
                          <td align=right
                background="/UTR/design/bas_tile_droite.gif"
                height=6><img height=6
                  src="/UTR/design/bas_bord_droit.gif"
                  width=3></td>
                        </tr>
                      </tbody>
                    </table></td>
                </tr>
                <tr>
                  <td><img src="/UTR/design/spacer.gif" width="28" height="19"></td>
                </tr>
                <tr>
                  <td><table cellspacing=0 cellpadding=0 width="100%" border="0">
                      <tbody>
                        <tr>
                          <td width=482
                background="/UTR/design/nav.jpg"
                height=34><div align="left">&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                              &nbsp; &nbsp;Courses terminées</div></td>
                          <td align=right
                background="/UTR/design/navtile.jpg">&nbsp;</td>
                          <td align=right
                background="/UTR/design/navtile.jpg"><img
                  height=34
                  src="/UTR/design/navdroite.gif"
                  width=2></td>
                        </tr>
                      </tbody>
                    </table>
                    <table cellspacing=0 cellpadding=0 width="100%" border=0>
                      <tbody>
                        <tr>
                          <td width=6
                background="/UTR/design/tile_gauche.gif">&nbsp;</td>
                          <td><table width="100%" border="0" class="liste">
                              <tr>
                                <th class="titre">Nom</th>
                                <th class="titre">Type</th>
                                <th class="titre">Date</th>
                                <th class="titre">Longueur</th>
                                <th class="titre">Difficulté</th>
                                <th class="titre">Participants</th>
                                <th class="titre">Organisateur</th>
                              </tr>
                              <?php
	$requeteCoursesTerminees= "	SELECT 	IdCourse, Cou_Nom, Cou_IdType, Cou_Date, TypeCou_Libelle, Cou_NiveauMin, Cou_NiveauMax,
										Cou_NbCompetiteursMax, Cou_IdTronconDepart, COUNT(IdInscriptionCourse) AS Cou_NbCompetiteurs, Cou_IdManager, Man_Nom
								FROM course, type_course, manager, inscription_course
								WHERE IC_IdCourse = IdCourse
								AND Cou_IdType = IdTypeCourse
								AND IC_Position IS NOT NULL
								AND IdManager = Cou_IdManager
								GROUP BY IdCourse
								ORDER BY Cou_Date DESC";
	$resultatCoursesTerminees = mysql_query($requeteCoursesTerminees) or die (mysql_error());

	while($courseTerminee=mysql_fetch_assoc($resultatCoursesTerminees))
	{
		$difficulte = difficulteCourse($courseTerminee['Cou_IdTronconDepart'])/longueurCourse($courseTerminee['Cou_IdTronconDepart']);
?>
                              <tr class="piece">
                                <td><a href="fiche.php?IdCourse=<?php echo $courseTerminee['IdCourse'];?>"><?php echo $courseTerminee['Cou_Nom'];?></a></td>
                                <td>
                                  <?php echo  $courseTerminee['TypeCou_Libelle'];?>
                                </td>
                                <td>
                                  <?php echo implode(" / ",array_reverse(explode("-",$courseTerminee['Cou_Date'])))?>
                                </td>
                                <td><?php echo longueurCourse($courseTerminee['Cou_IdTronconDepart']);?>
                                  m</td>
                                <td>
                                  <?php
	//echo $difficulte."<br>";
	if($difficulte > 110)
		echo "Facile";
	else if($difficulte > 100)
		echo "Moyen";
	else if($difficulte > 90)
		echo "Difficile";
	else echo "Très difficile";
?>
                                </td>
                                <td>
                                  <?php echo $courseTerminee['Cou_NbCompetiteurs']?>
                                </td>
                                <td>
                                  <a href="../manager/fiche.php?IdManager=<?php echo $courseTerminee['Cou_IdManager']?>"><?php echo $courseTerminee['Man_Nom']?></a>
                                </td>
                              </tr>
                              <?php
	}
?>
                            </table> </td>
                          <td width=2
                background="/UTR/design/tile_droit.gif">&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                    <table cellspacing=0 cellpadding=0 width="100%" border=0>
                      <tbody>
                        <tr>
                          <td width=482
                background="/UTR/design/bas2.gif"
                height=6></td>
                          <td
                background="/UTR/design/bas_tile_droite.gif"
                height=6></td>
                          <td align=right
                background="/UTR/design/bas_tile_droite.gif"
                height=6><img height=6
                  src="/UTR/design/bas_bord_droit.gif"
                  width=3></td>
                        </tr>
                      </tbody>
                    </table></td>
                </tr>
              </table>

            </div></td>
        </tr>
      </table>

    </td>
	</tr>
	<tr>

    <td colspan="2"> <br><div align="center">
        <?php
	include("../../frame/piedpage.php");
?>
      </div></td>
	</tr>
</table>
</body>
</html>