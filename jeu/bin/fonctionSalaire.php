<?php
	function traitementSalaire()
	{
		//echo"traitementSalaire<br>";
		$requeteDateDernierSalaire ="	SELECT UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(UTR_DateDernierSalaire) AS UTR_DeltaDernierSalaire FROM utr";
		$resultatDateDernierSalaire = mysql_query($requeteDateDernierSalaire)or die("Requete Date Dernier Salaire :<br />$requeteDateDernierSalaire<br /><br />".mysql_error());
		$tempsSalaire = mysql_fetch_row($resultatDateDernierSalaire);
		$dureeJour = 24 * 3600;
		if($tempsSalaire[0] > $dureeJour)
		{
			$nombreSalaires = intval($tempsSalaire[0] / $dureeJour);
			$requeteSalaireNiv1 = "	UPDATE manager, job
										SET Man_Solde = Man_Solde + (Job_Salaire * ".$nombreSalaires.")
										WHERE Man_IdJob = IdJob
										AND Man_Niveau = '1'";
			mysql_query($requeteSalaireNiv1);

			$requeteSalaireNiv2 = "	UPDATE manager, sponsor
										SET Man_Solde = Man_Solde + (Spon_Salaire * ".$nombreSalaires.")
										WHERE Man_IdSponsor = Idsponsor
										AND Man_Niveau > '1'";
			mysql_query($requeteSalaireNiv2);

			$requeteMAJDate="	UPDATE utr
									SET UTR_DateDernierSalaire = NOW()";
			mysql_query($requeteMAJDate);
		}
	}
?>