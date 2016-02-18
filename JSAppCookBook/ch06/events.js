// events.js

var keys = '';
var change = true;
var x1, x2, y1, y2;

// This function enables and disables the 
// onmousemove event handler of both 
// Navigator and MSIE
function enableEffects(ev) {
	if(change) { 
		if(document.layers) {
			x1 = ev.screenX;
			y1 = ev.screenY;
			document.captureEvents(Event.MOUSEMOVE);	
			}
		else { 
			x1 = event.screenX;
			y1 = event.screenY;
			}
			document.onmousemove = showXY;
		}
	else { 
		if (document.layers) { 
			x2 = ev.screenX;
			y2 = ev.screenY;			
			document.releaseEvents(Event.MOUSEMOVE); 
			}
		else { 
			x2 = event.screenX;
			y2 = event.screenY;
			document.onmousemove = null; 
			}
		window.status = 'Start: (' + x1 + ',' + y1 + 
			')  End: (' + x2 + ',' + y2 + ')  Distance: ' + 
			(Math.abs((x2 - x1) + (y2 - y1))) + ' pixels';
		}
	change = !change;
	}

// This function alerts a string of keys that the user has typed
function showKeys() {
	if (keys != '') { 
		alert('You have typed: ' + keys);
		window.status = keys = ''; 
		}
	else { alert('You have to type some keys first.'); }
	}

// This function displays the keys pressed in the status bar
function showXY(ev) {
	if (document.all) { ev = event; } 
	window.status = 'X: ' + ev.screenX + ' Y: ' + ev.screenY;
	}

// This function captures the keys pressed one at a time
function keepKeys(ev) {
	if (document.layers) { 
		keys += String.fromCharCode(ev.which); 
		window.status = 'Key pressed: ' + String.fromCharCode(ev.which);
		} 
	else { 
		keys += String.fromCharCode(event.keyCode); 
		window.status = 'Key pressed: ' + String.fromCharCode(event.keyCode);		
		}
	}

