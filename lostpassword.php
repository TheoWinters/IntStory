<?php

require( dirname(__FILE__) . '\tools.php' );
OpenDatabase();
$Sesson = LoadCurrentSesson();
if($Sesson != null)
{
    // If they are current logged in and going to the lost password page, just log them out.
    LogOutUser();
}

$refPath = "";
$refLink = "";

if(isset($_GET['ref']))
{
    $refPath = $_GET['ref'];
    $refLink = "?ref=".$refPath;
}
else if(isset($_SERVER['HTTP_REFERER']))
{
    $refPath = $_SERVER['HTTP_REFERER'];
    $refLink = "?ref=".$refPath;
}
else
{
    $refPath = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
    $refLink = "?ref=".$refPath;
}

$Message = "";
$State = 0;
$UserName = "";
$EMail = "";


// actions
// * None: request a lost password e-mail
// * request: send the lost password e-mail, offer to resend
// * reclaim: All them to reset the password

if(isset($_POST['action']) && $_POST['action'] == 'request')
{
    $UserName = mysql_fix_string($_POST['UserName']);
    $EMail = mysql_fix_string($_POST['EMail']);
    
    if($UserName == "")
    {
        $Message = "Username Missing";
    }
    else if ($EMail == "")
    {
        $Message = "E-Mail Address Missing";
    }
    else
    {
        $UserInfo = FindAccountByNameandEmail($UserName, $EMail);
        if(mysql_num_rows($UserInfo) == 0)
        {
            $Message = "Unable to find user ".$UserName.", or the e-mail address dosn't match.";
        }
        else
        {
            $UserData = mysql_fetch_row($UserInfo);
            $ResetCode = EmailResetCode($UserData[1]);
            $ResetData = AddReset($UserData[0], $ResetCode);
            $ResetID = mysql_insert_id();
            
            
            $EmailMessage = "Hello ".$UserData[1].".\r\n\r\nYou are receiving this notification because you have (or someone pretending to be you has) requested a password recovery for your account on 'The Changing Mirror' interactive story site.\r\n\r\nIf you did not request this notification then please ignore it, if you keep receiving it please contact the board administrator.\r\n\r\nTo change your password please visit this site:\r\n\r\nhttp://".$_SERVER['HTTP_HOST'].parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)."?keycode=".$ResetCode."&ID=".$ResetID."\r\n\r\nIf successful you will be able to login with the new password.\r\n\r\n";
            
            
            if(mail($UserData[3], "Account Reactivation", $EmailMessage))
            {
                $Message = "Sending e-mail...";
                $State = 1;
            }
            else
            {
                $Message = "Unable to send e-mail.";
            }
        }
    }
}
else
{
}

echo <<< _END
<html>
<head>
<title>Lost Password</title>
</head>
<body>
<h2>Lost Password</h2>

<p>$Message</p>
_END;

if($State == 0)
{
$reCAPATCHAField = reCAPATCHA();

   
echo <<< _END
    <form method="post" action="lostpassword.php$refLink">
        <input type="hidden" name="action" value="request"/>

        <p>Username: <input type="text" name="UserName" value="$UserName" size="40"></p>
        <p>E-mail Address: <input type="text" name="EMail" value="$EMail" size="80"></p>

        $reCAPATCHAField

        <p><input type="submit" value="Send last password e-mail"></p>
    </form>

    <p><a href="lostpassword.php">Lost Password</a> | <a href="newaccount.php">New Account</a></p>
_END;

}

echo <<< _END

<hr />
<p><a href=".">Interactive Stories Homepage</a>.</p>
</body>
</html>
_END;

?>