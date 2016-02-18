// strings.js

function wordCount(str) {
	var wordArray = new Array();
	str = prepStr(str);
	var tempArray = str.split(' ').sort();
	var count = 1;

	// Iterate through all the words
	for (var i = 0; i < tempArray.length; i++) {

		// If an array element with the same name as
		// the current word exists, increment its value by 1
		if (wordArray[tempArray[i]]) {
			wordArray[tempArray[i]]++;
			}

		// Otherwise, assign the array element to the 
		/// name of the word, and give it a value of 1
		else { wordArray[tempArray[i]] = 1; }
		}
		var arrStr = '';

		// Create table rows that display the word and the frequency
		for (word in wordArray) {
			if (word != "") {
				arrStr += '<TR><TD>' + word + '</TD><TD>' + wordArray[word] + '</TD></TR>';
				count++;
				}
			}
		return '<TABLE BORDER=0 CELLPADDING=10><TR><TD WIDTH=300 VALIGN=TOP ROWSPAN=' + 
			count + '><B>Original Formatted Text</B><BR><I>' + str + 
			'</I><TD><B>Word</B><TD><B>Freqency</B></TR>' + arrStr + '</TABLE>';
	}

// Define a function to format strings for easier manipulation
function prepStr(str) {
	str = str.toLowerCase();
	str = str.replace(/['"-]/g, "");
	str = str.replace(/\W/g, " ");
	str = str.replace(/\s+/g, " ");
	return str;
	}

function camelCaps(str, theCase) {
	var tempArray = str.split(' ');

	// Make the first character of each word upper- or lowercase
	// depending on the value of theCase
	for (var i = 0; i < tempArray.length; i++) {
		if (theCase) {
			tempArray[i] = tempArray[i].charAt(0).toUpperCase() + tempArray[i].substring(1);
			}
		else {
			tempArray[i] = tempArray[i].charAt(0).toLowerCase() + tempArray[i].substring(1);
			}
		}
	return tempArray.join(' ');
	}

var order = true;

function reorder(str) {
	str = prepStr(str);
	str = str.replace(/\d/g, "");
	order = !order;

	// Use the sort() method for traditional sorts
	// Use the reverse() method in conjunction for reverse sorts()
	if(!order) { str = str.split(' ').sort().join(' '); }
	else { str = str.split(' ').sort().reverse().join(' '); }
	return str.replace(/^\s+/, "");
	}