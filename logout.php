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

if(isset($_POST['LogoutAction']) && $_POST['LogoutAction'] == 'Yes')
{
    LogOutUser();

    header( 'Location: '.$refPath) ;
    exit();
}
else if(isset($_POST['LogoutAction']) && $_POST['LogoutAction'] == 'No')
{
    // Just bail back to the ref page    
    header( 'Location: '.$refPath) ;
    exit();
}


echo <<< _END
<html>
<head>
<title>Log Out</title>
</head>
<body>
<h2>Log Out</h2>
<p>Are you sure you wish to log out?</p>
<p>
<form method="post" action="logout.php$refLink">
<input type="submit" name="LogoutAction" value="Yes"> | 
<input type="submit" name="LogoutAction" value="No"></p>

</form>


<hr />
<p><a href=".">Interactive Stories Homepage</a>.</p>
</body>
</html>
_END;

?>