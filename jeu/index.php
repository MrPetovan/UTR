<?php
	session_name("Joueur");
	session_start();

	if(!isset($_SESSION['IdJoueur']) || !isset($_SESSION['Man_Niveau']))
	{
		header("Location:accueilJeu.php?erreur=3");
		exit;
	}
	else
	{
		header("Location:accueilJeu.php");
		exit;
	}
?>