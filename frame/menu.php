<table width=142 border=0 align="left" cellpadding=0 cellspacing=0>
  <tbody>
    <tr>
      <td width="142" align=middle valign=top><table width=142 border=0 cellpadding=0 cellspacing=0>
          <tbody>
            <tr>
              <td width="142"><img height=41
                  src="/UTR/design/infos.gif"
                  width=142></td>
            </tr>
            <tr>
              <td align=right
                background="/UTR/design/tile.gif"><br> <table width="90%" border=0 cellpadding=0 cellspacing=0>
                  <tbody>
                    <tr>
                      <td> <p align="left"><img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a
                        href="/UTR/frame/regles.php">Les règles</a><br>
                          <img
                        height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a
                        href="/UTR/jeu/accueilJeu.php">Jouer !</a> <br>
                          <br>
                        </p></td>
                    </tr>
                  </tbody>
                </table></td>
            </tr>
            <tr>
              <td><img height=6
                  src="/UTR/design/bas.gif"
                  width=142></td>
            </tr>
          </tbody>
        </table>

        <div align="left">
          <?php
			if(isset($_SESSION['IdJoueur']))
			{
				$IdManager=$_SESSION['IdManager'];

				$requeteManager = "	SELECT IdManager, Man_Nom, Man_Niveau, Man_Solde, Man_Reputation, COUNT(IdMessage) AS Man_NbMessage
											FROM manager
											LEFT JOIN message ON Mess_Proprietaire = IdManager AND Mess_Lu = 'n'
											WHERE IdManager = '$IdManager'
											GROUP BY IdManager";
				$resultatManager = mysql_query($requeteManager)or die(mysql_error());
				$infoManager = mysql_fetch_assoc($resultatManager);
				$Man_Niveau = $infoManager['Man_Niveau'];
?>
          <br>
        </div>
        <table width=142 border=0 cellpadding=0 cellspacing=0>
          <tbody>
            <tr>
              <td><img height=41
                  src="/UTR/design/members.gif"
                  width=142></td>
            </tr>
            <tr>
              <td align=right
                background="/UTR/design/tile.gif"><br> <table cellspacing=0 cellpadding=0 width="90%" border=0>
                  <tbody>
                    <tr>
                      <td><p align="left"> <img height=8 src="/UTR/design/fleche.gif" width=8>
                          &nbsp;<a href="/UTR/jeu/joueur/stat.php">Infos générales</a>
                          <br>
                          <img height=8 src="/UTR/design/fleche.gif" width=8>
                          &nbsp;<a href="/UTR/jeu/voiture/liste.php?page=parking">Le garage</a>
                          <br>
                          <img height=8 src="/UTR/design/fleche.gif" width=8>
                          &nbsp;<a href="/UTR/jeu/piece/liste.php?page=stock&type=tous">L'entrepôt</a>
                          <br>
                          <img height=8 src="/UTR/design/fleche.gif" width=8>
                          &nbsp;<a href="/UTR/jeu/pilote/liste.php">Le vestiaire</a>
                          <br>
                          <img height=8 src="/UTR/design/fleche.gif" width=8>
                          &nbsp;<a href="/UTR/jeu/course/liste.php">Le planning</a>
                          <br>
                          <img height=8 src="/UTR/design/fleche.gif" width=8>
                          &nbsp;<a href="/UTR/jeu/messagerie/messagerie.php?num=1">Com-Taker(<?php echo $infoManager['Man_NbMessage']?>)</a> <br>
                          <br>
                          <?php
				if($Man_Niveau >= 3)
				{
?>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/bin/admin.php">Admin</a> <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/joueur/liste.php">Joueurs</a>
                          <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/manager/liste.php">Managers</a>
                          <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/job/liste.php">Jobs</a> <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/sponsor/liste.php">Sponsors</a>
                          <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/marque/liste.php">Marques</a>
                          <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/modele_piece/liste.php">Modèles
                          pièce</a> <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/modele_voiture/liste.php">Modèles
                          voiture</a> <br>
                          <br>
                          <?php
				}
?>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/logout.php">Déconnexion</a>
                          <br>
                          <br>
                        </p></td>
                    </tr>
                  </tbody>
                </table></td>
            </tr>
            <tr>
              <td><img height=6
                  src="/UTR/design/bas.gif"
                  width=142></td>
            </tr>
          </tbody>
        </table></br>
        </td>
    </tr>
<?php
			}
?>
      </td>
    </tr>
  </TBODY>
</table>
