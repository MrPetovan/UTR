<?php
//Nommage de la session :
	session_name("Joueur");
//Démarrage de la session :
	session_start();
//Destruction de la session si déjà loggué
	session_unset();
	session_set_cookie_params(session_name(),'',0,"/");

	include("../include/connexion.inc.php");

	$login = trim($_POST['Jou_Login']);
	$password = trim($_POST['Jou_PassWord']);

	// Réalisation des traitements
	if ($login!=""&& $password != "" )
	{
		$requeteMdp= "	SELECT Jou_MotDePasse
							FROM joueur
							WHERE Jou_Login = '".$login."'";
		$resultatMdp = mysql_query($requeteMdp)or die(mysql_error());
		$pass = mysql_fetch_row($resultatMdp);

		$passwordcrypte = $pass[0];

		if(crypt($password,$passwordcrypte)==$passwordcrypte)
		{
		//Initialisation des variables de session :
			$requeteLogin = "	SELECT IdJoueur, Man_Niveau, IdManager
									FROM joueur, manager
									WHERE Jou_Login = '$login'
									AND Jou_MotDePasse = '$passwordcrypte'
									AND IdJoueur = Man_IdJoueur
									AND Jou_CodeInscription IS NULL";
			$joueur = mysql_fetch_assoc(mysql_query($requeteLogin));

			if($joueur !="")
			{
				$_SESSION['IdJoueur']=$joueur['IdJoueur'];
				$_SESSION['IdManager']=$joueur['IdManager'];
				$_SESSION['Man_Niveau']=$joueur['Man_Niveau'];

				include('bin/fonctionMath.php');
				include('../include/Xp.php');
				include('../include/fonctions.php');
				include('../include/fonctionsMessagerie.php');
				include('bin/fonctionCourse.php');
				include('bin/fonctionSalaire.php');
				include('bin/fonctionPieces.php');
				include('bin/fonctionVoitures.php');

				creationPiece();
				creationVoiture();
				traitementCourse();
				traitementSalaire();

				$requeteMAJDateLogin ="	UPDATE joueur
												SET Jou_DernierLogin = CURRENT_DATE()
												WHERE IdJoueur = '".$joueur['IdJoueur']."'";
				mysql_query($requeteMAJDateLogin);

				header("location:joueur/stat.php?".session_name()."=".session_id());
				exit;
			}
			else
			{
				header("location:accueilJeu.php?erreur=4");
			exit;
			}
		}
		else
		{
			header("location:accueilJeu.php?erreur=1");
			exit;
		}
	}
	else
	{
		header("Location: accueilJeu.php?erreur=2");
		exit;
	}
?>