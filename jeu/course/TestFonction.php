<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>Test fonction</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="/UTR/design/utr.css" type="text/css" rel="styleSheet" />
<?php
function calcTemps($acc, $accInit, $vMax, $vInit, $dist) {

	echo "test réussit $acc, $accInit, $vMax, $vInit, $dist<br />";
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
	$p = ($c /$a) - ($b*$b /3 /$a /$a);
	$q=($b*$b*$b /$a /$a /$a /13.5) + ($d /$a) - ($b*$c /3 /$a /$a);
	global $q;
	if (abs($p) < 1e-12) { $p=0; }
	if (abs($q) < 1e-12) { $q=0; }
	$del = $q*$q /4 + $p*$p*$p /27;
	global $del;
	if (abs($del) < 1e-12) { $del=0; }

	$t0 = (2*$accInit*$vMax)/($acc*$acc);
	echo "<br /> t0 = $t0 <br />";

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
			echo "$triple x1 = $resultat[0] -- x2 = $resultat[1] -- x3 = $resultat[2]";
			//alert(triple+"\n"+"x1="+x[0]+"\n"+"x2="+x[1]+"\n"+"x3="+x[2]);
		}
		else
		{
			$xuni = arrondi(uv(1) + uv(-1) + $vt);
			echo "resultat unique, x = $xuni";
			//alert("x unique, x = " + xuni);
		}

	//echo "<br />", pi(), "<br />";
	//echo "<br />", $pi, "<br />";

}

function uv ($sg)
{
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
?>
</head>

<body>
<p>Salut world!! </p>
<?php
//calcTemps($acc, $accInit, $vMax, $vInit, $dist)
	calcTemps(5, 5, 40, 0, 50);
	calcTemps(5, 4, 3, 2, 1);
	calcTemps(15, 32, 3, 84, 55);
?>
</body>
</html>
