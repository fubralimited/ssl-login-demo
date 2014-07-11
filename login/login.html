<?php

include_once '../certs.inc.php';

// if SSLVerifyClient isn't set to "require", this might be true...
if ($_SERVER['SSL_CLIENT_VERIFY'] !== 'SUCCESS')
{
    header('HTTP/1.1 401 Unauthorized');
    die("<h1>You didn't provide a valid certificate!</h1>");
}

db_connect();

$qry = mysqli_query($mysqli, 'SELECT * FROM user WHERE email="'.mes($_SERVER['SSL_CLIENT_S_DN_CN']).'"');
if (!$qry) throw new RuntimeException(mysqli_error($mysqli));

$user = mysqli_fetch_assoc($qry);

if (!$user)
{
    // valid certificate but no account in database... redirect to signup.html
    header('Location: ../signup/');
    exit;
}


?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1" />
<title>You are logged in!</title>
<link rel="stylesheet" type="text/css" href="../certs.css" />

<script type="text/javascript">

function toggleTechDetails()
{
    var d = document.getElementById('techDetails');

    if (d.style.display == 'block')
    {
        d.style.display = 'none';
    }
    else if (d.style.display == 'none')
    {
        d.style.display = 'block';
    }
    else
    {
        alert(d);
    }

}

</script>

</head>
<body>
<div id="main">
<?php

echo '<h1>Logged in successfully</h1>';


if (empty($_GET['triedToSignUp']))
{
    echo '<p>Hello "<b>'.htmlspecialchars($_SERVER['SSL_CLIENT_S_DN_CN']).'</b>". You just used your SSL client certificate to log in!</p>'.PHP_EOL;
}
else
{
    echo '<p>Hello "<b>'.htmlspecialchars($_SERVER['SSL_CLIENT_S_DN_CN']).'</b>". You tried to sign up using your SSL client certificate but you\'ve already registered with it so I\'ve logged you in!</p>'.PHP_EOL;
}


echo '<p>I found you in our database.  Here is the text you entered when you signed up...</p>'.PHP_EOL;
echo '<p style="border: 1px dashed blue; display: inline-block; font-size: large; margin: 2px;">'.htmlspecialchars($user['details'], ENT_QUOTES, 'UTF-8').'&nbsp;</pre>'.PHP_EOL;

$ua = determineWebBrowser($_SERVER['HTTP_USER_AGENT']);

?>

<h2>Other info</h2>

<p>Want to login with a different certificate?  <a href="../logging-out.html" onClick="MyWindow=window.open('../logging-out.html','MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=550,height=700'); return false;">Learn about logging out</a></p>

<p><a href="../">Go back to the main menu</a></p>

<!-- =================== -->

<h2>Technical details from your certificate</h2>

<p>[<a href="#" onclick="toggleTechDetails();">Show/Hide technical details</a>]</p>

<div id="techDetails" style="display:none;">

<p>Here is all the information that this webserver has on the SSL certificate that you sent.  If you hover over the bold names, you can get a better description</p>

<ol>
<?php

displayClientCert();

?>
</ol>

</div>

</div>
</body>
</html>
