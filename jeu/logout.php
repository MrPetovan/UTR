<?php
	//Nommage de la session :
		session_name("Joueur");

	//Démarrage de la session :
		session_start();

		session_unset();
		//print_r(session_get_cookie_params());
		session_set_cookie_params(session_name(),'',0,"/");
		//print_r(session_get_cookie_params());
		session_destroy();
		//print_r(session_get_cookie_params());

		header("location:../frame/accueil.php");
		exit;
?>
