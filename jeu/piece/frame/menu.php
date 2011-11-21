<table border="0" width="100%" height="450">
	<tr>
		<td>
			<p>Menu</p>
			<p><a href="/UTR/frame/regles.php">Les Règles</a></p>
			<p><a href="/UTR/jeu/accueilJeu.php">Jouer !</a></p>
<?php
			if(isset($_SESSION['IdJoueur']))
			{
?>
			<p>Menu de jeu</p>
			<p><a href="/UTR/jeu/voiture/liste.php">Voiture</a></p>
			<p><a href="/UTR/jeu/piece/liste.php">Pieces détachées</a></p>
			<p><a href="/UTR/jeu/pilote/liste.php">Pilotes</a></p>
			<p><a href="/UTR/jeu/course/liste.php">Courses</a></p>
			<p><a href="/UTR/jeu/logout.php">Déconnexion</a></p>
<?php
			$IdManager=$_SESSION['IdManager'];

			$requeteManager = "	SELECT IdManager, Man_Nom, Man_Niveau, Man_Solde, Man_Reputation
								FROM manager
								WHERE IdManager = '$IdManager'";
			$resultatManager = mysql_query($requeteManager)or die(mysql_error());
			$infoManager = mysql_fetch_assoc($resultatManager);
?>
			<table>
				<tr>
					<th>Manager n°<?php echo $infoManager['IdManager']?></th>
				</tr>
				<tr>
					<td><?php echo $infoManager['Man_Nom']?></td>
				</tr>
				<tr>
					<th>Niveau</th>
				</tr>
				<tr>
					<td><?php echo $infoManager['Man_Niveau']?></td>
				</tr>
				<tr>
					<th>Réputation</th>
				</tr>
				<tr>
					<td><?php echo $infoManager['Man_Reputation']?></td>
				</tr>
				<tr>
					<th>Solde</th>
				</tr>
				<tr>
					<td><?php echo $infoManager['Man_Solde']?> &euro;</td>
				</tr>
			</table>
<?php
			}
?>
		</td>
	</tr>
</table>

