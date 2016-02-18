// arrays.js

// Add up the values of all the elements, then 
// divide that sum by the number of elements
function avg(arrObj) {
	var sum = 0;
	for (var i = 0; i < arrObj.length; i++) {
		sum += arrObj[i];
		}
	return (sum / i);
	}

// Iterate through the elements, 
// and return the highest
function high(arrObj) {
	var highest = arrObj[0];
	for (var i = 1; i < arrObj.length; i++) {
		highest = (arrObj[i] > highest ? arrObj[i] : highest);
		}
	return (highest);	
	}

// Iterate through the elements, 
// and return the lowest
function low(arrObj) {
	var lowest = arrObj[0];
	for (var i = 1; i < arrObj.length; i++) {
		lowest = (arrObj[i] < lowest ? arrObj[i] : lowest);
		}
	return (lowest);	
	}

// Iterate through the elements, amd perform
// a string replacement on each
function jsGrep(arrObj, regexp, subStr) {
	for (var i = 0; i < arrObj.length; i++) {
		arrObj[i] = arrObj[i].replace(regexp, subStr);
		}
	return arrObj;
	}

// This function returns a copy of an array object
// with the last element removed
function truncate(arrObj) {
	arrObj.length = arrObj.length - 1;
	return arrObj;
	}


// This function returns a copy of an array
// object with the first element removed
function shrink(arrObj) {
	var tempArray = new Array(); 
	for(var p = 1; p < arrObj.length; p++) {
		tempArray[p - 1] = arrObj[p];
		}
	return tempArray;
	}


// This function returns a copy of an array 
// object with the elements of another array 
// object added beginning from a specified index.
function integrate(arrObj, elemArray, startIndex) {
	startIndex = (parseInt(Math.abs(startIndex)) < arrObj.length ? parseInt(Math.abs(startIndex)) : arrObj.length);
	var tempArray = new Array(); 
	for( var p = 0; p < startIndex; p++) {
		tempArray[p] = arrObj[p];
		}
	for( var q = startIndex; q < startIndex + elemArray.length; q++) {
		tempArray[q] = elemArray[q - startIndex];
		}
	for( var r = startIndex + elemArray.length; r < (arrObj.length + elemArray.length); r++) {
		tempArray[r] = arrObj[r - elemArray.length];
		}
	return tempArray;
	}

// This function returns an array reordered according to a multiple 
// passed in by the user. The defualt is 1.
function reorganize(formObj, stepUp) {
	stepUp = (Math.abs(parseInt(stepUp)) > 0 ? Math.abs(parseInt(stepUp)) : 1);
	var nextRound = 1;
	var idx = 0;
	var tempArray = new Array();

	for (var i = 0; i < formObj.length; i++) {
		tempArray[i] = formObj[idx];
		if (idx + stepUp >= formObj.length) {
			idx = nextRound;
			nextRound++;
			}
		else {
			idx += stepUp;
			}
		}
	return tempArray;
	}