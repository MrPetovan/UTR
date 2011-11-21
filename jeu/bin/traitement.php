<?php
	session_name("Joueur");
	session_start();
	error_reporting(E_ALL ^E_NOTICE);
	if(!isset($_SESSION['IdJoueur']))
	{
		header("location:../../index.php");
	}
	$IdJoueur = $_SESSION['IdJoueur'];
	$Man_Niveau = $_SESSION['Man_Niveau'];
	$IdManager = $_SESSION['IdManager'];
	if($Man_Niveau < 3)
	{
		header("location:../../index.php");
	}

	include('../../include/connexion.inc.php');

	include('fonctionMath.php');
	include('../../include/Xp.php');
	include('../../include/fonctions.php');
	include('../../include/fonctionsMessagerie.php');
	include('fonctionCourse.php');
	include('fonctionSalaire.php');
	include('fonctionPieces.php');
	include('fonctionVoitures.php');


	if($_POST['UTR_TraiterCourse']=="on") traitementCourse();
	if($_POST['UTR_VerserSalaire']=="on") traitementSalaire();
	if($_POST['UTR_CreerPieces']=="on") creationPiece();
	if($_POST['UTR_CreerVoitures']=="on") creationVoiture();

	header("Location: admin.php?ok=1");
	exit;
?>
</body>
</html>