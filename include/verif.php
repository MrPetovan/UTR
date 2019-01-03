<?php
	function is_NotNull($champ,$codeErreur)
	{
		if ($champ != "") return "";
		else return $codeErreur;
	}
	
	function is_Nul($champ)
	{
		return($champ == "");
	}
	
	function is_Date($date,$codeErreur1,$codeErreur2)
	{
		$ok=true;
		//ereg("^[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}$",$date)
		if(!ereg("^[0-9]{4}[-][0-9]{2}[-][0-9]{2}$",$date)) $ok = false;
		
		if($ok) list($annee,$mois,$jour) = split("-",$date);
		
		if ($ok && !checkdate ( $mois, $jour, $annee))
		{
			$ok=false;
		}
		if(!$ok)return $codeErreur1;
		else
		{
			$dateJour=date("Y-m-d");
			list($anneeActuelle,$moisActuel,$jourActuel) = split("-",$dateJour);
			if($anneeActuelle < $annee) $ok=false;
			
			if($ok && $anneeActuelle == $annee && $moisActuel < $mois ) $ok=false;
			if($ok && $moisActuel == $mois && $jourActuel < $jour ) $ok=false;
		}
		if(!$ok) return $codeErreur2;
		else return "";
	}
	
	function is_Number($nombre,$taille,$codeErreur)
	{
		if (ereg("^[0-9]{1,".$taille."}[,.]{0,1}[0-9]{0,".$taille."}$",$nombre)) return "";
		else return $codeErreur;
	}

	function is_IP($adresse,$codeErreur)
	{
		if (ereg("^[0-9]{1,3}[.][0-9]{1,3}[.][0-9]{1,3}[.][0-9]{1,3}$",$adresse)) return "";
		else return $codeErreur;
	}
	
	function is_File_Ok($FILES,$chemin,$codeErreur)
	{
		$TypesAutorises=Array("text/plain","text/html");
		$ExtensionsAutorisees=Array("txt","htm","html");
		
		$VerifTexte = explode(".",$FILES["Com_EmplacementFichierTexte"]["name"]);
		//Vérification du nom, du type et de l'extension
		if(ereg("[a-zA-Z0-9\-_]*",$VerifTexte[0]) && in_array($VerifTexte[1],$ExtensionsAutorisees) && 
				in_array($FILES["Com_EmplacementFichierTexte"]["type"],$TypesAutorises))
		{
			$chemin .= $VerifTexte[0].".txt";
			$uploadfrom = $FILES["Com_EmplacementFichierTexte"]["tmp_name"];
			move_uploaded_file($uploadfrom, $chemin);
			return "";
		} 
		else return $codeErreur;
	}
?>