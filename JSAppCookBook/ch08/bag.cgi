#!/usr/bin/perl

# bag.pl

########## Print the content header
print "Content-type: text/html\n\n";


########## Require cgi-lib.pl
require "cgi-lib.pl";


########## Read input and assign variables 
&ReadParse(*input);


########## Generate a unique file based on random number and 
########## customer last name
srand($$ ^ time);
$filename = $input{'lname'} . int(rand(999));

if (-e "$filename.txt") { 
	print "The order for $filename has already been placed.";
	exit 0;
	}

########## Open file and write the order
open (FILE,">$filename.txt");
select(FILE);
printInfo();
close(FILE);

######### Now write the confirmation back to the user
select(STDOUT);
printInfo();

exit 0;

sub printInfo() {
$clock = localtime();
########## Print customer information

print <<CUSTOMER_INFO;
<PRE><FONT FACE=Tahoma SIZE=3>
<H2>Shopping Bag Order Confirmation Receipt</H2>

<B>$clock</B>
<B>Reference Code: $filename</B>


--------------------
Customer Information

Customer First Name:	$input{'fname'}
Customer Last Name:	$input{'lname'}
Company Name:		   $input{'cname'}
Street Address 1:	   $input{'saddress1'}
Street Address 2:	   $input{'saddress2'}
City:			         $input{'city'}
State/Province:		$input{'stpro'}
Country:		         $input{'country'}
Zip/Mail Code:		   $input{'zip'}

CUSTOMER_INFO

########## Print payment information

print <<PAYMENT_INFO;

Payment Information

Credit Card Type: 	$input{'ctype'}
Credit Card Number:  $input{'cnumb'}
Expiration Date:	   $input{'edate'}

PAYMENT_INFO

########## Print product information

print "Product Information\n\n";

$idx = 0;

while ($input{'prod' . $idx}) {
	@getProdInfo = split("-", $input{'prod' . $idx}); 
	print "Product PLU:\t\t$getProdInfo[0]\n";
	print "\tQuantity:\t$getProdInfo[1]\n\n";
	$idx++;
	}

########## Print total information

print <<TOTAL_INFO;

Total Information (\$US)

Subtotal:		  $input{'subtotal'}	
Tax Total:		 $input{'taxtotal'} 
Ship Total:		 $input{'shiptotal'}
Bag Total: 		 $input{'bagtotal'}

--------------------
</FONT></PRE>
TOTAL_INFO
}