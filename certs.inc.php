<?php

include_once 'config.inc.php';
// config.inc.php needs to define the following for db_connect()
//
// DB_SERVER
// DB_USER
// DB_PASSWORD
// DB_DATABASE


function db_connect()
{
    global $mysqli;

    @$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

    if ($mysqli->connect_errno)
    {
        throw new RuntimeException('Failed to connect to MySQL: ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
    }

    if (!mysqli_set_charset($mysqli, 'utf8'))
    {
        throw new RuntimeException('Failed to set charset');
    }
}


function mes($str)
{
    global $mysqli;

    if (empty($mysqli)) throw new RuntimeException('No DB connection for mes() to use');

    return mysqli_real_escape_string($mysqli, $str);
}


function hsc($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}



function displayClientCert()
{

    $vars_to_show = array(
                            'SSL_CLIENT_M_VERSION' => 'The version of the client certificate',
                            'SSL_CLIENT_M_SERIAL'  => 'The serial of the client certificate',
                            'SSL_CLIENT_S_DN'      => 'Subject DN in client\'s certificate',
                            'SSL_CLIENT_I_DN'      => 'Issuer DN of client\'s certificate',
                            'SSL_CLIENT_V_START'   => 'Validity of client\'s certificate (start time)',
                            'SSL_CLIENT_V_END'     => 'Validity of client\'s certificate (end time)',
                            'SSL_CLIENT_V_REMAIN'  => 'Number of days until client\'s certificate expires',
                            'SSL_CLIENT_A_SIG'     => 'Algorithm used for the signature of client\'s certificate',
                            'SSL_CLIENT_A_KEY'     => 'Algorithm used for the public key of client\'s certificate',
                            'SSL_CLIENT_CERT'      => 'PEM-encoded client certificate',
                            'SSL_CLIENT_VERIFY'    => 'NONE, SUCCESS, GENEROUS or FAILED:reason',

                            'SSL_CLIENT_S_DN_Email' => 'Subject DN in client\'s certificate (Email)',
                            'SSL_CLIENT_S_DN_CN'    => 'Subject DN in client\'s certificate (CN)',
                        );
    /*
    see: http://httpd.apache.org/docs/2.2/mod/mod_ssl.html

     can also use ...
     SSL_CLIENT_S_DN_x509
     SSL_CLIENT_I_DN_x509
    where 'x509' specifies a component of an X.509 DN; one of C,ST,L,O,OU,CN,T,I,G,S,D,UID,Email.
    In Apache 2.1 and later, x509 may also include a numeric _n suffix.
    If the DN in question contains multiple attributes of the same name, this suffix is used as an index to select a particular attribute.
    For example, where the server certificate subject DN included two OU fields, SSL_SERVER_S_DN_OU_0 and SSL_SERVER_S_DN_OU_1 could be used to reference each
    */

    foreach ($vars_to_show as $var=>$desc)
    {
        echo '<li style="font-family: monospace;">';
        echo '<b><abbr title="'.htmlspecialchars($desc).'">'.$var.'</abbr></b> = ';
        if (isset($_SERVER[$var]))
            echo nl2br(print_r($_SERVER[$var], true));
        else
            echo '-undefined-';
        echo '</li>';
    }
}


function determineWebBrowser($userAgentString)
{

    $pregs = array();

    // regular expressions that identify the browser and extract the version number
    // first match wins
    $pregs['seamonkey'][] = 'Seamonkey/([^ ]+)';
    $pregs['firefox'][] = 'Firefox/([^ ]+)';

    $pregs['chromium'][] = 'Chromium/([^ ]+)';
    $pregs['chrome'][] = 'Chrome/([^ ]+)';
    $pregs['safari'][] = 'Safari/([^ ]+)';

    $pregs['opera'][] = '^OPR/([^ ]+)';
    $pregs['opera'][] = '^Opera/([^ ]+)';

    $pregs['ie'][] = ';MSIE ([^ ]+)';

    foreach ($pregs as $name=>$patterns)
    {
        foreach ($patterns as $pattern)
        {
            $matches = array();
            if (preg_match('~'.$pattern.'~', $userAgentString, $matches))
            {
                $ret['name'] = $name;
                $ret['version'] = $matches[1];
                return $ret;
            }

        }

    }
    return null;
}
