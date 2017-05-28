<?php

require( dirname(__FILE__) . '\tools.php' );
OpenDatabase();
$Sesson = LoadCurrentSesson();
if($Sesson != null)
{
    // If they are current logged in and going to the lost password page, just log them out.
    LogOutUser();
}

$Message = "";
$State = 0;

$UserName = "";
$EMail = "";
$KeyCode = "";
$ID = "";

// actions
// * None: request a lost password e-mail
// * request: send the lost password e-mail, offer to resend
// * reclaim: All them to reset the password

if(isset($_GET['keycode']) && isset($_GET['ID']))
{
    $KeyCode = mysql_fix_string($_GET['keycode']);
    $ID = mysql_fix_string($_GET['ID']);

    // Handle the reset link
    $Reset = GetMessage($ID);
    if(mysql_num_rows($Reset) == 0)
    {      
        $Message = "Password Reset link is invalid.";
        $State = 0;
    }
    else
    {
        $ResetData = mysql_fetch_row($Reset);
        if(strcasecmp($ResetData[1], $KeyCode) != 0 || $ResetData[3] != 0x80)
        {
            $Message = "Password Reset link is invalid.";
            $State = 0;
        }
        else
        {
            // Allow a three hour window for the password reset link to remain valid
            if(time() > $ResetData[4] + 324000)
            {
                $Message = "Password Reset link has expired.";
                $State = 0;
            }
            else
            {
                $UserInfo = LoadAccountByID($ResetData[2]);                
                
                if(mysql_num_rows($UserInfo) == 0)
                {
                    $Message = "Password Reset link is invalid.";
                    $State = 0;               
                }
                else
                {
                    $_SESSION['MessageID'] = $ID;
                    $_SESSION['UserStatus'] = 0xFF; // Temp Status, but otherwise invlaid
                    $_SESSION['UserID'] = $ResetData[2];                
                    
                    header( 'Location: ./passwordreset.php') ;
                    exit();
                }
            }
        }
    }
}
else if(isset($_POST['action']) && $_POST['action'] == 'request')
{
    $UserName = mysql_fix_string($_POST['UserName']);
    $EMail = mysql_fix_string($_POST['EMail']);
    
    $reCAPTCHA = reCAPATCHACheck();

    // Validate the input data
    if($reCAPTCHA == false)
    {
        $Message = "reCAPTCHA failed.";
    }    
    else if($UserName == "")
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
            $ResetCode = EmailMessageCode($UserData[1]);
            $ResetData = AddMessage($UserData[0], $ResetCode, 0x80); 
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
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<h2>Lost Password</h2>

<p>$Message</p>
_END;

// Request password e-mail
if($State == 0)
{
$reCAPATCHAField = reCAPATCHA();

   
echo <<< _END
    <form method="post" action="lostpassword.php">
        <input type="hidden" name="action" value="request"/>

        <p>Username: <input type="text" name="UserName" value="$UserName" size="40"></p>
        <p>E-mail Address: <input type="text" name="EMail" value="$EMail" size="80"></p>

        $reCAPATCHAField

        <p><input type="submit" value="Send last password e-mail"></p>
    </form>

    <p><a href="lostpassword.php">Lost Password</a> | <a href="newaccount.php">New Account</a></p>
_END;
}

// Change Password
if($State == 1)
{
    
}

echo <<< _END

<hr />
<p><a href=".">Interactive Stories Homepage</a>.</p>
</body>
</html>
_END;

?>