<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1" />
<title>Certs 'R' Us - SSL client certificates demo</title>
<link rel="stylesheet" type="text/css" href="certs.css" />
<body>
<div id="main">

<h1>Certs 'R' Us</h1>

<p>This site demonstrates the process of logging in to a website using an SSL client certificate (and the corresponding private key.)</p>

<p>If you haven't already done so, you should probably read the accompanying blog post:<br />
<a href="http://catn.com/2014/07/11/passwords-are-past-it-are-certificates-the-key-to-better-online-security/">"Passwords are past it - are certificates the key to better online security?"</a>

<h2>Take the demo</h2>

<ol>
    <li><em>OPTIONAL:</em>Get a 'proper' SSL certificate from a certificate authority (CA)</li>
    <li><a href="signup/">Sign up with a certificate</a> (a demo one will be generated for you if necessary, or you can present your own)</a></li>
    <li><a href="login/">Log in using that certificate</a></li>
</ol>

<p>Want to login with a different certificate?  <a href="logging-out.html" onClick="MyWindow=window.open('logging-out.html','MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=550,height=700'); return false;">Learn about logging out</a></p>

<h2>Certificate Authorities</h2>

<?php
/*
$ca_bundle = file('ca-bundle.crt.txt', FILE_IGNORE_NEW_LINES);
if ($ca_bundle === false)
    throw new RuntmeException('Failed to open CA bundle');

$ca_subjects = preg_grep('/Subject:/', $ca_bundle);
$ca_subjects = array_map('trim', $ca_subjects);

//TODO: we should extract the Subject data fields and display them nicely

echo '<ul>'.PHP_EOL;
foreach($ca_subjects as $ca_subject)
{
    echo '<li>'.htmlspecialchars($ca_subject, ENT_QUOTES, 'UTF-8').'</li>';
}
echo '</ul>'.PHP_EOL;
*/
?>
<p>View a <a href="ca-bundle.crt.txt">raw certificate bundle of Certificate Authorities that this demo site recognises</a></p>

<hr />

<p><a href=".">Certs 'R' Us</a>, a demo site by <a href="http://www.bennish.net">Ben Kennish</a>, with <a href="http://catn.com">CatN</a>, 2014.</p>

</div>
</body>
</html>
