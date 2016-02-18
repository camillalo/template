// objects.js

// This function creates new objects
function makeObj() {
	if (arguments.length % 2 != 0) {
		arguments[arguments.length] = "";
		}
	for ( var i = 0; i < arguments.length; i += 2 ) {
		this[arguments[i]] = arguments[i + 1] ;
   	}
	return this;
	}


// This function recursively inspects passed objects
// and stores property information in a string. This string is 
// returned after there are no more object properties to examine
function parseObj(obj) {
	objCount = 0;
	var objStr = '';
		for (prop in obj) {
			objStr += '<TR><TD>Property: </TD><TD><B>' + prop + '</B></TD><TD>Type: </TD><TD><B>' + typeof(obj[prop]) + 
				'</B></TD><TD>Value: </TD><TD><B>'  + obj[prop] + '</B></TD></TR>';
			if (typeof(obj[prop]) == "object") {
				objStr += parseObj(obj[prop]);
				}
			}
	return objStr;
	}

// This function displays object properties accumulated from parseObj() 
function objProfile() {
	var objTable = '<TABLE BORDER=2 CELLSPACING=0><TR><TD><H1>Object Profile</H1></TD></TR>';
	for (var i = 0; i < arguments.length; i++) { 
		objTable += '<TR><TD><BR><BR><H2><TT>' + (i + 1) + ') ' + arguments[i] +  '</H2></TD></TR>';
		objTable += '<TR><TD><TT><TABLE CELLPADDING=5>' + parseObj(eval(arguments[i])) + '</TABLE></TD></TR>';
		}
	objTable += '</TABLE><BR><BR><BR>';
	return objTable;
	}