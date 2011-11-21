<?
function calcTemps($acc,&$accInit, $vMax,&$vInit, $dist)
{
	$pi = pi();

	$resultat = array();
	$resultat[0] = 0;
	$resultat[1] = 0;
	$resultat[2] = 0;

	$a = -$acc*$acc /12 /$vMax;
	$b = $accInit /2;
	$c = $vInit;
	$d = -$dist;

	$vt = -$b /3 /$a;
	$mvt = -$vt;
	$p = $c /$a- $b*$b /3 /$a /$a;
	$q=$b*$b*$b /$a /$a /$a /13.5 + $d /$a - $b*$c /3 /$a /$a;

	if (abs($p) < 1e-12) { $p=0; }
	if (abs($q) < 1e-12) { $q=0; }
	$del = $q*$q /4 + $p*$p*$p /27;
	global $del;
	if (abs($del) < 1e-12) { $del=0; }

	$t0 = 2*$accInit*$vMax /$acc*$acc;
	//echo "<br /> t0 = $t0 <br />";

	if ($del <= 0)
	{
		if ($p != 0) {
			$kos = -$q /2 /sqrt(-$p*$p*$p /27);
			$r = sqrt(-$p/3);
		}
		else {
			$kos=0;
			$r=0;
		}

		if (abs($kos==1)) {
			$alpha=-$pi*($kos-1) /2;
		}
		else {
			$alpha=acos($kos);
		}

		for($k=0; $k <= 2; $k++) {
			$xk=2*$r*cos(($alpha + 2*$k*$pi) /3) + $vt;
			$resultat[$k] = arrondi($xk);
		}

		if($r==0) {
			$triple = "Solution triple :";
		}
		else {
			$triple="Trois solutions :";
		}
		//echo "triple x1 = $resultat[0] -- x2 = $resultat[1] -- x3 = $resultat[2]";
		echo "triple : x1=".$resultat[0]." x2=".$resultat[1]." x3=".$resultat[2];
		$vInit = ((-$acc*$acc)/(4*$vMax))* $resultat[2]*$resultat[2] + $accInit * $resultat[2] + $vInit;
		$accInit = ((-$acc*$acc)/(2 * $vMax)) * $resultat[2] + $accInit;
		return $resultat[2];
	}
	else
	{
		$xuni = arrondi(uv(1) + uv(-1) + $vt);
		//echo "resultat unique, x = $xuni";
		//alert("x unique, x = " + xuni);
	}
}

function uv ($sg) {

	$r = sqrt($del);
	$z = -$q /2 + $sg*$r;
	return sgn($z)*pow(abs($z), 1/3);

}

function sgn($x) {

	$s = ($x>0) - ($x< 0);
	return $s;

}

function arrondi($x) {

	return sgn($x)*floor( abs($x)*1e10 + 0.5) /1e10;

}
/*$distance = 250;
$vInit = 0;
$Acceleration = $accInit = 16.15;
$vitesseMax = 52;
$vitInit = 0;

$tempsVitesseMax = 2 * $accInit * $vitesseMax /($Acceleration*$Acceleration);
$distanceVitesseMax = 	((-$Acceleration * $Acceleration) / (12 * $vitesseMax)) * $tempsVitesseMax * $tempsVitesseMax * $tempsVitesseMax +
						($accInit / 2) * $tempsVitesseMax * $tempsVitesseMax + $vitInit * $tempsVitesseMax;
if($distanceVitesseMax > $distance)
{
	echo "Pas Vitesse max : calcTemps($Acceleration,$accInit,$vitesseMax,$vitInit,$distance)<br>";
	$tempsTotalTroncon = calcTemps($Acceleration,$accInit,$vitesseMax,$vitInit,$distance);
}
else
{
	echo "Vitesse max<br>";
	$tempsTotalTroncon = $tempsVitesseMax + ($distance - $distanceVitesseMax)/ $vitesseMax;
}

echo "<hr>$distance m : ".$tempsTotalTroncon."<hr>";
*/?>