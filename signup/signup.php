<?php

include_once '../certs.inc.php';

// references
// http://stackoverflow.com/questions/9197484/generating-client-side-certificates-in-browser-and-signing-on-server
// http://orcaman.blogspot.co.uk/2013/12/client-certificates-and-html5-kegen-tag.html
// https://openweb.or.kr/html5/download.txt


// this file should contain OpenSSL config
define('APACHE_SSL_CONFIG', '/var/www/etc/openssl.cnf');


$sslClientVerified = false;

if (!empty($_SERVER['SSL_CLIENT_VERIFY']) && $_SERVER['SSL_CLIENT_VERIFY']==='SUCCESS')
{
    $sslClientVerified = true;

    db_connect();

    $qry = mysqli_query($mysqli, 'SELECT * FROM user WHERE email="'.mes($_SERVER['SSL_CLIENT_S_DN_CN']).'"');
    if (!$qry) throw new RuntimeException(mysqli_error($mysqli));

    $user = mysqli_fetch_assoc($qry);

    if ($user)
    {
        header('Location: ../login/?triedToSignUp=1');
        exit;
    }

}



if (!empty($_POST['action']))
{

    db_connect();

    // NB: we should try to do most verification in JavaScript before submitting the form
    // (e.g. client side and/or using AJAX) because the browser will generate a private key each type the Submit button is pressed
    // (I don't know what happens to the private keys without a certificate tbh!)

    // verify form values

    if ($_POST['action'] == 'register')
    {
        $email = filter_input(INPUT_SERVER, 'SSL_CLIENT_S_DN_Email', FILTER_VALIDATE_EMAIL)
            or die('The email within the SSL client certificate you provided looks dodgy to me');
    }
    else
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)
            or die('The email address you typed in looks dodgy to me');
    }

    $qry = mysqli_query($mysqli, 'SELECT COUNT(*) FROM user WHERE email="'.mes($email).'"');
    if (!$qry) throw new RuntimeException(mysqli_error($mysqli));
    list($count) = mysqli_fetch_row($qry);
    if ($count)
    {
        die('I\'ve already got the email address '.hsc($email).' in the database');
    }

    $details = substr(filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW), 0, 5000);

    switch($_POST['action'])
    {
        case 'certifyAndRegister':

            if (empty($_POST['pubkey']))
                throw new RuntimeException("Your web browser didn't send me a public key");

            //TODO: validate that $_POST['pubkey'] represents a valid RSA public key?

            // $keyreq needs to include their public key and the details that normally go inside a CSR

            $keyreq_array['SPKAC'] = str_replace(str_split(" \t\n\r\0\x0B"), '', $_POST['pubkey']);

            // can set other keys for keyreq_array here (e.g. organizationName)
            $keyreq_array['CN'] = $email;
            $keyreq_array['emailAddress'] = $email;

            $keyreq = '';
            foreach ($keyreq_array as $key=>$val)
            {
                $keyreq .= ($keyreq ? "\n" : '').$key.'='.$val;
            }

            $tmpSPKACfname = '/tmp/SPK'.md5(time().rand());
            $tmpCERTfname = '/tmp/CRT'.md5(time().rand());

            if (file_put_contents($tmpSPKACfname, $keyreq, LOCK_EX) === false)
            {
                throw new RuntimeException('Failed to create SPKAC file '.$tmpSPKACfname);
            }

            $cmd = 'openssl ca -config '.APACHE_SSL_CONFIG.' -verbose -batch -notext -spkac '.$tmpSPKACfname.' -out '.$tmpCERTfname.' 2>&1';
            $ret = 0;
            $output = '';

            exec($cmd, $output, $ret);

            if ($ret !== 0)
            {
                throw new RuntimeException('openssl call returned errcode: '.$ret.' - output was: '.print_r($output, true));
            }

            header('Last-Modified: '.date('r+b'));
            header('Accept-Ranges: bytes');
            header('Content-Length: '.filesize($tmpCERTfname));
            header('Content-Type: application/x-x509-user-cert');

            readfile($tmpCERTfname);
            unlink ($tmpCERTfname);

            $qry = mysqli_query($mysqli, 'INSERT INTO user SET email="'.mes($email).'", details="'.mes($details).'", signup=NOW()');
            if (!$qry) throw new RuntimeException(mysqli_error($mysqli));

            exit;
            break;

        case 'register':

            if (!$sslClientVerified)
            {
                die('You tried to register using an existing client certificate but then didn\'t present one');
            }

            $qry = mysqli_query($mysqli, 'INSERT INTO user SET email="'.mes($email).'", details="'.mes($details).'", signup=NOW()');
            if (!$qry) throw new RuntimeException(mysqli_error($mysqli));

            header('Location: /certs/login/');
            exit;

            break;
    }

}

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1" />
<title>Sign up</title>
<link rel="stylesheet" type="text/css" href="../certs.css" />
</head>
<body>
<div id="main">

<h1>Sign up</h1>

<?php

if ($sslClientVerified)
{
    echo '<p>Your browser supplied a valid SSL client certificate so now you just need to give a few more details and we will create your account.</p>'.PHP_EOL;
}
else
{
    echo '<p>Please enter an email address and some info about yourself (that this web server will store) and click to create an SSL client certificate that you can use to log in.</p>'.PHP_EOL;
}

if (!empty($_POST))
{
    echo '<h2>POST data</h2>'.PHP_EOL;
    echo '<pre>'.PHP_EOL;
    var_dump($_POST);
    echo '</pre>'.PHP_EOL;
}


echo '<form action="?" method="POST">'.PHP_EOL;

if ($sslClientVerified)
{
    //TODO: use SSL_CLIENT_S_DN_CN if SSL_CLIENT_S_DN_Email isn't valid?
    echo '<p>Your email: <b>'.hsc($_SERVER['SSL_CLIENT_S_DN_Email']).'</b> (defined in your certificate)</p>'.PHP_EOL;

    echo '<ul>'.PHP_EOL;
    displayClientCert();
    echo '</ul>'.PHP_EOL;
}
else
{
    echo '<p><label for="email">Your email: </label><input type="email" name="email" id="email" placeholder="you@example.com" required /></p>'.PHP_EOL;
}
?>
<p><label for="details">Tell us a little bit about yourself ....</label></p>
<p><textarea name="details" id="details" style="min-width:270px; min-height: 100px;" placeholder="Type a brief description of yourself or else just anything you like. This is just to prove to you that you are logged in and have retrieved your 'profile' data" required maxlength="5000"></textarea></p>

<?php

if (!$sslClientVerified)
{
    ?><p>If prompted for a key strength, simply choose the highest grade / key strength provided by your browser:</p>
<p>
    <label for="pubkey">Key strength:</label>
    <keygen keytype="RSA" name="pubkey" challenge="anneka" id="pubkey" />
</p>

    <?php
    $ua = determineWebBrowser($_SERVER['HTTP_USER_AGENT']);

    if ($ua == 'ie')
        echo '<p><b>This page does not currently work with Internet Explorer as it does NOT support the HTML5 &lt;keygen&gt; tag.  Sorry!</b></p>'.PHP_EOL;
    else
        echo '<p><input type="hidden" name="action" value="certifyAndRegister" /><input type="submit" value="Generate my Certificate and create my account" /></p>'.PHP_EOL;
}
else
{
    echo '<p><input type="hidden" name="action" value="register" /><input type="submit" value="Create my account" /></p>'.PHP_EOL;
}

?>
</form>

<?php
if (!$sslClientVerified)
{
    ?>
<p>A number of things happen when you click the button...</p>

<ol>
    <li>Your web browser generates a new private key and public key</li>
    <li>It then sends the public key to the server along with the other form details</li>
    <li>The server takes the public key and the other details and creates a certificate that is signed using the server's own private key</li>
    <li>The server returns this certificate to the browser which stores it alongside the private key it created previously</li>
</ol>

<p>Once you have clicked 'Generate' and installed a certificate, you can <a href="../login/">log in here</a></p>
    <?php
}

?>

<hr />
<p><a href="../">Certs 'R' Us</a>, a demo site by <a href="http://www.bennish.net">Ben Kennish</a>, with <a href="http://catn.com">CatN</a>, 2014.</p>

</div>
</body>
</html>
