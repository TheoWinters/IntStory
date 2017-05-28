<?php

function OpenDatabase()
{
    require( dirname(__FILE__) . '\config.php' );
    require( dirname(__FILE__) . '\sessionhandler.php' );

    $db_connection = mysql_connect($db_hostname, $db_ussernam, $db_password);
    if (!$db_connection)
        mysql_fatal_error("Unable to connect to MySQL");

    mysql_select_db($db_database) or mysql_fatal_error("Unable to select database");

    ini_set('session.use_only_cookies', 1);

    // Change the session name
    session_name($session_name);

    $session = new SessionHandler();

    // add db data
    $session->setDbDetails($db_hostname, $db_ussernam, $db_password, $db_database);

    $session->setDbTable('session_handler_table');
    session_set_save_handler(array($session, 'open'),
                             array($session, 'close'),
                             array($session, 'read'),
                             array($session, 'write'),
                             array($session, 'destroy'),
                             array($session, 'gc'));
    session_start();
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
    if($UserID >= 1000)
        $val = mysql_query("SELECT * FROM accounts WHERE ACCOUNT_ID='$UserID'");
    else    
        $val = mysql_query("SELECT * FROM user WHERE USER_ID='$UserID'");

    if(!$val)
        mysql_fatal_error("LoadUser Failed for $UserID");

    return $val;
}

/*
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
*/

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

function FindAccountByNameandEmail($UserName, $UserEmail)
{
    $val = mysql_query("SELECT * FROM accounts WHERE USER_NAME='$UserName' and EMAIL='$UserEmail'");
    if(!$val)
        mysql_fatal_error("FindAccount Failed for $UserName");

    return $val;
}

function FindAccountByName($UserName)
{
    $val = mysql_query("SELECT * FROM accounts WHERE USER_NAME='$UserName'");
    if(!$val)
        mysql_fatal_error("FindAccount Failed for $UserName");

    return $val;
}

function FindAccountByEmail($UserEmail)
{
    $val = mysql_query("SELECT * FROM accounts WHERE EMAIL='$UserEmail'");
    if(!$val)
        mysql_fatal_error("FindAccount Failed for $UserEmail");

    return $val;
}

function LoadAccountByID($AccountID)
{
    $val = mysql_query("SELECT * FROM accounts WHERE ACCOUNT_ID='$AccountID'");
    if(!$val)
        mysql_fatal_error("LoadAccoun Failed");

    return $val;
}

function AddAccount($UserName, $UserEmail, $EncodedPW)
{
    $Query = "INSERT INTO accounts VALUES(NULL, '$UserName', '$EncodedPW', '$UserEmail', 0, 0, 0)";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("AddUserWithPW Failed.");

    return $val;
}

function UpdateAccountPW($AccountID, $EncodedPW)
{
    $Query = "UPDATE accounts SET PASSWORD='$EncodedPW' where ACCOUNT_ID='$AccountID'";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("UpdateAccountEmail Failed.");

    return $val;
}

function UpdateAccountStatus($AccountID, $Status)
{
    $Query = "UPDATE accounts SET STATUS='$Status' where ACCOUNT_ID='$AccountID'";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("UpdateAccountEmail Failed.");

    return $val;
}

function UpdateAccountAccess($AccountID, $Access)
{
    $Query = "UPDATE accounts SET ACCESS='$Access' where ACCOUNT_ID='$AccountID'";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("UpdateAccountEmail Failed.");

    return $val;
}

function EncodePassword($Password)
{
    $Encoded = md5("268E27056A3E52CF3755D193CBEB0594".$Password);
    $Encoded = md5($Encoded."9517FD0BF8FAA655990A4DFFE358E13E");
    $Encoded = strtoupper($Encoded);

    return $Encoded;    
}

// This function validates the password for whatever rules we have for them on the site
function ValidatePasswordRules($Password)
{
    $Message = "";
    
    if(strlen($Password) < 8)
        $Message = $Message."Password to short (Min 8 characters)";
        
    if(strlen($Password) > 128)
        $Message = $Message."Password to long (Max 128 characters)";

    return $Message;
}

// Type: 0x40 - Account Activation
// Type: 0x80 - Password reset
function AddMessage($UserID, $Keycode, $Type)
{
    $Query = "INSERT INTO message_data VALUES(NULL, '$Keycode', '$UserID', '$Type', '".time()."')";

    $val = mysql_query($Query);
    if(!$val)
        mysql_fatal_error("AddReset Failed.");

    return $val;
}

function GetMessage($MessageID)
{
    $val = mysql_query("SELECT * FROM message_data WHERE MESSAGE_ID='$MessageID'");
    if(!$val)
        mysql_fatal_error("GetMessage Failed");

    return $val;
}

function DeleteMessage($MessageID)
{
    $val = mysql_query("DELETE FROM message_data WHERE MESSAGE_ID='$MessageID'");
    if(!$val)
        mysql_fatal_error("DeleteMessage Failed");

    return $val;
}


function EmailMessageCode($UserName)
{
    $Encoded = md5("5A82FA3C8D3D4AD0B604430BD76BE2FEA5BFD199".time().$UserName);
    $Encoded = md5($Encoded."B8758F24B54B088E5F94336CEAD2EF8F76E009AB");
    $Encoded = strtolower($Encoded);

    return $Encoded;    
}




function ValidateAccount($UserID, $EncodedPW)
{
    $val = mysql_query("SELECT * FROM accounts WHERE USER_NAME='$UserID' AND PASSWORD='$EncodedPW'");
    if(!$val)
        mysql_fatal_error("ValidateAccount Failed for $UserID");

    return $val;
}


function LogInUser($UserID, $UserName, $Access, $Status)
{
    $_SESSION['UserID'] = $UserID;
    $_SESSION['UserName'] = $UserName;
    $_SESSION['UserAccess'] = $Access;
    $_SESSION['UserStatus'] = $Status;
}

function LogOutUser()
{
    unset($_SESSION['UserID']);
    unset($_SESSION['UserName']);
    unset($_SESSION['UserAccess']);
    unset($_SESSION['UserStatus']);
}

function LoadCurrentSesson()
{
    if(isset($_SESSION['UserID']))
        $ID = $_SESSION['UserID'];
    else
        return null;
    
    if(isset($_SESSION['UserName']))
        $Name = $_SESSION['UserName'];
    else
        return null;
    
    if(isset($_SESSION['UserAccess']))
        $Access = $_SESSION['UserAccess'];
    else
        return null;

    if(isset($_SESSION['UserStatus']))
        $Status = $_SESSION['UserStatus'];
    else
        return null;
        
    return array(
        "ID" => $ID,
        "Name" => $Name,
        "Access" => $Access,
        "Status" => $Status,
    );
}

function CanAddNewPages($Sesson)
{
    if($Sesson == null)
        return false;
        
    // Status = 1 is account is good standing
    if($_SESSION['UserStatus'] != 1)
        return false;
        
    $Access = $_SESSION['UserAccess'];
    
    if($Access & 0x02)
        return true;
        
    return false;
}

function CanAddNewStories($Sesson)
{
    if($Sesson == null)
        return false;

    // Status = 1 is account is good standing
    if($_SESSION['UserStatus'] != 1)
        return false;

    $Access = $_SESSION['UserAccess'];
    
    if($Access & 0x01)
        return true;
        
    return false;
}


function reCAPATCHA()
{
    require( dirname(__FILE__) . '\config.php' );

    return '<div class="g-recaptcha" data-sitekey="'.$reCAPATCHASiteKey.'"></div>';
}

function reCAPATCHACheck()
{
    require( dirname(__FILE__) . '\config.php' );

   $Response = $_POST['g-recaptcha-response'];
   $RemoteIP = $_SERVER['REMOTE_ADDR'];
   
    $post_data = http_build_query(
        array(
            'secret' => $reCAPATCHASecret,
            'response' => $Response,
            'remoteip' => $RemoteIP
        )
    );

    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($verify);
    $result = json_decode($response);
    
    //echo $response;
    
    return $result->success;
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