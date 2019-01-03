<LINK href="/UTR/design/utr.css"type=text/css rel=styleSheet>
<body bgcolor="#000000" leftmargin="0" topmargin="0">
<table width=161 border=0 align="left" cellpadding=0 cellspacing=0><tbody>
  <tr>
    <td width="161" align=middle valign=top
          background="/UTR/design/gauche_r1_c1.jpg"><br>
        <table cellspacing=0 cellpadding=0 width=142 border=0>
          <tbody>
            <tr>
              <td><img height=41
                  src="/UTR/design/infos.gif"
                  width=142></td>
            </tr>
            <tr>
              <td align=right
                background="/UTR/design/tile.gif"><br>
                <table cellspacing=0 cellpadding=0 width="90%" border=0>
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
            <tr>
              <td></td>
            </tr>
            <tr>
              <td>
                <?php
			if(isset($_SESSION['IdJoueur']))
			{
?>
              </td>
            </tr>
            <tr>
              <td><img height=41
                  src="/UTR/design/members.gif"
                  width=142></td>
            </tr>
            <tr>
              <td align=right
                background="/UTR/design/tile.gif"><br>
                <table cellspacing=0 cellpadding=0 width="90%" border=0>
                  <tbody>
                    <tr>
                      <td> <p align="left"><img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/voiture/liste.php">Voiture</a>
                          <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/piece/liste.php">Pieces détachées</a>
                          <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/pilote/liste.php">Pilotes</a>
                          <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/course/liste.php">Courses</a>
                          <br>
                          <img height=8
                        src="/UTR/design/fleche.gif"
                        width=8> <a href="/UTR/jeu/logout.php">Déconnexion</a>
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
        </table></td>
  </tr>
  <tr>
    <td align=middle valign=top
          background="/UTR/design/gauche_r3_c1.jpg"><p align="center"></p>
      <?php
			$IdManager=$_SESSION['IdManager'];

			$requeteManager = "	SELECT IdManager, Man_Nom, Man_Niveau, Man_Solde, Man_Reputation
								FROM manager
								WHERE IdManager = '$IdManager'";
			$resultatManager = mysql_query($requeteManager)or die(mysql_error());
			$infoManager = mysql_fetch_assoc($resultatManager);
?>
      <br><div align="center"></div>
      <div align="center">
        <table>
          <tr>
            <th>Manager n°
              <?php echo $infoManager['IdManager']?>
            </th>
          </tr>
          <tr>
            <td>
              <?php echo $infoManager['Man_Nom']?>
            </td>
          </tr>
          <tr>
            <th>Niveau</th>
          </tr>
          <tr>
            <td>
              <?php echo $infoManager['Man_Niveau']?>
            </td>
          </tr>
          <tr>
            <th>Réputation</th>
          </tr>
          <tr>
            <td>
              <?php echo $infoManager['Man_Reputation']?>
            </td>
          </tr>
          <tr>
            <th>Solde</th>
          </tr>
          <tr>
            <td>
              <?php echo $infoManager['Man_Solde']?>
              €</td>
          </tr>
        </table>
        <?php
			}
?>
      </div>
      <div align="center"></div>
      </td>
  </tr>
  <tr>
    <td valign=top><img height=9
            src="/UTR/design/gauche_r4_c1.jpg"
            width=161></td>
  </tr></TBODY>
</table>
