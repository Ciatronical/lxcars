function emptyElementMessage(view, element)
{
	if(!element.hasAttribute('aria-label')) view.innerHTML = 'Bitte füllen Sie alle Formularfelder mit einem Stern (*) aus.';
	else view.innerHTML = 'Bitte füllen Sie alle Formularfelder mit einem Stern (*) aus. Das Feld ' + element.getAttribute('aria-label') + ' fehlt.';
}

function validateForm()
{
	const response = document.getElementById('response');
	let elements = document.getElementsByTagName('input');
	response.className = 'text-danger';
	response.innerHTML = '';
	let requireds = document.getElementsByClassName('required');
	for(element of requireds)
	{
		if(element.value.length == 0)
		{
			emptyElementMessage(response, element);
			return false;
		}
	}
	if(document.getElementById('firma-radio').checked)
	{
		let gewerbeItems = document.getElementsByClassName('gewerbe');
		for(element of gewerbeItems)
		{
			if(element.value.length == 0)
			{
				emptyElementMessage(response, element);
				return false;
			}
		}
	}
	if(!document.getElementById('mandat-identisch').checked)
	{
		let gewerbeItems = document.getElementsByClassName('sepa-mandat');
		for(element of gewerbeItems)
		{
			if(element.value.length == 0)
			{
				emptyElementMessage(response, element);
				return false;
			}
		}
	}
	if(document.getElementById('zulassung').checked || document.getElementById('umschreibung').checked)
	{
		let gewerbeItems = document.getElementsByClassName('sepa-required');
		for(element of gewerbeItems)
		{
			if(element.value.length == 0)
			{
				emptyElementMessage(response, element);
				return false;
			}
		}
		if(!IBAN.isValid(document.getElementById('mandats-iban').value))
		{
			response.innerHTML = "Die IBAN ist nicht korrekt."
			return false;
		}
	}
	if(document.getElementById('ersatz').checked)
	{
		if(!validateEidstattChbx()) return false;
		if(document.getElementById('erklaerung').value.length == 0)
		{
			response.innerHTML = "Bitte geben Sie eine Erklärung ein.";
			return false;
		}
	}
	return true;
}

function validateEidstattChbx()
{
	let eidstattItems = document.getElementsByClassName('eidstatt-chbx');
	let checked = false;
	for(element of eidstattItems)
	{
		checked = checked || element.checked;
	}
	if(!checked)
	{
		response.innerHTML = "Bitte wählen Sie das/die Dokument(e) die abhandengekommen sind.";
		return false;
	}
	if(document.getElementById('sonstiges').checked)
	{
		if(document.getElementById('sonstiges-text').value.length == 0)
		{
			response.innerHTML = "Bitte fühlen Sie das Textfeld für Sontiges aus.";
			return false;
		}
	}
	return true;
}

function auswahlAuftrag()
{
	sepaMandatNeeded();
	evbNummerNeeded();
	eidstattNeeded();
}

function sepaMandatNeeded()
{
	const div = document.getElementById('sepa-mandat-div');
	if(document.getElementById('zulassung').checked || document.getElementById('umschreibung').checked)
	{
		div.style.display = "";
	}
	else
	{
		div.style.display = "none";
		document.getElementById('mandat-identisch').checked = true;
		hideShowSepaMandant();
	}
}

function evbNummerNeeded()
{
	const div = document.getElementById('evb-nummer-div');
	const evbNummer = document.getElementById('evb-nummer');
	if(document.getElementById('zulassung').checked || document.getElementById('umschreibung').checked)
	{
		div.style.display = "";
		evbNummer.className = "required"
	}
	else
	{
		div.style.display = "none";
		evbNummer.className = ""
	}
}

function eidstattNeeded()
{
	const div = document.getElementById('eidstatt-div');
	if(document.getElementById('ersatz').checked)
	{
		div.style.display = "";
	}
	else
	{
		div.style.display = "none";
	}
}

function hideShowSepaMandant()
{
	let mandat = document.getElementById('sepa-mandat');
	if(document.getElementById('mandat-identisch').checked)
	{
		mandat.style.display = "none";
	}
	else
	{
		mandat.style.display = "";
	}
}

function hideShowGewerbeanschrift()
{
	let div = document.getElementById('gewerbeanschrift');
	if(document.getElementById('firma-radio').checked)
	{
		div.style.display = "";
	}
	else
	{
		div.style.display = "none";
	}
}

function toUpperCase(id)
{
	let e = document.getElementById(id);
	e.value = e.value.toUpperCase();
}

function checkfin( fin, cn ){
     sum = 0;
    if(cn=='-'){return true;}
    if(cn==''){return false;}
    mult = new Array(9,8,7,6,5,4,3,2,10,9,8,7,6,5,4,3,2);
    for(i in mult){
        sum+=(mult[i])*(EBtoNum(fin[i]));
   }
   check=sum%11;
    if(check==10){checkchar='X';}
    else{checkchar=check;}
    if(cn==checkchar){return true;}
    else{return false;}
}

function EBtoNum( fin ){
    if(fin=='O'||fin=='0'){return 0;}
    if(fin=='A'||fin=='J'||fin=='1'){return 1;}
    if(fin=='B'||fin=='K'||fin=='S'||fin=='2'){return 2;}
    if(fin=='C'||fin=='L'||fin=='T'||fin=='3'){return 3;}
    if(fin=='D'||fin=='M'||fin=='U'||fin=='4'){return 4;}
    if(fin=='E'||fin=='N'||fin=='V'||fin=='5'){return 5;}
    if(fin=='F'||fin=='W'||fin=='6'){return 6;}
    if(fin=='G'||fin=='P'||fin=='X'||fin=='7'){return 7;}
    if(fin=='H'||fin=='Q'||fin=='Y'||fin=='8'){return 8;}
    if(fin='I'||fin=='R'||fin=='Z'||fin=='9'){return 9;}
    else{alert("EBtoFin Error!!!");}
}

function onkeyupEVB()
{
	document.getElementById('check-evb').innerHTML = "";
	toUpperCase('evb-nummer');
}

function checkEVB()
{
	const response = document.getElementById('check-evb');
	response.className = 'text-danger';
	let evbnummer = document.getElementById('evb-nummer').value;
	if(evbnummer.length == 0)
	{
		response.innerHTML = "Das Feld 'eVB-Nummer' ist leer.";
		return;
	}
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function()
	{
		if (httpRequest.readyState === 4)
		{
			const response = document.getElementById('check-evb');
			if (httpRequest.status === 200)
			{
				if(null != httpRequest.responseText)
				{
					if(!httpRequest.responseText.includes('notfound'))
					{
						response.className = 'text-success';
						response.innerHTML = 'Die eVB-Nummer ist gültig.';
					}
					else
					{
						response.innerHTML = 'Die eVB-Nummer ist ungültig!';
					}
				}
				else
				{
					response.innerHTML = 'Die Nachricht konnte nicht versendet werden';
					console.log("error empty response");
				}
  			}
			else
			{
				response.innerHTML = 'Die Nachricht konnte nicht versendet werden';
				console.log("error loading response");
			}
		}
	};
	let formData = new FormData();
	formData.append('evbsearch', evbnummer);
	httpRequest.open('POST', "evbsearch.php");
	httpRequest.send(formData);
}

function textVorlage()
{
	if(!validateEidstattChbx()) return;
	document.getElementById('response').innerHTML = '';
	
	let text = '';
	let checked = 0;
	let eidstattItems = document.getElementsByClassName('eidstatt-chbx');
	for(element of eidstattItems)
	{
		if(element.checked)
		{
			if(text.length > 0) text += ', ';
			if(element.id != 'sonstiges') text += element.getAttribute('aria-label');
			else text += document.getElementById('sonstiges-text').value;
			checked++;
		}
	}
	const date = new Date(Date.now() - 1.814e9);
	text += (checked <= 1)? ' wurde ' : ' wurden ';
	text += ' am ' + date.toLocaleDateString('de') + ' bei mir Zuhause (';
	text += document.getElementById('strasse').value + ' ' + document.getElementById('hsnr').value;
	text += ', ' + document.getElementById('plz').value + ' ' + document.getElementById('ort').value;
	text += ') vermutlich mit dem Altzeitungsstapel in der blauen Tonne ensorgt.';
	document.getElementById('erklaerung').value = text;
}

