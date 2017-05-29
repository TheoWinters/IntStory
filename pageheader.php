<?php
if($Sesson == null)
{
    echo '<p><a href="login.php">Log in</a> or <a href="newaccount.php">Register</a> </p>';
}
else
{
    echo '<p>Welcome '.$Sesson["Name"].'</p>';     
    //echo '<p><a href="userpanel.php">User Control Panel</a> | <a href="logout.php">Log Out</a></p>';
    echo '<p><a href="logout.php">Log Out</a></p>';
}

echo '<hr />'
?>
