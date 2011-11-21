function cacherElements(indice)
{
	eval("var remove_el=document.getElementById(\"remove"+indice+"\")");
	if(remove_el!=null)
	{
		if (remove_el!=''&&remove_el.length==null)
		{
			remove_el.style.display='none';
		}
		else
		{
			for (i=0;i<remove_el.length;i++)	remove_el[i].style.display='none';
		}

		var td=document.getElementsByTagName("td");
		for(var i=0; i<td.length; i++)
			if(td[i].id==indice)
			{
				td[i].className='normal';
				td[i].onmouseout=function (){this.className='normal'};
				td[i].onmouseover=function (){this.className='over'};
			}
	}
}
function montrerElement(indice)
{
	eval("var remove_el=document.getElementById(\"remove"+indice+"\")");

	if (remove_el!=''&&remove_el.length==null)remove_el.style.display='';
	else
	{
		for (i=0;i<remove_el.length;i++)	remove_el[i].style.display='';
	}
	var td=document.getElementsByTagName("td");
	for(var i=0; i<td.length; i++)
		if(td[i].id==indice)
		{
			td[i].className='select';
			td[i].onmouseout="";
			td[i].onmouseover="";
		}

}
function changerFormulaire(indice)
{
	for(var i=1; i<= 21 ; i++)
		if(i != indice) cacherElements(i);
		else montrerElement(i);
}