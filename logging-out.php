<?php

include_once 'certs.inc.php';

$ua = determineWebBrowser($_SERVER['HTTP_USER_AGENT']);

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1" />
<title>How to 'log out'</title>
<link rel="stylesheet" type="text/css" href="certs.css" />

<script type="text/javascript">

function logOut()
{
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.timeout = 2000; // 2 seconds

    xmlHttp.onreadystatechange = function ()
    {
        if (xmlHttp.readyState == 4)
        {
            console.log("status: "+xmlHttp.status);
            console.log("response: '"+xmlHttp.responseText+"'");
        }
    };
    xmlHttp.open("GET", "/certs/logout/", true);
    xmlHttp.send();
}
</script>

</head>
<body>
<div id="main">

<h1>Logging out</h1>

<p>"Logging out" of a certificate (i.e. getting the browser to ask you which certificate to use after you have chosen one already) can be a bit tricky with most current web browsers.  Normally <strong>restarting your browser</strong> is the simplest way to do this.  You can also achieve the same effect with different tricks depending on what browser you use:</p>

<h2>Firefox <?php if ($ua['name'] == 'firefox') echo ' &larr; your browser'; ?></h2>

<!--
<p><strong>Easiest method</strong> : <a href="javascript:window.crypto.logout(); window.opener.location.reload(false); window.close();">Click here to logout</a> (uses <a href="https://developer.mozilla.org/en/docs/JavaScript_crypto" target="_blank">the window.crypto Mozilla extension</a>.)</p>
-->

<p><strong>Method</strong> : click 'History' menu (press ALT key to display menu if it's not there) &rarr; 'Clear Recent History' &rarr; tick only 'Active Logins' and click 'Clear Now' button. Then <a href="javascript: window.opener.location.reload(false); window.close();">close this page and reload the previous page</a>.</p>

<h2>Chrome / Safari <?php if ($ua['name'] == 'chrome' || $ua['name'] == 'safari') echo ' &larr; your browser'; ?></h2>
<p>Try clicking <a href="#logout" onclick="logOut();">logout in background</a> multiple times until you are prompted to choose a certificate. When prompted for a certificate, click "Cancel" then <a href="javascript: window.opener.location.reload(false); window.close();">close this page and reload the previous page</a>.</p>

<!-- <p><a href="/certs/logout/">Logout in foreground (regular link)</a></p> -->

<h2>In Internet Explorer<?php if ($ua['name'] == 'ie') echo ' &larr; your browser'; ?></h2>
<p>Try <a href="javascript:document.execCommand('ClearAuthenticationCache'); window.opener.location.reload(false); window.close()">document.execCommand('ClearAuthenticationCache')</a></p>

</div>
</body>
</html>
