// dhtml.js

// Set browser-determined global variables
var NN 		 = (document.layers ? true : false);
var hideName = (NN ? 'hide' : 'hidden');
var showName = (NN ? 'show' : 'visible');
var zIdx 	 = -1;
function genLayer(sName, sLeft, sTop, sWdh, sHgt, sVis, copy) {
	// alert(sVis);
	if (NN) {
		document.writeln('<LAYER NAME="' + sName + '" LEFT=' + sLeft + ' TOP=' + sTop + 
		' WIDTH=' + sWdh + ' HEIGHT=' + sHgt + ' VISIBILITY="' + sVis + '"' + 
		' z-Index=' + (++zIdx) + '>' + copy + '</LAYER>');
		}
	else {
		document.writeln('<DIV ID="' + sName + '" STYLE="position:absolute; overflow:none; left:' + 
			sLeft + 'px; top:' + sTop + 'px; width:' + sWdh + 'px; height:' + sHgt + 'px;' + 
			' visibility:' + sVis + '; z-Index=' + (++zIdx) + '">' + 
			copy + '</DIV>'
			);
		}
	}

// Define a function to hide layers
function hideSlide(name) {
	refSlide(name).visibility = hideName;
	}

// Define a function to reveal layers
function showSlide(name) {
	refSlide(name).visibility = showName;
	}

// Define a central function to reference layers
function refSlide(name) {
	if (NN) { return document.layers[name]; }
	else { return eval('document.all.' + name + '.style'); }
	}

// Define a function to create SELECT lists on the fly
function genSelect(name, count, start, select) {
	var optStr = "";
	for (var h = start; h <= count; h++) {
		optStr += "<OPTION VALUE=" + h + (h == select ? " SELECTED" : "") + ">" + h;
		}
	 return ("<SELECT NAME=" + name + ">" + optStr + "</SELECT>");
	}