// Define global browser-detection variables
var NN 		 = (document.layers ? true : false);
var hideName = (NN ? 'hide' : 'hidden');
var showName = (NN ? 'show' : 'visible');
var zIdx 	 = -1;
var helpWdh  = 200;
var helpHgt  = 200;
var x, y, totalWidth, totalHeight;
var wdh = hgt = 300;

/* Bug Report: 
   Some versions of MSIE 4 have problems with the opening 
   window calling the remote window and vice versa (the remote 
   window referencing the opening window). Doing so often 
   throws the MSIE "Class doesn't support Automation" error. 
   
   As a workaround, variables wdh and hgt have been declared 
   here to remove any calls to those of the same name in the 
   opening window, which contains index.html. 
   
   top.wdh and top.hgt in function helpDisplay() below were replaced 
   with wdh and hgt, respectively.
   
   Changes have also been made to inContext() in nav.html to accommodate.
*/

// Set browser-determined global variables
function genLayer(sName, sLeft, sTop, sWdh, sHgt, sVis, copy) {
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

// Enable MouseMove event listeners 
// for both browsers
function motionListener() {
	if (NN) {
		window.captureEvents(Event.MOUSEMOVE);
		window.onmousemove = grabXY;
		}
	else { 
		document.onmousemove = grabXY;
		}
	}

// Track the x/y coordinates  
// of mouse pointer arrow
function grabXY(ev) {
	if(NN) {
		x = ev.pageX;
		y = ev.pageY;
		}
	else {
		x = event.x;
		y = event.y;
		}
	}

// Show or hide the help boxes, making sure
// they are not displayed off the screen 
function helpDisplay(name, action) {
	if(action) {
		totalWidth = x + helpWdh;
		totalHeight = y + helpHgt; 
		x = (totalWidth > wdh ? x - (totalWidth - wdh + 75) : x);
		y = (totalHeight > hgt ? y - (totalHeight - hgt) : y);
		refSlide(name).left = x - 10;
		refSlide(name).top = y + 8;
		showSlide(name);
		}
	else { hideSlide(name); }
	}

motionListener();
self.focus();