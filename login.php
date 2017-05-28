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

if(isset($_POST['action']) && $_POST['action'] == 'login')
{
    // Try to log in!
    $UserName = mysql_fix_string($_POST['UserName']);
    $Password = mysql_fix_string($_POST['Password']);
    
    // Validate the input data
    if($UserName == "")
    {
        $Message = "Username Missing";
    }
    else if ($Password == "")
    {
        $Message = "Password Missing";
    }
    else
    {   
        // Make the encoded password
        $Encoded = EncodePassword($Password);

        $User = ValidateAccount($UserName, $Encoded);
        $UserData = mysql_fetch_row($User);
        
        // And see if the user even exists
        if(!isset($UserData[0]))
        {
            $Message = "Invalid Username or Password";
        }
        else
        {
            // We are Live at 5.
            LogInUser($UserData[0], $UserData[1], $UserData[4], $UserData[5]);
            
            header( 'Location: '.$refPath) ;
            exit();
        }   
    }
}


echo <<< _END
<html>
<head>
<title>Log In</title>
</head>
<body>
<h2>Log In</h2>
<p>$Message</p>
<form method="post" action="login.php$refLink">
<input type="hidden" name="action" value="login"/>
<p>Username:<input type="text" name="UserName"></p>
<p>Password:<input type="password" name="Password"></p>
<p><input type="submit" value="Log In"></p>

</form>

<p><a href="lostpassword.php">Lost Password</a> | <a href="newaccount.php">New Account</a></p>

<hr />
<p><a href=".">Interactive Stories Homepage</a>.</p>
</body>
</html>
_END;

?>