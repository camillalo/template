#!/usr/bin/perl

### Require cgi-lib.pl ###

require 'cgi-lib.pl';

### Read from the standard input ###

&ReadParse(*in);
$msg = $in{'EntireMessage'};
$fileID = $in{'UniqueID'};
$recip = $in{'Recipient'};
$baseURL = $in{'BaseURL'};


### Create the greeting in a ###
### file with a unique name  ###
open(FILE, ">greet$fileID.html") || die "No can do: $!";
select(FILE);
print <<GREETING;
<HTML>
<HEAD>
	<TITLE>Your Personal Cyber Greeting</TITLE>
</HEAD>
<BODY>
	$msg
</BODY>
</HTML>
GREETING

close(FILE);
select(STDOUT);


### Generate a response for the sender ###
print "Content-type: text/html\n\n";

print <<RESPONSE;

<HTML>
<HEAD>
	<TITLE>Cyber Greeting Response</TITLE>
</HEAD>
<BODY>
<TABLE WIDTH="500">
	<TR>
		<TD>
		<H2>Congratulations!</H2>
		You have successsfully created a Cyber Greeting for <B>$recip</B>
		All you have to do is send him or her an e-mail to announce the 
		greeting. Just push the button below, and the e-mail will be on the 
		way.
		<CENTER>
		<FORM NAME="SendEmail" ENCTYPE="text/plain" ACTION="mailto:$recip?Subject=You Have A Cyber Greeting!">
		<INPUT TYPE=HIDDEN NAME="Message" VALUE="You have a Cyber Greeting. You can pick it up at $baseURL/greet$fileID.html">
		<INPUT TYPE=SUBMIT VALUE="Send CyberGreeting">
		</FORM>
		</CENTER>
		<BR>
		You might experience a delay while your e-mail software 
		contacts your mail server.
		<BR><BR>
		<A HREF="$baseURL/index.html">Return To Cyber Greeting</A>
		</TD>
	</TR>
</TABLE>
</BODY>
</HTML>

RESPONSE

exit 0;

## End of File