function is_NotNull(element,name,suffixe)
{
	if(element == "")
	{
		if(suffixe == undefined) suffixe ="";
		return (" - "+name+" doit être renseigné"+suffixe+"\n");
	}
	return "";
}

function is_Number(element,Taille, nom)
{
	var exp = new RegExp("^[0-9]{1,"+Taille+"}[,.]{0,1}[0-9]{0,"+Taille+"}$","");

	if (!exp.test(element))
	{
		if(Taille=="")	return(" - "+nom+" doit être un nombre.\n");
		else return(" - "+nom+" doit être un nombre de maximum "+Taille+" chiffres.\n");
	}
	else return "";
}

function is_IP(element,nom)
{
	var exp = new RegExp("^[0-9]{1,3}[.][0-9]{1,3}[.][0-9]{1,3}[.][0-9]{1,3}$","");

	if (!exp.test(element))	return(" - "+nom+" n'est pas saisie correctement\n");
	else return "";
}

function is_Null(valeur)
{
	return (valeur == "");
}

function is_Date(d,name,flag)
{
	// Cette fonction vérifie le format JJ/MM/AAAA saisi et la validité de la date.
	// Le séparateur est défini dans la variable separateur
	var exp = new RegExp("^[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}$","");

	//var separateur="/"; // separateur entre jour/mois/annee

	var ok=true;
	var chaine="";

	if(!exp.test(d))
	{
		return ("\t- "+name+" est mal saisie\n");
	}

	if (ok==true)
	{
		var j=(d.substring(0,2));
		var m=(d.substring(3,5));
		var a=(d.substring(6));

		var dateJour=new Date();

		var d2=new Date(a,m-1,j);
		j2=d2.getDate();
		m2=d2.getMonth()+1;
		a2=d2.getFullYear();
		if ( (j!=j2)||(m!=m2)||(a!=a2) )
		{
			return ("\t- "+name+" est invalide\n");
		}
		else
		{
//Contrôle de cohérence :
//flag == 1 : Futur
//flag == -1 : Passé
//Sinon pas de contrôle
			if((flag == 1 && d2 < dateJour) || (flag == -1) && (2 > dateJour))
					return ("\t- "+name+" est incohérente\n");
		}
	}
	return chaine;
}

function is_eMail(eMail)
{
	if (!eMail.search("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9]+)*$/"))
		return(" - L'adresse e-mail n'est pas correcte\n");
	else
		return "";
}