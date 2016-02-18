// images.js

// Set image variables
var imgPath 	= 'images/';
var arrayHandles = new Array('out', 'over');

// Dynamically create image arrays
for (var i = 0; i < arrayHandles.length; i++) {
	eval('var ' + arrayHandles[i] + ' = new Array()');
	}

// Preload the images
for (var i = 0; i < imgNames.length; i++) {
	imagePreLoad(imgNames[i], i);
	}


// Define a function to preload the images
function imagePreLoad(imgName, idx) {
	for(var j = 0; j < arrayHandles.length; j++) {
		eval(arrayHandles[j] + "[" + idx + "] = new Image()");
		eval(arrayHandles[j] + "[" + idx + "].src = '" + imgPath + imgName + arrayHandles[j] + ".gif'");
		}
	}

// Perform the image rollovers
function imageSwap(imagePrefix, imageIndex, arrayIdx) {
	document[imagePrefix].src = eval(arrayHandles[arrayIdx] + "[" + imageIndex + "].src");
	}

// This function displays the text passed in the browser status bar
function display(stuff) { window.status = stuff; }