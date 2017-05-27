<?php

require( dirname(__FILE__) . '\tools.php' );
OpenDatabase();

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
$UserName = "";
$EMail = "";

if(isset($_POST['action']) && $_POST['action'] == 'create')
{
    $UserName = mysql_fix_string($_POST['UserName']);
    $Password = mysql_fix_string($_POST['Password']);
    $Password2 = mysql_fix_string($_POST['Password2']);
    $EMail = mysql_fix_string($_POST['EMail']);

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
    else if($UserName == "")
    {
        $Message = "Username Missing";
    }
    else if ($Password == "" || $Password2 == "")
    {
        $Message = "Password Missing";
    }
    else if ($EMail == "")
    {
        $Message = "E-Mail Address Missing";
    }
    else
    {   
        $UserInfo = FindAccountByName($UserName);
        if(mysql_num_rows($UserInfo) != 0)
        {
            $Message = "A User by the name of ".$UserName." already exists";
        }
        else
        {
            $UserInfo = FindAccountByEmail($EMail);
            if(mysql_num_rows($UserInfo) != 0)
            {                
                $Message = "That E-mail address is already in use";
            }
            else
            {
                $Encoded = EncodePassword($Password);
                AddAccount($UserName, $EMail, $Encoded);
                $NewUser = ValidateAccount($UserName, $Encoded);
                
                $UserData = mysql_fetch_row($NewUser);
                
                // We are Live at 5.
                LogInUser($UserData[0], $UserData[1], $UserData[4]);            

                // Send confirmation e-mail
                
                header( 'Location: '.$refPath) ;
                exit();
            }
        }
    }
}


echo <<< _END
<html>
<head>
<title>Register a New Account</title>
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<h2>Register a New Account</h2>

<p>$refPath</p>
<p>$refLink</p>

<p>$Message</p>
_END;

$reCAPATCHAField = reCAPATCHA();

if($FormType == 0)
{
echo <<< _END
    <form method="post" action="newaccount.php$refLink">
        <input type="hidden" name="action" value="create"/>

        <p>Username: <input type="text" name="UserName" value="$UserName" size="40"></p>
        <p>E-mail Address: <input type="text" name="EMail" value="$EMail" size="80"></p>
        <p>Password: <input type="password" name="Password" size="40"></p>
        <p>Confirm Password: <input type="password" name="Password2" size="40"></p>
        
        $reCAPATCHAField

        <p><input type="submit" value="Create Account"></p>
    </form>
_END;
}

echo <<< _END
    <hr />
    <p><a href=".">Interactive Stories Homepage</a>.</p>
    </body>
    </html>
_END;


?>