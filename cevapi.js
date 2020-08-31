var modal = document.getElementById("myModal");
var loader = document.getElementById("loader");
var modal_form = document.getElementById("restoran");
var modal_izvjestaj = document.getElementById("izvjestaj");

var span = document.getElementsByClassName("modal-close")[0];

function novi_izvjestaj() {
	 modal_izvjestaj.style.display = "block";
	 
}
 
function obrisi_izvjestaj(id)  {
	var m = confirm("Jeste li sigurni da želite obrisati izvještaj?");
	if (m == true) 
	{
		loader.style.display = "block";
		AJAXPost('obrisi_izvjestaj.php', 'id='+id).then((a)=>{
			if (a.trim()=='OK')
			{
				loader.style.display = "none";
				//alert('Obrisano');
				ucitaj_stranicu(null,'izvjestaji');
			}
			else
			{
			loader.style.display = "none";
				alert(a);
			}
				
		}, (b)=>{
			 loader.style.display = "none";
			 alert(b);
		})
	}
}

function napravi_izvjestaj(s) {
	var nazivi = document.getElementById("izvjestaj_naziv");
	var godi = document.getElementById("izvjestaj_godina");
		if (nazivi.value!='' & godi.value!='')
		{

		  modal_izvjestaj.style.display = "none";
			 loader.style.display = "block";
				 PostForm(s).then((a)=>{
					 if (a.trim()=='OK')
					 {
						 loader.style.display = "none";
						alert('Završeno');
						//modal_form.style.display = "none";						
						ucitaj_stranicu(null,'izvjestaji');
					 }
					else
					{
						loader.style.display = "none";
						alert(a);
					}
				 }, (b)=>{
					 loader.style.display = "none";
					 alert(b);
				 }); 
		}
		else
		{
			alert("Obavezno je unjeti naziv i godinu!");
		}
}
 
function spremi_postavke(s)  {
	 loader.style.display = "block";

	 PostForm(s).then((a)=>{
		 if (a.trim()=='OK')
		 {
			loader.style.display = "none";
			alert('Spremljeno');
		 }
		else
		{
			loader.style.display = "none";
			alert(a);
		}
	 }, (b)=>{
		 loader.style.display = "none";
		 alert(b);
	 });
}
 
function obrisi_restoran(r) {
	var m = confirm("Jeste li sigurni da želite obrisati restoran?");
	if (m == true) 
	{
		loader.style.display = "block";
		AJAXPost('obrisi_restoran.php', 'id='+r).then((a)=>{
		 if (a.trim()=='OK')
		 {
			loader.style.display = "none";
			//alert('Obrisano');
			ucitaj_stranicu(null,'restorani');
		 }
		else
		{
		loader.style.display = "none";
			alert(a);
		}
			
	 }, (b)=>{
		 loader.style.display = "none";
		 alert(b);
		 
	 })
	}
 }
 
 function post_restoran(r) {
	 
	var nazivr = document.getElementById("naziv_restorana");
	
		if (nazivr.value!='')
		{
			 PostForm(r).then((a)=>{
				 if (a.trim()=='OK')
				 {
					//alert('Spremljeno');
					modal_form.style.display = "none";	
					ucitaj_stranicu(null,'restorani');
				 }
				else
				{
					alert(a);
				}
			 }, (b)=>{
				 
				 alert(b);
			 })
	 	}else{
			alert('Naziv je obavezan!');
		}
 }
 
 
function AJAXPost(url,params) {
	 
	return new Promise(function(resolve, reject) {
	 
	 
		if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else { 
			// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}

		xmlhttp.open("POST",url,false);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		 xmlhttp.onload = function() {
		  if (xmlhttp.status == 200) {
		  resolve(xmlhttp.response);
		  }
		  else {
			reject(Error(xmlhttp.statusText));
		  }
		};
		xmlhttp.send(params);
	}); 
}
 

function PostForm(myself) {
	
    var elem   = myself.form.elements;
    var url    = myself.form.action;  
	
    var params = "";
    var value;
	
    for (var i = 0; i < elem.length; i++) {
		
        if (elem[i].tagName == "SELECT") {
            value = elem[i].options[elem[i].selectedIndex].value;
        } else {
			if (elem[i].checked)
				value = 1;      
			else
				value = elem[i].value;                
        }
        params += elem[i].name + "=" + encodeURIComponent(value) + "&";
    }

	return AJAXPost(url, params);
}

	
		
function ucitaj_stranicu(e, page) {
	
	if (e!=null){
		var x = document.querySelectorAll('.nav-link,.active');
		 for (i = 0; i < x.length; i++) {
				x[i].classList.remove("active");
		 }
		 
		e.classList.add("active");
	}
    fetch("sadrzaj.php?page="+page /*, options */)
    .then((response) => response.text())
    .then((html) => {
        document.getElementById("main-content").innerHTML = html;
	
    })
    .catch((error) => {
        console.warn(error);
    });
} 

function ucitaj_formu(id) {
	var loader = document.getElementById("loader");
	loader.style.display = "block";
	 
	 var load = function(resolve,reject) {
			fetch("dodaj_uredi_restoran.php?id="+id /*, options */)
			.then((response) => response.text())
			.then((html) => {
				document.getElementById("modal-form-html").innerHTML = html;
				loader.style.display = "none";
				resolve();
			})
			.catch((error) => {
				console.warn(error);
				loader.style.display = "none";
				reject();
			});
	 }
	 return new Promise(load);
} 

function prikazi_izvjestaj(id) {
		
	loader.style.display = "block";
	 
	var load = function(resolve,reject) {
		fetch("prikaz_izvjestaja.php?id="+id /*, options */)
		.then((response) => response.text())
		.then((html) => {
			document.getElementById("modal-html").innerHTML = html;
			loader.style.display = "none";
			resolve();
		})
		.catch((error) => {
			console.warn(error);
			loader.style.display = "none";
			reject();
		});
	}
	return new Promise(load);
}


function showModal(id) {
	prikazi_izvjestaj(id).then(()=>{
		modal.style.display = "block";
	});			
}

function hideFormModal(){
		modal_form.style.display = "none";
}

function hideModalIzvjestaj(){
		modal_izvjestaj.style.display = "none";
}
function showForm(id)
{
	ucitaj_formu(id);
	modal_form.style.display = "block";	
}

span.onclick = function() {
	modal.style.display = "none";
}


window.onclick = function(event) {
  if (event.target == modal) {
		modal.style.display = "none";
  }
  if (event.target == modal_form) {
		modal_form.style.display = "none";
  }
    if (event.target == modal_izvjestaj) {
		modal_izvjestaj.style.display = "none";
  }
} 

ucitaj_stranicu(null,'izvjestaji');