function calcTemps(acc,vmax,dist)
{
	alert("YAYOUUUU!");
	var pi,q,del;
	pi=Math.PI;e=Math.E;
	a=4;b=-5;c=-23;d=6
	var x=new Array()
	for(k=0;k<=2;k++) {x[k]=0};
	with (Math)
	{
		//a x t^3 + b x t² + c x t + d = 0
		var a = -a*a/12/vmax;
		var b = a/2;
		var c = 0;
		var d = -dist;
		
		vt=-b/3/a;mvt=-vt;
		p=c/a-b*b/3/a/a;
		q=b*b*b/a/a/a/13.5+d/a-b*c/3/a/a;
		if (abs(p)< 1e-12) {p=0};
		if (abs(q)< 1e-12) {q=0};
		del=q*q/4+p*p*p/27;
		if (abs(del)< 1e-12) {del=0}
		if (del<=0)
		{
			if (p!=0){kos=-q/2/sqrt(-p*p*p/27);r=sqrt(-p/3)} else {kos=0;r=0} 
			
			if (abs(kos==1)) {alpha=-pi*(kos-1)/2} else alpha=acos(kos)}
			
			for(k=0;k<=2;k++){xk=2*r*cos((alpha+2*k*pi)/3)+vt;x[k]=arrondi(xk)}
			
			if(r==0){triple="Solution triple :"} else {triple="Trois solutions:"}
			
			alert(triple+"\n"+"x1="+x[0]+"\n"+"x2="+x[1]+"\n"+"x3="+x[2]);
		} 
		else
		{
			xuni=arrondi(uv(1)+uv(-1)+vt);
			alert("x unique, x = "+xuni);
		}
		
	} 
}

function uv(sg)
{
	with(Math)
	{
		r=sqrt(del);
		z=-q/2+sg*r;
		return sgn(z)*pow(abs(z),1/3);
	}
}

function sgn(x)
{
	s=(x>0) - (x< 0);
	return s;
}

function arrondi(x)
{
	return sgn(x)*Math.floor(Math.abs(x)*1e10+.5)/1e10;
}

