// navbar.js

var navURLs  = new Array('astronomy.html', 'science.html', 'sports.html', 'music.htm', 'people.htm');
var linkText = new Array('Astronomy', 'Other Sciences', 'Sports', 'Musicians\' Corner', 'Cool People');

function navbar() {
	var navStr= '';
	for (var i = 0; i < navURLs.length; i++) {
		//	if (location.href != navURLs[i]) {		    	   Uncomment this line and comment 
		if (location.href.indexOf(navURLs[i]) == -1) {	// this one when not browsing locally
			navStr += ' <B>[</B><A HREF="' + navURLs[i] + '">' + linkText[i] + '</A><B>]</B> '; 
			}
		}
	document.writeln('<BR><BR>' + navStr);
	}