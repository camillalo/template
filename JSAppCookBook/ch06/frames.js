// frames.js

function keepIn(parentHREF) {	
	if (top.location.href == self.location.href) {
		alert('[Wheez]. . .  [Gasp]. . . Must. . . load. . . original. . . frameset.');
		top.location.href = parentHREF;
		}
	}

function keepOut() {
	if (top.location.href != self.location.href) {
		alert('This document bows to no frameset.');
		top.location.href = self.location.href;
		}
	}
