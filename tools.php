<?php

function OpenDatabase()
{
    require_once 'config.php';

    $db_connection = mysql_connect($db_hostname, $db_ussernam, $db_password);
    if (!$db_connection)
        mysql_fatal_error("Unable to connect to MySQL");

    mysql_select_db($db_database) or mysql_fatal_error("Unable to select database");
}

function mysql_fatal_error($msg)
{
    $msg2 = mysql_error();

    echo <<< _END
        We are sorry, but it was not possible to complete
        the requested task. The error message we got was:

        <p>$msg: $msg2</p>

        Please click the back button on your browser
        and try again. If you are still having problems,
        Querying a MySQL Database with PHP
        please contact
        our administrator</a>. Thank you.
_END;
    die();

}

function PageError($ErrorString)
{
    $Ref = "";
    if(isset($_SERVER['HTTP_REFERER']))
        $Ref = $_SERVER['HTTP_REFERER'];

    echo
<<< _END
<html>
<head><title>Page Error</title></head>
<body>
<p>$ErrorString</p>
<a href="$Ref">Pervious Page</a>
</body></html>
_END;

    die();
}

function LoadStories()
{
    $val = mysql_query("SELECT * FROM stories WHERE APPROVED!='0'");
    if(!$val)
        mysql_fatal_error("LoadStories Failed.");

    return $val;
}

function LoadStory($StoryID)
{
    $val = mysql_query("SELECT * FROM stories WHERE STORY_ID='$StoryID'");
    if(!$val)
        mysql_fatal_error("LoadPage Failed for $StoryID");

    return $val;
}

function AddStory($Title, $Desc)
{
    $Query = "INSERT INTO stories VALUES(NULL, '$Title', '$Desc', 0, 0)";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("AddStory Failed.");

    return $val;
}

function UpdateStory($StoryID, $FirstPageID)
{
    $Query = "UPDATE stories SET FIRST_PAGE_ID='$FirstPageID' where STORY_ID='$StoryID'";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("AddStory Failed.");

    return $val;
}

function LoadPageList($Count)
{
    $val = mysql_query("SELECT * FROM pages ORDER BY MODIFIED DESC LIMIT 0, $Count");
    if(!$val)
        mysql_fatal_error("LoadPageList Failed");

    return $val;
}

function LoadAllPages($StoryID)
{
    $val = mysql_query("SELECT * FROM pages WHERE STORY_ID='$StoryID'");
    if(!$val)
        mysql_fatal_error("LoadPage Failed for $PageID");

    return $val;    
}

function LoadPage($PageID)
{
    $val = mysql_query("SELECT * FROM pages WHERE PAGE_ID='$PageID'");
    if(!$val)
        mysql_fatal_error("LoadPage Failed for $PageID");

    return $val;
}

function AddPage($ParrentPageID, $Story_ID, $Title, $Contents, $UserID)
{
    $IP = $_SERVER['REMOTE_ADDR'];
    $Query = "INSERT INTO pages VALUES(NULL, '$ParrentPageID', '$Story_ID', '$Title', '$Contents', '$UserID', '$IP', NULL)";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("AddPage Failed.");

    return $val;
}

function LoadUser($UserID)
{
    $val = mysql_query("SELECT * FROM user WHERE USER_ID='$UserID'");
    if(!$val)
        mysql_fatal_error("LoadUser Failed for $UserID");

    return $val;
}

function FindUser($UserEmail)
{
    $val = mysql_query("SELECT * FROM user WHERE EMAIL='$UserEmail'");
    if(!$val)
        mysql_fatal_error("LoadUser Failed for $UserID");

    return $val;
}

function AddUser($UserName, $UserEmail)
{
    $Query = "INSERT INTO user VALUES(NULL, '$UserName', '', '$UserEmail', 0)";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("AddUser Failed.");

    return $val;
}

function LoadPage_Links($PageID)
{
    $val = mysql_query("SELECT * FROM page_link WHERE OWNER_PAGE_ID='$PageID'");
    if(!$val)
        mysql_fatal_error("LoadPage_Links Failed for $PageID");

    return $val;
}

function LoadPage_Link($LinkID)
{
    $val = mysql_query("SELECT * FROM page_link WHERE LINK_ID='$LinkID'");
    if(!$val)
        mysql_fatal_error("Load_Links Failed for $LinkID");

    return $val;
}

function AddPage_Links($OwnerPageID, $Title)
{
    $Query = "INSERT INTO page_link VALUES(NULL, '$OwnerPageID', 0, '$Title', 0)";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("AddPage_Links Failed.");

    return $val;
}

function UpdatePage_Links($LinkID, $PageID)
{
    $Query = "UPDATE page_link SET DEST_PAGE_ID='$PageID' where LINK_ID='$LinkID'";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("LockPage_Links Failed.");

    return $val;
}

function LockPage_Links($LinkID)
{
    $LockTime = time() + 3600; // 60 min lock time

    $Query = "UPDATE page_link SET `LOCK`='$LockTime' where LINK_ID='$LinkID'";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("LockPage_Links Failed.");

    setcookie("CYOC_LOCK", $LockTime, $LockTime);

    return $val;
}

function UnlockPage_Links($LinkID)
{
    $Query = "UPDATE page_link SET `LOCK`='0' where LINK_ID='$LinkID'";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("LockPage_Links Failed.");


    // set the expiration date to one hour ago
    setcookie("CYOC_LOCK", "", time()-3600);

    return $val;
}

function CheckLockPage_Links($LinkID)
{
    $Link = LoadPage_Link($LinkID);
    $LinkData = mysql_fetch_row($Link);

    if($LinkData[4] == 0 || time() > $LinkData[4])
        return true;

    $CookieValue = 0;

    if(isset($_COOKIE['CYOC_LOCK']))
        $CookieValue = $_COOKIE['CYOC_LOCK'];

    // if the cookie is the same as the LOCK value, we're the ones who working with the page
    if($LinkData[4] == $CookieValue)
        return true;

    return false;
}

function mysql_entities_string($string)
{
    if (get_magic_quotes_gpc())
        $string = stripslashes($string);

    return htmlentities(utf8_encode($string));
}

function mysql_entities_fix_string($string)
{
    return htmlentities(mysql_fix_string($string));
}

function mysql_fix_string($string)
{
    if (get_magic_quotes_gpc())
        $string = stripslashes($string);

    return mysql_real_escape_string($string);
}

?>