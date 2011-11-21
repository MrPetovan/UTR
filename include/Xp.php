<?php
	function niveauAdd($XP,$seuil)
	{
		$XP -= $seuil;
		if($XP >= 0)
		{
			$seuil += 1000;
			return niveauAdd($XP,$seuil)+1;
		}
		else return 1;
	}

	function niveauDouble($XP,$seuil)
	{
		$temp = $XP - $seuil;
		if($temp >= 0)
		{
			$seuil *= 2;
			return niveauDouble($XP,$seuil)+1;
		}
		else return 1;
	}

	function niveauCarre($XP)
	{
		return intval(sqrt($XP / 1000))+1;
	}
?>
