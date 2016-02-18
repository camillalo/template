// number.js

function twoPlaces(amount) {
	// returns the amount in the .99 format
	return (amount == Math.floor(amount)) ? amount + '.00' : (  (amount*10 == Math.floor(amount*10)) ? amount + '0' : amount);
	}

function round(number,X) {
	// rounds number to X decimal places, defaults to 2
	X = (!X ? 2 : X);
	return Math.round(number*Math.pow(10,X))/Math.pow(10,X);
	}

function totals(num) {
	return twoPlaces(Math.floor((num - 0) * 100) / 100);
	}
