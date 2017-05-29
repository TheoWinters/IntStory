<?php

require( dirname(__FILE__) . '/tools.php' );
OpenDatabase();

$Sesson = LoadCurrentSesson();

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
$FormType = 0;
$ResetType = 0;

if($Sesson == null)
{
    // Check to see if this is an e-mail request or if they just arn't logged in
    if($_SESSION['UserStatus'] == 0xFF)
    {
        // Flag that we are doing an e-mail reset
        $ResetType = 1;
    }
    else
    {    
        // Otherwise just go back to whereever they came from
        header( 'Location: '.$refPath) ;
        exit();    
    }
}

$UserID = "";
if($ResetType == 0)    
    $UserID = $Sesson["ID"];   
else
    $UserID = $_SESSION['UserID'];

if(isset($_POST['action']) && $_POST['action'] == 'reset')
{
    $Password = mysql_fix_string($_POST['NewPassword']);
    $Password2 = mysql_fix_string($_POST['NewPassword2']);

    $reCAPTCHA = reCAPATCHACheck();

    // Validate the input data
    if($reCAPTCHA == false)
    {
        $Message = "reCAPTCHA failed.";
    }    
    else if($Password != $Password2)
    {
        $Message = "Password dosn't match.";
    }    
    else if(ValidatePasswordRules($Password) != "")
    {
        $Message = "Password isn't vaild for the site.<br>".ValidatePasswordRules($Password);
    }
    else
    {
        $UserInfo = LoadAccountByID($UserID);
        if(mysql_num_rows($UserInfo) == 0)
        {
            // Unknown account, bail back
            $Message = "Unknown Account.";
            $FormType = 1;
        }
        
        $Encoded = EncodePassword($Password);
        
        UpdateAccountPW($UserID, $Encoded);
       
        
        $Message = "Password Changed.";
        $FormType = 1;
        
        if($ResetType == 1)
        {
            // Delete the message
            DeleteMessage($_SESSION['MessageID']);
            
            // Clear the temp data for the special reset
            unset($_SESSION['MessageID']);
            LogOutUser();
        }
    }    
}
else
{
    $UserInfo = LoadAccountByID($UserID);
    if(mysql_num_rows($UserInfo) == 0)
    {
        // Unknown account, bail back
        header( 'Location: '.$refPath) ;
        exit();    
    }
    
    $UserData = mysql_fetch_row($UserInfo);
    
    $Message = "Please enter your new password.";
}

echo <<< _END
<html>
<head>
<title>Change your password</title>
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<h2>Change your password</h2>

<p>$Message</p>
_END;

$reCAPATCHAField = reCAPATCHA();

if($FormType == 0)
{
echo <<< _END
    <form method="post" action="passwordreset.php$refLink">
        <input type="hidden" name="action" value="reset"/>

        <p>Password: <input type="password" name="NewPassword" size="40" value = ""></p>
        <p>Confirm Password: <input type="password" name="NewPassword2" size="40"></p>
        
        $reCAPATCHAField

        <p><input type="submit" value="Change Password"></p>
    </form>
_END;
}

if($FormType == 1 & $ResetType == 1)
{
echo <<< _END
    <p> Please <a href="login.php$refLink">log in</a> to use your account.</p>
_END;
}

echo <<< _END
    <hr />
    <p><a href=".">Interactive Stories Homepage</a>.</p>
    </body>
    </html>
_END;


?>